@extends('layouts.admin')

@push('styles')
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

{{-- 3. PENGATURAN + RENDER --}}
<div class="card">
    <div class="card-head"><span>3 · Format &amp; Rakit</span></div>
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
var ffmpeg = null, ffmpegLoaded = false, busy = false, lastUrl = null;

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

// ===== Ratio =====
function pickRatio(el){
    document.querySelectorAll('.ratio-btn').forEach(function(x){ x.classList.remove('sel'); });
    el.classList.add('sel'); ratio = el.dataset.r;
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

async function doRender(){
    if (!selImage){ alert('Pilih gambar dulu.'); return; }
    if (!selAudio){ alert('Pilih audio dulu.'); return; }
    if (busy) return;
    var btn = document.getElementById('renderBtn');
    busy = true; btn.disabled = true;
    try {
        await ensureFfmpeg();
        var dims = ratioDims(), W = dims[0], H = dims[1];
        var imgName = 'img.' + (selImage.ext || 'jpg');
        var audName = 'aud.' + (selAudio.ext || 'mp3');

        setStatus('<span class="spinner"></span> Memuat aset…');
        var imgBytes = await fetchBytes(selImage.kind === 'url' ? selImage.src : selImage.file);
        await ffmpeg.writeFile(imgName, imgBytes);
        await ffmpeg.writeFile(audName, await fetchBytes(selAudio.blob));

        var vf = 'scale=' + W + ':' + H + ':force_original_aspect_ratio=decrease,pad=' + W + ':' + H + ':(ow-iw)/2:(oh-ih)/2:black,setsar=1,format=yuv420p';
        setStatus('<span class="spinner"></span> Merender video…');

        // coba H.264; bila gagal, fallback ke mpeg4
        var rc = await ffmpeg.exec(['-loop','1','-i',imgName,'-i',audName,
            '-vf',vf,'-c:v','libx264','-preset','ultrafast','-tune','stillimage',
            '-r','25','-c:a','aac','-b:a','160k','-shortest','-movflags','+faststart','out.mp4']);
        if (rc !== 0) {
            setStatus('<span class="spinner"></span> Merender (mode kompatibel)…');
            try { ffmpeg.deleteFile('out.mp4'); } catch(e){}
            rc = await ffmpeg.exec(['-loop','1','-i',imgName,'-i',audName,
                '-vf',vf,'-c:v','mpeg4','-q:v','4','-r','25','-c:a','aac','-b:a','160k','-shortest','out.mp4']);
        }
        if (rc !== 0) throw new Error('Render gagal (kode ' + rc + '). Coba audio/gambar lain.');

        var data = await ffmpeg.readFile('out.mp4');
        try { ffmpeg.deleteFile(imgName); ffmpeg.deleteFile(audName); ffmpeg.deleteFile('out.mp4'); } catch(e){}

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
        busy = false; btn.disabled = false;
    }
}
</script>

@endsection
