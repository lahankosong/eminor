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

    /* Waveform + region */
    .region-wave-wrap { position:relative; border-radius:8px; overflow:hidden; background:#0a0e1a; margin:12px 0 4px; cursor:crosshair; }
    #adminWave { display:block; width:100%; height:80px; }
    .region-play { position:absolute; top:0; bottom:0; width:2px; background:#fff; opacity:.7; left:0; pointer-events:none; }
    .region-time { display:flex; justify-content:space-between; font-size:11px; color:var(--text-3); font-variant-numeric:tabular-nums; }
    .range-pair { margin-top:8px; }
    .range-pair input[type=range] { width:100%; accent-color:var(--accent); margin:2px 0; }

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
            <audio id="player" controls preload="metadata"></audio>

            <div class="region-wave-wrap" id="regionTrack">
                <canvas id="adminWave"></canvas>
                <div class="region-play" id="regionPlay" style="display:none;"></div>
            </div>
            <div class="region-time"><span>0:00</span><span id="durLabel">0:00</span></div>

            <div class="range-pair">
                <input type="range" id="startRange" min="0" max="100" step="0.1" value="0">
                <input type="range" id="endRange" min="0" max="100" step="0.1" value="100">
            </div>

            <div class="row" style="margin-top:10px;justify-content:space-between;">
                <div class="row">
                    <button class="btn btn-soft btn-sm" onclick="setEdge('start')">⏮️ Awal di sini</button>
                    <button class="btn btn-soft btn-sm" onclick="setEdge('end')">Akhir di sini ⏭️</button>
                    <button class="btn btn-soft btn-sm" id="previewBtn" onclick="previewRegion()">▶️ Preview</button>
                </div>
                <span class="muted" id="segLabel"></span>
            </div>

            <div class="row" style="margin-top:14px;">
                <button class="btn btn-primary" id="cutBtn" onclick="doCut()">✂️ Potong bagian ini</button>
                <span class="muted">geser slider lalu klik lagi untuk potong part lain — tanpa reload</span>
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

<script>
// ── Admin Audio Cutter — Web Audio API (no ffmpeg needed) ──
var player    = document.getElementById('player');
var _ctx = null, _buf = null, _src = null;
var srcUrl = null, srcName = 'lagu', duration = 0;
var _startT = 0, _endT = 0, _playing = false, _prevStop = null, _raf = null;
var lastClipBlob = null, lastClipUrl = null;

function fmt(s){ s=Math.max(0,s||0); var m=Math.floor(s/60),x=Math.floor(s%60); return m+':'+(x<10?'0':'')+x; }
function getExt(n){ var m=(n||'').match(/\.([a-z0-9]+)(?:\?|$)/i); return m?m[1].toLowerCase():'mp3'; }
function setStatus(html){ document.getElementById('status').innerHTML = html||''; }

// ── Load from song library ──
document.getElementById('songSelect').addEventListener('change', function(){
    if (!this.value) return;
    var opt = this.options[this.selectedIndex];
    document.getElementById('fileInput').value = '';
    fetchAndLoad(this.value, opt.getAttribute('data-title')||'lagu');
});
document.getElementById('fileInput').addEventListener('change', function(){
    var f=this.files[0]; if(!f) return;
    document.getElementById('songSelect').value='';
    readFileAndLoad(f);
});

function readFileAndLoad(file){
    srcName = file.name.replace(/\.[^.]+$/,'');
    setStatus('<span class="spinner"></span> Membaca file…');
    var reader=new FileReader();
    reader.onload=function(e){ decodeBuffer(e.target.result); };
    reader.readAsArrayBuffer(file);
}
async function fetchAndLoad(url, name){
    srcUrl=url; srcName=name;
    setStatus('<span class="spinner"></span> Mengambil file dari server…');
    try {
        var res = await fetch(url);
        var ab  = await res.arrayBuffer();
        decodeBuffer(ab);
    } catch(e){ setStatus('⚠️ Gagal mengambil file: '+e.message); }
}

function decodeBuffer(ab){
    if(_ctx){ try{_ctx.close();}catch(e){} }
    _ctx = new (window.AudioContext||window.webkitAudioContext)();
    setStatus('<span class="spinner"></span> Mendekode audio…');
    _ctx.decodeAudioData(ab, function(buf){
        _buf=buf; duration=buf.duration;
        _startT=0; _endT=duration;
        player.src=''; // clear native player
        document.getElementById('srcInfo').textContent = '🎵 '+srcName+' · '+fmt(duration);
        document.getElementById('durLabel').textContent = fmt(duration);
        var sr=document.getElementById('startRange'), er=document.getElementById('endRange');
        sr.max=er.max=duration.toFixed(1); sr.step=er.step=(duration/1000).toFixed(4);
        sr.value=0; er.value=duration.toFixed(1);
        updateRegion(); drawAdminWave();
        document.getElementById('editArea').style.display='block';
        document.getElementById('resultWrap').style.display='none';
        setStatus('');
    }, function(){ setStatus('⚠️ Gagal mendekode — coba format lain.'); });
}

// ── Waveform ──
function drawAdminWave(){
    var canvas=document.getElementById('adminWave');
    var wrap=document.getElementById('regionTrack');
    var W=wrap.clientWidth||560, H=80;
    canvas.width=W; canvas.height=H;
    var ctx=canvas.getContext('2d');
    ctx.fillStyle='#0a0e1a'; ctx.fillRect(0,0,W,H);
    if(!_buf) return;
    var data=_buf.getChannelData(0), step=Math.ceil(data.length/W);
    var sx=(_startT/_endT||0)*0, ex2=(_endT/duration)*W, sx2=(_startT/duration)*W;
    ctx.fillStyle='rgba(99,102,241,.1)'; ctx.fillRect(sx2,0,ex2-sx2,H);
    for(var i=0;i<W;i++){
        var max=0;
        for(var j=0;j<step;j++){ var v=Math.abs(data[i*step+j]||0); if(v>max)max=v; }
        var bH=Math.max(1,max*H*.9), y=(H-bH)/2;
        ctx.fillStyle=(i>=sx2&&i<=ex2)?'#6366f1':'#1e293b';
        ctx.fillRect(i,y,1,bH);
    }
    ctx.fillStyle='#818cf8'; ctx.fillRect(sx2,0,2,H);
    ctx.fillStyle='#f59e0b'; ctx.fillRect(ex2-2,0,2,H);
}

// ── Sliders ──
var startRange=document.getElementById('startRange'), endRange=document.getElementById('endRange');
startRange.addEventListener('input', function(){
    _startT=parseFloat(this.value);
    if(_startT>=_endT-0.1){_startT=_endT-0.1;this.value=_startT.toFixed(4);}
    updateRegion(); drawAdminWave();
});
endRange.addEventListener('input', function(){
    _endT=parseFloat(this.value);
    if(_endT<=_startT+0.1){_endT=_startT+0.1;this.value=_endT.toFixed(4);}
    updateRegion(); drawAdminWave();
});
function getStart(){ return _startT; }
function getEnd(){ return _endT; }
function updateRegion(){
    document.getElementById('segLabel').textContent='🟦 '+fmt(_startT)+' – '+fmt(_endT)+' · durasi '+fmt(_endT-_startT);
}
function setEdge(which){
    if(!duration) return;
    var t = _ctx ? (_ctx.currentTime - (_playCtxTime||0) + (_startT||0)) : 0;
    if(which==='start'){ _startT=Math.max(0,Math.min(t,_endT-0.1)); startRange.value=_startT.toFixed(4); }
    else { _endT=Math.max(_startT+0.1,Math.min(t,duration)); endRange.value=_endT.toFixed(4); }
    updateRegion(); drawAdminWave();
}

document.getElementById('regionTrack').addEventListener('click', function(ev){
    if(!duration) return;
    var rect=this.getBoundingClientRect();
    var t=(ev.clientX-rect.left)/rect.width*duration;
    var ds=Math.abs(t-_startT), de=Math.abs(t-_endT);
    if(ds<de){ _startT=Math.max(0,Math.min(t,_endT-0.1)); startRange.value=_startT.toFixed(4); }
    else { _endT=Math.max(_startT+0.1,Math.min(t,duration)); endRange.value=_endT.toFixed(4); }
    updateRegion(); drawAdminWave();
});

// ── Playback ──
var _playCtxTime=0, _playOffset=0;
function previewRegion(){
    if(!_buf) return;
    _stopSrc();
    _ctx.resume();
    _src=_ctx.createBufferSource(); _src.buffer=_buf; _src.connect(_ctx.destination);
    _playOffset=_startT; _playCtxTime=_ctx.currentTime;
    _src.start(0,_startT,_endT-_startT);
    _playing=true; _rafTick();
    _prevStop=setTimeout(_stopSrc, (_endT-_startT)*1000+300);
}
function _stopSrc(){
    if(_prevStop){clearTimeout(_prevStop);_prevStop=null;}
    if(_raf){cancelAnimationFrame(_raf);_raf=null;}
    if(_src){try{_src.stop();}catch(e){}_src=null;}
    _playing=false;
    document.getElementById('regionPlay').style.display='none';
}
function _rafTick(){
    if(!_playing) return;
    var elapsed=_ctx.currentTime-_playCtxTime;
    var pos=(_playOffset+elapsed)/duration;
    if(pos>1){_stopSrc();return;}
    var canvas=document.getElementById('adminWave');
    var ph=document.getElementById('regionPlay');
    ph.style.display='block'; ph.style.left=(pos*canvas.width)+'px';
    _raf=requestAnimationFrame(_rafTick);
}

// ── Cut ──
function doCut(){
    if(!_buf){alert('Pilih lagu dulu.');return;}
    var s=_startT, dur=_endT-s;
    if(dur<0.1){alert('Bagian terlalu pendek.');return;}
    var cut=document.getElementById('cutBtn');
    cut.disabled=true; setStatus('<span class="spinner"></span> Memotong…');
    setTimeout(function(){
        try{
            var blob=_wavEncode(_buf,s,_endT);
            if(lastClipUrl) URL.revokeObjectURL(lastClipUrl);
            lastClipBlob=blob; lastClipUrl=URL.createObjectURL(blob);
            document.getElementById('clipPlayer').src=lastClipUrl;
            var dl=document.getElementById('downloadBtn');
            dl.href=lastClipUrl; dl.download=srcName+'_'+fmt(s).replace(':','m')+'-'+fmt(_endT).replace(':','m')+'.wav';
            document.getElementById('clipName').value=srcName+' ('+fmt(s)+'–'+fmt(_endT)+')';
            document.getElementById('resultWrap').style.display='block';
            setStatus('✓ Potongan jadi ('+fmt(dur)+'). Simpan/unduh, atau geser slider & potong part lain.');
        }catch(e){ setStatus('⚠️ Gagal: '+(e.message||e)); }
        finally{ cut.disabled=false; }
    },50);
}

function _wavEncode(buffer,s,e){
    var sr=buffer.sampleRate, nCh=buffer.numberOfChannels;
    var ss=Math.floor(s*sr), es=Math.min(Math.ceil(e*sr),buffer.length), n=es-ss;
    var ab=new ArrayBuffer(44+n*nCh*2), v=new DataView(ab);
    function ws(off,str){for(var i=0;i<str.length;i++)v.setUint8(off+i,str.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);
    var off=44;
    for(var i=0;i<n;i++) for(var ch=0;ch<nCh;ch++){
        var x=Math.max(-1,Math.min(1,buffer.getChannelData(ch)[ss+i]));
        v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true); off+=2;
    }
    return new Blob([ab],{type:'audio/wav'});
}

// ── IndexedDB ──
function idbOpen(){return new Promise(function(res,rej){var r=indexedDB.open('mafAudioClips',1);r.onupgradeneeded=function(){r.result.createObjectStore('clips',{keyPath:'id',autoIncrement:true});};r.onsuccess=function(){res(r.result);};r.onerror=function(){rej(r.error);};});}
async function idbAll(){var db=await idbOpen();return new Promise(function(res){var t=db.transaction('clips').objectStore('clips').getAll();t.onsuccess=function(){res(t.result||[]);};t.onerror=function(){res([]);};});}
async function idbAdd(rec){var db=await idbOpen();return new Promise(function(res){var t=db.transaction('clips','readwrite').objectStore('clips').add(rec);t.onsuccess=function(){res(t.result);};});}
async function idbDel(id){var db=await idbOpen();return new Promise(function(res){db.transaction('clips','readwrite').objectStore('clips').delete(id).onsuccess=function(){res();};});}

async function saveClip(){
    if(!lastClipBlob){alert('Belum ada potongan.');return;}
    var name=(document.getElementById('clipName').value||'Potongan').trim();
    await idbAdd({name:name,ext:'wav',blob:lastClipBlob,size:lastClipBlob.size,createdAt:Date.now()});
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
window.addEventListener('resize',function(){if(_buf)drawAdminWave();});
</script>

@endsection
