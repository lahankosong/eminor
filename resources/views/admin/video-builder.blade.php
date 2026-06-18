@extends('layouts.admin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Caveat:wght@700&family=Poppins:wght@700;800&family=Montserrat:wght@700&family=Oswald:wght@600&family=Playfair+Display:wght@700&family=Lobster&display=swap" rel="stylesheet">
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
    .img-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(56px,1fr)); gap:6px; max-height:240px; overflow-y:auto; padding:2px; }
    .img-pick { position:relative; aspect-ratio:9/16; border-radius:6px; overflow:hidden; border:2px solid var(--border); cursor:pointer; background:var(--bg-3); }
    .img-pick img { width:100%; height:100%; object-fit:cover; display:block; }
    .img-pick.sel { border-color:var(--accent); box-shadow:0 0 0 2px var(--accent-dim); }
    .img-pick.sel::after { content:attr(data-ord); position:absolute; top:2px; left:3px; color:#fff; background:var(--accent); border-radius:50%; min-width:15px; height:15px; padding:0 3px; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .vb-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.1rem; align-items:start; }
    .vb-grid > .card { margin-bottom:0; }
    .vb-right { position:sticky; top:70px; }
    @media (max-width:900px){ .vb-grid { grid-template-columns:1fr; } .vb-right { position:static; } }
    .img-del { position:absolute; top:2px; right:2px; width:17px; height:17px; border:none; border-radius:50%; background:rgba(0,0,0,0.6); color:#fff; font-size:13px; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; }
    .img-del:hover { background:#ef4444; }

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
    .sec { display:flex; justify-content:space-between; align-items:center; gap:10px; font-size:11px; font-weight:600; color:var(--text-2); text-transform:uppercase; letter-spacing:0.04em; margin:16px 0 8px; padding-top:12px; border-top:1px solid var(--border-2); }
    .sec:first-child { border-top:none; padding-top:0; margin-top:0; }
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

<div class="vb-grid">
{{-- CARD A: ASET & CAPTION --}}
<div class="card">
    <div class="card-head"><span>🎨 Aset &amp; Caption</span></div>
    <div class="card-body">
        {{-- Gambar --}}
        <div class="sec"><span>Gambar</span>
            <label class="btn btn-soft" style="font-size:11px;padding:4px 10px;cursor:pointer;">+ Upload<input type="file" id="imgUpload" accept="image/*" hidden></label>
        </div>
        <div class="img-grid" id="imgGrid">
            @forelse($images as $img)
            <div class="img-pick" data-id="{{ $img->id }}" data-src="{{ $img->url }}" onclick="pickImage(this)" title="{{ \Illuminate\Support\Str::limit($img->prompt, 80) }}">
                <img src="{{ $img->url }}" loading="lazy" alt="">
                <button class="img-del" title="Hapus" onclick="delImage(event, this)">×</button>
            </div>
            @empty
            <div class="empty-row" style="grid-column:1/-1;">Belum ada gambar. Buat di <a href="{{ route('admin.ai-agent') }}" style="color:var(--accent);">AI Agent</a> atau + Upload.</div>
            @endforelse
        </div>
        <div class="muted" id="imgInfo" style="margin-top:6px;">Pilih 1 gambar (statis) atau beberapa (urut = scene berganti).</div>

        {{-- Audio --}}
        <div class="sec"><span>Audio</span>
            <label class="btn btn-soft" style="font-size:11px;padding:4px 10px;cursor:pointer;">+ Upload<input type="file" id="audUpload" accept="audio/*" hidden></label>
        </div>
        <div id="audList" style="max-height:200px;overflow-y:auto;"><div class="empty-row">Memuat…</div></div>
        <div class="muted" id="audInfo" style="margin-top:6px;"></div>

        {{-- Caption --}}
        <div class="sec"><span>Caption (opsional)</span></div>
        <div class="row" style="gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <textarea class="fi" id="capNarasi" rows="2" style="flex:1;min-width:170px;resize:vertical;" placeholder="narasi build-up…"></textarea>
            <input type="text" class="fi" id="capGong" style="flex:1;min-width:170px;" placeholder="🎯 gong pamungkas…">
        </div>
        <div class="row" style="gap:8px;flex-wrap:wrap;margin-bottom:10px;align-items:center;">
            <select class="fi" id="capFontSel" onchange="onFontChange()" style="flex:1;min-width:120px;">
                <option value="">Font: template</option>
                <option>Anton</option><option>Bebas Neue</option><option>Poppins</option><option>Montserrat</option><option>Oswald</option><option>Playfair Display</option><option>Lobster</option><option>Caveat</option>
            </select>
            <span class="muted">Narasi</span>
            <select class="fi" id="narSizeRange" onchange="onSizeChange()" style="width:auto;">
                <option value="4">4</option><option value="5" selected>5</option><option value="6">6</option><option value="7">7</option>
            </select>
            <span class="muted">Gong</span>
            <select class="fi" id="gongSizeRange" onchange="onSizeChange()" style="width:auto;">
                <option value="7">7</option><option value="9" selected>9</option><option value="11">11</option><option value="13">13</option>
            </select>
            <input type="color" id="capColorSel" value="#ffffff" oninput="onColorChange()" title="Warna" style="width:42px;height:38px;border:1px solid var(--border);border-radius:8px;background:var(--bg-3);cursor:pointer;">
        </div>
        <div class="ratio-opt" id="tplOpt" style="margin-bottom:10px;">
            <span class="ratio-btn sel" data-t="impact"  onclick="pickTpl(this)">Impact</span>
            <span class="ratio-btn" data-t="boxbold" onclick="pickTpl(this)">Bold Box</span>
            <span class="ratio-btn" data-t="neon"    onclick="pickTpl(this)">Neon</span>
            <span class="ratio-btn" data-t="tulis"   onclick="pickTpl(this)">Tulis tangan</span>
        </div>
        <div class="muted" style="margin-bottom:5px;">Preview — seret teks untuk atur posisi</div>
        <canvas id="capPreview" style="border:1px solid var(--border);border-radius:8px;max-width:100%;display:block;cursor:grab;touch-action:none;"></canvas>
    </div>
</div>

{{-- CARD B: RAKIT & HASIL --}}
<div class="card vb-right">
    <div class="card-head"><span>🎬 Rakit Video</span></div>
    <div class="card-body">
        <div class="row" style="align-items:center;">
            <span class="muted">Format</span>
            <select class="fi" id="ratioSel" onchange="onRatioChange()" style="width:auto;">
                <option value="9:16">📱 9:16 Short</option>
                <option value="1:1">⬛ 1:1</option>
                <option value="16:9">🖥️ 16:9</option>
            </select>
            <button class="btn btn-primary" id="renderBtn" onclick="doRender()">🎬 Rakit</button>
        </div>
        <div class="status" id="status"></div>

        <div id="resultWrap" style="display:none;margin-top:12px;">
            <video class="result-video" id="resultVideo" controls></video>
            <div class="row" style="margin-top:10px;">
                <a class="btn btn-accent" id="dlBtn" download="video.mp4">⬇️ Unduh</a>
                <button class="btn btn-soft" onclick="saveVideo()">💾 Simpan</button>
                <span class="muted" id="resultMeta"></span>
            </div>
        </div>

        <div class="sec"><span>Video Tersimpan</span></div>
        <div id="vidList"><div class="empty-row">Belum ada video tersimpan.</div></div>
    </div>
</div>
</div>{{-- /vb-grid --}}

<script src="{{ asset('ffmpeg/ffmpeg.js') }}"></script>
<script>
var FFMPEG_BASE = '{{ asset('ffmpeg') }}';
var CSRF = '{{ csrf_token() }}';
var IMG_DEL_BASE = '{{ url('admin/ai-agent/image') }}';
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
var selImages = [];    // urutan scene: [{kind:'url'|'file', src|file, ext, el}]
var selAudio = null;   // {kind:'idb'|'file', blob, ext, name}
var ratio = '9:16';
var selTpl = 'impact';
var capFont = '', capColor = '';                       // '' = ikut template
var narSize = 0.05, gongSize = 0.09;                   // sizeFactor (×H)
var posN = {x:0.5, y:0.84}, posG = {x:0.5, y:0.5};     // posisi caption (normalized, bisa di-seret)
var ffmpeg = null, ffmpegLoaded = false, busy = false, lastUrl = null, lastBlob = null;

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
    var tpl = CAP_TEMPLATES[selTpl];
    capFont = ''; document.getElementById('capFontSel').value = '';            // ikut template
    capColor = ''; document.getElementById('capColorSel').value = tpl.color;   // tampilkan warna template
    renderPreview();
}
function onFontChange(){ capFont = document.getElementById('capFontSel').value; renderPreview(); }
function onColorChange(){ capColor = document.getElementById('capColorSel').value; renderPreview(); }
function onSizeChange(){
    narSize  = parseFloat(document.getElementById('narSizeRange').value)/100;
    gongSize = parseFloat(document.getElementById('gongSizeRange').value)/100;
    renderPreview();
}

function setStatus(h){ document.getElementById('status').innerHTML = h || ''; }
function extFromType(t, fallback){ if(!t) return fallback; var m=t.split('/')[1]; return m ? m.replace('jpeg','jpg').split(';')[0] : fallback; }

// ===== Gambar (multi-select → scene berurutan) =====
function pickImage(el){
    var i = selImages.findIndex(function(s){ return s.el === el; });
    if (i >= 0){ selImages.splice(i,1); el.classList.remove('sel'); }
    else {
        var isUp = el.dataset.upload === '1';
        selImages.push(isUp ? {kind:'file', file:el._file, ext:el._ext||'jpg', el:el}
                            : {kind:'url', src:el.dataset.src, ext:'jpg', el:el});
        el.classList.add('sel');
    }
    renumberImages();
}
function renumberImages(){
    selImages.forEach(function(s, idx){ if (s.el) s.el.dataset.ord = (idx+1); });
    document.querySelectorAll('.img-pick').forEach(function(c){ if (!c.classList.contains('sel')) c.removeAttribute('data-ord'); });
    var info = document.getElementById('imgInfo'); if (!info) return;
    var n = selImages.length;
    info.textContent = n ? (n===1 ? '1 gambar dipilih.' : n + ' gambar — jadi ' + n + ' scene berurutan.') : 'Belum ada gambar dipilih.';
}
function delImage(ev, btn){
    ev.stopPropagation();
    var cell = btn.closest('.img-pick'), id = cell.dataset.id;
    if (!confirm('Hapus gambar ini dari stok? (terhapus juga di Cloudinary)')) return;
    btn.disabled = true;
    fetch(IMG_DEL_BASE + '/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
        .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.json().catch(function(){return {};}); })
        .then(function(){
            selImages = selImages.filter(function(s){ return s.el !== cell; });
            cell.remove(); renumberImages();
            var grid = document.getElementById('imgGrid');
            if (grid && !grid.querySelector('.img-pick')) grid.innerHTML = '<div class="empty-row" style="grid-column:1/-1;">Stok kosong.</div>';
        })
        .catch(function(e){ btn.disabled=false; alert('Gagal hapus: '+e.message); });
}
function delUpload(ev, btn){
    ev.stopPropagation();
    var cell = btn.closest('.img-pick');
    selImages = selImages.filter(function(s){ return s.el !== cell; });
    cell.remove(); renumberImages();
}
document.getElementById('imgUpload').addEventListener('change', function(){
    var f = this.files[0]; if (!f) return;
    var grid = document.getElementById('imgGrid');
    var er = grid.querySelector('.empty-row'); if (er) er.remove();
    var url = URL.createObjectURL(f);
    var cell = document.createElement('div');
    cell.className = 'img-pick'; cell.dataset.upload = '1'; cell.dataset.src = url;
    cell._file = f; cell._ext = extFromType(f.type,'jpg');
    cell.innerHTML = '<img src="'+url+'" alt=""><button class="img-del" title="Hapus" onclick="delUpload(event,this)">×</button>';
    cell.addEventListener('click', function(ev){ if (ev.target.classList.contains('img-del')) return; pickImage(cell); });
    grid.insertBefore(cell, grid.firstChild);
    pickImage(cell);
    this.value = '';
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
function onRatioChange(){
    ratio = document.getElementById('ratioSel').value;
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

// o: {size, cx, cy(center px), family, color}. Mengembalikan bounding box utk drag.
function drawCaptionOn(x, text, tpl, W, H, o){
    var fpx = Math.round(H * o.size);
    var family = o.family || tpl.family;
    var weight = o.family ? '700' : tpl.weight;
    var color  = o.color  || tpl.color;
    x.font = weight + ' ' + fpx + "px '" + family + "', sans-serif";
    x.textAlign = 'center'; x.textBaseline = 'middle';
    var lines = wrapText(x, text, W * 0.9), lineH = fpx * 1.22, totalH = lines.length * lineH;
    var maxW = 0; lines.forEach(function(ln){ maxW = Math.max(maxW, x.measureText(ln).width); });
    var cx = o.cx, cyC = o.cy;
    lines.forEach(function(ln, i){
        var yy = cyC - totalH/2 + lineH/2 + i*lineH;
        if (tpl.box){
            var tw = x.measureText(ln).width;
            x.fillStyle = tpl.box;
            roundRect(x, cx - tw/2 - fpx*0.32, yy - lineH*0.48, tw + fpx*0.64, lineH*0.96, Math.max(4, fpx*0.16)); x.fill();
        }
        if (tpl.shadow){ x.shadowColor = tpl.shadow; x.shadowBlur = fpx*0.28; x.shadowOffsetY = fpx*0.05; }
        if (tpl.stroke){ x.lineWidth = Math.max(2, fpx*0.13); x.strokeStyle = tpl.stroke; x.lineJoin='round'; x.strokeText(ln, cx, yy); }
        x.fillStyle = color; x.fillText(ln, cx, yy);
        x.shadowColor = 'transparent'; x.shadowBlur = 0; x.shadowOffsetY = 0;
    });
    return { x: cx - maxW/2, y: cyC - totalH/2, w: maxW, h: totalH };
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
        var fam = c.family || tpl.family;
        try { await document.fonts.load('700 ' + Math.round(H*c.size) + "px '" + fam + "'"); } catch(e){}
        x.globalAlpha = (c.alpha != null) ? c.alpha : 1;
        drawCaptionOn(x, c.text, tpl, W, H, { size:c.size, cx:c.cx, cy:c.cy, family:c.family, color:c.color });
        x.globalAlpha = 1;
    }
    return await new Promise(function(res){ cv.toBlob(function(b){ b.arrayBuffer().then(function(ab){ res(new Uint8Array(ab)); }); }, 'image/jpeg', 0.92); });
}

var previewSeq = 0, boxN = null, boxG = null;
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
    var nar = (document.getElementById('capNarasi').value||'').trim() || 'contoh narasi build-up';
    var gng = (document.getElementById('capGong').value||'').trim() || 'gong pamungkas';
    var fam = capFont || tpl.family;
    try { await document.fonts.load("700 40px '" + fam + "'"); await document.fonts.load(tpl.weight + " 40px '" + tpl.family + "'"); } catch(e){}
    if (seq !== previewSeq) return;
    boxN = drawCaptionOn(x, nar, tpl, PW, PH, { size:narSize,  cx:posN.x*PW, cy:posN.y*PH, family:capFont, color:capColor });
    boxG = drawCaptionOn(x, gng, tpl, PW, PH, { size:gongSize, cx:posG.x*PW, cy:posG.y*PH, family:capFont, color:capColor });
}
// Seret caption di preview untuk atur posisi
(function(){
    var cv = document.getElementById('capPreview'); if (!cv) return;
    var drag = null;
    function toCv(ev){ var r = cv.getBoundingClientRect(); return { x:(ev.clientX-r.left)*(cv.width/r.width), y:(ev.clientY-r.top)*(cv.height/r.height) }; }
    function hit(b,p){ return b && p.x>=b.x-12 && p.x<=b.x+b.w+12 && p.y>=b.y-12 && p.y<=b.y+b.h+12; }
    cv.addEventListener('pointerdown', function(ev){
        var p = toCv(ev);
        if (hit(boxG,p)) drag='G'; else if (hit(boxN,p)) drag='N';
        if (drag){ try{ cv.setPointerCapture(ev.pointerId); }catch(e){} cv.style.cursor='grabbing'; ev.preventDefault(); }
    });
    cv.addEventListener('pointermove', function(ev){
        if (!drag) return;
        var p = toCv(ev), t = (drag==='G') ? posG : posN;
        t.x = Math.min(0.99, Math.max(0.01, p.x/cv.width));
        t.y = Math.min(0.97, Math.max(0.03, p.y/cv.height));
        renderPreview();
    });
    function end(){ drag = null; cv.style.cursor='grab'; }
    cv.addEventListener('pointerup', end); cv.addEventListener('pointercancel', end);
})();
function probeDuration(blob){
    return new Promise(function(res){
        var a = new Audio(); a.preload = 'metadata';
        a.onloadedmetadata = function(){ res(isFinite(a.duration) ? a.duration : 0); };
        a.onerror = function(){ res(0); };
        a.src = URL.createObjectURL(blob);
    });
}

function vcodec(c, still){ return c==='libx264' ? ['-c:v','libx264','-preset','ultrafast'].concat(still?['-tune','stillimage']:[], ['-pix_fmt','yuv420p']) : ['-c:v','mpeg4','-q:v','4']; }
function aTail(){ return ['-c:a','aac','-b:a','160k','-shortest','-movflags','+faststart','out.mp4']; }

async function doRender(){
    if (!selImages.length){ alert('Pilih minimal 1 gambar.'); return; }
    if (!selAudio){ alert('Pilih audio dulu.'); return; }
    if (busy) return;
    var btn = document.getElementById('renderBtn');
    busy = true; btn.disabled = true;
    var tmpUrls = [];
    try {
        await ensureFfmpeg();
        var dims = ratioDims(), W = dims[0], H = dims[1];
        var audName = 'aud.' + (selAudio.ext || 'mp3');

        setStatus('<span class="spinner"></span> Memuat aset…');
        // muat semua gambar via blob URL (hindari canvas taint)
        var ims = [];
        for (var si=0; si<selImages.length; si++){
            var s = selImages[si];
            var bytes = await fetchBytes(s.kind === 'url' ? s.src : s.file);
            var u = URL.createObjectURL(new Blob([bytes])); tmpUrls.push(u);
            ims.push(await loadImageEl(u));
        }
        await ffmpeg.writeFile(audName, await fetchBytes(selAudio.blob));

        var narasi = (document.getElementById('capNarasi').value || '').trim();
        var gong   = (document.getElementById('capGong').value || '').trim();
        var tpl = CAP_TEMPLATES[selTpl] || CAP_TEMPLATES.impact;
        var hasN = narasi !== '', hasG = gong !== '';
        var dur = await probeDuration(selAudio.blob);
        var N = ims.length;

        function narCap(){ return {text:narasi, cx:posN.x*W, cy:posN.y*H, size:narSize, family:capFont, color:capColor}; }
        function gongCap(sc, al){ return {text:gong, cx:posG.x*W, cy:posG.y*H, size:gongSize*(sc||1), alpha:al, family:capFont, color:capColor}; }

        // ===== Bangun daftar segmen {im, caps, dur} =====
        var segments = [];
        if (N === 1){
            if (hasN && hasG && dur > 1.5){
                // 1 gambar: narasi → gong masuk (animasi fade+zoom) → hold
                var gongSec = Math.min(2.8, Math.max(1.2, dur*0.4)), T = Math.max(0.5, dur - gongSec);
                var specs = [ {a:0.35,s:0.80,d:0.07}, {a:0.7,s:0.97,d:0.07}, {a:1.0,s:1.10,d:0.07}, {a:1.0,s:1.0,d:Math.max(0.4, gongSec-0.21)} ];
                segments.push({ im:ims[0], caps:[narCap()], dur:T });
                specs.forEach(function(sp){ segments.push({ im:ims[0], caps:[gongCap(sp.s, sp.a)], dur:sp.d }); });
            } else {
                var caps = []; if (hasN) caps.push(narCap()); if (hasG) caps.push(gongCap(1,1));
                segments.push({ im:ims[0], caps:caps, dur: 0 });   // dur 0 → loop + shortest
            }
        } else {
            // ===== MULTI-SCENE: tiap gambar = 1 scene bergantian; narasi di build-up, gong di akhir =====
            if (!dur || dur < 1) dur = N * 2;
            var segLen = dur / N, gSec = hasG ? Math.min(2.6, Math.max(1.0, Math.min(segLen, dur*0.3))) : 0, gStart = dur - gSec;
            for (var i=0; i<N; i++){
                var s0 = i*segLen, s1 = (i+1)*segLen;
                if (!hasG || s1 <= gStart + 0.02){
                    segments.push({ im:ims[i], caps: hasN?[narCap()]:[], dur: segLen });
                } else if (s0 >= gStart){
                    segments.push({ im:ims[i], caps: [gongCap(1,1)], dur: segLen });
                } else {
                    if (hasN) segments.push({ im:ims[i], caps:[narCap()], dur: gStart - s0 });
                    segments.push({ im:ims[i], caps:[gongCap(1,1)], dur: s1 - gStart });
                }
            }
        }

        // ===== Tulis frame per segmen =====
        setStatus('<span class="spinner"></span> Menyiapkan frame…');
        var written = [];
        for (var k=0; k<segments.length; k++){
            var fn = 'v'+k+'.jpg';
            await ffmpeg.writeFile(fn, await frameJpeg(segments[k].im, W, H, segments[k].caps, tpl));
            written.push(fn);
        }

        var args;
        if (segments.length === 1 && !segments[0].dur){
            args = function(codec){ return ['-loop','1','-i','v0.jpg','-i',audName,'-r','25'].concat(vcodec(codec,true), aTail()); };
        } else {
            args = function(codec){
                var ins = [], lbl = '';
                for (var k2=0; k2<segments.length; k2++){ ins.push('-loop','1','-t',(segments[k2].dur||1).toFixed(2),'-i','v'+k2+'.jpg'); lbl += '['+k2+':v]'; }
                ins.push('-i', audName);
                return ins.concat(['-filter_complex', lbl + 'concat=n='+segments.length+':v=1:a=0,format=yuv420p[v]',
                    '-map','[v]','-map', segments.length+':a','-r','25'], vcodec(codec,false), aTail());
            };
        }

        setStatus('<span class="spinner"></span> Merender video' + (N>1 ? ' ('+N+' scene)' : '') + '…');
        var rc = await ffmpeg.exec(args('libx264'));
        if (rc !== 0){
            setStatus('<span class="spinner"></span> Merender (mode kompatibel)…');
            try { ffmpeg.deleteFile('out.mp4'); } catch(e){}
            rc = await ffmpeg.exec(args('mpeg4'));
        }
        if (rc !== 0) throw new Error('Render gagal (kode ' + rc + '). Coba audio/gambar lain.');

        var data = await ffmpeg.readFile('out.mp4');
        written.concat([audName,'out.mp4']).forEach(function(f){ try{ ffmpeg.deleteFile(f); }catch(e){} });

        if (lastUrl) URL.revokeObjectURL(lastUrl);
        var blob = new Blob([data.buffer], { type:'video/mp4' });
        lastBlob = blob; lastUrl = URL.createObjectURL(blob);
        document.getElementById('resultVideo').src = lastUrl;
        var dl = document.getElementById('dlBtn'); dl.href = lastUrl;
        dl.download = 'maftune_' + ratio.replace(':','x') + '_' + Date.now() + '.mp4';
        document.getElementById('resultMeta').textContent = ratio + ' · ' + N + ' scene · ' + Math.round(blob.size/1024) + ' KB';
        document.getElementById('resultWrap').style.display = 'block';
        setStatus('✓ Video jadi! Unduh lalu upload ke TikTok / IG / YouTube.');
    } catch(e){
        console.error('render error:', e);
        setStatus('⚠️ ' + ((e && e.message) || e));
    } finally {
        tmpUrls.forEach(function(u){ URL.revokeObjectURL(u); });
        busy = false; btn.disabled = false;
    }
}

// ===== Video library (IndexedDB 'mafVideos') =====
function vidOpen(){
    return new Promise(function(res, rej){
        var r = indexedDB.open('mafVideos', 1);
        r.onupgradeneeded = function(){ r.result.createObjectStore('vids', { keyPath:'id', autoIncrement:true }); };
        r.onsuccess = function(){ res(r.result); };
        r.onerror = function(){ rej(r.error); };
    });
}
async function vidAll(){ var db = await vidOpen(); return new Promise(function(res){ var t=db.transaction('vids').objectStore('vids').getAll(); t.onsuccess=function(){res(t.result||[]);}; t.onerror=function(){res([]);}; }); }
async function vidAdd(rec){ var db = await vidOpen(); return new Promise(function(res){ db.transaction('vids','readwrite').objectStore('vids').add(rec).onsuccess=function(){res();}; }); }
async function vidDel(id){ var db = await vidOpen(); return new Promise(function(res){ db.transaction('vids','readwrite').objectStore('vids').delete(id).onsuccess=function(){res();}; }); }

async function saveVideo(){
    if (!lastBlob){ alert('Belum ada video.'); return; }
    var name = prompt('Nama video:', 'maftune ' + new Date().toLocaleString('id'));
    if (name === null) return;
    await vidAdd({ name: (name||'video').trim(), ratio: ratio, blob: lastBlob, size: lastBlob.size, createdAt: Date.now() });
    setStatus('✓ Video tersimpan di perangkat.');
    renderVideoList();
}
async function renderVideoList(){
    var list = document.getElementById('vidList'); if (!list) return;
    var all = await vidAll();
    if (!all.length){ list.innerHTML = '<div class="empty-row">Belum ada video tersimpan.</div>'; return; }
    list.innerHTML = '';
    all.sort(function(a,b){ return b.createdAt - a.createdAt; }).forEach(function(c){
        var url = URL.createObjectURL(c.blob), mb = (c.size/1048576).toFixed(1);
        var d = document.createElement('div');
        d.className = 'aud-item'; d.style.cursor = 'default'; d.style.alignItems = 'flex-start';
        d.innerHTML =
            '<video controls src="' + url + '" style="width:120px;border-radius:6px;flex-shrink:0;"></video>' +
            '<div style="flex:1;min-width:0;"><div class="aud-name">' + (c.name||'Video').replace(/</g,'&lt;') + '</div>' +
            '<div class="aud-meta">' + c.ratio + ' · ' + mb + ' MB</div>' +
            '<div class="row" style="margin-top:6px;"><a class="btn btn-accent" href="' + url + '" download="' + (c.name||'video') + '.mp4" style="font-size:11px;padding:5px 11px;">⬇️ Unduh</a>' +
            '<button class="btn btn-soft" data-del="' + c.id + '" style="font-size:11px;padding:5px 11px;">Hapus</button></div></div>';
        d.querySelector('[data-del]').addEventListener('click', async function(){
            if (!confirm('Hapus video ini?')) return;
            await vidDel(c.id); renderVideoList();
        });
        list.appendChild(d);
    });
}
renderVideoList();
</script>

@endsection
