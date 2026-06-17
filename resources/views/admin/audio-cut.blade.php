@extends('layouts.admin')

@push('styles')
<style>
    .ai-header { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border); }
    .ai-header h2 { font-size:1rem; font-weight:500; color:var(--text); }
    .ai-header p { font-size:12px; color:var(--text-3); margin-top:2px; }
    .btn-back { font-size:12px; color:var(--text-2); text-decoration:none; border:1px solid var(--border); padding:6px 14px; border-radius:8px; }
    .btn-back:hover { color:var(--text); border-color:var(--text-3); }

    .card { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; }
    .card-head { padding:0.9rem 1.1rem; border-bottom:1px solid var(--border); font-size:12px; color:var(--text-2); font-weight:600; letter-spacing:0.04em; }
    .card-body { padding:1.1rem; }

    .fg { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
    .fg label { font-size:11px; color:var(--text-3); text-transform:uppercase; letter-spacing:0.05em; }
    .fi { background:var(--bg-3); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px; padding:9px 11px; outline:none; font-family:inherit; width:100%; }
    .fi:focus { border-color:var(--text-3); }
    .btn { padding:9px 16px; border-radius:8px; font-size:13px; font-weight:500; border:none; cursor:pointer; transition:0.2s; }
    .btn-primary { background:var(--text); color:var(--bg); }
    .btn-primary:hover { filter:brightness(0.88); }
    .btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
    .btn-soft { background:var(--bg-3); border:1px solid var(--border); color:var(--text-2); }
    .btn-soft:hover { border-color:var(--text-3); color:var(--text); }
    .btn-accent { background:var(--accent-dim); color:var(--accent); }

    .row { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
    .muted { font-size:11px; color:var(--text-3); }

    /* Region selector */
    .region-track { position:relative; height:46px; background:var(--bg-3); border:1px solid var(--border); border-radius:8px; margin:14px 0 6px; overflow:hidden; }
    .region-sel { position:absolute; top:0; bottom:0; background:rgba(99,102,241,0.25); border-left:2px solid var(--accent); border-right:2px solid var(--accent); }
    .region-play { position:absolute; top:0; bottom:0; width:2px; background:#fff; opacity:0.85; left:0; }
    .region-time { display:flex; justify-content:space-between; font-size:11px; color:var(--text-3); font-variant-numeric:tabular-nums; }
    .range-pair { display:flex; flex-direction:column; gap:6px; margin-top:10px; }
    .range-pair input[type=range] { width:100%; accent-color:var(--accent); }
    .seg-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:8px; }
    @media(max-width:600px){ .seg-grid{ grid-template-columns:1fr; } }

    .clip-item { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--border-2); flex-wrap:wrap; }
    .clip-item:last-child { border-bottom:none; }
    .clip-name { font-size:13px; color:var(--text); font-weight:500; }
    .clip-meta { font-size:11px; color:var(--text-3); }
    .btn-del { background:transparent; border:1px solid var(--border); color:var(--text-3); border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer; }
    .btn-del:hover { border-color:#ef4444; color:#ef4444; }

    .status { font-size:12px; color:var(--text-3); margin-top:10px; min-height:18px; }
    .spinner { display:inline-block; width:13px; height:13px; border:2px solid var(--text-3); border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }
    audio { width:100%; margin-top:8px; }
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

{{-- ===== SUMBER ===== --}}
<div class="card">
    <div class="card-head">1 · Pilih Sumber Lagu</div>
    <div class="card-body">
        <div class="seg-grid">
            <div class="fg">
                <label>Dari pustaka lagu</label>
                <select class="fi" id="songSelect">
                    <option value="">— Pilih lagu —</option>
                    @foreach($songs as $song)
                    <option value="{{ asset($song->audio_file) }}" data-title="{{ $song->title }}">{{ $song->title }}@if($song->era) · {{ $song->era }}@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label>Atau unggah MP3 dari perangkat</label>
                <input type="file" class="fi" id="fileInput" accept="audio/*">
            </div>
        </div>
        <div class="muted" id="srcInfo">Belum ada lagu dimuat.</div>
    </div>
</div>

{{-- ===== EDITOR ===== --}}
<div class="card" id="editorCard" style="display:none;">
    <div class="card-head">2 · Pilih Bagian</div>
    <div class="card-body">
        <audio id="player" controls preload="metadata"></audio>

        <div class="region-track" id="regionTrack">
            <div class="region-sel" id="regionSel"></div>
            <div class="region-play" id="regionPlay"></div>
        </div>
        <div class="region-time">
            <span>0:00</span>
            <span id="durLabel">0:00</span>
        </div>

        <div class="range-pair">
            <label class="muted">Awal: <b id="startLabel">0:00</b></label>
            <input type="range" id="startRange" min="0" max="100" step="0.1" value="0">
            <label class="muted">Akhir: <b id="endLabel">0:00</b></label>
            <input type="range" id="endRange" min="0" max="100" step="0.1" value="100">
        </div>

        <div class="row" style="margin-top:12px;">
            <button class="btn btn-soft" onclick="setEdge('start')">⏮️ Awal = posisi player</button>
            <button class="btn btn-soft" onclick="setEdge('end')">Akhir = posisi player ⏭️</button>
            <button class="btn btn-soft" id="previewBtn" onclick="previewRegion()">▶️ Preview bagian</button>
            <span class="muted" id="segLabel" style="align-self:center;"></span>
        </div>
    </div>
</div>

{{-- ===== POTONG ===== --}}
<div class="card" id="cutCard" style="display:none;">
    <div class="card-head">3 · Potong</div>
    <div class="card-body">
        <p class="muted" style="margin-bottom:10px;">
            Pertama kali, editor mengunduh mesin pemotong (ffmpeg ± 30MB, sekali saja lalu tersimpan di cache browser). Proses potong berjalan 100% di perangkatmu.
        </p>
        <div class="row">
            <button class="btn btn-primary" id="loadFfmpegBtn" onclick="loadFfmpeg()">⚙️ Muat mesin pemotong</button>
            <button class="btn btn-primary" id="cutBtn" onclick="doCut()" style="display:none;" disabled>✂️ Potong bagian ini</button>
        </div>
        <div class="status" id="status"></div>

        <div id="resultWrap" style="display:none;margin-top:14px;">
            <div class="card-head" style="border:none;padding:0 0 6px;">Hasil potongan</div>
            <audio id="clipPlayer" controls></audio>
            <div class="row" style="margin-top:10px;">
                <input type="text" class="fi" id="clipName" style="flex:1;min-width:160px;" placeholder="Nama potongan (mis. Lagu X - verse)">
                <a class="btn btn-accent" id="downloadBtn" download>⬇️ Unduh</a>
                <button class="btn btn-soft" onclick="saveClip()">💾 Simpan di perangkat</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== POTONGAN TERSIMPAN ===== --}}
<div class="card">
    <div class="card-head">📁 Potongan Tersimpan (cache perangkat)</div>
    <div class="card-body">
        <p class="muted" style="margin-bottom:10px;">Tersimpan di browser ini saja (IndexedDB), tidak di server. Dipakai nanti untuk membuat video (Fase C).</p>
        <div id="clipList"><p class="muted">Belum ada potongan tersimpan.</p></div>
    </div>
</div>

<script src="https://unpkg.com/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js"></script>
<script>
// Helper pengganti @ffmpeg/util (hindari ketergantungan path CDN)
async function toBlobURL(url, mime){
    var buf = await (await fetch(url)).arrayBuffer();
    return URL.createObjectURL(new Blob([buf], { type: mime }));
}
async function fetchFile(input){
    var data;
    if (typeof input === 'string') data = await (await fetch(input)).arrayBuffer();
    else if (input instanceof Blob) data = await input.arrayBuffer();
    else data = input;
    return new Uint8Array(data);
}
</script>
<script>
// ====== State ======
var player = document.getElementById('player');
var srcUrl = null;       // URL audio (object URL atau asset)
var srcExt = 'mp3';
var srcName = 'lagu';
var duration = 0;
var ffmpeg = null;
var ffmpegLoaded = false;
var lastClipBlob = null;

function fmt(s){ s = Math.max(0, s||0); var m = Math.floor(s/60), x = Math.floor(s%60); return m + ':' + (x<10?'0':'') + x; }
function getExt(name){ var m = (name||'').match(/\.([a-z0-9]+)(?:\?|$)/i); return m ? m[1].toLowerCase() : 'mp3'; }

// ====== Muat sumber ======
document.getElementById('songSelect').addEventListener('change', function(){
    if (!this.value) return;
    var opt = this.options[this.selectedIndex];
    document.getElementById('fileInput').value = '';
    loadSource(this.value, opt.getAttribute('data-title') || 'lagu', getExt(this.value));
});
document.getElementById('fileInput').addEventListener('change', function(){
    var f = this.files[0]; if (!f) return;
    document.getElementById('songSelect').value = '';
    loadSource(URL.createObjectURL(f), f.name.replace(/\.[^.]+$/, ''), getExt(f.name));
});

function loadSource(url, name, ext){
    srcUrl = url; srcName = name; srcExt = ext || 'mp3';
    player.src = url;
    document.getElementById('srcInfo').textContent = '🎵 ' + name + ' (.' + srcExt + ') — memuat…';
    document.getElementById('editorCard').style.display = 'block';
    document.getElementById('cutCard').style.display = 'block';
    document.getElementById('cutBtn').style.display = ffmpegLoaded ? 'inline-block' : 'none';
}

player.addEventListener('loadedmetadata', function(){
    duration = player.duration || 0;
    document.getElementById('durLabel').textContent = fmt(duration);
    document.getElementById('srcInfo').textContent = '🎵 ' + srcName + ' (.' + srcExt + ') · durasi ' + fmt(duration);
    var sr = document.getElementById('startRange'), er = document.getElementById('endRange');
    sr.max = er.max = duration.toFixed(1);
    sr.value = 0; er.value = duration.toFixed(1);
    updateRegion();
});

// ====== Region ======
var startRange = document.getElementById('startRange');
var endRange = document.getElementById('endRange');
startRange.addEventListener('input', function(){
    if (parseFloat(startRange.value) > parseFloat(endRange.value) - 0.2) startRange.value = (parseFloat(endRange.value) - 0.2).toFixed(1);
    updateRegion();
});
endRange.addEventListener('input', function(){
    if (parseFloat(endRange.value) < parseFloat(startRange.value) + 0.2) endRange.value = (parseFloat(startRange.value) + 0.2).toFixed(1);
    updateRegion();
});

function getStart(){ return parseFloat(startRange.value) || 0; }
function getEnd(){ return parseFloat(endRange.value) || 0; }

function updateRegion(){
    var s = getStart(), e = getEnd();
    document.getElementById('startLabel').textContent = fmt(s);
    document.getElementById('endLabel').textContent = fmt(e);
    document.getElementById('segLabel').textContent = '⏱️ durasi bagian: ' + fmt(e - s);
    var sel = document.getElementById('regionSel');
    if (duration > 0){
        sel.style.left = (s/duration*100) + '%';
        sel.style.width = ((e-s)/duration*100) + '%';
    }
}

function setEdge(which){
    if (!duration) return;
    var t = player.currentTime || 0;
    if (which === 'start') startRange.value = Math.min(t, getEnd()-0.2).toFixed(1);
    else endRange.value = Math.max(t, getStart()+0.2).toFixed(1);
    updateRegion();
}

// klik track utk seek
document.getElementById('regionTrack').addEventListener('click', function(ev){
    if (!duration) return;
    var rect = this.getBoundingClientRect();
    player.currentTime = (ev.clientX - rect.left) / rect.width * duration;
});

player.addEventListener('timeupdate', function(){
    if (duration > 0) document.getElementById('regionPlay').style.left = (player.currentTime/duration*100) + '%';
});

// ====== Preview region ======
var previewStop = null;
function previewRegion(){
    if (!duration) return;
    var e = getEnd();
    player.currentTime = getStart();
    player.play();
    if (previewStop) player.removeEventListener('timeupdate', previewStop);
    previewStop = function(){ if (player.currentTime >= e){ player.pause(); player.removeEventListener('timeupdate', previewStop); previewStop = null; } };
    player.addEventListener('timeupdate', previewStop);
}

// ====== ffmpeg ======
function setStatus(html){ document.getElementById('status').innerHTML = html; }

async function loadFfmpeg(){
    if (ffmpegLoaded) return;
    var btn = document.getElementById('loadFfmpegBtn');
    btn.disabled = true;
    setStatus('<span class="spinner"></span> Mengunduh mesin pemotong… (sekali saja, lalu di-cache)');
    try {
        var FF = (window.FFmpegWASM || window.FFmpeg);
        ffmpeg = new FF.FFmpeg();
        ffmpeg.on('progress', function(p){
            if (p && p.progress >= 0 && p.progress <= 1) setStatus('✂️ Memproses… ' + Math.round(p.progress*100) + '%');
        });
        var base = 'https://unpkg.com/@ffmpeg/core@0.12.6/dist/umd';
        var ffBase = 'https://unpkg.com/@ffmpeg/ffmpeg@0.12.10/dist/umd';
        await ffmpeg.load({
            // worker di-blob-kan agar same-origin (worker lintas-origin diblokir browser)
            classWorkerURL: await toBlobURL(ffBase + '/814.ffmpeg.js', 'text/javascript'),
            coreURL: await toBlobURL(base + '/ffmpeg-core.js', 'text/javascript'),
            wasmURL: await toBlobURL(base + '/ffmpeg-core.wasm', 'application/wasm'),
        });
        ffmpegLoaded = true;
        btn.style.display = 'none';
        var cut = document.getElementById('cutBtn');
        cut.style.display = 'inline-block'; cut.disabled = false;
        setStatus('✓ Mesin siap. Klik "Potong bagian ini".');
    } catch(e){
        btn.disabled = false;
        setStatus('⚠️ Gagal memuat mesin: ' + e.message);
    }
}

async function doCut(){
    if (!ffmpegLoaded){ await loadFfmpeg(); if (!ffmpegLoaded) return; }
    if (!srcUrl){ alert('Muat lagu dulu.'); return; }
    var s = getStart(), dur = getEnd() - s;
    if (dur < 0.2){ alert('Bagian terlalu pendek.'); return; }

    var cut = document.getElementById('cutBtn');
    cut.disabled = true;
    setStatus('<span class="spinner"></span> Menyiapkan…');
    try {
        var inName = 'in.' + srcExt, outName = 'out.' + srcExt;
        await ffmpeg.writeFile(inName, await fetchFile(srcUrl));
        setStatus('<span class="spinner"></span> Memotong bagian…');
        await ffmpeg.exec(['-ss', s.toFixed(2), '-i', inName, '-t', dur.toFixed(2), '-c', 'copy', outName]);
        var data = await ffmpeg.readFile(outName);
        var type = srcExt === 'mp3' ? 'audio/mpeg' : 'audio/' + srcExt;
        lastClipBlob = new Blob([data.buffer], { type: type });
        var url = URL.createObjectURL(lastClipBlob);

        document.getElementById('clipPlayer').src = url;
        var dl = document.getElementById('downloadBtn');
        dl.href = url; dl.download = (srcName + '_' + fmt(s).replace(':','m') + '-' + fmt(getEnd()).replace(':','m') + '.' + srcExt);
        document.getElementById('clipName').value = srcName + ' (' + fmt(s) + '–' + fmt(getEnd()) + ')';
        document.getElementById('resultWrap').style.display = 'block';
        setStatus('✓ Potongan jadi (' + fmt(dur) + '). Unduh atau simpan di perangkat.');
        try { ffmpeg.deleteFile(inName); ffmpeg.deleteFile(outName); } catch(e){}
    } catch(e){
        setStatus('⚠️ Gagal memotong: ' + e.message + (srcExt !== 'mp3' ? ' (coba file MP3)' : ''));
    } finally {
        cut.disabled = false;
    }
}

// ====== IndexedDB potongan ======
function idbOpen(){
    return new Promise(function(res, rej){
        var r = indexedDB.open('mafAudioClips', 1);
        r.onupgradeneeded = function(){ r.result.createObjectStore('clips', { keyPath:'id', autoIncrement:true }); };
        r.onsuccess = function(){ res(r.result); };
        r.onerror = function(){ rej(r.error); };
    });
}
async function idbAll(){ var db = await idbOpen(); return new Promise(function(res){ var t = db.transaction('clips').objectStore('clips').getAll(); t.onsuccess = function(){ res(t.result || []); }; t.onerror = function(){ res([]); }; }); }
async function idbAdd(rec){ var db = await idbOpen(); return new Promise(function(res){ var t = db.transaction('clips','readwrite').objectStore('clips').add(rec); t.onsuccess = function(){ res(t.result); }; }); }
async function idbDel(id){ var db = await idbOpen(); return new Promise(function(res){ db.transaction('clips','readwrite').objectStore('clips').delete(id).onsuccess = function(){ res(); }; }); }

async function saveClip(){
    if (!lastClipBlob){ alert('Belum ada potongan.'); return; }
    var name = (document.getElementById('clipName').value || 'Potongan').trim();
    await idbAdd({ name: name, ext: srcExt, blob: lastClipBlob, size: lastClipBlob.size, createdAt: Date.now() });
    setStatus('✓ Tersimpan di perangkat: ' + name);
    renderClips();
}

async function renderClips(){
    var list = document.getElementById('clipList');
    var all = await idbAll();
    if (!all.length){ list.innerHTML = '<p class="muted">Belum ada potongan tersimpan.</p>'; return; }
    list.innerHTML = '';
    all.sort(function(a,b){ return b.createdAt - a.createdAt; }).forEach(function(c){
        var url = URL.createObjectURL(c.blob);
        var kb = Math.round(c.size/1024);
        var div = document.createElement('div');
        div.className = 'clip-item';
        div.innerHTML =
            '<div style="flex:1;min-width:160px;">' +
                '<div class="clip-name">' + (c.name||'Potongan').replace(/</g,'&lt;') + '</div>' +
                '<div class="clip-meta">.' + c.ext + ' · ' + kb + ' KB</div>' +
                '<audio controls src="' + url + '"></audio>' +
            '</div>' +
            '<a class="btn btn-accent" href="' + url + '" download="' + (c.name||'potongan') + '.' + c.ext + '">⬇️</a>' +
            '<button class="btn-del" data-id="' + c.id + '">Hapus</button>';
        div.querySelector('.btn-del').addEventListener('click', async function(){
            if (!confirm('Hapus potongan ini?')) return;
            await idbDel(c.id); renderClips();
        });
        list.appendChild(div);
    });
}
renderClips();
</script>

@endsection
