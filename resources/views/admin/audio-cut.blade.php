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

    /* Region selector */
    .region-track { position:relative; height:52px; background:linear-gradient(var(--bg-3),var(--bg-3)); border:1px solid var(--border); border-radius:8px; margin:12px 0 4px; overflow:hidden; cursor:pointer; }
    .region-sel { position:absolute; top:0; bottom:0; background:rgba(99,102,241,0.22); border-left:2px solid var(--accent); border-right:2px solid var(--accent); }
    .region-play { position:absolute; top:0; bottom:0; width:2px; background:var(--text); opacity:0.7; left:0; }
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

            <div class="region-track" id="regionTrack">
                <div class="region-sel" id="regionSel"></div>
                <div class="region-play" id="regionPlay"></div>
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

<script src="{{ asset('ffmpeg/ffmpeg.js') }}"></script>
<script>
var FFMPEG_BASE = '{{ asset('ffmpeg') }}';   // file ffmpeg di-host sendiri (same-origin)
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
var srcUrl = null, srcExt = 'mp3', srcName = 'lagu', duration = 0;
var ffmpeg = null, ffmpegLoaded = false, ffmpegBusy = false;
var lastClipBlob = null, lastClipUrl = null;

function fmt(s){ s = Math.max(0, s||0); var m = Math.floor(s/60), x = Math.floor(s%60); return m + ':' + (x<10?'0':'') + x; }
function getExt(name){ var m = (name||'').match(/\.([a-z0-9]+)(?:\?|$)/i); return m ? m[1].toLowerCase() : 'mp3'; }
function setStatus(html){ document.getElementById('status').innerHTML = html || ''; }

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
    document.getElementById('srcInfo').textContent = '🎵 ' + name + ' — memuat…';
    document.getElementById('editArea').style.display = 'block';
    document.getElementById('resultWrap').style.display = 'none';   // reset hasil saat ganti lagu
    setStatus('');
}

player.addEventListener('loadedmetadata', function(){
    duration = player.duration || 0;
    document.getElementById('durLabel').textContent = fmt(duration);
    document.getElementById('srcInfo').textContent = '🎵 ' + srcName + ' · ' + fmt(duration);
    var sr = document.getElementById('startRange'), er = document.getElementById('endRange');
    sr.max = er.max = duration.toFixed(1);
    sr.value = 0; er.value = duration.toFixed(1);
    updateRegion();
});

// ====== Region ======
var startRange = document.getElementById('startRange'), endRange = document.getElementById('endRange');
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
    document.getElementById('segLabel').textContent = '🟦 ' + fmt(s) + ' – ' + fmt(e) + ' · durasi ' + fmt(e - s);
    var sel = document.getElementById('regionSel');
    if (duration > 0){ sel.style.left = (s/duration*100) + '%'; sel.style.width = ((e-s)/duration*100) + '%'; }
}
function setEdge(which){
    if (!duration) return;
    var t = player.currentTime || 0;
    if (which === 'start') startRange.value = Math.min(t, getEnd()-0.2).toFixed(1);
    else endRange.value = Math.max(t, getStart()+0.2).toFixed(1);
    updateRegion();
}
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

// ====== ffmpeg (auto-muat saat potong pertama) ======
async function ensureFfmpeg(){
    if (ffmpegLoaded) return true;
    var FF = (window.FFmpegWASM || window.FFmpeg);
    if (!FF || !FF.FFmpeg) throw new Error('Library ffmpeg tidak termuat (cek koneksi).');
    ffmpeg = new FF.FFmpeg();
    ffmpeg.on('progress', function(p){
        if (p && p.progress >= 0 && p.progress <= 1) setStatus('<span class="spinner"></span> Memproses… ' + Math.round(p.progress*100) + '%');
    });
    setStatus('<span class="spinner"></span> Menyiapkan mesin pemotong (sekali saja, lalu di-cache)…');
    await ffmpeg.load({ coreURL: FFMPEG_BASE + '/ffmpeg-core.js', wasmURL: FFMPEG_BASE + '/ffmpeg-core.wasm' });
    ffmpegLoaded = true;
    return true;
}

async function doCut(){
    if (!srcUrl){ alert('Pilih lagu dulu.'); return; }
    if (ffmpegBusy) return;
    var s = getStart(), dur = getEnd() - s;
    if (dur < 0.2){ alert('Bagian terlalu pendek.'); return; }

    var cut = document.getElementById('cutBtn');
    ffmpegBusy = true; cut.disabled = true;
    try {
        await ensureFfmpeg();
        setStatus('<span class="spinner"></span> Memotong bagian…');
        var inName = 'in.' + srcExt, outName = 'out.' + srcExt;
        await ffmpeg.writeFile(inName, await fetchFile(srcUrl));
        await ffmpeg.exec(['-ss', s.toFixed(2), '-i', inName, '-t', dur.toFixed(2), '-c', 'copy', outName]);
        var data = await ffmpeg.readFile(outName);
        try { ffmpeg.deleteFile(inName); ffmpeg.deleteFile(outName); } catch(e){}

        if (lastClipUrl) URL.revokeObjectURL(lastClipUrl);  // hindari kebocoran memori
        lastClipBlob = new Blob([data.buffer], { type: srcExt === 'mp3' ? 'audio/mpeg' : 'audio/' + srcExt });
        lastClipUrl = URL.createObjectURL(lastClipBlob);

        document.getElementById('clipPlayer').src = lastClipUrl;
        var dl = document.getElementById('downloadBtn');
        dl.href = lastClipUrl;
        dl.download = srcName + '_' + fmt(s).replace(':','m') + '-' + fmt(getEnd()).replace(':','m') + '.' + srcExt;
        document.getElementById('clipName').value = srcName + ' (' + fmt(s) + '–' + fmt(getEnd()) + ')';
        document.getElementById('resultWrap').style.display = 'block';
        setStatus('✓ Potongan jadi (' + fmt(dur) + '). Simpan/unduh, atau geser slider & potong part lain.');
    } catch(e){
        console.error('cut error:', e);
        setStatus('⚠️ Gagal: ' + ((e && e.message) || e) + (srcExt !== 'mp3' ? ' (coba file MP3)' : ''));
    } finally {
        ffmpegBusy = false; cut.disabled = false;
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
    setStatus('✓ Tersimpan: ' + name + '. Bisa langsung potong part lain.');
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
                '<audio class="mini" controls src="' + url + '"></audio>' +
            '</div>' +
            '<a class="btn btn-accent btn-sm" href="' + url + '" download="' + (c.name||'potongan') + '.' + c.ext + '">⬇️</a>' +
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
