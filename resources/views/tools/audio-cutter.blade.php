@extends('layouts.app')

@push('styles')
<style>
    :root {
        --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12);
        --rd:#ef4444; --green:#22c55e;
    }
    .ac-page { padding:1.5rem 1rem 4rem; }

    .ac-hero { text-align:center; margin-bottom:1.75rem; }
    .ac-hero-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .ac-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .ac-hero p  { font-size:13px;color:var(--text-3,#94a3b8);max-width:500px;margin:0 auto;line-height:1.7; }

    .ac-drop { border:2px dashed var(--border,#334155);border-radius:18px;padding:2rem 1.5rem;text-align:center;cursor:pointer;transition:.2s;background:var(--card-bg,#0f172a); }
    .ac-drop:hover,.ac-drop.drag-over { border-color:var(--ac);background:var(--ac-lt); }
    .ac-drop-icon { font-size:2.2rem;margin-bottom:.6rem; }
    .ac-drop-text { font-size:14px;font-weight:600;color:var(--text,#f0f0f0);margin-bottom:.25rem; }
    .ac-drop-sub  { font-size:11px;color:var(--text-3,#94a3b8); }
    #acFileInput  { display:none; }

    .ac-editor { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:18px;overflow:hidden;margin-top:1rem;display:none; }
    .ac-editor.show { display:block; }
    .ac-editor-head { padding:.75rem 1.1rem;border-bottom:1px solid var(--border,#334155);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px; }
    .ac-file-name  { font-weight:600;font-size:13px;color:var(--text,#f0f0f0);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px; }
    .ac-file-meta  { font-size:11px;color:var(--text-3,#94a3b8); }
    .ac-editor-body { padding:1.1rem; }

    /* Waveform */
    .ac-wave-wrap { position:relative;border-radius:10px;overflow:hidden;background:#070d18;margin-bottom:.3rem;cursor:crosshair;touch-action:none; }
    #acWave { display:block;width:100%;height:140px; }
    .ac-playhead { position:absolute;top:0;bottom:0;width:2px;background:#fff;opacity:.85;pointer-events:none;display:none; }

    /* Zoom bar */
    .ac-zoom-bar { display:flex;align-items:center;gap:6px;margin:.5rem 0 .9rem; }
    .ac-zoom-btn { padding:4px 10px;border-radius:6px;border:1px solid var(--border,#334155);background:var(--card-bg,#0f172a);color:var(--text-3,#94a3b8);font-size:13px;cursor:pointer;line-height:1; }
    .ac-zoom-btn:hover { border-color:var(--ac);color:var(--ac); }
    .ac-zoom-label { font-size:11px;color:var(--text-3,#94a3b8);font-variant-numeric:tabular-nums;white-space:nowrap; }
    .ac-scroll-wrap { flex:1;height:6px;background:var(--border,#334155);border-radius:3px;position:relative;cursor:grab; }
    .ac-scroll-bar  { height:100%;background:var(--ac-lt);border:1px solid var(--ac);border-radius:3px;position:absolute; }

    /* Selection */
    .ac-sel-row { display:flex;align-items:center;gap:8px;margin-bottom:.9rem;flex-wrap:wrap; }
    .ac-sel-chip { display:flex;align-items:center;gap:4px;background:var(--ac-lt);border:1px solid rgba(56,189,248,.22);border-radius:8px;padding:4px 10px;font-size:12px; }
    .ac-sel-lbl { color:var(--text-3,#94a3b8);font-size:10px;text-transform:uppercase;letter-spacing:.06em; }
    .ac-sel-val { font-weight:700;font-variant-numeric:tabular-nums; }

    /* Buttons */
    .ac-controls { display:flex;gap:7px;flex-wrap:wrap;margin-bottom:.75rem; }
    .ac-btn { display:inline-flex;align-items:center;gap:5px;padding:9px 14px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:.15s;font-family:inherit; }
    .ac-btn:disabled { opacity:.4;cursor:not-allowed; }
    .ac-btn-play  { background:var(--card-bg,#1e293b);border:1px solid var(--border,#334155);color:var(--text,#f0f0f0); }
    .ac-btn-play:not(:disabled):hover  { border-color:var(--green);color:var(--green); }
    .ac-btn-pause { background:var(--card-bg,#1e293b);border:1px solid #facc1540;color:#facc15; }
    .ac-btn-pause:not(:disabled):hover { border-color:#facc15; }
    .ac-btn-prev  { background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);color:var(--ac); }
    .ac-btn-prev:not(:disabled):hover  { background:rgba(56,189,248,.2); }
    .ac-btn-stop  { background:var(--card-bg,#1e293b);border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8); }
    .ac-btn-stop:not(:disabled):hover  { border-color:var(--rd);color:var(--rd); }
    .ac-btn-cut   { background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;box-shadow:0 4px 14px rgba(56,189,248,.25);flex:1;justify-content:center; }
    .ac-btn-cut:not(:disabled):hover   { transform:translateY(-1px);box-shadow:0 6px 20px rgba(56,189,248,.4); }
    .ac-status { font-size:12px;color:var(--text-3,#94a3b8);min-height:16px;margin-bottom:.5rem; }
    .ac-status.err { color:var(--rd); }

    .ac-result { background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.28);border-radius:14px;padding:1rem 1.1rem;display:none; }
    .ac-result.show { display:block; }
    .ac-result audio { width:100%;border-radius:8px;margin:.5rem 0; }
    .ac-dl    { display:inline-flex;align-items:center;gap:7px;background:var(--green);color:#fff;padding:9px 20px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;transition:.15s; }
    .ac-dl:hover { opacity:.88;transform:translateY(-1px); }
    .ac-again { display:inline-flex;align-items:center;gap:5px;background:transparent;border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8);padding:9px 16px;border-radius:10px;font-size:12px;cursor:pointer;margin-left:7px;font-family:inherit; }

    .ac-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:2rem; }
    .ac-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .ac-info-icon  { font-size:1.4rem;margin-bottom:.4rem; }
    .ac-info-title { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .ac-info-body  { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }
    .ac-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .ac-back:hover { color:var(--text); }
    .ac-drag-hint { font-size:10px;color:var(--text-3,#94a3b8);text-align:center;margin-top:3px;opacity:.75; }

    @@media(max-width:480px){ .ac-controls{flex-direction:column;} .ac-btn-cut{flex:none;} }
</style>
@endpush

@section('content')
<div class="page-rail-wrap">
<div class="ac-page">

    <a href="{{ route('home') }}" class="ac-back">← Beranda</a>
    @include('partials.tool-share')

    <div class="ac-hero">
        <div class="ac-hero-badge">✂️ Tool Gratis</div>
        <h1>Pemotong Lagu Online</h1>
        <p>Potong bagian lagu langsung di browser — tanpa upload ke server, tanpa install, tanpa biaya.</p>
    </div>

    <div class="ac-drop" id="acDrop" onclick="document.getElementById('acFileInput').click()">
        <div class="ac-drop-icon">🎵</div>
        <div class="ac-drop-text">Seret file audio ke sini atau klik untuk pilih</div>
        <div class="ac-drop-sub">MP3 · WAV · OGG · FLAC · AAC · M4A &nbsp;|&nbsp; Maks 300 MB &nbsp;|&nbsp; Tidak diunggah ke server</div>
    </div>
    <input type="file" id="acFileInput" accept="audio/*">

    <div class="ac-editor" id="acEditor">
        <div class="ac-editor-head">
            <div>
                <div class="ac-file-name" id="acFileName">—</div>
                <div class="ac-file-meta" id="acFileMeta">—</div>
            </div>
            <button class="ac-btn ac-btn-stop" onclick="acChangeFile()" style="font-size:11px;padding:5px 11px;">🔄 Ganti</button>
        </div>
        <div class="ac-editor-body">

            {{-- Waveform canvas --}}
            <div class="ac-wave-wrap" id="acWaveWrap">
                <canvas id="acWave"></canvas>
                <div class="ac-playhead" id="acPlayhead"></div>
            </div>
            <div class="ac-drag-hint">Seret segitiga ◀cyan (Mulai) &amp; kuning▶ (Akhir) langsung di gelombang &nbsp;·&nbsp; Scroll mouse / pinch untuk zoom</div>

            {{-- Zoom bar --}}
            <div class="ac-zoom-bar">
                <button class="ac-zoom-btn" onclick="acZoomIn()" title="Zoom In">🔍+</button>
                <button class="ac-zoom-btn" onclick="acZoomOut()" title="Zoom Out">🔍−</button>
                <button class="ac-zoom-btn" onclick="acZoomReset()" title="Lihat semua">↺</button>
                <div class="ac-scroll-wrap" id="acScrollWrap">
                    <div class="ac-scroll-bar" id="acScrollBar" style="left:0;width:100%;"></div>
                </div>
                <span class="ac-zoom-label" id="acZoomLabel">Lihat semua</span>
            </div>

            {{-- Sel info --}}
            <div class="ac-sel-row">
                <div class="ac-sel-chip">
                    <span class="ac-sel-lbl" style="color:#22d3ee;">Mulai</span>
                    <span class="ac-sel-val" id="acSelStart" style="color:#22d3ee;">0:00.0</span>
                </div>
                <div style="color:var(--text-3,#94a3b8);font-size:16px;">→</div>
                <div class="ac-sel-chip">
                    <span class="ac-sel-lbl" style="color:#f59e0b;">Akhir</span>
                    <span class="ac-sel-val" id="acSelEnd" style="color:#f59e0b;">0:00.0</span>
                </div>
                <div style="color:var(--text-3);font-size:16px;">|</div>
                <div class="ac-sel-chip" style="background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.2);">
                    <span class="ac-sel-lbl">Durasi</span>
                    <span class="ac-sel-val" id="acSelDur" style="color:#22c55e;">0:00.0</span>
                </div>
                <span id="acPosLabel" style="font-size:11px;color:var(--text-3,#94a3b8);font-variant-numeric:tabular-nums;"></span>
            </div>

            {{-- Controls --}}
            <div class="ac-controls">
                <button class="ac-btn ac-btn-play"  id="acBtnPlay"  onclick="acPlay()">▶ Play</button>
                <button class="ac-btn ac-btn-pause" id="acBtnPause" onclick="acPause()" style="display:none;">⏸ Pause</button>
                <button class="ac-btn ac-btn-prev"  onclick="acPreview()">▶ Preview Pilihan</button>
                <button class="ac-btn ac-btn-stop"  onclick="acStop()">⏹ Stop</button>
                <button class="ac-btn ac-btn-cut" id="acBtnCut" onclick="acCut()">✂️ Potong &amp; Unduh</button>
            </div>
            <div class="ac-status" id="acStatus"></div>

            {{-- Result --}}
            <div class="ac-result" id="acResult">
                <div style="font-size:13px;font-weight:700;color:var(--green);margin-bottom:.4rem;">✅ Potongan siap diunduh</div>
                <audio id="acClipPlayer" controls></audio>
                <div>
                    <a id="acDlLink" class="ac-dl" download>⬇️ Unduh WAV</a>
                    <button class="ac-again" onclick="acCutAgain()">✂️ Potong Lagi</button>
                </div>
            </div>

        </div>
    </div>

    <section style="margin-top:2.5rem;">
        <h2 style="font-family:'Space Grotesk','Sora',sans-serif;font-size:1rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.75rem;">Cara Pakai</h2>
        <ol style="font-size:13px;color:var(--text-3,#94a3b8);line-height:2.2;padding-left:1.2rem;">
            <li>Seret atau pilih file audio (MP3, WAV, OGG, FLAC, dll.)</li>
            <li>Drag segitiga <span style="color:#22d3ee;font-weight:700;">cyan</span> (Mulai) dan <span style="color:#f59e0b;font-weight:700;">kuning</span> (Akhir) langsung di waveform</li>
            <li>Scroll mouse atau pinch di layar sentuh untuk zoom — semakin zoom semakin presisi</li>
            <li>Klik <b>▶ Preview Pilihan</b> untuk dengarkan dulu bagian yang dipilih</li>
            <li>Klik <b>✂️ Potong &amp; Unduh</b> — file WAV langsung terunduh ke perangkat</li>
        </ol>
    </section>

    <div class="ac-info-grid">
        <div class="ac-info-card"><div class="ac-info-icon">🔒</div><div class="ac-info-title">Privasi 100%</div><div class="ac-info-body">File tidak pernah dikirim ke server. Semua diproses di browser via Web Audio API.</div></div>
        <div class="ac-info-card"><div class="ac-info-icon">🎯</div><div class="ac-info-title">Presisi Tinggi</div><div class="ac-info-body">Drag handle langsung di waveform, zoom untuk detail frame demi frame.</div></div>
        <div class="ac-info-card"><div class="ac-info-icon">🎵</div><div class="ac-info-title">Format Luas</div><div class="ac-info-body">MP3, WAV, OGG, FLAC, AAC, M4A. Output WAV 16-bit kualitas tinggi.</div></div>
        <div class="ac-info-card"><div class="ac-info-icon">🎸</div><div class="ac-info-title">Untuk Musisi</div><div class="ac-info-body">Potong intro, verse, chorus untuk latihan, preview, atau konten sosmed.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">EMINOR Ekosistem</a> — komunitas musisi Indonesia 🎸
    </p>


</div>{{-- .ac-page --}}

<aside class="page-rail-aside">
    @include('partials.content-rail')
</aside>

</div>{{-- .page-rail-wrap --}}
@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════════
//  PEMOTONG LAGU FULL — Web Audio API
//  Fitur: drag handle di canvas, zoom (wheel+pinch+button), play/pause
// ══════════════════════════════════════════════════════════════════
var _ctx=null,_buf=null,_src=null;
var _startT=0,_endT=0,_dur=0;
var _viewS=0,_viewE=0;
var _playing=false,_paused=false,_pausedAt=0;
var _playCtxTime=0,_playOffset=0;
var _prevStop=null,_rafId=null;
var _dragHandle=null,_pinchDist=null;
var _panDrag=false,_panX=0,_panVS=0;
var _fileName='lagu',_resultUrl=null;

function fmtP(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=s%60;return m+':'+(x<10?'0':'')+x.toFixed(1);}
function fmtS(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=Math.floor(s%60);return m+':'+(x<10?'0':'')+x;}
function setStatus(m,e){var el=document.getElementById('acStatus');el.textContent=m||'';el.className='ac-status'+(e?' err':'');}

/* ── File ── */
var drop=document.getElementById('acDrop');
drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('drag-over');});
drop.addEventListener('dragleave',function(){drop.classList.remove('drag-over');});
drop.addEventListener('drop',function(e){e.preventDefault();drop.classList.remove('drag-over');var f=e.dataTransfer.files[0];if(f)loadFile(f);});
document.getElementById('acFileInput').addEventListener('change',function(){if(this.files[0])loadFile(this.files[0]);});

function loadFile(file){
    if(file.size>300*1024*1024){alert('Maks 300 MB');return;}
    _fileName=file.name.replace(/\.[^.]+$/,'');
    document.getElementById('acFileName').textContent=file.name;
    document.getElementById('acFileMeta').textContent=(file.size/1024/1024).toFixed(1)+' MB · memuat…';
    setStatus('Membaca dan menggambar waveform…');
    if(_ctx){try{_ctx.close();}catch(e){}}
    _ctx=new(window.AudioContext||window.webkitAudioContext)();
    var r=new FileReader();
    r.onload=function(e){
        _ctx.decodeAudioData(e.target.result,function(buf){
            _buf=buf;_dur=buf.duration;_startT=0;_endT=_dur;_viewS=0;_viewE=_dur;
            document.getElementById('acFileMeta').textContent=
                (file.size/1024/1024).toFixed(1)+' MB · '+fmtS(_dur)+' · '+buf.sampleRate+' Hz · '+buf.numberOfChannels+'ch';
            drawWave();updSel();updZoomUI();
            document.getElementById('acEditor').classList.add('show');
            document.getElementById('acResult').classList.remove('show');
            setStatus('');_showPlay();
        },function(){setStatus('Gagal membaca file.',true);});
    };
    r.readAsArrayBuffer(file);
}
function acChangeFile(){
    acStop();
    document.getElementById('acEditor').classList.remove('show');
    document.getElementById('acResult').classList.remove('show');
    document.getElementById('acFileInput').value='';
    _buf=null;_dur=0;setStatus('');
}

/* ── Draw ── */
function drawWave(){
    var canvas=document.getElementById('acWave');
    var wrap=document.getElementById('acWaveWrap');
    var W=wrap.clientWidth||720,H=140;
    canvas.width=W;canvas.height=H;
    var ctx=canvas.getContext('2d');
    ctx.fillStyle='#070d18';ctx.fillRect(0,0,W,H);
    if(!_buf)return;
    var vs=_viewS,ve=_viewE,vR=ve-vs;
    var data=_buf.getChannelData(0);
    var spp=(vR/_dur)*(_buf.length/W);

    /* selection fill */
    var sx=Math.max(0,(_startT-vs)/vR)*W;
    var ex=Math.min(1,(_endT-vs)/vR)*W;
    ctx.fillStyle='rgba(56,189,248,.1)';ctx.fillRect(sx,0,ex-sx,H);

    /* bars */
    for(var i=0;i<W;i++){
        var tS=vs+(i/W)*vR;
        var s0=Math.floor(tS/_dur*_buf.length);
        var cnt=Math.max(1,Math.round(spp));
        var max=0;
        for(var j=0;j<cnt;j++){var v=Math.abs(data[s0+j]||0);if(v>max)max=v;}
        var bH=Math.max(1,max*H*.9),y=(H-bH)/2;
        ctx.fillStyle=(i>=sx-1&&i<=ex+1)?'#38bdf8':'#1a2e4a';
        ctx.fillRect(i,y,1,bH);
    }

    /* start handle */
    var sxC=clamp((_startT-vs)/vR*W,0,W);
    ctx.fillStyle='#22d3ee';
    ctx.fillRect(sxC,0,2,H);
    ctx.beginPath();ctx.moveTo(sxC,0);ctx.lineTo(sxC+16,0);ctx.lineTo(sxC,22);ctx.closePath();ctx.fill();

    /* end handle */
    var exC=clamp((_endT-vs)/vR*W,0,W);
    ctx.fillStyle='#f59e0b';
    ctx.fillRect(exC-2,0,2,H);
    ctx.beginPath();ctx.moveTo(exC,0);ctx.lineTo(exC-16,0);ctx.lineTo(exC,22);ctx.closePath();ctx.fill();

    /* time ticks */
    ctx.fillStyle='rgba(148,163,184,.25)';
    ctx.font='9px monospace';ctx.fillStyle='rgba(148,163,184,.45)';
    var step=niceDur(vR/7);
    for(var t=Math.ceil(vs/step)*step;t<=ve;t+=step){
        var px=(t-vs)/vR*W;
        ctx.fillRect(px,H-6,1,6);
        if(px>4)ctx.fillText(fmtS(t),px+2,H-1);
    }
}
function clamp(v,lo,hi){return v<lo?lo:v>hi?hi:v;}
function niceDur(a){var s=[.1,.25,.5,1,2,5,10,15,30,60,120,300];for(var i=0;i<s.length;i++)if(s[i]>=a)return s[i];return 600;}

/* ── Canvas mouse ── */
var ww=document.getElementById('acWaveWrap');
function canvasT(cx){var r=ww.getBoundingClientRect();return _viewS+clamp((cx-r.left)/r.width,0,1)*(_viewE-_viewS);}
function nearH(cx,tol){
    var r=ww.getBoundingClientRect(),W=r.width,vR=_viewE-_viewS;
    var sx=(_startT-_viewS)/vR*W+r.left;
    var ex=(_endT  -_viewS)/vR*W+r.left;
    if(Math.abs(cx-sx)<tol)return'start';
    if(Math.abs(cx-ex)<tol)return'end';
    return null;
}
ww.addEventListener('mousedown',function(e){
    if(!_buf)return;
    _dragHandle=nearH(e.clientX,16);
    if(!_dragHandle){var t=canvasT(e.clientX);_dragHandle=Math.abs(t-_startT)<Math.abs(t-_endT)?'start':'end';}
    ww.style.cursor='ew-resize';
    e.preventDefault();
});
document.addEventListener('mousemove',function(e){
    if(!_dragHandle)return;
    var t=clamp(canvasT(e.clientX),0,_dur);
    if(_dragHandle==='start')_startT=Math.min(t,_endT-.05);
    else _endT=Math.max(t,_startT+.05);
    drawWave();updSel();
});
document.addEventListener('mouseup',function(){if(_dragHandle){_dragHandle=null;ww.style.cursor='crosshair';}});
ww.addEventListener('mousemove',function(e){if(!_dragHandle)ww.style.cursor=nearH(e.clientX,16)?'ew-resize':'crosshair';});

/* ── Touch drag ── */
ww.addEventListener('touchstart',function(e){
    if(!_buf)return;
    if(e.touches.length===2){_pinchDist=Math.hypot(e.touches[0].clientX-e.touches[1].clientX,e.touches[0].clientY-e.touches[1].clientY);return;}
    var cx=e.touches[0].clientX;
    _dragHandle=nearH(cx,24)||(_=>{var t=canvasT(cx);return Math.abs(t-_startT)<Math.abs(t-_endT)?'start':'end';})();
    e.preventDefault();
},{passive:false});
ww.addEventListener('touchmove',function(e){
    if(e.touches.length===2&&_pinchDist!=null){
        var d=Math.hypot(e.touches[0].clientX-e.touches[1].clientX,e.touches[0].clientY-e.touches[1].clientY);
        var px=(e.touches[0].clientX+e.touches[1].clientX)/2;
        _zoomAround(canvasT(px),_pinchDist/d);
        _pinchDist=d;e.preventDefault();return;
    }
    if(!_dragHandle)return;
    var t=clamp(canvasT(e.touches[0].clientX),0,_dur);
    if(_dragHandle==='start')_startT=Math.min(t,_endT-.05);
    else _endT=Math.max(t,_startT+.05);
    drawWave();updSel();e.preventDefault();
},{passive:false});
ww.addEventListener('touchend',function(){_dragHandle=null;_pinchDist=null;});

/* ── Zoom ── */
ww.addEventListener('wheel',function(e){e.preventDefault();_zoomAround(canvasT(e.clientX),e.deltaY>0?1.35:.74);},{passive:false});
function _zoomAround(pivot,factor){
    var vR=_viewE-_viewS,nr=clamp(vR*factor,.3,_dur);
    var ratio=_dur>0?(pivot-_viewS)/vR:.5;
    _viewS=clamp(pivot-ratio*nr,0,_dur-nr);
    _viewE=_viewS+nr;
    drawWave();updZoomUI();
}
function acZoomIn(){_zoomAround((_viewS+_viewE)/2,.5);}
function acZoomOut(){_zoomAround((_viewS+_viewE)/2,2);}
function acZoomReset(){_viewS=0;_viewE=_dur;drawWave();updZoomUI();}

function updZoomUI(){
    if(!_dur)return;
    var frac=(_viewE-_viewS)/_dur;
    var wrap=document.getElementById('acScrollWrap');
    var bar=document.getElementById('acScrollBar');
    var wW=wrap.offsetWidth;
    bar.style.left=((_viewS/_dur)*wW)+'px';
    bar.style.width=Math.max(8,frac*wW)+'px';
    document.getElementById('acZoomLabel').textContent=
        frac>=.999?'Lihat semua':fmtP(_viewS)+'–'+fmtP(_viewE);
}

/* pan scroll-bar */
var sw=document.getElementById('acScrollWrap');
sw.addEventListener('mousedown',function(e){_panDrag=true;_panX=e.clientX;_panVS=_viewS;});
document.addEventListener('mousemove',function(e){
    if(!_panDrag||!_dur)return;
    var wrap=document.getElementById('acScrollWrap');
    var dx=(e.clientX-_panX)/wrap.offsetWidth*_dur;
    var vR=_viewE-_viewS;
    _viewS=clamp(_panVS+dx,0,_dur-vR);_viewE=_viewS+vR;
    drawWave();updZoomUI();
});
document.addEventListener('mouseup',function(){_panDrag=false;});

/* ── Sel display ── */
function updSel(){
    document.getElementById('acSelStart').textContent=fmtP(_startT);
    document.getElementById('acSelEnd').textContent  =fmtP(_endT);
    document.getElementById('acSelDur').textContent  =fmtP(_endT-_startT);
}

/* ── Playback ── */
function _showPlay(){document.getElementById('acBtnPlay').style.display='';document.getElementById('acBtnPause').style.display='none';}
function _showPause(){document.getElementById('acBtnPlay').style.display='none';document.getElementById('acBtnPause').style.display='';}

function _startSrc(offset){
    _stopSrc();_ctx.resume();
    _src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);
    _playOffset=offset;_playCtxTime=_ctx.currentTime;
    _src.start(0,offset);
    _playing=true;_paused=false;_showPause();_rafTick();
}
function acPlay(){if(!_buf)return;_startSrc(_paused?_pausedAt:0);}
function acPreview(){
    if(!_buf)return;
    _startSrc(_startT);
    _prevStop=setTimeout(acStop,(_endT-_startT)*1000+300);
}
function acPause(){
    if(!_playing)return;
    _pausedAt=clamp(_playOffset+(_ctx.currentTime-_playCtxTime),0,_dur);
    _stopSrc();_paused=true;_showPlay();
    /* keep playhead visible */
    var canvas=document.getElementById('acWave');
    var ph=document.getElementById('acPlayhead');
    ph.style.display='block';
    ph.style.left=clamp((_pausedAt-_viewS)/(_viewE-_viewS)*canvas.width,0,canvas.width)+'px';
}
function acStop(){
    if(_prevStop){clearTimeout(_prevStop);_prevStop=null;}
    _stopSrc();_paused=false;_pausedAt=0;
    document.getElementById('acPlayhead').style.display='none';
    document.getElementById('acPosLabel').textContent='';
    _showPlay();
}
function _stopSrc(){
    if(_rafId){cancelAnimationFrame(_rafId);_rafId=null;}
    if(_src){try{_src.stop();}catch(e){}_src=null;}_playing=false;
}
function _rafTick(){
    if(!_playing)return;
    var pos=_playOffset+(_ctx.currentTime-_playCtxTime);
    if(pos>=_dur){acStop();return;}
    /* auto-scroll view */
    if(pos<_viewS||pos>_viewE-.05*(_viewE-_viewS)){
        var vR=_viewE-_viewS;
        _viewS=clamp(pos-.05*vR,0,_dur-vR);_viewE=_viewS+vR;
        updZoomUI();
    }
    drawWave();
    var canvas=document.getElementById('acWave');
    var ph=document.getElementById('acPlayhead');
    ph.style.display='block';
    ph.style.left=clamp((pos-_viewS)/(_viewE-_viewS)*canvas.width,0,canvas.width)+'px';
    document.getElementById('acPosLabel').textContent='▶ '+fmtP(pos);
    _rafId=requestAnimationFrame(_rafTick);
}

/* ── Export WAV ── */
function acCut(){
    if(!_buf)return;
    if(_endT-_startT<.05){setStatus('Pilihan terlalu pendek.',true);return;}
    setStatus('Memproses…');document.getElementById('acBtnCut').disabled=true;
    setTimeout(function(){
        try{
            var blob=_wavEnc(_buf,_startT,_endT);
            if(_resultUrl)URL.revokeObjectURL(_resultUrl);
            _resultUrl=URL.createObjectURL(blob);
            document.getElementById('acClipPlayer').src=_resultUrl;
            var dl=document.getElementById('acDlLink');
            dl.href=_resultUrl;
            dl.download=_fileName+'_'+fmtS(_startT).replace(':','m')+'s-'+fmtS(_endT).replace(':','m')+'s.wav';
            document.getElementById('acResult').classList.add('show');
            setStatus('');
            document.getElementById('acResult').scrollIntoView({behavior:'smooth',block:'nearest'});
        }catch(e){setStatus('Gagal: '+(e.message||e),true);}
        finally{document.getElementById('acBtnCut').disabled=false;}
    },50);
}
function acCutAgain(){document.getElementById('acResult').classList.remove('show');}
function _wavEnc(buf,s,e){
    var sr=buf.sampleRate,nCh=buf.numberOfChannels;
    var ss=Math.floor(s*sr),es=Math.min(Math.ceil(e*sr),buf.length),n=es-ss;
    var ab=new ArrayBuffer(44+n*nCh*2),v=new DataView(ab);
    function ws(o,str){for(var i=0;i<str.length;i++)v.setUint8(o+i,str.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);
    var off=44;
    for(var i=0;i<n;i++)for(var ch=0;ch<nCh;ch++){var x=clamp(buf.getChannelData(ch)[ss+i],-1,1);v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;}
    return new Blob([ab],{type:'audio/wav'});
}
window.addEventListener('resize',function(){if(_buf){drawWave();updZoomUI();}});
</script>
@endpush

