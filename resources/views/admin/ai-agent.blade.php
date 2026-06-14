@extends('layouts.app')

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
    .narr-prompt { font-size:11px; color:var(--text-3); margin-top:5px; line-height:1.5; background:var(--bg-3); border:1px solid var(--border-2); border-radius:6px; padding:6px 8px; font-family:monospace; }
    .narr-copy { font-size:10px; color:var(--accent); cursor:pointer; margin-left:8px; }

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

<div class="ai-header">
    <div>
        <h2>AI Agent v2</h2>
        <p>Niche → topik → narasi → prompt gambar → jadwalkan ke Calendar</p>
    </div>
    <a href="{{ route('admin.index') }}" class="btn-back">← Panel Admin</a>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- ===== PENGATURAN AI (provider) ===== --}}
<details class="card">
    <summary>⚙️ Pengaturan AI — Provider &amp; API Key ({{ $providers->count() }})</summary>
    <div class="card-body">
        @if($providers->count())
        <div style="margin-bottom:1rem;">
            @foreach($providers as $prov)
            <div class="prov-item">
                <div style="flex:1;min-width:0;">
                    <span class="prov-name">{{ $prov->name }}</span>
                    <span class="prov-badge">{{ $prov->format }}</span>
                    <div class="prov-meta">{{ $prov->model }} ·
                        @if($prov->api_key)<span class="prov-key-ok">● key terisi</span>@else<span class="prov-key-no">● key kosong</span>@endif
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.ai-agent.provider.destroy', $prov->id) }}" onsubmit="return confirm('Hapus provider {{ $prov->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn-del">Hapus</button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p style="font-size:12px;color:var(--text-3);margin-bottom:1rem;">Belum ada provider. Tambah di bawah (mulai dari preset gratis seperti Gemini / Groq).</p>
        @endif

        <form method="POST" action="{{ route('admin.ai-agent.provider.store') }}">
            @csrf
            <div class="fg">
                <label>Preset cepat (otomatis isi kolom)</label>
                <select class="fi" id="presetSelect" onchange="applyPreset(this.value)">
                    <option value="">— Pilih preset / isi manual —</option>
                    <option value="gemini">Google Gemini (gratis)</option>
                    <option value="groq">Groq — Llama 3.3 (gratis)</option>
                    <option value="openrouter">OpenRouter (ada model gratis)</option>
                    <option value="openai">OpenAI</option>
                    <option value="deepseek">DeepSeek (murah)</option>
                    <option value="claude">Claude (Anthropic)</option>
                </select>
            </div>
            <div class="row2">
                <div class="fg"><label>Nama</label><input type="text" name="name" id="pName" class="fi" placeholder="Gemini Flash" required></div>
                <div class="fg"><label>Model</label><input type="text" name="model" id="pModel" class="fi" placeholder="gemini-2.0-flash" required></div>
            </div>
            <div class="fg"><label>Base URL</label><input type="text" name="base_url" id="pUrl" class="fi" placeholder="https://..." required></div>
            <div class="row2">
                <div class="fg"><label>Format</label>
                    <select name="format" id="pFormat" class="fi">
                        <option value="openai">openai-compatible</option>
                        <option value="anthropic">anthropic</option>
                    </select>
                </div>
                <div class="fg"><label>API Key (disimpan terenkripsi)</label><input type="password" name="api_key" class="fi" placeholder="sk-... / AIza..." autocomplete="off"></div>
            </div>
            <button class="btn btn-primary" type="submit">Simpan Provider</button>
        </form>
    </div>
</details>

{{-- ===== GENERATE ===== --}}
<div class="card">
    <div class="card-head">✨ Generate Konten</div>
    <div class="card-body">
        <div class="gen-bar">
            <div class="fg">
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
                    @foreach($providers as $prov)
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
        <div class="gen-status" id="genStatus"></div>
    </div>
</div>

{{-- ===== HASIL ===== --}}
<div id="results" style="display:none;">
    <div class="niche-box">
        <div class="lbl">Niche</div>
        <div class="val" id="nicheVal"></div>
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
var currentSongId = null;
var SAVED = {!! json_encode($saved) !!};       // hasil tersimpan per song_id
var LAST_SONG = {{ $lastSongId ?? 'null' }};   // generasi terakhir

var PRESETS = {
    gemini:    {name:'Gemini Flash',   base_url:'https://generativelanguage.googleapis.com/v1beta/openai', model:'gemini-2.0-flash', format:'openai'},
    groq:      {name:'Groq Llama 3.3', base_url:'https://api.groq.com/openai/v1', model:'llama-3.3-70b-versatile', format:'openai'},
    openrouter:{name:'OpenRouter',     base_url:'https://openrouter.ai/api/v1', model:'deepseek/deepseek-chat-v3.1:free', format:'openai'},
    openai:    {name:'OpenAI',         base_url:'https://api.openai.com/v1', model:'gpt-4o-mini', format:'openai'},
    deepseek:  {name:'DeepSeek',       base_url:'https://api.deepseek.com', model:'deepseek-chat', format:'openai'},
    claude:    {name:'Claude Haiku',   base_url:'https://api.anthropic.com/v1', model:'claude-haiku-4-5-20251001', format:'anthropic'},
};
function applyPreset(k) {
    if (!k || !PRESETS[k]) return;
    var p = PRESETS[k];
    document.getElementById('pName').value = p.name;
    document.getElementById('pUrl').value = p.base_url;
    document.getElementById('pModel').value = p.model;
    document.getElementById('pFormat').value = p.format;
}

function esc(s){ return (s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

function doGenerate() {
    var songId = document.getElementById('songSelect').value;
    var provId = document.getElementById('providerSelect').value;
    if (!songId) { alert('Pilih lagu dulu.'); return; }
    if (!provId) { alert('Pilih AI provider dulu (atau tambah di Pengaturan AI).'); return; }
    if (!confirm('Generate akan memakai kuota/kredit AI provider yang dipilih. Lanjutkan?')) return;

    var mode = document.getElementById('modeSelect').value;
    var btn = document.getElementById('genBtn');
    btn.disabled = true;
    document.getElementById('genStatus').innerHTML = '<span class="spinner"></span> Menganalisis lirik & membuat konten… (10–40 detik)';

    fetch(ROUTE_GEN_BASE + '/' + songId, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ provider_id: provId, mode: mode })
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
            '<span class="mini-btn" onclick="toggleTopic(this)">Pilih semua</span></div>';
        (t.narrations || []).forEach(function(n){
            html += '<div class="narr">' +
                '<input type="checkbox" class="narrChk" data-type="short" data-text="' + esc(n.text) + '" data-prompt="' + esc(n.image_prompt) + '" onchange="updateCount()">' +
                '<div class="narr-body">' +
                    '<div class="narr-text">' + esc(n.text) + '</div>' +
                    '<div class="narr-prompt">🎨 ' + esc(n.image_prompt) +
                        '<span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(n.image_prompt) + '\')">[copy]</span>' +
                    '</div>' +
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
                '<span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(sc.image_prompt) + '\')">[copy]</span></div>';
        }).join('');
        lfWrap.innerHTML =
            '<div class="section-divider">🎬 VIDEO 3–5 MENIT</div>' +
            '<div class="topic"><div class="topic-head">' +
                '<span class="topic-title">🎬 Video Panjang · ' + esc(lf.duration_estimate || '3–5 menit') + '</span>' +
                '<label style="font-size:11px;color:var(--text-2);display:flex;gap:6px;align-items:center;cursor:pointer;">' +
                    '<input type="checkbox" class="narrChk" data-type="long" data-text="' + esc(lf.title || 'Video panjang') + '" data-prompt="' + esc(lf.narration) + '" onchange="updateCount()"> jadwalkan</label>' +
            '</div>' +
            '<div class="narr"><div class="narr-body">' +
                '<div class="narr-text" style="font-weight:600;margin-bottom:6px;">' + esc(lf.title || '') +
                    ' <span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(lf.narration) + '\')">[copy narasi]</span></div>' +
                '<div class="narr-prompt" style="white-space:pre-wrap;font-family:inherit;line-height:1.7;">' + esc(lf.narration) + '</div>' +
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
                    '<span class="narr-copy" onclick="copyText(this,\'' + encodeURIComponent(sc.image_prompt) + '\')">[copy]</span></div>';
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
