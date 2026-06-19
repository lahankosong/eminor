?@extends('layouts.admin')

@push('styles')
<style>
    .ai-header { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border); }
    .ai-header h2 { font-size:1rem; font-weight:500; color:var(--text); }
    .ai-header p { font-size:12px; color:var(--text-3); margin-top:2px; }
    .btn-back { font-size:12px; color:var(--text-2); text-decoration:none; border:1px solid var(--border); padding:6px 14px; border-radius:8px; }
    .btn-back:hover { color:var(--text); border-color:var(--text-3); }
    .alert-success { background:#0d2e1a; color:#4ade80; border:1px solid #166534; padding:10px 16px; border-radius:8px; margin-bottom:1.25rem; font-size:13px; }

    .card { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; }
    .card > summary, .card-head { padding:0.9rem 1.1rem; border-bottom:1px solid var(--border); font-size:12px; color:var(--text-2); font-weight:600; letter-spacing:0.04em; }
    .card > summary { cursor:pointer; list-style:none; display:flex; justify-content:space-between; align-items:center; }
    .card > summary::-webkit-details-marker { display:none; }
    .card > summary::after { content:'▾'; color:var(--text-3); transition:transform 0.2s; }
    details.card[open] > summary::after { transform:rotate(180deg); }
    .card-body { padding:1.1rem; }

    .fg { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
    .fg label { font-size:11px; color:var(--text-3); text-transform:uppercase; letter-spacing:0.05em; }
    .fi { background:var(--bg-3); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px; padding:9px 11px; outline:none; font-family:inherit; width:100%; }
    .fi:focus { border-color:var(--text-3); }
    .row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .btn { padding:9px 18px; border-radius:8px; font-size:13px; font-weight:500; border:none; cursor:pointer; transition:0.2s; }
    .btn-primary { background:var(--text); color:var(--bg); }
    .btn-primary:hover { filter:brightness(0.88); }
    .btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
    .btn-accent { background:var(--accent-dim); color:var(--accent); }

    .prov-item { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--border-2); flex-wrap:wrap; }
    .prov-item:last-child { border-bottom:none; }
    .prov-name { font-size:13px; color:var(--text); font-weight:500; }
    .prov-meta { font-size:11px; color:var(--text-3); }
    .prov-badge { font-size:10px; padding:2px 7px; border-radius:20px; background:var(--bg-3); color:var(--text-3); border:1px solid var(--border); }
    .prov-key-ok { color:#4ade80; } .prov-key-no { color:#f87171; }
    .btn-del { background:transparent; border:1px solid var(--border); color:var(--text-3); border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer; }
    .btn-del:hover { border-color:#ef4444; color:#ef4444; }

    .gen-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
    .gen-bar .fg { flex:1; min-width:160px; margin-bottom:0; }
    .gen-status { font-size:12px; color:var(--text-3); margin-top:10px; min-height:18px; }
    .style-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:10px; }
    .style-grid .fg { margin-bottom:0; }
    @media(max-width:600px){ .style-grid{ grid-template-columns:1fr 1fr; } }

    .niche-box { background:var(--accent-dim); border:1px solid transparent; border-radius:10px; padding:12px 14px; margin-bottom:1.25rem; }
    .niche-box .lbl { font-size:10px; color:var(--accent); text-transform:uppercase; letter-spacing:0.1em; }
    .niche-box .val { font-size:14px; color:var(--text); margin-top:3px; }
    .section-divider { font-size:12px; font-weight:700; color:var(--text-2); letter-spacing:0.06em; margin:1.25rem 0 0.6rem; padding-bottom:6px; border-bottom:1px solid var(--border); }
    .topic { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; margin-bottom:1rem; overflow:hidden; }
    .topic-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:0.8rem 1.1rem; border-bottom:1px solid var(--border); }
    .topic-title { font-size:13px; font-weight:600; color:var(--text); }
    .mini-btn { font-size:11px; padding:4px 10px; border-radius:6px; background:var(--bg-3); border:1px solid var(--border); color:var(--text-2); cursor:pointer; }
    .narr { display:flex; gap:10px; padding:11px 1.1rem; border-bottom:1px solid var(--border-2); }
    .narr:last-child { border-bottom:none; }
    .narr input[type=checkbox] { margin-top:3px; width:15px; height:15px; cursor:pointer; flex-shrink:0; }
    .narr-body { flex:1; min-width:0; }
    .narr-text { font-size:13px; color:var(--text); line-height:1.5; }
    .narr-gong { font-size:13px; font-weight:700; color:var(--accent); margin-top:4px; letter-spacing:0.01em; }
    .narr-prompt { font-size:11px; color:var(--text-3); margin-top:5px; line-height:1.5; background:var(--bg-3); border:1px solid var(--border-2); border-radius:6px; padding:6px 8px; font-family:monospace; }
    .narr-copy { font-size:10px; color:var(--accent); cursor:pointer; margin-left:8px; }
    .narr-img-btn { font-size:10px; color:var(--accent); cursor:pointer; margin-left:8px; }
    .img-toolbar { display:flex; gap:8px; align-items:center; flex-wrap:wrap; background:var(--bg-2); border:1px solid var(--border); border-radius:10px; padding:9px 12px; margin-bottom:1rem; }
    .gen-img-wrap { margin-top:8px; }
    .gen-img-wrap img { max-width:170px; max-height:300px; border-radius:8px; border:1px solid var(--border); display:block; }
    .gen-img-actions { display:flex; gap:10px; margin-top:5px; }
    .gen-img-actions a, .gen-img-actions span { font-size:10px; color:var(--text-3); cursor:pointer; text-decoration:none; }
    .gen-img-actions a:hover { color:var(--text); }
    .gen-img-loading { font-size:11px; color:var(--text-3); margin-top:6px; }
    .tts-block { margin-top:8px; }
    .tts-wrap { margin-top:6px; }
    .tts-wrap audio { width:100%; max-width:420px; height:36px; display:block; }
    .tts-actions { display:flex; gap:10px; margin-top:5px; }
    .tts-actions a, .tts-actions span { font-size:10px; color:var(--text-3); cursor:pointer; text-decoration:none; }
    .tts-actions a:hover, .tts-actions span:hover { color:var(--text); }

    .sched-bar { position:sticky; bottom:0; background:var(--bg-2); border:1px solid var(--border); border-radius:12px; padding:12px 14px; margin-top:1rem; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; }
    .sched-bar .fg { margin-bottom:0; }
    .sched-count { font-size:12px; color:var(--text-3); }

    .toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:var(--text); color:var(--bg); padding:10px 20px; border-radius:8px; font-size:13px; opacity:0; pointer-events:none; transition:0.25s; z-index:999; }
    .toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
    .spinner { display:inline-block; width:13px; height:13px; border:2px solid var(--text-3); border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }

    @media (max-width:600px){ .row2{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

@php
    $textProviders  = $providers->filter(fn($p) => ($p->kind ?? 'text') === 'text')->values();
    $imageProviders = $providers->filter(fn($p) => ($p->kind ?? 'text') === 'image')->values();
    $ttsProviders   = $providers->filter(fn($p) => ($p->kind ?? 'text') === 'tts')->values();
@endphp

<div class="ai-header">
    <div>
        <h2>AI Agent v2</h2>
        <p>Niche → topik → narasi → prompt gambar → jadwalkan ke Calendar</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.ai-settings') }}" class="btn-back">⚙️ Pengaturan AI</a>
        <a href="{{ route('admin.index') }}" class="btn-back">Panel Admin</a>
    </div>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- ===== GENERATE ===== --}}
<div class="card">
    <div class="card-head">✨ Generate Konten</div>
    <div class="card-body">
        <div class="gen-bar">
            <div class="fg">
                <label>Sumber</label>
                <select class="fi" id="sourceType" onchange="onSourceType()">
                    <option value="lagu">🎵 Lagu</option>
                    <option value="link">🔗 Link berita</option>
                    <option value="text">📝 Teks</option>
                </select>
            </div>
            <div class="fg" id="songFg">
                <label>Lagu</label>
                <select class="fi" id="songSelect">
                    <option value="">— Pilih lagu —</option>
                    @foreach($songs as $song)
                    <option value="{{ $song->id }}" data-title="{{ $song->title }}">{{ $song->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label>AI Provider</label>
                <select class="fi" id="providerSelect">
                    <option value="">— Pilih AI —</option>
                    @foreach($textProviders as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->name }} ({{ $prov->model }})</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label>Jenis konten</label>
                <select class="fi" id="modeSelect">
                    <option value="short">📱 Short saja (hemat)</option>
                    <option value="long">🎬 Video panjang saja</option>
                    <option value="umum">🌐 Umum saja (backsound tema)</option>
                    <option value="all">Semua (3 jenis)</option>
                </select>
            </div>
            <button class="btn btn-primary" id="genBtn" onclick="doGenerate()">Generate</button>
        </div>

        <div class="fg" id="sourceFg" style="margin-top:12px;display:none;">
            <label id="sourceLabel">Sumber</label>
            <textarea class="fi" id="sourceInput" rows="2" placeholder="Tempel link atau teks…"></textarea>
            <span style="font-size:11px;color:var(--text-3);margin-top:3px;">AI akan membaca/mengolah isi link atau teks ini menjadi konten (tanpa lagu).</span>
        </div>

        <details style="margin-top:6px;">
            <summary style="cursor:pointer;font-size:12px;color:var(--text-2);padding:4px 0;">🎨 Pengaturan Gaya Gambar (opsional)</summary>
            <div class="style-grid">
                <div class="fg"><label>Orientasi</label>
                    <select class="fi" id="styleOrientation">
                        <option value="9:16">9:16 Potrait (short)</option>
                        <option value="16:9">16:9 Landscape</option>
                        <option value="1:1">1:1 Kotak</option>
                    </select>
                </div>
                <div class="fg"><label>Gaya gambar</label>
                    <select class="fi" id="styleArt">
                        <option value="">Default (sinematik)</option>
                        <option value="cinematic photorealistic">Realistis sinematik</option>
                        <option value="illustration, flat design">Ilustrasi</option>
                        <option value="anime style">Anime</option>
                        <option value="vintage film grain, retro">Vintage / retro</option>
                        <option value="dramatic high-contrast">Dramatis kontras</option>
                    </select>
                </div>
                <div class="fg"><label>Jumlah orang</label>
                    <select class="fi" id="stylePeople">
                        <option value="">Default</option>
                        <option value="no people, scenery only">Tanpa orang</option>
                        <option value="a single person">Sendiri</option>
                        <option value="two people">Berdua</option>
                        <option value="a group of people">Ramai</option>
                    </select>
                </div>
                <div class="fg"><label>Gender</label>
                    <select class="fi" id="styleGender">
                        <option value="">Default</option>
                        <option value="male subject">Pria</option>
                        <option value="female subject">Wanita</option>
                    </select>
                </div>
                <div class="fg"><label>Usia</label>
                    <select class="fi" id="styleAge">
                        <option value="">Default</option>
                        <option value="child">Anak-anak</option>
                        <option value="teenager">Remaja</option>
                        <option value="adult">Dewasa</option>
                        <option value="elderly person">Orang tua</option>
                    </select>
                </div>
                <div class="fg"><label>Waktu</label>
                    <select class="fi" id="styleTime">
                        <option value="">Default</option>
                        <option value="morning light">Pagi</option>
                        <option value="midday">Siang</option>
                        <option value="golden hour, sunset">Sore</option>
                        <option value="night">Malam</option>
                    </select>
                </div>
                <div class="fg"><label>Pencahayaan</label>
                    <select class="fi" id="styleLight">
                        <option value="">Default</option>
                        <option value="bright, well-lit">Terang</option>
                        <option value="dim, moody lighting">Redup</option>
                        <option value="dark, low-key lighting">Gelap</option>
                    </select>
                </div>
            </div>
        </details>

        <div class="gen-status" id="genStatus"></div>
    </div>
</div>

{{-- ===== HASIL ===== --}}
<div id="results" style="display:none;">
    <div class="niche-box">
        <div class="lbl">Niche</div>
        <div class="val" id="nicheVal"></div>
    </div>

    <div class="img-toolbar">
        <span style="font-size:11px;color:var(--text-3);">🎨 Buat gambar pakai:</span>
        <select class="fi" id="imgProviderSelect" style="width:auto;padding:6px 9px;font-size:12px;">
            <option value="">Pollinations (gratis)</option>
            @foreach($imageProviders as $prov)
            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
            @endforeach
        </select>
        <select class="fi" id="imgRatioSelect" style="width:auto;padding:6px 9px;font-size:12px;">
            <option value="9:16">9:16 Potrait</option>
            <option value="16:9">16:9 Landscape</option>
            <option value="1:1">1:1 Kotak</option>
        </select>
        @if(!($cloudinary['cloud'] && $cloudinary['secret_set']))
        <span style="font-size:11px;color:#f87171;">⚠️ Atur Cloudinary dulu di panel atas untuk menyimpan gambar.</span>
        @endif
    </div>

    <div class="img-toolbar">
        <span style="font-size:11px;color:var(--text-3);">🔊 Suara narasi pakai:</span>
        <select class="fi" id="ttsProviderSelect" style="width:auto;padding:6px 9px;font-size:12px;">
            @forelse($ttsProviders as $prov)
            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
            @empty
            <option value="">(belum ada — atur di Pengaturan AI)</option>
            @endforelse
        </select>
        <select class="fi" id="ttsVoiceSelect" style="width:auto;padding:6px 9px;font-size:12px;">
            <option value="Kore">Kore (perempuan, tegas)</option>
            <option value="Aoede">Aoede (perempuan, ramah)</option>
            <option value="Leda">Leda (perempuan, muda)</option>
            <option value="Puck">Puck (laki, ceria)</option>
            <option value="Charon">Charon (laki, dalam)</option>
            <option value="Orus">Orus (laki, mantap)</option>
            <option value="Fenrir">Fenrir (laki, energik)</option>
            <option value="Zephyr">Zephyr (cerah)</option>
        </select>
    </div>
    <div id="topicsWrap"></div>
    <div id="longFormWrap"></div>
    <div id="umumWrap"></div>

    <div class="sched-bar">
        <div class="fg"><label>Mulai tanggal</label><input type="date" id="schedDate" class="fi" value="{{ now()->toDateString() }}"></div>
        <div class="fg"><label>Platform</label><input type="text" id="schedPlatforms" class="fi" value="TikTok,Instagram" placeholder="TikTok,Instagram"></div>
        <div style="flex:1;"></div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
            <span class="sched-count" id="schedCount">0 narasi dipilih</span>
            <button class="btn btn-accent" onclick="doSchedule()">📅 Jadwalkan terpilih ke Calendar</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
var CSRF = '{{ csrf_token() }}';
var ROUTE_GEN_BASE = '{{ url('admin/ai-agent/generate') }}';
var ROUTE_SCHEDULE = '{{ route('admin.ai-agent.schedule') }}';
var ROUTE_CALENDAR = '{{ route('admin.calendar') }}';
var ROUTE_IMAGE    = '{{ route('admin.ai-agent.image') }}';
var ROUTE_TTS      = '{{ route('admin.ai-agent.tts') }}';
var CLOUD_READY    = {{ ($cloudinary['cloud'] && $cloudinary['secret_set']) ? 'true' : 'false' }};
var HAS_TTS        = {{ $ttsProviders->count() ? 'true' : 'false' }};
var currentSongId = null;
var SAVED = {!! json_encode($saved) !!};       // hasil tersimpan per song_id
var LAST_SONG = {{ $lastSongId ?? 'null' }};   // generasi terakhir

function esc(s){ return (s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

// Kirim narasi + gong + gambar (bila sudah dibuat) ke Video Builder
function toVideo(btn){
    var narasi = decodeURIComponent(btn.dataset.narasi || '');
    var gong   = decodeURIComponent(btn.dataset.gong || '');
    var img = null, body = btn.closest('.narr-body');
    if (body){ var im = body.querySelector('.gen-img-wrap img'); if (im) img = im.src; }
    try { sessionStorage.setItem('vb_prefill', JSON.stringify({ narasi: narasi, gong: gong, image: img })); } catch(e){}
    window.location = '{{ route('admin.video-builder') }}';
}
// Kirim SEMUA item topik (batch) ke Video Builder
function toBatch(btn){
    var topic = btn.closest('.topic'); if (!topic) return;
    var items = [];
    topic.querySelectorAll('.narr-body').forEach(function(body){
        var v = body.querySelector('[data-narasi]'); if (!v) return;
        var img = null, im = body.querySelector('.gen-img-wrap img'); if (im) img = im.src;
        items.push({ narasi: decodeURIComponent(v.dataset.narasi||''), gong: decodeURIComponent(v.dataset.gong||''), image: img });
    });
    if (!items.length){ alert('Tidak ada item di topik ini.'); return; }
    if (!confirm('Kirim ' + items.length + ' item ke Video Builder (batch)? Pastikan gambar tiap item sudah dibuat (🖼️) agar ikut terbawa.')) return;
    try { sessionStorage.setItem('vb_batch', JSON.stringify(items)); } catch(e){}
    window.location = '{{ route('admin.video-builder') }}';
}

// ===== Generator gambar (Fase A) =====
function imgTools(promptStr){
    var enc = encodeURIComponent(promptStr || '');
    return '<span class="narr-copy" onclick="copyText(this,\'' + enc + '\')">[copy]</span>' +
           '<span class="narr-img-btn" onclick="genImage(this,\'' + enc + '\')">🖼️ buat gambar</span>' +
           '<div class="gen-img-wrap"></div>';
}

function genImage(btn, enc){
    if (!CLOUD_READY) { alert('Atur kredensial Cloudinary dulu di panel "Generator Gambar & Penyimpanan".'); return; }
    var prompt = decodeURIComponent(enc);
    var wrap = btn.closest('.narr-prompt').querySelector('.gen-img-wrap');
    var provId = document.getElementById('imgProviderSelect').value;
    var ratio  = document.getElementById('imgRatioSelect').value;
    btn.style.pointerEvents = 'none'; btn.textContent = '⏳ membuat…';
    wrap.innerHTML = '<div class="gen-img-loading"><span class="spinner"></span> Membuat gambar… (10–40 detik)</div>';

    fetch(ROUTE_IMAGE, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ prompt: prompt, ratio: ratio, provider_id: provId || null, song_id: currentSongId })
    })
    .then(function(r){ return r.json().then(function(d){ return {ok:r.ok, d:d}; }); })
    .then(function(res){
        btn.style.pointerEvents = ''; btn.textContent = '🖼️ buat lagi';
        if (!res.ok || res.d.error) {
            wrap.innerHTML = '<div class="gen-img-loading" style="color:#f87171;">⚠️ ' + esc(res.d.error || 'Gagal membuat gambar.') + '</div>';
            return;
        }
        wrap.innerHTML =
            '<img src="' + res.d.url + '" alt="hasil">' +
            '<div class="gen-img-actions">' +
                '<a href="' + res.d.url + '" target="_blank" download>⬇️ unduh</a>' +
                '<span onclick="copyText(this,\'' + encodeURIComponent(res.d.url) + '\')">🔗 salin URL</span>' +
                '<span style="color:var(--text-3);">via ' + esc(res.d.provider) + '</span>' +
            '</div>';
    })
    .catch(function(e){
        btn.style.pointerEvents = ''; btn.textContent = '🖼️ buat gambar';
        wrap.innerHTML = '<div class="gen-img-loading" style="color:#f87171;">⚠️ ' + e.message + '</div>';
    });
}

// ====== TTS narasi (suara) ======
function narrAudioTools(text){
    var enc = encodeURIComponent(text || '');
    return '<div class="tts-block">' +
        '<span class="narr-img-btn" onclick="genTts(this,\'' + enc + '\')">🔊 buat suara narasi</span>' +
        '<div class="tts-wrap"></div></div>';
}

function genTts(btn, enc){
    if (!HAS_TTS){ alert('Belum ada provider TTS. Tambahkan key Gemini di Pengaturan AI → Suara Narasi.'); return; }
    var text = decodeURIComponent(enc);
    var wrap = btn.closest('.tts-block').querySelector('.tts-wrap');
    var provId = document.getElementById('ttsProviderSelect').value;
    var voice  = document.getElementById('ttsVoiceSelect').value;
    if (!provId){ alert('Pilih provider TTS di bar atas hasil.'); return; }
    btn.style.pointerEvents = 'none'; btn.textContent = '⏳ membuat suara…';
    wrap.innerHTML = '<div class="gen-img-loading"><span class="spinner"></span> Membuat suara… (10–40 detik)</div>';

    fetch(ROUTE_TTS, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ text: text, voice: voice, provider_id: provId })
    })
    .then(function(r){ return r.json().then(function(d){ return {ok:r.ok, d:d}; }); })
    .then(function(res){
        btn.style.pointerEvents = ''; btn.textContent = '🔊 buat ulang';
        if (!res.ok || res.d.error){
            wrap.innerHTML = '<div class="gen-img-loading" style="color:#f87171;">⚠️ ' + esc(res.d.error || 'Gagal membuat suara.') + '</div>';
            return;
        }
        var dataUri = res.d.audio;
        wrap.innerHTML =
            '<audio controls src="' + dataUri + '"></audio>' +
            '<div class="tts-actions">' +
                '<a href="' + dataUri + '" download="narasi_' + voice + '.wav">⬇️ unduh</a>' +
                '<span onclick="saveTts(this,\'' + voice + '\')">💾 simpan ke perangkat</span>' +
                '<span style="color:var(--text-3);">suara ' + esc(res.d.voice) + '</span>' +
            '</div>';
        wrap.querySelector('audio').dataset.uri = dataUri;
    })
    .catch(function(e){
        btn.style.pointerEvents = ''; btn.textContent = '🔊 buat suara narasi';
        wrap.innerHTML = '<div class="gen-img-loading" style="color:#f87171;">⚠️ ' + e.message + '</div>';
    });
}

// Simpan suara ke IndexedDB 'mafAudioClips' (sama dgn Pemotong Lagu → siap untuk Fase C)
function ttsIdbOpen(){
    return new Promise(function(res, rej){
        var r = indexedDB.open('mafAudioClips', 1);
        r.onupgradeneeded = function(){ r.result.createObjectStore('clips', { keyPath:'id', autoIncrement:true }); };
        r.onsuccess = function(){ res(r.result); };
        r.onerror = function(){ rej(r.error); };
    });
}
function saveTts(el, voice){
    var uri = el.closest('.tts-wrap').querySelector('audio').dataset.uri;
    fetch(uri).then(function(r){ return r.blob(); }).then(function(blob){
        return ttsIdbOpen().then(function(db){
            db.transaction('clips','readwrite').objectStore('clips').add({
                name: 'Narasi (' + voice + ')', ext: 'wav', blob: blob, size: blob.size, createdAt: Date.now()
            });
        });
    }).then(function(){
        var old = el.textContent; el.textContent = '✓ tersimpan'; el.style.color = '#4ade80';
        setTimeout(function(){ el.textContent = old; el.style.color = ''; }, 1500);
    }).catch(function(e){ alert('Gagal simpan: ' + e.message); });
}

function onSourceType(){
    var t = document.getElementById('sourceType').value;
    document.getElementById('songFg').style.display   = (t==='lagu') ? '' : 'none';
    document.getElementById('sourceFg').style.display = (t==='lagu') ? 'none' : '';
    var lbl = document.getElementById('sourceLabel'), inp = document.getElementById('sourceInput');
    if (t==='link'){ lbl.textContent='Link berita/artikel'; inp.placeholder='https://...'; }
    else if (t==='text'){ lbl.textContent='Teks bahan konten'; inp.placeholder='Tempel teks yang mau diolah jadi konten…'; }
}
function doGenerate() {
    var stype = document.getElementById('sourceType').value;
    var provId = document.getElementById('providerSelect').value;
    if (!provId) { alert('Pilih AI provider dulu (atau tambah di Pengaturan AI).'); return; }
    var sv = function(id){ var e = document.getElementById(id); return e ? e.value : ''; };
    var songId = '0', source = '';
    if (stype === 'lagu'){
        songId = document.getElementById('songSelect').value;
        if (!songId) { alert('Pilih lagu dulu.'); return; }
    } else {
        source = (sv('sourceInput')||'').trim();
        if (!source) { alert(stype==='link' ? 'Tempel link dulu.' : 'Isi teks dulu.'); return; }
    }
    if (!confirm('Generate akan memakai kuota/kredit AI provider yang dipilih. Lanjutkan?')) return;

    var mode = document.getElementById('modeSelect').value;
    var style = {
        orientation: sv('styleOrientation'),
        art:    sv('styleArt'),
        people: sv('stylePeople'),
        gender: sv('styleGender'),
        age:    sv('styleAge'),
        time:   sv('styleTime'),
        light:  sv('styleLight')
    };
    var btn = document.getElementById('genBtn');
    btn.disabled = true;
    document.getElementById('genStatus').innerHTML = '<span class="spinner"></span> Menganalisis lirik & membuat konten… (10–40 detik)';

    fetch(ROUTE_GEN_BASE + '/' + songId, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ provider_id: provId, mode: mode, source: source, style: style })
    })
    .then(function(r){ return r.json().then(function(d){ return {ok:r.ok, d:d}; }); })
    .then(function(res){
        btn.disabled = false;
        if (!res.ok || res.d.error) {
            document.getElementById('genStatus').textContent = '⚠️ ' + (res.d.error || 'Gagal generate.');
            return;
        }
        document.getElementById('genStatus').textContent = '✓ Selesai via ' + res.d.provider + ' · hasil tersimpan otomatis';
        // gabung dengan hasil mode lain yang sudah tersimpan
        var prev = SAVED[res.d.song_id] || {};
        var merged = {
            song_id:   res.d.song_id,
            niche:     res.d.niche     || prev.niche,
            topics:    res.d.topics    || prev.topics,
            long_form: res.d.long_form || prev.long_form,
            umum:      res.d.umum      || prev.umum,
            provider:  res.d.provider
        };
        SAVED[res.d.song_id] = { niche: merged.niche, topics: merged.topics, long_form: merged.long_form, umum: merged.umum };
        renderResults(merged);
    })
    .catch(function(e){
        btn.disabled = false;
        document.getElementById('genStatus').textContent = '⚠️ Error: ' + e.message;
    });
}

function renderResults(d) {
    currentSongId = d.song_id;
    document.getElementById('nicheVal').textContent = d.niche || '—';
    var wrap = document.getElementById('topicsWrap');
    wrap.innerHTML = (d.topics && d.topics.length) ? '<div class="section-divider">📱 KONTEN SHORT VIDEO · 9:16</div>' : '';

    (d.topics || []).forEach(function(t, ti){
        var html = '<div class="topic"><div class="topic-head">' +
            '<span class="topic-title">' + (ti+1) + '. ' + esc(t.label) + '</span>' +
            '<span style="display:flex;gap:6px;"><span class="mini-btn" onclick="toBatch(this)">🎬 Batch video</span>' +
            '<span class="mini-btn" onclick="toggleTopic(this)">Pilih semua</span></span></div>';
        (t.narrations || []).forEach(function(n){
            var combo = (n.text || '') + (n.gong ? '  →  GONG: ' + n.gong : '');
            html += '<div class="narr">' +
                '<input type="checkbox" class="narrChk" data-type="short" data-text="' + esc(combo) + '" data-prompt="' + esc(n.image_prompt) + '" onchange="updateCount()">' +
                '<div class="narr-body">' +
                    '<div class="narr-text">' + esc(n.text) + '</div>' +
                    (n.gong ? '<div class="narr-gong">🎯 ' + esc(n.gong) + '</div>' : '') +
                    '<div class="narr-prompt">🎨 ' + esc(n.image_prompt) + imgTools(n.image_prompt) +
                    '</div>' +
                    '<div style="margin-top:5px;"><span class="narr-img-btn" onclick="toVideo(this)" data-narasi="' + encodeURIComponent(n.text||'') + '" data-gong="' + encodeURIComponent(n.gong||'') + '">🎬 buat video</span></div>' +
                '</div></div>';
        });
        html += '</div>';
        wrap.insertAdjacentHTML('beforeend', html);
    });

    // ===== Long form (video 3-5 menit) =====
    var lfWrap = document.getElementById('longFormWrap');
    lfWrap.innerHTML = '';
    var lf = d.long_form;
    if (lf && lf.narration) {
        var scenes = (lf.scenes || []).map(function(sc, i){
            return '<div class="narr-prompt">🎨 ' + (i+1) + '. ' + esc(sc.image_prompt) +
                imgTools(sc.image_prompt) + '</div>';
        }).join('');
        var lfScenesText = (lf.scenes || []).map(function(sc, i){ return (i+1) + '. ' + sc.image_prompt; }).join('\n');
        var lfCombined = (lf.narration || '') + (lfScenesText ? '\n\nImage prompts:\n' + lfScenesText : '');
        lfWrap.innerHTML =
            '<div class="section-divider">🎬 VIDEO 3–5 MENIT</div>' +
            '<div class="topic"><div class="topic-head">' +
                '<span class="topic-title">🎬 Video Panjang · ' + esc(lf.duration_estimate || '3–5 menit') + '</span>' +
                '<label style="font-size:11px;color:var(--text-2);display:flex;gap:6px;align-items:center;cursor:pointer;">' +
                    '<input type="checkbox" class="narrChk" data-type="long" data-text="' + esc(lf.title || 'Video panjang') + '" data-prompt="' + esc(lfCombined) + '" onchange="updateCount()"> jadwalkan</label>' +
            '</div>' +
            '<div class="narr"><div class="narr-body">' +
                '<div class="narr-text" style="font-weight:600;margin-bottom:6px;">' + esc(lf.title || '') +
                    ' <span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(lf.narration) + '\')">[copy narasi]</span></div>' +
                '<div class="narr-prompt" style="white-space:pre-wrap;font-family:inherit;line-height:1.7;">' + esc(lf.narration) + '</div>' +
                narrAudioTools(lf.narration) +
                (scenes ? '<div style="margin-top:8px;font-size:11px;color:var(--text-3);">Image prompts (vertical 9:16):</div>' + scenes : '') +
            '</div></div></div>';
    }

    // ===== Tema Umum (backsound cerita) =====
    var umWrap = document.getElementById('umumWrap');
    umWrap.innerHTML = '';
    var um = d.umum;
    if (um && um.length) {
        var uh = '<div class="section-divider">🌐 TEMA UMUM · video panjang 3–5 menit</div>';
        um.forEach(function(u){
            var scenesArr = (u.scenes && u.scenes.length) ? u.scenes : (u.image_prompt ? [{image_prompt:u.image_prompt}] : []);
            var scenesHtml = scenesArr.map(function(sc, i){
                return '<div class="narr-prompt">🎨 ' + (i+1) + '. ' + esc(sc.image_prompt) +
                    imgTools(sc.image_prompt) + '</div>';
            }).join('');
            var scenesText = scenesArr.map(function(sc, i){ return (i+1) + '. ' + sc.image_prompt; }).join('\n');
            var combined = 'Angle: ' + (u.angle||'') + '\n\nNarasi:\n' + (u.narration||'') + (scenesText ? '\n\nImage prompts:\n' + scenesText : '');
            uh += '<div class="topic"><div class="topic-head">' +
                '<span class="topic-title">🌐 ' + esc(u.theme) + '</span>' +
                '<label style="font-size:11px;color:var(--text-2);display:flex;gap:6px;align-items:center;cursor:pointer;">' +
                    '<input type="checkbox" class="narrChk" data-type="umum" data-text="' + esc(u.theme) + '" data-prompt="' + esc(combined) + '" onchange="updateCount()"> jadwalkan</label>' +
                '</div>' +
                '<div class="narr"><div class="narr-body">' +
                    '<div style="font-size:12px;color:var(--text-3);">💡 ' + esc(u.angle) + '</div>' +
                    '<div class="narr-prompt" style="white-space:pre-wrap;font-family:inherit;line-height:1.7;margin-top:6px;">' + esc(u.narration) +
                        ' <span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(u.narration) + '\')">[copy narasi]</span></div>' +
                    narrAudioTools(u.narration) +
                    (scenesHtml ? '<div style="margin-top:8px;font-size:11px;color:var(--text-3);">Image prompts (9:16):</div>' + scenesHtml : '') +
                '</div></div></div>';
        });
        umWrap.innerHTML = uh;
    }

    document.getElementById('results').style.display = 'block';
    updateCount();
    document.getElementById('results').scrollIntoView({behavior:'smooth'});
}

function toggleTopic(btn) {
    var topic = btn.closest('.topic');
    var chks = topic.querySelectorAll('.narrChk');
    var allOn = Array.prototype.every.call(chks, function(c){ return c.checked; });
    chks.forEach(function(c){ c.checked = !allOn; });
    updateCount();
}

function updateCount() {
    var n = document.querySelectorAll('.narrChk:checked').length;
    document.getElementById('schedCount').textContent = n + ' narasi dipilih';
}

function copyText(el, enc) {
    navigator.clipboard.writeText(decodeURIComponent(enc)).then(function(){
        var old = el.textContent; el.textContent = '[tersalin]';
        setTimeout(function(){ el.textContent = old; }, 1200);
    });
}

function doSchedule() {
    var checked = document.querySelectorAll('.narrChk:checked');
    if (!checked.length) { alert('Centang minimal 1 narasi dulu.'); return; }
    var items = Array.prototype.map.call(checked, function(c){
        return { text: c.getAttribute('data-text'), image_prompt: c.getAttribute('data-prompt'), type: c.getAttribute('data-type') || 'short' };
    });
    var body = {
        song_id: currentSongId,
        start_date: document.getElementById('schedDate').value,
        platforms: document.getElementById('schedPlatforms').value,
        items: items
    };
    fetch(ROUTE_SCHEDULE, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify(body)
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.success) {
            showToast('✓ ' + d.count + ' jadwal dibuat. Membuka Calendar…');
            setTimeout(function(){ window.location = ROUTE_CALENDAR; }, 1400);
        } else { showToast('⚠️ ' + (d.error || 'Gagal menjadwalkan')); }
    })
    .catch(function(e){ showToast('⚠️ ' + e.message); });
}

function showToast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 2200);
}

// Tampilkan hasil tersimpan saat lagu dipilih
function showSaved(id) {
    if (id && SAVED[id]) {
        renderResults({ song_id: parseInt(id), niche: SAVED[id].niche, topics: SAVED[id].topics, long_form: SAVED[id].long_form, umum: SAVED[id].umum, provider: 'tersimpan' });
        document.getElementById('genStatus').textContent = '📁 Hasil tersimpan ditampilkan. Generate lagi untuk memperbarui.';
        return true;
    }
    document.getElementById('results').style.display = 'none';
    document.getElementById('genStatus').textContent = '';
    return false;
}
document.getElementById('songSelect').addEventListener('change', function(){ showSaved(this.value); });

// Saat halaman dibuka: pulihkan generasi terakhir
if (LAST_SONG && SAVED[LAST_SONG]) {
    document.getElementById('songSelect').value = LAST_SONG;
    showSaved(LAST_SONG);
}
</script>

@endsection

