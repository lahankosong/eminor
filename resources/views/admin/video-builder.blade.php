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
    .img-pick { position:relative; aspect-ratio:1/1; border-radius:6px; overflow:hidden; border:2px solid var(--border); cursor:pointer; background:#1b2630; }
    .img-pick img { width:100%; height:100%; object-fit:contain; display:block; }
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
    .modal-ov { position:fixed; inset:0; background:rgba(0,0,0,0.6); display:none; align-items:center; justify-content:center; z-index:1000; padding:16px; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; padding:16px; max-width:360px; width:100%; }
    .modal-bx h4 { font-size:13px; font-weight:600; color:var(--text); margin-bottom:10px; display:flex; justify-content:space-between; align-items:center; }
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
            <span style="display:flex;gap:6px;">
                <button class="btn btn-soft" style="font-size:11px;padding:4px 10px;" onclick="openCrop()">✂️ Crop</button>
                <label class="btn btn-soft" style="font-size:11px;padding:4px 10px;cursor:pointer;">+ Upload<input type="file" id="imgUpload" accept="image/*" hidden></label>
            </span>
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

        <div class="sec"><span>Efek &amp; Transisi</span></div>
        <div class="row" style="gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="muted">Warna</span>
            <select class="fi" id="vfxSel" onchange="onEffectChange()" style="width:auto;">
                <option value="">Tanpa efek</option>
                <option value="cinematic">Sinematik</option>
                <option value="warm">Hangat</option>
                <option value="cool">Dingin</option>
                <option value="vintage">Vintage</option>
                <option value="bw">Hitam-putih</option>
                <option value="vivid">Vivid</option>
            </select>
            <span class="muted">Transisi scene</span>
            <select class="fi" id="transSel" style="width:auto;">
                <option value="none">Tanpa</option>
                <option value="fade">Fade</option>
            </select>
            <span class="muted" style="opacity:0.7;">(transisi aktif jika ≥2 gambar)</span>
        </div>
        <label class="muted" style="display:flex;align-items:center;gap:5px;cursor:pointer;margin-top:9px;">
            <input type="checkbox" id="autoSub"> 📝 Auto subtitle dari audio (dipakai jika narasi &amp; gong kosong)
        </label>

        <div class="sec"><span>Timing caption (detik)</span></div>
        <div class="row" style="gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="muted">Narasi</span>
            <input type="number" class="fi" id="narStart" min="0" step="0.5" placeholder="muncul" style="width:70px;" title="narasi muncul detik">
            <input type="number" class="fi" id="narEnd" min="0" step="0.5" placeholder="hilang" style="width:70px;" title="narasi hilang detik">
            <span class="muted">Gong</span>
            <input type="number" class="fi" id="gongStart" min="0" step="0.5" placeholder="muncul" style="width:70px;" title="gong muncul detik">
            <input type="number" class="fi" id="gongEnd" min="0" step="0.5" placeholder="hilang" style="width:70px;" title="gong hilang detik">
        </div>
        <div class="muted" style="margin-top:4px;">Kosong = otomatis (narasi build-up, gong di akhir).<span id="timingDur"></span></div>
    </div>
</div>

{{-- CARD B: RAKIT & HASIL --}}
<div class="card vb-right">
    <div class="card-head"><span>🎬 Rakit Video</span></div>
    <div class="card-body">
        <div id="batchBanner" style="display:none;background:var(--accent-dim);border:1px solid var(--accent);border-radius:8px;padding:8px 11px;margin-bottom:10px;font-size:12px;color:var(--text);"></div>
        <div class="row" style="align-items:center;margin-bottom:8px;">
            <span class="muted">Watermark</span>
            <input type="text" class="fi" id="wmInput" value="EMINOR.my.id" oninput="renderPreview()" style="flex:1;min-width:130px;" placeholder="kosongkan = tanpa">
        </div>
        <div class="row" style="align-items:center;">
            <span class="muted">Format</span>
            <select class="fi" id="ratioSel" onchange="onRatioChange()" style="width:auto;">
                <option value="9:16">📱 9:16 (TikTok/Reels/Shorts)</option>
                <option value="1:1">⬛ 1:1 (Feed)</option>
                <option value="16:9">🖥️ 16:9 (YouTube)</option>
            </select>
            <select class="fi" id="qualitySel" onchange="onQualityChange()" style="width:auto;" title="Resolusi">
                <option value="720" selected>720p</option>
                <option value="1080">1080p HD</option>
            </select>
            <button class="btn btn-primary" id="renderBtn" onclick="doRender()">🎬 Rakit</button>
            <button class="btn btn-accent" id="batchBtn" onclick="doBatch()" style="display:none;">🎬 Rakit semua</button>
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

        <div class="sec"><span>Proyek</span>
            <button class="btn btn-soft" style="font-size:11px;padding:4px 10px;" onclick="saveProject()">💾 Simpan proyek</button>
        </div>
        <div id="projList"><div class="empty-row">Belum ada proyek tersimpan.</div></div>

        <div class="sec"><span>Video Tersimpan</span></div>
        <div id="vidList"><div class="empty-row">Belum ada video tersimpan.</div></div>
    </div>
</div>
</div>{{-- /vb-grid --}}

{{-- MODAL CROP --}}
<div class="modal-ov" id="cropModal">
    <div class="modal-bx">
        <h4><span>✂️ Crop Gambar <span id="cropIdx" class="muted"></span></span>
            <button onclick="closeCrop()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-3);line-height:1;">&times;</button>
        </h4>
        <canvas id="cropCanvas" style="width:100%;border-radius:8px;border:1px solid var(--border);cursor:grab;touch-action:none;display:block;background:#000;"></canvas>
        <div class="ratio-opt" id="cropFitOpt" style="margin-top:8px;">
            <span class="ratio-btn sel" data-f="cover" onclick="cropFit(this)">Penuhi (crop)</span>
            <span class="ratio-btn" data-f="contain" onclick="cropFit(this)">Muat semua (latar blur)</span>
        </div>
        <div class="muted" id="cropHint" style="margin-top:6px;">Seret untuk geser · slider untuk zoom</div>
        <div class="row" style="gap:8px;align-items:center;margin-top:6px;">
            <span class="muted">Zoom</span>
            <input type="range" id="cropZoom" min="1" max="3" step="0.05" value="1" oninput="onCropZoom()" style="flex:1;accent-color:var(--accent);">
            <button class="btn btn-soft" style="font-size:11px;padding:4px 8px;" onclick="cropReset()">Reset</button>
        </div>
        <div class="row" style="gap:8px;margin-top:12px;justify-content:space-between;">
            <span class="row" style="gap:6px;">
                <button class="btn btn-soft" style="font-size:11px;padding:5px 10px;" onclick="cropNav(-1)">‹</button>
                <button class="btn btn-soft" style="font-size:11px;padding:5px 10px;" onclick="cropNav(1)">›</button>
            </span>
            <button class="btn btn-primary" style="font-size:12px;" onclick="closeCrop()">Selesai</button>
        </div>
    </div>
</div>

<script src="{{ asset('ffmpeg/ffmpeg.js') }}"></script>
<script>
var FFMPEG_BASE = '{{ asset('ffmpeg') }}';
var CSRF = '{{ csrf_token() }}';
var IMG_DEL_BASE = '{{ url('admin/ai-agent/image') }}';
var ROUTE_TRANSCRIBE = '{{ route('admin.ai-agent.transcribe') }}';
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
var quality = 720;
var selTpl = 'impact';
var capFont = '', capColor = '';                       // '' = ikut template
var narSize = 0.05, gongSize = 0.09;                   // sizeFactor (×H)
var posN = {x:0.5, y:0.84}, posG = {x:0.5, y:0.5};     // posisi caption (normalized, bisa di-seret)
var vfFilter = 'none';                                 // efek warna (canvas filter)
var VFX = {
    '':          'none',
    cinematic:   'contrast(1.15) saturate(1.12) brightness(0.97)',
    warm:        'sepia(0.28) saturate(1.2) brightness(1.04)',
    cool:        'saturate(1.12) hue-rotate(-12deg) brightness(1.02) contrast(1.06)',
    vintage:     'sepia(0.45) contrast(0.95) brightness(1.06) saturate(0.85)',
    bw:          'grayscale(1) contrast(1.12)',
    vivid:       'saturate(1.45) contrast(1.1)'
};
function onEffectChange(){ vfFilter = VFX[document.getElementById('vfxSel').value] || 'none'; renderPreview(); }
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

function clamp(v,a,b){ return Math.min(b, Math.max(a, v)); }
function fmtTime(s){ s=Math.max(0,s||0); var m=Math.floor(s/60), x=Math.round(s%60); if(x===60){m++;x=0;} return m+':'+(x<10?'0':'')+x; }
var selDur = 0;
function onAudioSelected(){
    var info = document.getElementById('audInfo');
    if (info) info.textContent = selAudio ? ('🔊 ' + (selAudio.name||'Audio') + (selDur ? ' · ' + fmtTime(selDur) + ' (' + selDur.toFixed(1) + ' dtk)' : '')) : '';
    var th = document.getElementById('timingDur');
    if (th) th.textContent = selDur ? (' · durasi audio: ' + selDur.toFixed(1) + ' dtk') : '';
}
function setStatus(h){ document.getElementById('status').innerHTML = h || ''; }
function extFromType(t, fallback){ if(!t) return fallback; var m=t.split('/')[1]; return m ? m.replace('jpeg','jpg').split(';')[0] : fallback; }

// ===== Gambar (multi-select → scene berurutan) =====
function pickImage(el){
    var i = selImages.findIndex(function(s){ return s.el === el; });
    if (i >= 0){ selImages.splice(i,1); el.classList.remove('sel'); }
    else {
        var isUp = el.dataset.upload === '1';
        selImages.push(isUp ? {kind:'file', file:el._file, ext:el._ext||'jpg', el:el, crop:{z:1,ox:0.5,oy:0.5,fit:'cover'}}
                            : {kind:'url', src:el.dataset.src, ext:'jpg', el:el, crop:{z:1,ox:0.5,oy:0.5,fit:'cover'}});
        el.classList.add('sel');
    }
    renumberImages();
    updatePreviewImg();
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
        d.innerHTML = '<div style="flex:1;min-width:0;"><div class="aud-name">'+(c.name||'Audio').replace(/</g,'&lt;')+'</div><div class="aud-meta">.'+c.ext+' · '+kb+' KB <span class="aud-dur"></span></div></div>';
        var durSpan = d.querySelector('.aud-dur');
        probeDuration(c.blob).then(function(dd){ c._dur = dd; if (durSpan) durSpan.textContent = '· ' + fmtTime(dd); });
        d.addEventListener('click', async function(){
            document.querySelectorAll('.aud-item').forEach(function(x){ x.classList.remove('sel'); });
            d.classList.add('sel');
            selAudio = { kind:'idb', blob:c.blob, ext:c.ext||'wav', name:c.name };
            selDur = c._dur || await probeDuration(c.blob); c._dur = selDur;
            onAudioSelected();
        });
        list.appendChild(d);
    });
}
document.getElementById('audUpload').addEventListener('change', async function(){
    var f=this.files[0]; if(!f) return;
    document.querySelectorAll('.aud-item').forEach(function(x){ x.classList.remove('sel'); });
    selAudio = { kind:'file', blob:f, ext: extFromType(f.type,'mp3'), name:f.name };
    selDur = await probeDuration(f);
    onAudioSelected();
});
loadAudio();

// Preview caption: update saat ketik / ganti template / rasio
['capNarasi','capGong'].forEach(function(id){ var el=document.getElementById(id); if(el) el.addEventListener('input', renderPreview); });
window.addEventListener('load', renderPreview);
renderPreview();

// ===== Ratio =====
function onRatioChange(){ ratio = document.getElementById('ratioSel').value; renderPreview(); }
function onQualityChange(){ quality = parseInt(document.getElementById('qualitySel').value,10) || 720; }
function ratioDims(){
    var q = quality, e = function(n){ return Math.round(n/2)*2; };  // genap
    if (ratio === '16:9') return [e(q*16/9), q];
    if (ratio === '1:1')  return [q, q];
    return [q, e(q*16/9)];   // 9:16
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
function watermarkText(){ var e = document.getElementById('wmInput'); return e ? (e.value||'').trim() : ''; }
function drawWatermark(ctx, W, H){
    var t = watermarkText(); if (!t) return;
    var fp = Math.max(8, Math.round(H*0.013));
    ctx.font = '600 ' + fp + "px 'Poppins', sans-serif";
    ctx.textAlign = 'right'; ctx.textBaseline = 'bottom'; ctx.globalAlpha = 0.7; ctx.fillStyle = '#fff';
    ctx.shadowColor = 'rgba(0,0,0,0.55)'; ctx.shadowBlur = fp*0.4; ctx.shadowOffsetY = 1;
    ctx.fillText(t, W - fp*0.8, H - fp*0.7);
    ctx.shadowColor='transparent'; ctx.shadowBlur=0; ctx.shadowOffsetY=0; ctx.globalAlpha=1;
    ctx.textAlign='center'; ctx.textBaseline='middle';
}
function loadImageEl(src){
    return new Promise(function(res, rej){
        var im = new Image();
        im.onload = function(){ res(im); };
        im.onerror = function(){ rej(new Error('Gambar gagal dimuat.')); };
        im.src = src;
    });
}
// crop: {z(zoom>=1), ox, oy(fokus 0..1), fit:'cover'|'contain'}.
function coverDraw(ctx, im, W, H, crop){
    var iw = im.naturalWidth || im.width, ih = im.naturalHeight || im.height;
    var vf = (vfFilter && vfFilter !== 'none') ? vfFilter : '';
    if (crop && crop.fit === 'contain'){
        // latar = versi cover yang di-blur + digelapkan; gambar utuh di atasnya
        var sB = Math.max(W/iw, H/ih) * 1.1, dwB = iw*sB, dhB = ih*sB;
        ctx.filter = ('blur(' + Math.max(8, Math.round(W*0.04)) + 'px) brightness(0.55) ' + vf).trim();
        ctx.drawImage(im, (W-dwB)/2, (H-dhB)/2, dwB, dhB);
        var sC = Math.min(W/iw, H/ih), dwC = iw*sC, dhC = ih*sC;
        ctx.filter = vf || 'none';
        ctx.drawImage(im, (W-dwC)/2, (H-dhC)/2, dwC, dhC);
        ctx.filter = 'none';
        return;
    }
    var z = (crop && crop.z) ? crop.z : 1;
    var s = Math.max(W/iw, H/ih) * z, dw = iw*s, dh = ih*s;
    var ox = crop ? crop.ox : 0.5, oy = crop ? crop.oy : 0.5;
    var oxMin = (W/2)/dw, oyMin = (H/2)/dh;
    ox = clamp(ox, oxMin, 1-oxMin); oy = clamp(oy, oyMin, 1-oyMin);
    ctx.filter = vf || 'none';
    ctx.drawImage(im, W/2 - ox*dw, H/2 - oy*dh, dw, dh);
    ctx.filter = 'none';
}
// Bake caption LANGSUNG ke frame gambar → JPEG bytes. im2/blend = crossfade transisi.
async function frameJpeg(im, W, H, caps, tpl, im2, blend, crop, crop2){
    var cv = document.createElement('canvas'); cv.width = W; cv.height = H;
    var x = cv.getContext('2d');
    coverDraw(x, im, W, H, crop);
    if (im2){ x.globalAlpha = blend; coverDraw(x, im2, W, H, crop2); x.globalAlpha = 1; }
    for (var i=0;i<caps.length;i++){
        var c = caps[i]; if (!c.text) continue;
        var fam = c.family || tpl.family;
        try { await document.fonts.load('700 ' + Math.round(H*c.size) + "px '" + fam + "'"); } catch(e){}
        x.globalAlpha = (c.alpha != null) ? c.alpha : 1;
        drawCaptionOn(x, c.text, tpl, W, H, { size:c.size, cx:c.cx, cy:c.cy, family:c.family, color:c.color });
        x.globalAlpha = 1;
    }
    drawWatermark(x, W, H);
    return await new Promise(function(res){ cv.toBlob(function(b){ b.arrayBuffer().then(function(ab){ res(new Uint8Array(ab)); }); }, 'image/jpeg', 0.92); });
}

var previewSeq = 0, boxN = null, boxG = null, previewImg = null, previewKey = null;
async function updatePreviewImg(){
    if (!selImages.length){ previewImg = null; previewKey = null; renderPreview(); return; }
    var s = selImages[0];
    var key = s.kind === 'url' ? s.src : (s.el ? s.el.dataset.src : 'file');
    if (key === previewKey && previewImg){ renderPreview(); return; }
    previewKey = key;
    try {
        var bytes = await fetchBytes(s.kind === 'url' ? s.src : s.file);
        var u = URL.createObjectURL(new Blob([bytes]));
        previewImg = await loadImageEl(u); URL.revokeObjectURL(u);
    } catch(e){ previewImg = null; }
    renderPreview();
}
async function renderPreview(){
    var cv = document.getElementById('capPreview'); if (!cv) return;
    var seq = ++previewSeq;
    var dims = ratioDims(), AR = dims[0]/dims[1];
    var PH = 250, PW = Math.round(PH * AR);
    cv.width = PW; cv.height = PH;
    var x = cv.getContext('2d');
    if (previewImg){ coverDraw(x, previewImg, PW, PH, selImages[0] && selImages[0].crop); }
    else { var g = x.createLinearGradient(0,0,0,PH); g.addColorStop(0,'#3a4a57'); g.addColorStop(1,'#1b2630'); x.fillStyle = g; x.fillRect(0,0,PW,PH); }
    var tpl = CAP_TEMPLATES[selTpl] || CAP_TEMPLATES.impact;
    var nar = (document.getElementById('capNarasi').value||'').trim() || 'contoh narasi build-up';
    var gng = (document.getElementById('capGong').value||'').trim() || 'gong pamungkas';
    var fam = capFont || tpl.family;
    try { await document.fonts.load("700 40px '" + fam + "'"); await document.fonts.load(tpl.weight + " 40px '" + tpl.family + "'"); } catch(e){}
    if (seq !== previewSeq) return;
    boxN = drawCaptionOn(x, nar, tpl, PW, PH, { size:narSize,  cx:posN.x*PW, cy:posN.y*PH, family:capFont, color:capColor });
    boxG = drawCaptionOn(x, gng, tpl, PW, PH, { size:gongSize, cx:posG.x*PW, cy:posG.y*PH, family:capFont, color:capColor });
    drawWatermark(x, PW, PH);
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
var autoSubs = [];   // [{start,end,text}] hasil transkrip
async function transcribeAudio(blob){
    var fd = new FormData();
    fd.append('audio', blob, 'a.' + (selAudio.ext || 'mp3'));
    var r = await fetch(ROUTE_TRANSCRIBE, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: fd });
    var d = await r.json().catch(function(){ return {}; });
    if (!r.ok || d.error) throw new Error(d.error || ('HTTP ' + r.status));
    return (d.segments || []).map(function(s){ return { start: parseFloat(s.start)||0, end: parseFloat(s.end)||0, text: (s.text||'').trim() }; })
        .filter(function(s){ return s.text && s.end > s.start; });
}

function vcodec(c, still){ return c==='libx264' ? ['-c:v','libx264','-preset','ultrafast'].concat(still?['-tune','stillimage']:[], ['-pix_fmt','yuv420p']) : ['-c:v','mpeg4','-q:v','4']; }
function aTail(){ return ['-c:a','aac','-b:a','160k','-shortest','-movflags','+faststart','out.mp4']; }

async function renderToBlob(){
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
        var crops = selImages.map(function(s){ return s.crop || {z:1,ox:0.5,oy:0.5}; });
        await ffmpeg.writeFile(audName, await fetchBytes(selAudio.blob));

        var narasi = (document.getElementById('capNarasi').value || '').trim();
        var gong   = (document.getElementById('capGong').value || '').trim();
        var tpl = CAP_TEMPLATES[selTpl] || CAP_TEMPLATES.impact;
        var hasN = narasi !== '', hasG = gong !== '';
        var dur = await probeDuration(selAudio.blob);
        var N = ims.length;

        // Auto subtitle (kalau caption kosong & dicentang)
        autoSubs = [];
        if (!hasN && !hasG && document.getElementById('autoSub').checked){
            setStatus('<span class="spinner"></span> Membuat subtitle otomatis dari audio…');
            try { autoSubs = await transcribeAudio(selAudio.blob); }
            catch(e){ console.warn('transcribe:', e); setStatus('⚠️ Subtitle gagal: ' + (e.message||e) + ' — lanjut tanpa subtitle.'); }
        }

        function narCap(){ return {text:narasi, cx:posN.x*W, cy:posN.y*H, size:narSize, family:capFont, color:capColor}; }
        function gongCap(sc, al){ return {text:gong, cx:posG.x*W, cy:posG.y*H, size:gongSize*(sc||1), alpha:al, family:capFont, color:capColor}; }

        // ===== Timing caption (manual detik, kosong = otomatis) =====
        if (!dur || dur < 1) dur = N>1 ? N*2 : 6;
        function pf(id){ var v = parseFloat(document.getElementById(id).value); return isNaN(v) ? null : v; }
        var autoGong = Math.min(2.6, Math.max(1.0, dur*0.3));
        var gS = pf('gongStart'); if (gS===null) gS = Math.max(0, dur - autoGong);
        var gE = pf('gongEnd');   if (gE===null) gE = dur;
        var nS = pf('narStart');  if (nS===null) nS = 0;
        var nE = pf('narEnd');    if (nE===null) nE = gS;
        nS=clamp(nS,0,dur); nE=clamp(nE,nS,dur); gS=clamp(gS,0,dur); gE=clamp(gE,gS,dur);
        function capsAt(t){
            var c=[]; if(hasN && t>=nS && t<nE) c.push(narCap()); if(hasG && t>=gS && t<gE) c.push(gongCap(1,1));
            if (autoSubs.length){ for (var sj=0; sj<autoSubs.length; sj++){ if (t>=autoSubs[sj].start && t<autoSubs[sj].end){ c.push({text:autoSubs[sj].text, cx:posN.x*W, cy:posN.y*H, size:narSize, family:capFont, color:capColor}); break; } } }
            return c;
        }

        // ===== Bangun blok gambar (scene + crossfade) lalu pecah di batas timing caption =====
        var segments = [];
        if (N === 1 && !hasN && !hasG && !autoSubs.length){
            segments = [{ im:ims[0], crop:crops[0], caps:[], dur:0 }];   // diam, loop + shortest
        } else {
            var capEdges = [nS,nE,gS,gE]; autoSubs.forEach(function(s){ capEdges.push(s.start); capEdges.push(s.end); });
            var blocks = [];
            if (N === 1){
                blocks = [{ im:ims[0], crop:crops[0], dur:dur, t0:0 }];
            } else {
                var trans = document.getElementById('transSel').value, segLen = dur / N;
                var tDur = (trans === 'fade' && N >= 2) ? Math.min(0.5, segLen*0.5) : 0, nF = 5, t = 0;
                for (var i=0; i<N; i++){
                    var isLast = (i === N-1), hold = isLast ? segLen : Math.max(0.25, segLen - tDur);
                    blocks.push({ im:ims[i], crop:crops[i], dur:hold, t0:t }); t += hold;
                    if (!isLast && tDur > 0){
                        for (var f=1; f<=nF; f++){ blocks.push({ im:ims[i], crop:crops[i], im2:ims[i+1], crop2:crops[i+1], blend:f/(nF+1), dur:tDur/nF, t0:t }); t += tDur/nF; }
                    }
                }
            }
            blocks.forEach(function(b){
                if (b.im2){ b.caps = capsAt(b.t0 + b.dur/2); segments.push(b); return; }   // crossfade: utuh
                var edges = [b.t0, b.t0 + b.dur];
                capEdges.forEach(function(e){ if (e > b.t0 + 0.02 && e < b.t0 + b.dur - 0.02) edges.push(e); });
                edges.sort(function(a,b2){ return a-b2; });
                var ue = []; edges.forEach(function(e){ if (!ue.length || e - ue[ue.length-1] > 0.02) ue.push(e); });
                for (var s=0; s<ue.length-1; s++){
                    var a = ue[s], bb = ue[s+1], len = bb - a, mid = (a+bb)/2;
                    var curN = hasN && mid>=nS && mid<nE, curG = hasG && mid>=gS && mid<gE;
                    var caps = []; if (curN) caps.push(narCap());
                    if (curG && Math.abs(a - gS) < 0.05 && len > 0.3){   // onset gong → animasi pop
                        var fade = [{a:0.4,s:0.82,d:0.07},{a:0.7,s:0.97,d:0.07},{a:1.0,s:1.08,d:0.07}];
                        fade.forEach(function(fp){ var c = caps.slice(); c.push(gongCap(fp.s, fp.a)); segments.push({ im:b.im, crop:b.crop, caps:c, dur:fp.d }); });
                        var rest = len - 0.21; if (rest > 0.03){ var c2 = caps.slice(); c2.push(gongCap(1,1)); segments.push({ im:b.im, crop:b.crop, caps:c2, dur:rest }); }
                    } else {
                        if (curG) caps.push(gongCap(1,1));
                        segments.push({ im:b.im, crop:b.crop, caps:caps, dur:len });
                    }
                }
            });
            if (!segments.length) segments.push({ im:ims[0], crop:crops[0], caps:[], dur:dur });
        }

        // ===== Tulis frame per segmen =====
        setStatus('<span class="spinner"></span> Menyiapkan frame…');
        var written = [];
        for (var k=0; k<segments.length; k++){
            var fn = 'v'+k+'.jpg';
            await ffmpeg.writeFile(fn, await frameJpeg(segments[k].im, W, H, segments[k].caps, tpl, segments[k].im2, segments[k].blend, segments[k].crop, segments[k].crop2));
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
        return new Blob([data.buffer], { type:'video/mp4' });
    } finally {
        tmpUrls.forEach(function(u){ URL.revokeObjectURL(u); });
    }
}

async function doRender(){
    if (!selImages.length){ alert('Pilih minimal 1 gambar.'); return; }
    if (!selAudio){ alert('Pilih audio dulu.'); return; }
    if (busy) return;
    var btn = document.getElementById('renderBtn'); busy = true; btn.disabled = true;
    try {
        var blob = await renderToBlob();
        if (lastUrl) URL.revokeObjectURL(lastUrl);
        lastBlob = blob; lastUrl = URL.createObjectURL(blob);
        document.getElementById('resultVideo').src = lastUrl;
        var dl = document.getElementById('dlBtn'); dl.href = lastUrl;
        dl.download = 'maftune_' + ratio.replace(':','x') + '_' + Date.now() + '.mp4';
        document.getElementById('resultMeta').textContent = ratio + ' · ' + selImages.length + ' scene · ' + Math.round(blob.size/1024) + ' KB';
        document.getElementById('resultWrap').style.display = 'block';
        setStatus('✓ Video jadi! Unduh lalu upload ke TikTok / IG / YouTube.');
    } catch(e){ console.error('render error:', e); setStatus('⚠️ ' + ((e && e.message) || e)); }
    finally { busy = false; btn.disabled = false; }
}

var batchItems = [];
async function doBatch(){
    if (!batchItems.length) return;
    if (!selAudio){ alert('Pilih audio dulu (dipakai untuk semua video).'); return; }
    if (busy) return;
    if (!confirm('Render ' + batchItems.length + ' video sekaligus? Bisa makan waktu.')) return;
    var bb = document.getElementById('batchBtn'); busy = true; bb.disabled = true;
    var ok = 0;
    try {
        for (var i=0; i<batchItems.length; i++){
            var it = batchItems[i];
            document.getElementById('capNarasi').value = it.narasi || '';
            document.getElementById('capGong').value = it.gong || '';
            selImages = [];
            document.querySelectorAll('.img-pick').forEach(function(x){ x.classList.remove('sel'); x.removeAttribute('data-ord'); });
            if (it.image){ var found=null; document.querySelectorAll('.img-pick').forEach(function(x){ if(x.dataset.src===it.image) found=x; }); if(found) pickImage(found); }
            if (!selImages.length){ continue; }
            setStatus('<span class="spinner"></span> Batch ' + (i+1) + '/' + batchItems.length + '…');
            var blob = await renderToBlob();
            await vidAdd({ name:'Batch '+(i+1)+' · '+((it.gong||it.narasi||'video').slice(0,28)), ratio:ratio, blob:blob, size:blob.size, createdAt:Date.now() });
            ok++;
        }
        setStatus('✓ ' + ok + ' video tersimpan di "Video Tersimpan".');
        renderVideoList();
    } catch(e){ console.error(e); setStatus('⚠️ Batch berhenti: ' + ((e&&e.message)||e) + ' (' + ok + ' berhasil)'); }
    finally { busy = false; bb.disabled = false; }
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

// ===== Crop gambar (per-gambar: geser + zoom) =====
var cropIdx = 0, cropImg = null;
function openCrop(){
    if (!selImages.length){ alert('Pilih gambar dulu (klik thumbnail).'); return; }
    cropIdx = 0; document.getElementById('cropModal').classList.add('open'); cropLoad();
}
function closeCrop(){ document.getElementById('cropModal').classList.remove('open'); renderPreview(); }
function cropNav(d){ cropIdx = (cropIdx + d + selImages.length) % selImages.length; cropLoad(); }
function cropDims(){ var dm = ratioDims(), AR = dm[0]/dm[1], W = 300, H = Math.round(W/AR); return [W,H]; }
async function cropLoad(){
    var s = selImages[cropIdx]; if (!s) return;
    if (!s.crop) s.crop = {z:1,ox:0.5,oy:0.5,fit:'cover'};
    if (!s.crop.fit) s.crop.fit = 'cover';
    document.getElementById('cropIdx').textContent = (cropIdx+1) + '/' + selImages.length;
    document.getElementById('cropZoom').value = s.crop.z;
    document.querySelectorAll('#cropFitOpt .ratio-btn').forEach(function(b){ b.classList.toggle('sel', b.dataset.f === s.crop.fit); });
    document.getElementById('cropHint').textContent = s.crop.fit === 'contain' ? 'Gambar tampil utuh, latar otomatis blur — geser/zoom dinonaktifkan.' : 'Seret untuk geser · slider untuk zoom';
    try { var bytes = await fetchBytes(s.kind==='url'?s.src:s.file); var u = URL.createObjectURL(new Blob([bytes])); cropImg = await loadImageEl(u); URL.revokeObjectURL(u); }
    catch(e){ cropImg = null; }
    cropDraw();
}
function cropFit(el){
    if (!selImages[cropIdx]) return;
    document.querySelectorAll('#cropFitOpt .ratio-btn').forEach(function(b){ b.classList.remove('sel'); });
    el.classList.add('sel');
    selImages[cropIdx].crop.fit = el.dataset.f;
    document.getElementById('cropHint').textContent = el.dataset.f === 'contain' ? 'Gambar tampil utuh, latar otomatis blur — geser/zoom dinonaktifkan.' : 'Seret untuk geser · slider untuk zoom';
    cropDraw();
}
function cropDraw(){
    var cv = document.getElementById('cropCanvas'); var dm = cropDims(); cv.width = dm[0]; cv.height = dm[1];
    var x = cv.getContext('2d'); x.fillStyle='#000'; x.fillRect(0,0,cv.width,cv.height);
    if (cropImg) coverDraw(x, cropImg, cv.width, cv.height, selImages[cropIdx] && selImages[cropIdx].crop);
}
function onCropZoom(){ if(selImages[cropIdx] && selImages[cropIdx].crop.fit!=='contain'){ selImages[cropIdx].crop.z = parseFloat(document.getElementById('cropZoom').value); cropDraw(); } }
function cropReset(){ if(selImages[cropIdx]){ var f=selImages[cropIdx].crop.fit||'cover'; selImages[cropIdx].crop = {z:1,ox:0.5,oy:0.5,fit:f}; document.getElementById('cropZoom').value=1; cropDraw(); } }
(function(){
    var cv = document.getElementById('cropCanvas'); if (!cv) return;
    var dr=false, lx=0, ly=0;
    function tc(ev){ var r=cv.getBoundingClientRect(); return {x:(ev.clientX-r.left)*(cv.width/r.width), y:(ev.clientY-r.top)*(cv.height/r.height)}; }
    cv.addEventListener('pointerdown', function(ev){ if(!cropImg) return; dr=true; var p=tc(ev); lx=p.x; ly=p.y; try{cv.setPointerCapture(ev.pointerId);}catch(e){} cv.style.cursor='grabbing'; ev.preventDefault(); });
    cv.addEventListener('pointermove', function(ev){
        if (!dr || !cropImg) return;
        var crop = selImages[cropIdx].crop; if (crop.fit === 'contain') return;
        var p=tc(ev), dx=p.x-lx, dy=p.y-ly; lx=p.x; ly=p.y;
        var iw=cropImg.naturalWidth, ih=cropImg.naturalHeight;
        var s = Math.max(cv.width/iw, cv.height/ih)*crop.z, dw=iw*s, dh=ih*s;
        crop.ox = clamp(crop.ox - dx/dw, 0, 1); crop.oy = clamp(crop.oy - dy/dh, 0, 1);
        cropDraw();
    });
    function end(){ dr=false; cv.style.cursor='grab'; }
    cv.addEventListener('pointerup', end); cv.addEventListener('pointercancel', end);
})();

// ===== Proyek (simpan/muat konfigurasi) — IndexedDB 'mafProjects' =====
function getConfig(){
    return {
        narasi: document.getElementById('capNarasi').value, gong: document.getElementById('capGong').value,
        font: document.getElementById('capFontSel').value, color: document.getElementById('capColorSel').value,
        narN: document.getElementById('narSizeRange').value, gongN: document.getElementById('gongSizeRange').value,
        tpl: selTpl, posN: {x:posN.x,y:posN.y}, posG: {x:posG.x,y:posG.y},
        vfx: document.getElementById('vfxSel').value, trans: document.getElementById('transSel').value,
        narStart: document.getElementById('narStart').value, narEnd: document.getElementById('narEnd').value,
        gongStart: document.getElementById('gongStart').value, gongEnd: document.getElementById('gongEnd').value,
        ratio: document.getElementById('ratioSel').value, quality: document.getElementById('qualitySel').value,
        watermark: document.getElementById('wmInput').value,
        imgs: selImages.filter(function(s){ return s.kind==='url'; }).map(function(s){ return {src:s.src, crop:s.crop||null}; }),
        audio: selAudio ? selAudio.name : null
    };
}
function applyConfig(c){
    document.getElementById('capNarasi').value = c.narasi||'';
    document.getElementById('capGong').value = c.gong||'';
    document.getElementById('narSizeRange').value = c.narN||'5'; document.getElementById('gongSizeRange').value = c.gongN||'9'; onSizeChange();
    document.getElementById('vfxSel').value = c.vfx||''; onEffectChange();
    document.getElementById('transSel').value = c.trans||'none';
    ['narStart','narEnd','gongStart','gongEnd'].forEach(function(id){ document.getElementById(id).value = c[id]||''; });
    document.getElementById('ratioSel').value = c.ratio||'9:16'; ratio = c.ratio||'9:16';
    document.getElementById('qualitySel').value = c.quality||'720'; quality = parseInt(c.quality||'720',10);
    document.getElementById('wmInput').value = (c.watermark != null) ? c.watermark : 'EMINOR.my.id';
    var tb = document.querySelector('#tplOpt .ratio-btn[data-t="'+(c.tpl||'impact')+'"]'); if (tb) pickTpl(tb);
    document.getElementById('capFontSel').value = c.font||''; capFont = c.font||'';
    if (c.color){ document.getElementById('capColorSel').value = c.color; capColor = c.color; }
    if (c.posN) posN = {x:c.posN.x, y:c.posN.y}; if (c.posG) posG = {x:c.posG.x, y:c.posG.y};
    // pilih ulang gambar (cocokkan src) — hanya yg dari stok
    selImages = [];
    document.querySelectorAll('.img-pick').forEach(function(x){ x.classList.remove('sel'); x.removeAttribute('data-ord'); });
    (c.imgs||[]).forEach(function(it){
        var src = (typeof it==='string') ? it : it.src, found=null;
        document.querySelectorAll('.img-pick').forEach(function(x){ if(x.dataset.src===src) found=x; });
        if (found){ pickImage(found); var ent=selImages[selImages.length-1]; if(ent && it && it.crop) ent.crop=it.crop; }
    });
    // pilih ulang audio (cocokkan nama)
    if (c.audio){ document.querySelectorAll('.aud-item').forEach(function(d){ var nm=d.querySelector('.aud-name'); if(nm && nm.textContent===c.audio) d.click(); }); }
    renderPreview();
}
function projOpen(){ return new Promise(function(res,rej){ var r=indexedDB.open('mafProjects',1); r.onupgradeneeded=function(){ r.result.createObjectStore('proj',{keyPath:'id',autoIncrement:true}); }; r.onsuccess=function(){res(r.result);}; r.onerror=function(){rej(r.error);}; }); }
async function projAll(){ var db=await projOpen(); return new Promise(function(res){ var t=db.transaction('proj').objectStore('proj').getAll(); t.onsuccess=function(){res(t.result||[]);}; t.onerror=function(){res([]);}; }); }
async function projAdd(rec){ var db=await projOpen(); return new Promise(function(res){ db.transaction('proj','readwrite').objectStore('proj').add(rec).onsuccess=function(){res();}; }); }
async function projDel(id){ var db=await projOpen(); return new Promise(function(res){ db.transaction('proj','readwrite').objectStore('proj').delete(id).onsuccess=function(){res();}; }); }
async function saveProject(){
    var name = prompt('Nama proyek:', 'Proyek ' + new Date().toLocaleString('id'));
    if (name === null) return;
    await projAdd({ name:(name||'Proyek').trim(), config:getConfig(), createdAt:Date.now() });
    setStatus('✓ Proyek tersimpan.'); renderProjects();
}
async function renderProjects(){
    var list=document.getElementById('projList'); if(!list) return;
    var all=await projAll();
    if(!all.length){ list.innerHTML='<div class="empty-row">Belum ada proyek tersimpan.</div>'; return; }
    list.innerHTML='';
    all.sort(function(a,b){ return b.createdAt-a.createdAt; }).forEach(function(p){
        var d=document.createElement('div'); d.className='aud-item';
        d.innerHTML='<div style="flex:1;min-width:0;"><div class="aud-name">'+(p.name||'Proyek').replace(/</g,'&lt;')+'</div>'+
            '<div class="aud-meta">'+((p.config&&p.config.imgs)?p.config.imgs.length:0)+' gambar · '+((p.config&&p.config.ratio)||'9:16')+'</div></div>'+
            '<button class="btn btn-accent" data-load="'+p.id+'" style="font-size:11px;padding:5px 11px;">Muat</button>'+
            '<button class="btn-del" data-del="'+p.id+'">Hapus</button>';
        d.querySelector('[data-load]').addEventListener('click', function(){ applyConfig(p.config||{}); });
        d.querySelector('[data-del]').addEventListener('click', async function(){ if(!confirm('Hapus proyek?'))return; await projDel(p.id); renderProjects(); });
        list.appendChild(d);
    });
}
renderProjects();

// ===== Prefill dari AI Agent (sessionStorage 'vb_prefill') =====
(function(){
    var raw; try { raw = sessionStorage.getItem('vb_prefill'); } catch(e){}
    if (!raw) return;
    try { sessionStorage.removeItem('vb_prefill'); } catch(e){}
    var p; try { p = JSON.parse(raw); } catch(e){ return; }
    if (p.narasi != null) document.getElementById('capNarasi').value = p.narasi;
    if (p.gong != null) document.getElementById('capGong').value = p.gong;
    if (p.image){
        var found = null;
        document.querySelectorAll('.img-pick').forEach(function(x){ if (x.dataset.src === p.image) found = x; });
        if (found) pickImage(found);
    }
    renderPreview();
    setStatus('✓ Narasi/gong dari AI Agent dimuat. Pilih audio lalu Rakit.');
})();

// ===== Batch dari AI Agent (sessionStorage 'vb_batch') =====
(function(){
    var raw; try { raw = sessionStorage.getItem('vb_batch'); } catch(e){}
    if (!raw) return;
    try { sessionStorage.removeItem('vb_batch'); } catch(e){}
    var arr; try { arr = JSON.parse(raw); } catch(e){ return; }
    if (!Array.isArray(arr) || !arr.length) return;
    batchItems = arr;
    var bn = document.getElementById('batchBanner');
    bn.style.display = 'block';
    bn.textContent = '🎬 Batch: ' + arr.length + ' video dari AI Agent. Pilih audio + atur template/efek/watermark, lalu klik "Rakit semua".';
    document.getElementById('batchBtn').style.display = 'inline-block';
    // tampilkan item pertama sebagai preview
    document.getElementById('capNarasi').value = arr[0].narasi || '';
    document.getElementById('capGong').value = arr[0].gong || '';
    if (arr[0].image){ var found=null; document.querySelectorAll('.img-pick').forEach(function(x){ if(x.dataset.src===arr[0].image) found=x; }); if(found) pickImage(found); }
    renderPreview();
})();
</script>

@endsection
