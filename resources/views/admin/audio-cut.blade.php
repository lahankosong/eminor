@extends('layouts.admin')

@push('styles')
<style>
    .ai-header { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border); }
    .ai-header h2 { font-size:1rem; font-weight:500; color:var(--text); }
    .ai-header p { font-size:12px; color:var(--text-3); margin-top:2px; }
    .btn-back { font-size:12px; color:var(--text-2); text-decoration:none; border:1px solid var(--border); padding:6px 14px; border-radius:8px; }
    .btn-back:hover { color:var(--text); border-color:var(--text-3); }

    .card { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; margin-bottom:1.1rem; overflow:hidden; }
    .card-head { padding:0.8rem 1.1rem; border-bottom:1px solid var(--border); font-size:12px; color:var(--text-2); font-weight:600; letter-spacing:0.04em; display:flex; justify-content:space-between; align-items:center; gap:10px; }
    .card-body { padding:1.1rem; }

    .fi { background:var(--bg-3); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px; padding:8px 11px; outline:none; font-family:inherit; width:100%; }
    .fi:focus { border-color:var(--text-3); }
    .btn { padding:8px 15px; border-radius:8px; font-size:13px; font-weight:500; border:none; cursor:pointer; transition:0.2s; }
    .btn-primary { background:var(--text); color:var(--bg); }
    .btn-primary:hover { filter:brightness(0.88); }
    .btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
    .btn-soft { background:var(--bg-3); border:1px solid var(--border); color:var(--text-2); }
    .btn-soft:hover { border-color:var(--text-3); color:var(--text); }
    .btn-accent { background:var(--accent-dim); color:var(--accent); }
    .btn-sm { padding:5px 11px; font-size:12px; }

    .row { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .muted { font-size:11px; color:var(--text-3); }
    .src-grid { display:grid; grid-template-columns:1fr auto 1fr; gap:10px; align-items:center; }
    .src-grid .or { font-size:11px; color:var(--text-3); }
    @media(max-width:600px){ .src-grid{ grid-template-columns:1fr; } .src-grid .or{ text-align:center; } }

    /* Waveform + region — drag handles on canvas */
    .region-wave-wrap { position:relative; border-radius:8px; overflow:hidden; background:#070d18; margin:12px 0 4px; touch-action:none; }
    #adminWave { display:block; width:100%; height:120px; }
    .adm-zoom-bar { display:flex; align-items:center; gap:5px; margin:5px 0 8px; }
    .adm-zoom-btn { padding:4px 9px; border-radius:6px; border:1px solid var(--border); background:var(--bg-3); color:var(--text-3); font-size:12px; cursor:pointer; line-height:1; }
    .adm-zoom-btn:hover { border-color:var(--accent); color:var(--accent); }
    .adm-scroll-wrap { flex:1; height:5px; background:var(--border); border-radius:3px; position:relative; cursor:grab; }
    .adm-scroll-bar { position:absolute; top:0; height:100%; background:rgba(99,102,241,.35); border:1px solid #818cf8; border-radius:3px; left:0; width:100%; }
    .adm-sel-info { display:flex; align-items:center; gap:6px; margin-bottom:8px; flex-wrap:wrap; font-variant-numeric:tabular-nums; }

    .clip-item { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--border-2); flex-wrap:wrap; }
    .clip-item:last-child { border-bottom:none; }
    .clip-name { font-size:13px; color:var(--text); font-weight:500; }
    .clip-meta { font-size:11px; color:var(--text-3); }
    .btn-del { background:transparent; border:1px solid var(--border); color:var(--text-3); border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer; }
    .btn-del:hover { border-color:#ef4444; color:#ef4444; }

    .result-strip { margin-top:12px; padding:11px; border:1px solid var(--accent); border-radius:10px; background:var(--accent-dim); display:none; }
    .status { font-size:12px; color:var(--text-3); margin-top:10px; min-height:18px; }
    .spinner { display:inline-block; width:13px; height:13px; border:2px solid var(--text-3); border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }
    audio { width:100%; }
    audio.mini { height:34px; }
</style>
@endpush

@section('content')

<div class="ai-header">
    <div>
        <h2>✂️ Pemotong Lagu</h2>
        <p>Ambil part lagu (mis. verse) untuk video — diproses di browser, server tidak terbebani</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.ai-agent') }}" class="btn-back">← AI Agent</a>
        <a href="{{ route('admin.index') }}" class="btn-back">Panel Admin</a>
    </div>
</div>

{{-- ===== EDITOR (sumber + pilih bagian + potong jadi satu) ===== --}}
<div class="card">
    <div class="card-head">
        <span>🎵 Editor</span>
        <span class="muted" id="srcInfo">Pilih lagu untuk mulai</span>
    </div>
    <div class="card-body">
        {{-- Sumber --}}
        <div class="src-grid">
            <select class="fi" id="songSelect">
                <option value="">— Pilih dari pustaka lagu —</option>
                @foreach($songs as $song)
                <option value="{{ asset($song->audio_file) }}" data-title="{{ $song->title }}">{{ $song->title }}@if($song->era) · {{ $song->era }}@endif</option>
                @endforeach
            </select>
            <span class="or">atau</span>
            <input type="file" class="fi" id="fileInput" accept="audio/*">
        </div>

        {{-- Area edit (muncul setelah lagu dimuat) --}}
        <div id="editArea" style="display:none;margin-top:14px;">
            <audio id="player" controls preload="metadata" style="width:100%;margin-bottom:10px;"></audio>

            <div class="region-wave-wrap" id="regionTrack">
                <canvas id="adminWave"></canvas>
            </div>
            <div style="font-size:10px;color:var(--text-3);text-align:center;margin:3px 0 6px;">Seret △ indigo(Mulai) / △ kuning(Akhir) · scroll/pinch untuk zoom</div>

            {{-- Zoom bar --}}
            <div class="adm-zoom-bar">
                <button class="adm-zoom-btn" onclick="admZoomIn()">🔍+</button>
                <button class="adm-zoom-btn" onclick="admZoomOut()">🔍−</button>
                <button class="adm-zoom-btn" onclick="admZoomReset()">↺</button>
                <div class="adm-scroll-wrap" id="admScrollWrap">
                    <div class="adm-scroll-bar" id="admScrollBar"></div>
                </div>
                <span id="admZoomLbl" style="font-size:10px;color:var(--text-3);white-space:nowrap;min-width:46px;text-align:right;">Semua</span>
            </div>

            {{-- Sel info --}}
            <div class="adm-sel-info">
                <span style="font-size:11px;color:#818cf8;font-weight:700;">▶ <span id="admSelS">0:00.0</span></span>
                <span style="color:var(--text-3);">→</span>
                <span style="font-size:11px;color:#f59e0b;font-weight:700;"><span id="admSelE">0:00.0</span> ◀</span>
                <span style="color:var(--text-3);">|</span>
                <span style="font-size:11px;color:#22c55e;font-weight:700;"><span id="admSelD">0:00.0</span></span>
                <span class="muted" id="segLabel" style="margin-left:auto;"></span>
            </div>

            <div class="row" style="margin-top:8px;">
                <button class="btn btn-soft btn-sm" id="admBtnPlay"  onclick="admPlay()">▶ Play</button>
                <button class="btn btn-soft btn-sm" id="admBtnPause" onclick="admPause()" style="display:none;border-color:rgba(250,204,21,.3);color:#facc15;">⏸ Pause</button>
                <button class="btn btn-soft btn-sm" id="previewBtn"  onclick="previewRegion()">▶ Preview</button>
                <button class="btn btn-soft btn-sm"                  onclick="admStop()">⏹</button>
                <select id="admFmt" class="btn btn-soft btn-sm" style="cursor:pointer;font-size:11px;">
                    <option value="mp3-128">MP3 128k</option>
                    <option value="mp3-192">MP3 192k</option>
                    <option value="mp3-320">MP3 320k</option>
                    <option value="wav">WAV</option>
                </select>
                <button class="btn btn-primary" id="cutBtn" style="margin-left:auto;" onclick="doCut()">✂️ Potong</button>
            </div>
            <div class="status" id="status"></div>

            {{-- Hasil potongan terakhir --}}
            <div class="result-strip" id="resultWrap">
                <div class="muted" style="margin-bottom:6px;">✓ Hasil potongan terakhir</div>
                <audio id="clipPlayer" class="mini" controls></audio>
                <div class="row" style="margin-top:8px;">
                    <input type="text" class="fi" id="clipName" style="flex:1;min-width:150px;" placeholder="Nama potongan">
                    <button class="btn btn-accent btn-sm" onclick="saveClip()">💾 Simpan</button>
                    <a class="btn btn-soft btn-sm" id="downloadBtn" download>⬇️ Unduh</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== POTONGAN TERSIMPAN ===== --}}
<div class="card">
    <div class="card-head">
        <span>📁 Potongan Tersimpan</span>
        <span class="muted">di perangkat ini (IndexedDB), bukan server</span>
    </div>
    <div class="card-body">
        <div id="clipList"><p class="muted">Belum ada potongan tersimpan.</p></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
// ── Admin Audio Cutter v2 — drag handles + zoom ─────────────────────────────
var _ctx=null,_buf=null,_src=null,duration=0,srcName='lagu';
var _startT=0,_endT=0,_playing=false,_paused=false,_pausedAt=0;
var _playOffset=0,_playCtxTime=0,_prevStop=null,_raf=null;
var _vS=0,_vE=1,_drag=null,_pinchD=null,_pinchMid=0,_vS0=0,_vE0=0;
var _sbDrag=false,_sbX0=0,_sbVS0=0,_sbVE0=0;
var lastClipBlob=null,lastClipUrl=null;

function ga(id){return document.getElementById(id);}
function fmtA(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=Math.floor(s%60);return m+':'+(x<10?'0':'')+x;}
function fmtAP(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=s%60;return m+':'+(x<10?'0':'')+x.toFixed(1);}
function setStatus(html){ga('status').innerHTML=html||'';}

// ── Load ──────────────────────────────────────────────────────────────────────
ga('songSelect').addEventListener('change',function(){
    if(!this.value)return;
    ga('fileInput').value='';
    fetchAndLoad(this.value,this.options[this.selectedIndex].getAttribute('data-title')||'lagu');
});
ga('fileInput').addEventListener('change',function(){
    var f=this.files[0];if(!f)return;
    ga('songSelect').value='';readFileAndLoad(f);
});
function readFileAndLoad(file){
    srcName=file.name.replace(/\.[^.]+$/,'');
    setStatus('<span class="spinner"></span> Membaca file…');
    var r=new FileReader();r.onload=function(e){decodeBuffer(e.target.result);};r.readAsArrayBuffer(file);
}
async function fetchAndLoad(url,name){
    srcName=name;setStatus('<span class="spinner"></span> Mengambil file…');
    try{var res=await fetch(url);var ab=await res.arrayBuffer();decodeBuffer(ab);}
    catch(e){setStatus('⚠️ Gagal: '+e.message);}
}
function decodeBuffer(ab){
    if(_ctx){try{_ctx.close();}catch(e){}}
    _ctx=new(window.AudioContext||window.webkitAudioContext)();
    setStatus('<span class="spinner"></span> Mendekode…');
    _ctx.decodeAudioData(ab,function(buf){
        _buf=buf;duration=buf.duration;_startT=0;_endT=duration;_vS=0;_vE=1;
        ga('srcInfo').textContent='🎵 '+srcName+' · '+fmtA(duration);
        ga('editArea').style.display='block';
        ga('resultWrap').style.display='none';
        setStatus('');admDraw();
    },function(){setStatus('⚠️ Gagal mendekode — coba format lain.');});
}

// ── Draw ──────────────────────────────────────────────────────────────────────
function tToXA(t,W){return((t-_vS*duration)/((_vE-_vS)*duration))*W;}
function xToTA(x,W){return _vS*duration+(x/W)*(_vE-_vS)*duration;}
function admDraw(){
    var c=ga('adminWave'),wrap=ga('regionTrack');
    if(!c||!wrap)return;
    var W=Math.round(wrap.getBoundingClientRect().width||560),H=120;
    if(W<10)W=560;c.width=W;c.height=H;
    var gc=c.getContext('2d');
    gc.fillStyle='#070d18';gc.fillRect(0,0,W,H);
    if(!_buf)return;
    var data=_buf.getChannelData(0),sr=_buf.sampleRate;
    var totS=_vS*duration,totE=_vE*duration;
    for(var i=0;i<W;i++){
        var t0=totS+(i/W)*(totE-totS),t1=totS+((i+1)/W)*(totE-totS);
        var i0=Math.floor(t0*sr),i1=Math.ceil(t1*sr),max=0;
        for(var j=i0;j<i1&&j<data.length;j++){var av=Math.abs(data[j]);if(av>max)max=av;}
        var inS=(t0+t1)/2>=_startT&&(t0+t1)/2<=_endT;
        var bH=Math.max(2,max*(H-14)*.9),y=(H-14-bH)/2;
        gc.fillStyle=inS?'rgba(99,102,241,.88)':'rgba(99,102,241,.2)';
        gc.fillRect(i,y,1,bH);
    }
    var sx=tToXA(_startT,W),ex=tToXA(_endT,W);
    gc.fillStyle='rgba(99,102,241,.07)';gc.fillRect(sx,0,ex-sx,H-14);
    var vD=totE-totS,tk=vD<=2?.2:vD<=5?.5:vD<=20?1:vD<=60?5:vD<=300?30:60;
    gc.fillStyle='rgba(10,14,26,.9)';gc.fillRect(0,H-14,W,14);
    gc.font='8px monospace';
    for(var ts=Math.ceil(totS/tk)*tk;ts<=totE+.001;ts+=tk){
        var tx=tToXA(ts,W);if(tx<0||tx>W)continue;
        gc.fillStyle='rgba(255,255,255,.2)';gc.fillRect(tx,H-14,1,14);
        gc.fillStyle='rgba(255,255,255,.45)';gc.fillText(fmtAP(ts),tx+2,H-3);
    }
    if(_playing||_paused){
        var pp=_paused?_pausedAt:(_playOffset+(_ctx.currentTime-_playCtxTime));
        if(pp>=0&&pp<=duration){var px=tToXA(pp,W);gc.fillStyle='rgba(255,255,255,.85)';gc.fillRect(px-.75,0,1.5,H-14);}
    }
    function dHA(hx,col){if(hx<-12||hx>W+12)return;gc.beginPath();gc.moveTo(hx-9,0);gc.lineTo(hx+9,0);gc.lineTo(hx,16);gc.closePath();gc.fillStyle=col;gc.fill();gc.globalAlpha=0.4;gc.fillRect(hx-.75,16,1.5,H-14-16);gc.globalAlpha=1;}
    dHA(sx,'#818cf8');dHA(ex,'#f59e0b');
    ga('admSelS')&&(ga('admSelS').textContent=fmtAP(_startT));
    ga('admSelE')&&(ga('admSelE').textContent=fmtAP(_endT));
    ga('admSelD')&&(ga('admSelD').textContent=fmtAP(_endT-_startT));
    ga('segLabel')&&(ga('segLabel').textContent='durasi: '+fmtAP(_endT-_startT));
    var sw=ga('admScrollWrap'),sb=ga('admScrollBar'),zl=ga('admZoomLbl');
    if(sw&&sb){var spW=sw.getBoundingClientRect().width||200;sb.style.left=(_vS*spW)+'px';sb.style.width=Math.max(8,(_vE-_vS)*spW)+'px';}
    if(zl)zl.textContent=_vE-_vS>=.99?'Semua':Math.round(100*(_vE-_vS))+'%';
}

// ── Zoom ──────────────────────────────────────────────────────────────────────
function zoomA(p,f){if(!_buf)return;var len=_vE-_vS,nL=Math.min(1,Math.max(0.005,len*f));var s=_vS+p*len-p*nL,e=s+nL;if(s<0){s=0;e=Math.min(1,nL);}if(e>1){e=1;s=Math.max(0,1-nL);}_vS=s;_vE=e;admDraw();}
window.admZoomIn=function(){zoomA(.5,.6);};
window.admZoomOut=function(){zoomA(.5,1.7);};
window.admZoomReset=function(){if(!_buf)return;_vS=0;_vE=1;admDraw();};

// ── Canvas events ─────────────────────────────────────────────────────────────
var admC=ga('adminWave');
(function(){
    function cXA(cx){var r=admC.getBoundingClientRect();return(cx-r.left)*(admC.width/Math.max(1,r.width));}
    function nearHA(px,tol){if(!_buf)return null;var W=admC.width||560,sx=tToXA(_startT,W),ex=tToXA(_endT,W);var ds=Math.abs(px-sx),de=Math.abs(px-ex);if(ds<tol&&ds<=de)return'start';if(de<tol)return'end';return null;}
    admC.addEventListener('mousedown',function(e){var h=nearHA(cXA(e.clientX),22);if(h){_drag=h;e.preventDefault();}});
    admC.addEventListener('mousemove',function(e){
        if(_drag&&_drag!=='pinch'&&_buf){var t=xToTA(cXA(e.clientX),admC.width);if(_drag==='start')_startT=Math.max(0,Math.min(t,_endT-.05));else _endT=Math.min(duration,Math.max(t,_startT+.05));admDraw();}
        else admC.style.cursor=nearHA(cXA(e.clientX),22)?'ew-resize':'default';
    });
    document.addEventListener('mouseup',function(){_drag=null;});
    admC.addEventListener('wheel',function(e){if(!_buf)return;e.preventDefault();var r=admC.getBoundingClientRect();zoomA((e.clientX-r.left)/r.width,e.deltaY>0?1.4:0.7);},{passive:false});
    admC.addEventListener('touchstart',function(e){
        if(e.touches.length===2){var t0=e.touches[0],t1=e.touches[1];_drag='pinch';_pinchD=Math.hypot(t1.clientX-t0.clientX,t1.clientY-t0.clientY);var r=admC.getBoundingClientRect();_pinchMid=((t0.clientX+t1.clientX)/2-r.left)/r.width;_vS0=_vS;_vE0=_vE;e.preventDefault();return;}
        if(e.touches.length===1){var h=nearHA(cXA(e.touches[0].clientX),36);if(h){_drag=h;e.preventDefault();}}
    },{passive:false});
    admC.addEventListener('touchmove',function(e){
        if(_drag==='pinch'&&e.touches.length===2){var t0=e.touches[0],t1=e.touches[1];var d=Math.hypot(t1.clientX-t0.clientX,t1.clientY-t0.clientY),f=_pinchD/d,len=_vE0-_vS0,nL=Math.min(1,Math.max(0.01,len*f));var s=_vS0+_pinchMid*len-_pinchMid*nL,e2=s+nL;if(s<0){s=0;e2=Math.min(1,nL);}if(e2>1){e2=1;s=Math.max(0,1-nL);}_vS=s;_vE=e2;admDraw();e.preventDefault();}
        else if(_drag&&_drag!=='pinch'&&e.touches.length===1&&_buf){var t=xToTA(cXA(e.touches[0].clientX),admC.width);if(_drag==='start')_startT=Math.max(0,Math.min(t,_endT-.05));else _endT=Math.min(duration,Math.max(t,_startT+.05));admDraw();e.preventDefault();}
    },{passive:false});
    admC.addEventListener('touchend',function(){_drag=null;_pinchD=null;});
    // Scrollbar
    var sw=ga('admScrollWrap');
    if(sw){
        function sbSA(cx){var sb=ga('admScrollBar'),r=sw.getBoundingClientRect();var bL=parseFloat(sb.style.left)||0,bW=parseFloat(sb.style.width)||r.width;if(cx>=bL&&cx<=bL+bW){_sbDrag=true;_sbX0=cx-bL;_sbVS0=_vS;_sbVE0=_vE;return true;}return false;}
        function sbMA(cx){if(!_sbDrag)return;var r=sw.getBoundingClientRect(),span=_sbVE0-_sbVS0,s=Math.max(0,Math.min((cx-_sbX0)/r.width,1-span));_vS=s;_vE=s+span;admDraw();}
        sw.addEventListener('mousedown',function(e){if(sbSA(e.clientX-sw.getBoundingClientRect().left))e.preventDefault();});
        document.addEventListener('mousemove',function(e){sbMA(e.clientX-sw.getBoundingClientRect().left);});
        document.addEventListener('mouseup',function(){_sbDrag=false;});
    }
})();

// ── Playback ──────────────────────────────────────────────────────────────────
function ctxGoA(cb){if(!_ctx)return;(_ctx.state==='suspended'?_ctx.resume():Promise.resolve()).then(cb);}
function _stopSrc(){if(_prevStop){clearTimeout(_prevStop);_prevStop=null;}if(_raf){cancelAnimationFrame(_raf);_raf=null;}if(_src){try{_src.stop();}catch(e){}_src=null;}}
function _rafTick(){if(!_playing)return;admDraw();_raf=requestAnimationFrame(_rafTick);}
function showPauseA(v){var bp=ga('admBtnPlay'),pp=ga('admBtnPause');if(bp)bp.style.display=v?'none':'';if(pp)pp.style.display=v?'':'none';}
function admStop(){_stopSrc();_playing=false;_paused=false;showPauseA(false);admDraw();}
window.admStop=admStop;
window.admPlay=function(){if(!_buf)return;if(_paused){ctxGoA(function(){_src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);_playOffset=_pausedAt;_playCtxTime=_ctx.currentTime;_src.start(0,_pausedAt);_playing=true;_paused=false;showPauseA(true);_rafTick();});return;}admStop();ctxGoA(function(){_src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);_playOffset=0;_playCtxTime=_ctx.currentTime;_src.start(0,0);_playing=true;_paused=false;showPauseA(true);_rafTick();});};
window.admPause=function(){if(!_playing||_paused)return;_pausedAt=_playOffset+(_ctx.currentTime-_playCtxTime);_stopSrc();_paused=true;_playing=false;showPauseA(false);admDraw();};
function previewRegion(){if(!_buf)return;admStop();ctxGoA(function(){_src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);_playOffset=_startT;_playCtxTime=_ctx.currentTime;_src.start(0,_startT,_endT-_startT);_playing=true;_paused=false;_prevStop=setTimeout(admStop,(_endT-_startT)*1000+400);showPauseA(true);_rafTick();});}
window.previewRegion=previewRegion;

// ── Encode helpers ────────────────────────────────────────────────────────────
function _wavEncode(buf,s,e){
    var sr=buf.sampleRate,nCh=buf.numberOfChannels;
    var ss=Math.floor(s*sr),es=Math.min(Math.ceil(e*sr),buf.length),n=es-ss;
    var ab=new ArrayBuffer(44+n*nCh*2),v=new DataView(ab);
    function ws(off,str){for(var i=0;i<str.length;i++)v.setUint8(off+i,str.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);
    var off=44;
    for(var i=0;i<n;i++) for(var ch=0;ch<nCh;ch++){var x=Math.max(-1,Math.min(1,buf.getChannelData(ch)[ss+i]));v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;}
    return new Blob([ab],{type:'audio/wav'});
}
function _mp3Encode(buf,s,e,kbps){
    var sr=buf.sampleRate,nCh=buf.numberOfChannels;
    var ss=Math.floor(s*sr),es=Math.min(Math.ceil(e*sr),buf.length),n=es-ss;
    var enc=new lamejs.Mp3Encoder(nCh>1?2:1,sr,kbps||128),mp3=[],BLOCK=1152;
    function f2i(f){var a=new Int16Array(f.length);for(var i=0;i<f.length;i++)a[i]=Math.max(-32768,Math.min(32767,f[i]*32767));return a;}
    var l16=f2i(buf.getChannelData(0).subarray(ss,es));
    var r16=nCh>1?f2i(buf.getChannelData(1).subarray(ss,es)):l16;
    for(var i=0;i<n;i+=BLOCK){var d=enc.encodeBuffer(l16.subarray(i,i+BLOCK),r16.subarray(i,i+BLOCK));if(d.length)mp3.push(new Uint8Array(d));}
    var end=enc.flush();if(end.length)mp3.push(new Uint8Array(end));
    return new Blob(mp3,{type:'audio/mpeg'});
}

// ── Cut ──────────────────────────────────────────────────────────────────────
function doCut(){
    if(!_buf){alert('Pilih lagu dulu.');return;}
    var s=_startT,dur=_endT-s;
    if(dur<0.1){alert('Bagian terlalu pendek.');return;}
    var fv=ga('admFmt')?ga('admFmt').value:'mp3-128';
    var isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    var cut=document.getElementById('cutBtn');
    cut.disabled=true;setStatus('<span class="spinner"></span> Memotong…');
    setTimeout(function(){
        try{
            var blob=isWav?_wavEncode(_buf,s,_endT):_mp3Encode(_buf,s,_endT,kbps);
            if(lastClipUrl)URL.revokeObjectURL(lastClipUrl);
            lastClipBlob=blob;lastClipUrl=URL.createObjectURL(blob);
            document.getElementById('clipPlayer').src=lastClipUrl;
            var dl=document.getElementById('downloadBtn');
            dl.href=lastClipUrl;dl.download=srcName+'_'+fmt(s).replace(':','m')+'-'+fmt(_endT).replace(':','m')+'.'+ext;
            document.getElementById('clipName').value=srcName+' ('+fmt(s)+'–'+fmt(_endT)+')';
            document.getElementById('resultWrap').style.display='block';
            setStatus('✓ Potongan jadi ('+fmt(dur)+'). Simpan/unduh, atau geser handle & potong part lain.');
        }catch(e){setStatus('⚠️ Gagal: '+(e.message||e));}
        finally{cut.disabled=false;}
    },50);
}

// ── IndexedDB ──
function idbOpen(){return new Promise(function(res,rej){var r=indexedDB.open('mafAudioClips',1);r.onupgradeneeded=function(){r.result.createObjectStore('clips',{keyPath:'id',autoIncrement:true});};r.onsuccess=function(){res(r.result);};r.onerror=function(){rej(r.error);};});}
async function idbAll(){var db=await idbOpen();return new Promise(function(res){var t=db.transaction('clips').objectStore('clips').getAll();t.onsuccess=function(){res(t.result||[]);};t.onerror=function(){res([]);};});}
async function idbAdd(rec){var db=await idbOpen();return new Promise(function(res){var t=db.transaction('clips','readwrite').objectStore('clips').add(rec);t.onsuccess=function(){res(t.result);};});}
async function idbDel(id){var db=await idbOpen();return new Promise(function(res){db.transaction('clips','readwrite').objectStore('clips').delete(id).onsuccess=function(){res();};});}

async function saveClip(){
    if(!lastClipBlob){alert('Belum ada potongan.');return;}
    var name=(document.getElementById('clipName').value||'Potongan').trim();
    var extSaved=lastClipBlob.type.includes('mpeg')?'mp3':'wav';
    await idbAdd({name:name,ext:extSaved,blob:lastClipBlob,size:lastClipBlob.size,createdAt:Date.now()});
    setStatus('✓ Tersimpan: '+name+'. Bisa langsung potong part lain.');
    renderClips();
}
async function renderClips(){
    var list=document.getElementById('clipList'), all=await idbAll();
    if(!all.length){list.innerHTML='<p class="muted">Belum ada potongan tersimpan.</p>';return;}
    list.innerHTML='';
    all.sort(function(a,b){return b.createdAt-a.createdAt;}).forEach(function(c){
        var url=URL.createObjectURL(c.blob), kb=Math.round(c.size/1024);
        var div=document.createElement('div'); div.className='clip-item';
        div.innerHTML='<div style="flex:1;min-width:160px;"><div class="clip-name">'+(c.name||'Potongan').replace(/</g,'&lt;')+'</div><div class="clip-meta">.'+(c.ext||'wav')+' · '+kb+' KB</div><audio class="mini" controls src="'+url+'"></audio></div><a class="btn btn-accent btn-sm" href="'+url+'" download="'+(c.name||'potongan')+'.'+(c.ext||'wav')+'">⬇️</a><button class="btn-del" data-id="'+c.id+'">Hapus</button>';
        div.querySelector('.btn-del').addEventListener('click',async function(){if(!confirm('Hapus?'))return;await idbDel(c.id);renderClips();});
        list.appendChild(div);
    });
}
renderClips();
window.addEventListener('resize',function(){if(_buf)admDraw();});
</script>

@endsection
