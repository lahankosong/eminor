@extends('layouts.admin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Caveat:wght@700&family=Poppins:wght@800&display=swap" rel="stylesheet">
<style>
    .ai-header { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border); }
    .ai-header h2 { font-size:1rem; font-weight:500; color:var(--text); }
    .ai-header p { font-size:12px; color:var(--text-3); margin-top:2px; }
    .btn-back { font-size:12px; color:var(--text-2); text-decoration:none; border:1px solid var(--border); padding:6px 14px; border-radius:8px; }
    .btn-back:hover { color:var(--text); border-color:var(--text-3); }

    .card { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; margin-bottom:1.1rem; overflow:hidden; }
    .card-head { padding:0.8rem 1.1rem; border-bottom:1px solid var(--border); font-size:12px; color:var(--text-2); font-weight:600; display:flex; justify-content:space-between; align-items:center; gap:10px; }
    .card-body { padding:1.1rem; }

    .fi { background:var(--bg-3); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px; padding:8px 11px; outline:none; font-family:inherit; }
    .btn { padding:9px 16px; border-radius:8px; font-size:13px; font-weight:500; border:none; cursor:pointer; transition:0.2s; }
    .btn-primary { background:var(--text); color:var(--bg); }
    .btn-primary:hover { filter:brightness(0.88); }
    .btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
    .btn-soft { background:var(--bg-3); border:1px solid var(--border); color:var(--text-2); }
    .btn-accent { background:var(--accent-dim); color:var(--accent); }
    .row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .muted { font-size:11px; color:var(--text-3); }

    /* Image grid */
    .img-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(82px,1fr)); gap:8px; }
    .img-pick { position:relative; aspect-ratio:9/16; border-radius:8px; overflow:hidden; border:2px solid var(--border); cursor:pointer; background:var(--bg-3); }
    .img-pick img { width:100%; height:100%; object-fit:cover; display:block; }
    .img-pick.sel { border-color:var(--accent); box-shadow:0 0 0 2px var(--accent-dim); }
    .img-pick.sel::after { content:'✓'; position:absolute; top:3px; right:5px; color:#fff; background:var(--accent); border-radius:50%; width:18px; height:18px; font-size:12px; display:flex; align-items:center; justify-content:center; }

    /* Audio list */
    .aud-item { display:flex; align-items:center; gap:10px; padding:9px 11px; border:1px solid var(--border); border-radius:8px; margin-bottom:7px; cursor:pointer; }
    .aud-item.sel { border-color:var(--accent); background:var(--accent-dim); }
    .aud-name { font-size:13px; color:var(--text); font-weight:500; }
    .aud-meta { font-size:11px; color:var(--text-3); }

    .ratio-opt { display:flex; gap:8px; flex-wrap:wrap; }
    .ratio-btn { padding:7px 14px; border-radius:8px; font-size:12px; border:1px solid var(--border); background:var(--bg-3); color:var(--text-2); cursor:pointer; }
    .ratio-btn.sel { border-color:var(--accent); color:var(--accent); background:var(--accent-dim); font-weight:600; }

    .status { font-size:12px; color:var(--text-3); margin-top:10px; min-height:18px; }
    .spinner { display:inline-block; width:13px; height:13px; border:2px solid var(--text-3); border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .result-video { max-width:280px; border-radius:10px; border:1px solid var(--border); display:block; margin-top:10px; }
    .empty-row { font-size:12px; color:var(--text-3); padding:8px 0; }
</style>
@endpush

@section('content')

<div class="ai-header">
    <div>
        <h2>🎬 Video Builder</h2>
        <p>Rakit gambar + audio jadi video MP4 — diproses di browser, server tidak terbebani</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.ai-agent') }}" class="btn-back">← AI Agent</a>
        <a href="{{ route('admin.index') }}" class="btn-back">Panel Admin</a>
    </div>
</div>

{{-- 1. GAMBAR --}}
<div class="card">
    <div class="card-head">
        <span>1 · Pilih Gambar</span>
        <label class="btn btn-soft" style="font-size:11px;padding:5px 11px;cursor:pointer;">+ Upload<input type="file" id="imgUpload" accept="image/*" hidden></label>
    </div>
    <div class="card-body">
        @if($images->count())
        <div class="img-grid" id="imgGrid">
            @foreach($images as $img)
            <div class="img-pick" data-src="{{ $img->url }}" onclick="pickImage(this)" title="{{ \Illuminate\Support\Str::limit($img->prompt, 80) }}">
                <img src="{{ $img->url }}" loading="lazy" alt="">
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-row">Belum ada gambar AI. Buat dulu di <a href="{{ route('admin.ai-agent') }}" style="color:var(--accent);">AI Agent → 🖼️ buat gambar</a>, atau <b>+ Upload</b> gambar sendiri.</div>
        @endif
        <div class="muted" id="imgInfo" style="margin-top:10px;">Belum ada gambar dipilih.</div>
    </div>
</div>

{{-- 2. AUDIO --}}
<div class="card">
    <div class="card-head">
        <span>2 · Pilih Audio</span>
        <label class="btn btn-soft" style="font-size:11px;padding:5px 11px;cursor:pointer;">+ Upload<input type="file" id="audUpload" accept="audio/*" hidden></label>
    </div>
    <div class="card-body">
        <p class="muted" style="margin-bottom:10px;">Dari <b>Pemotong Lagu</b> (potongan lagu) &amp; <b>narasi TTS</b> yang kamu simpan (tersimpan di perangkat ini).</p>
        <div id="audList"><div class="empty-row">Memuat potongan tersimpan…</div></div>
        <div class="muted" id="audInfo" style="margin-top:6px;">Belum ada audio dipilih.</div>
    </div>
</div>

{{-- 3. CAPTION OVERLAY --}}
<div class="card">
    <div class="card-head"><span>3 · Caption Overlay <span class="muted">(opsional)</span></span></div>
    <div class="card-body">
        <p class="muted" style="margin-bottom:10px;">Tempel dari AI Agent: <b>narasi</b> (build-up) tampil sepanjang video, <b>🎯 gong</b> muncul di detik akhir. Kosongkan kalau tak mau caption.</p>
        <div style="margin-bottom:10px;">
            <label class="muted">Narasi (build-up)</label>
            <textarea class="fi" id="capNarasi" rows="2" style="width:100%;resize:vertical;margin-top:4px;" placeholder="tiap malam mikirin hal yang sama, padahal besok kerja"></textarea>
        </div>
        <div style="margin-bottom:12px;">
            <label class="muted">🎯 Gong (pamungkas)</label>
            <input type="text" class="fi" id="capGong" style="width:100%;margin-top:4px;" placeholder="overthinking emang gratis ya">
        </div>
        <label class="muted">Template</label>
        <div class="ratio-opt" id="tplOpt" style="margin-top:6px;">
            <span class="ratio-btn sel" data-t="impact"  onclick="pickTpl(this)">Impact</span>
            <span class="ratio-btn" data-t="boxbold" onclick="pickTpl(this)">Bold Box</span>
            <span class="ratio-btn" data-t="neon"    onclick="pickTpl(this)">Neon</span>
            <span class="ratio-btn" data-t="tulis"   onclick="pickTpl(this)">Tulisan tangan</span>
        </div>
        <div style="margin-top:12px;">
            <label class="muted">Preview <span style="opacity:0.7;">(narasi & gong ditampilkan bersamaan; di video aslinya gong muncul di akhir)</span></label>
            <div style="margin-top:6px;"><canvas id="capPreview" style="border:1px solid var(--border);border-radius:8px;max-width:100%;display:block;"></canvas></div>
        </div>
    </div>
</div>

{{-- 4. PENGATURAN + RENDER --}}
<div class="card">
    <div class="card-head"><span>4 · Format &amp; Rakit</span></div>
    <div class="card-body">
        <div class="ratio-opt" id="ratioOpt">
            <span class="ratio-btn sel" data-r="9:16" onclick="pickRatio(this)">📱 9:16 (Short)</span>
            <span class="ratio-btn" data-r="1:1" onclick="pickRatio(this)">⬛ 1:1</span>
            <span class="ratio-btn" data-r="16:9" onclick="pickRatio(this)">🖥️ 16:9</span>
        </div>
        <p class="muted" style="margin:10px 0;">Pertama kali memuat mesin (ffmpeg ±30MB, sekali, lalu di-cache). Durasi video = panjang audio. <b>Short (≤60 dtk) paling cepat</b>; video panjang bisa beberapa menit.</p>
        <div class="row">
            <button class="btn btn-primary" id="renderBtn" onclick="doRender()">🎬 Rakit Video</button>
        </div>
        <div class="status" id="status"></div>

        <div id="resultWrap" style="display:none;margin-top:12px;">
            <video class="result-video" id="resultVideo" controls></video>
            <div class="row" style="margin-top:10px;">
                <a class="btn btn-accent" id="dlBtn" download="video.mp4">⬇️ Unduh MP4</a>
                <span class="muted" id="resultMeta"></span>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('ffmpeg/ffmpeg.js') }}"></script>
<script>
var FFMPEG_BASE = '{{ asset('ffmpeg') }}';
async function fetchBytes(input){
    var data;
    if (typeof input === 'string') data = await (await fetch(input)).arrayBuffer();
    else if (input instanceof Blob) data = await input.arrayBuffer();
    else data = input;
    return new Uint8Array(data);
}
</script>
<script>
// ===== State =====
var selImage = null;   // {kind:'url'|'file', src|file, ext}
var selAudio = null;   // {kind:'idb'|'file', blob, ext, name}
var ratio = '9:16';
var selTpl = 'impact';
var ffmpeg = null, ffmpegLoaded = false, busy = false, lastUrl = null;

// Template caption: family (font web), warna, stroke, shadow, box
var CAP_TEMPLATES = {
    impact:  { family:'Anton',       weight:'400', color:'#ffffff', stroke:'#000000', shadow:null,                  box:null },
    boxbold: { family:'Poppins',     weight:'800', color:'#ffffff', stroke:null,      shadow:'rgba(0,0,0,0.6)',    box:'rgba(0,0,0,0.55)' },
    neon:    { family:'Bebas Neue',  weight:'400', color:'#FFE14D', stroke:null,      shadow:'rgba(255,196,0,0.95)', box:null },
    tulis:   { family:'Caveat',      weight:'700', color:'#ffffff', stroke:null,      shadow:'rgba(0,0,0,0.75)',   box:null },
};
function pickTpl(el){
    document.querySelectorAll('#tplOpt .ratio-btn').forEach(function(x){ x.classList.remove('sel'); });
    el.classList.add('sel'); selTpl = el.dataset.t;
    renderPreview();
}

function setStatus(h){ document.getElementById('status').innerHTML = h || ''; }
function extFromType(t, fallback){ if(!t) return fallback; var m=t.split('/')[1]; return m ? m.replace('jpeg','jpg').split(';')[0] : fallback; }

// ===== Gambar =====
function pickImage(el){
    document.querySelectorAll('.img-pick').forEach(function(x){ x.classList.remove('sel'); });
    el.classList.add('sel');
    selImage = { kind:'url', src: el.dataset.src, ext:'jpg' };
    document.getElementById('imgInfo').textContent = '🖼️ Gambar dari galeri AI dipilih.';
}
document.getElementById('imgUpload').addEventListener('change', function(){
    var f=this.files[0]; if(!f) return;
    document.querySelectorAll('.img-pick').forEach(function(x){ x.classList.remove('sel'); });
    selImage = { kind:'file', file:f, ext: extFromType(f.type,'jpg') };
    document.getElementById('imgInfo').textContent = '🖼️ Upload: ' + f.name;
});

// ===== Audio (IndexedDB mafAudioClips) =====
function idbOpen(){
    return new Promise(function(res, rej){
        var r = indexedDB.open('mafAudioClips', 1);
        r.onupgradeneeded = function(){ r.result.createObjectStore('clips', { keyPath:'id', autoIncrement:true }); };
        r.onsuccess = function(){ res(r.result); };
        r.onerror = function(){ rej(r.error); };
    });
}
async function idbAll(){ var db = await idbOpen(); return new Promise(function(res){ var t=db.transaction('clips').objectStore('clips').getAll(); t.onsuccess=function(){res(t.result||[]);}; t.onerror=function(){res([]);}; }); }

async function loadAudio(){
    var list = document.getElementById('audList');
    var all = await idbAll();
    if (!all.length){ list.innerHTML = '<div class="empty-row">Belum ada audio tersimpan. Buat di <a href="{{ route('admin.audio-cut') }}" style="color:var(--accent);">Pemotong Lagu</a> atau simpan narasi TTS dari AI Agent. Atau <b>+ Upload</b>.</div>'; return; }
    list.innerHTML = '';
    all.sort(function(a,b){ return b.createdAt - a.createdAt; }).forEach(function(c){
        var kb = Math.round(c.size/1024);
        var d = document.createElement('div');
        d.className = 'aud-item';
        d.innerHTML = '<div style="flex:1;min-width:0;"><div class="aud-name">'+(c.name||'Audio').replace(/</g,'&lt;')+'</div><div class="aud-meta">.'+c.ext+' · '+kb+' KB</div></div>';
        d.addEventListener('click', function(){
            document.querySelectorAll('.aud-item').forEach(function(x){ x.classList.remove('sel'); });
            d.classList.add('sel');
            selAudio = { kind:'idb', blob:c.blob, ext:c.ext||'wav', name:c.name };
            document.getElementById('audInfo').textContent = '🔊 ' + (c.name||'Audio') + ' dipilih.';
        });
        list.appendChild(d);
    });
}
document.getElementById('audUpload').addEventListener('change', function(){
    var f=this.files[0]; if(!f) return;
    document.querySelectorAll('.aud-item').forEach(function(x){ x.classList.remove('sel'); });
    selAudio = { kind:'file', blob:f, ext: extFromType(f.type,'mp3'), name:f.name };
    document.getElementById('audInfo').textContent = '🔊 Upload: ' + f.name;
});
loadAudio();

// Preview caption: update saat ketik / ganti template / rasio
['capNarasi','capGong'].forEach(function(id){ var el=document.getElementById(id); if(el) el.addEventListener('input', renderPreview); });
window.addEventListener('load', renderPreview);
renderPreview();

// ===== Ratio =====
function pickRatio(el){
    document.querySelectorAll('#ratioOpt .ratio-btn').forEach(function(x){ x.classList.remove('sel'); });
    el.classList.add('sel'); ratio = el.dataset.r;
    renderPreview();
}
function ratioDims(){
    if (ratio === '16:9') return [1280,720];
    if (ratio === '1:1')  return [720,720];
    return [720,1280];
}

// ===== ffmpeg =====
async function ensureFfmpeg(){
    if (ffmpegLoaded) return;
    var FF = (window.FFmpegWASM || window.FFmpeg);
    if (!FF || !FF.FFmpeg) throw new Error('Library ffmpeg tidak termuat.');
    ffmpeg = new FF.FFmpeg();
    ffmpeg.on('progress', function(p){ if (p && p.progress>=0 && p.progress<=1) setStatus('<span class="spinner"></span> Merender… ' + Math.round(p.progress*100) + '%'); });
    setStatus('<span class="spinner"></span> Menyiapkan mesin (sekali saja, lalu di-cache)…');
    await ffmpeg.load({ coreURL: FFMPEG_BASE + '/ffmpeg-core.js', wasmURL: FFMPEG_BASE + '/ffmpeg-core.wasm' });
    ffmpegLoaded = true;
}

// ===== Caption → PNG (canvas) =====
function wrapText(ctx, text, maxW){
    var words = (text||'').split(/\s+/), lines = [], line = '';
    for (var i=0;i<words.length;i++){
        var test = line ? line + ' ' + words[i] : words[i];
        if (ctx.measureText(test).width > maxW && line){ lines.push(line); line = words[i]; }
        else line = test;
    }
    if (line) lines.push(line);
    return lines.length ? lines : [''];
}
function roundRect(ctx,x,y,w,h,r){ ctx.beginPath(); ctx.moveTo(x+r,y); ctx.arcTo(x+w,y,x+w,y+h,r); ctx.arcTo(x+w,y+h,x,y+h,r); ctx.arcTo(x,y+h,x,y,r); ctx.arcTo(x,y,x+w,y,r); ctx.closePath(); }

function drawCaptionOn(x, text, tpl, W, H, region, sizeFactor){
    var fpx = Math.round(H * sizeFactor);
    x.font = tpl.weight + ' ' + fpx + "px '" + tpl.family + "', sans-serif";
    x.textAlign = 'center'; x.textBaseline = 'middle';
    var lines = wrapText(x, text, W * 0.86), lineH = fpx * 1.22, totalH = lines.length * lineH;
    var cy = region === 'center' ? H/2 : H - totalH/2 - H*0.12;
    lines.forEach(function(ln, i){
        var yy = cy - totalH/2 + lineH/2 + i*lineH;
        if (tpl.box){
            var tw = x.measureText(ln).width;
            x.fillStyle = tpl.box;
            roundRect(x, (W-tw)/2 - fpx*0.32, yy - lineH*0.48, tw + fpx*0.64, lineH*0.96, Math.max(4, fpx*0.16)); x.fill();
        }
        if (tpl.shadow){ x.shadowColor = tpl.shadow; x.shadowBlur = fpx*0.28; x.shadowOffsetY = fpx*0.05; }
        if (tpl.stroke){ x.lineWidth = Math.max(2, fpx*0.13); x.strokeStyle = tpl.stroke; x.lineJoin='round'; x.strokeText(ln, W/2, yy); }
        x.fillStyle = tpl.color; x.fillText(ln, W/2, yy);
        x.shadowColor = 'transparent'; x.shadowBlur = 0; x.shadowOffsetY = 0;
    });
}
function loadImageEl(src){
    return new Promise(function(res, rej){
        var im = new Image();
        im.onload = function(){ res(im); };
        im.onerror = function(){ rej(new Error('Gambar gagal dimuat.')); };
        im.src = src;
    });
}
function coverDraw(ctx, im, W, H){
    var iw = im.naturalWidth || im.width, ih = im.naturalHeight || im.height;
    var s = Math.max(W/iw, H/ih), dw = iw*s, dh = ih*s;
    ctx.drawImage(im, (W-dw)/2, (H-dh)/2, dw, dh);
}
// Bake caption LANGSUNG ke frame gambar → JPEG bytes (tanpa overlay filter ffmpeg)
async function frameJpeg(im, W, H, caps, tpl){
    var cv = document.createElement('canvas'); cv.width = W; cv.height = H;
    var x = cv.getContext('2d');
    coverDraw(x, im, W, H);
    for (var i=0;i<caps.length;i++){
        var c = caps[i]; if (!c.text) continue;
        try { await document.fonts.load(tpl.weight + ' ' + Math.round(H*c.size) + "px '" + tpl.family + "'"); } catch(e){}
        drawCaptionOn(x, c.text, tpl, W, H, c.region, c.size);
    }
    return await new Promise(function(res){ cv.toBlob(function(b){ b.arrayBuffer().then(function(ab){ res(new Uint8Array(ab)); }); }, 'image/jpeg', 0.92); });
}

var previewSeq = 0;
async function renderPreview(){
    var cv = document.getElementById('capPreview'); if (!cv) return;
    var seq = ++previewSeq;
    var dims = ratioDims(), AR = dims[0]/dims[1];
    var PH = 250, PW = Math.round(PH * AR);
    cv.width = PW; cv.height = PH;
    var x = cv.getContext('2d');
    var g = x.createLinearGradient(0,0,0,PH); g.addColorStop(0,'#3a4a57'); g.addColorStop(1,'#1b2630');
    x.fillStyle = g; x.fillRect(0,0,PW,PH);
    var tpl = CAP_TEMPLATES[selTpl] || CAP_TEMPLATES.impact;
    var nar = (document.getElementById('capNarasi').value||'').trim() || 'contoh narasi build-up di sini';
    var gng = (document.getElementById('capGong').value||'').trim() || 'gong pamungkas';
    try { await document.fonts.load(tpl.weight + " 40px '" + tpl.family + "'"); } catch(e){}
    if (seq !== previewSeq) return;  // sudah ada render preview lebih baru
    drawCaptionOn(x, nar, tpl, PW, PH, 'lower', 0.052);
    drawCaptionOn(x, gng, tpl, PW, PH, 'center', 0.085);
}
function probeDuration(blob){
    return new Promise(function(res){
        var a = new Audio(); a.preload = 'metadata';
        a.onloadedmetadata = function(){ res(isFinite(a.duration) ? a.duration : 0); };
        a.onerror = function(){ res(0); };
        a.src = URL.createObjectURL(blob);
    });
}

async function doRender(){
    if (!selImage){ alert('Pilih gambar dulu.'); return; }
    if (!selAudio){ alert('Pilih audio dulu.'); return; }
    if (busy) return;
    var btn = document.getElementById('renderBtn');
    busy = true; btn.disabled = true;
    var tmpUrl = null;
    try {
        await ensureFfmpeg();
        var dims = ratioDims(), W = dims[0], H = dims[1];
        var audName = 'aud.' + (selAudio.ext || 'mp3');

        setStatus('<span class="spinner"></span> Memuat aset…');
        // gambar via blob URL (hindari canvas taint) → Image element
        var imgBytes = await fetchBytes(selImage.kind === 'url' ? selImage.src : selImage.file);
        tmpUrl = URL.createObjectURL(new Blob([imgBytes]));
        var im = await loadImageEl(tmpUrl);
        await ffmpeg.writeFile(audName, await fetchBytes(selAudio.blob));

        var narasi = (document.getElementById('capNarasi').value || '').trim();
        var gong   = (document.getElementById('capGong').value || '').trim();
        var tpl = CAP_TEMPLATES[selTpl] || CAP_TEMPLATES.impact;
        var hasN = narasi !== '', hasG = gong !== '';
        var dur = (hasN && hasG) ? await probeDuration(selAudio.blob) : 0;
        var args;

        if (hasN && hasG && dur > 1.5){
            // 2 segmen: frame narasi (build-up) → frame gong (pamungkas) di detik akhir, lalu concat
            setStatus('<span class="spinner"></span> Menyiapkan caption…');
            var gongSec = Math.min(2.6, dur * 0.4), T = Math.max(0.5, dur - gongSec);
            await ffmpeg.writeFile('fa.jpg', await frameJpeg(im, W, H, [{text:narasi, region:'lower',  size:0.052}], tpl));
            await ffmpeg.writeFile('fb.jpg', await frameJpeg(im, W, H, [{text:gong,   region:'center', size:0.085}], tpl));
            args = function(codec){
                var v = codec === 'libx264' ? ['-c:v','libx264','-preset','ultrafast','-pix_fmt','yuv420p'] : ['-c:v','mpeg4','-q:v','4'];
                return ['-loop','1','-t',T.toFixed(2),'-i','fa.jpg','-loop','1','-t',gongSec.toFixed(2),'-i','fb.jpg','-i',audName,
                    '-filter_complex','[0:v][1:v]concat=n=2:v=1:a=0,format=yuv420p[v]','-map','[v]','-map','2:a','-r','25']
                    .concat(v, ['-c:a','aac','-b:a','160k','-shortest','-movflags','+faststart','out.mp4']);
            };
        } else {
            // 1 frame: caption (kalau ada) di-bake ke gambar
            var caps = [];
            if (hasN) caps.push({text:narasi, region:'lower',  size:0.052});
            if (hasG) caps.push({text:gong,   region:'center', size:0.085});
            await ffmpeg.writeFile('frame.jpg', await frameJpeg(im, W, H, caps, tpl));
            args = function(codec){
                var v = codec === 'libx264' ? ['-c:v','libx264','-preset','ultrafast','-tune','stillimage','-pix_fmt','yuv420p'] : ['-c:v','mpeg4','-q:v','4'];
                return ['-loop','1','-i','frame.jpg','-i',audName,'-r','25']
                    .concat(v, ['-c:a','aac','-b:a','160k','-shortest','-movflags','+faststart','out.mp4']);
            };
        }

        setStatus('<span class="spinner"></span> Merender video…');
        var rc = await ffmpeg.exec(args('libx264'));
        if (rc !== 0){
            setStatus('<span class="spinner"></span> Merender (mode kompatibel)…');
            try { ffmpeg.deleteFile('out.mp4'); } catch(e){}
            rc = await ffmpeg.exec(args('mpeg4'));
        }
        if (rc !== 0) throw new Error('Render gagal (kode ' + rc + '). Coba audio/gambar lain.');

        var data = await ffmpeg.readFile('out.mp4');
        ['fa.jpg','fb.jpg','frame.jpg', audName, 'out.mp4'].forEach(function(f){ try{ ffmpeg.deleteFile(f); }catch(e){} });

        if (lastUrl) URL.revokeObjectURL(lastUrl);
        var blob = new Blob([data.buffer], { type:'video/mp4' });
        lastUrl = URL.createObjectURL(blob);
        document.getElementById('resultVideo').src = lastUrl;
        var dl = document.getElementById('dlBtn'); dl.href = lastUrl;
        dl.download = 'maftune_' + ratio.replace(':','x') + '_' + Date.now() + '.mp4';
        document.getElementById('resultMeta').textContent = ratio + ' · ' + Math.round(blob.size/1024) + ' KB';
        document.getElementById('resultWrap').style.display = 'block';
        setStatus('✓ Video jadi! Unduh lalu upload ke TikTok / IG / YouTube.');
    } catch(e){
        console.error('render error:', e);
        setStatus('⚠️ ' + ((e && e.message) || e));
    } finally {
        if (tmpUrl) URL.revokeObjectURL(tmpUrl);
        busy = false; btn.disabled = false;
    }
}
</script>

@endsection
