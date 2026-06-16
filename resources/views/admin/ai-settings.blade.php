@extends('layouts.admin')

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

    .prov-item { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--border-2); flex-wrap:wrap; }
    .prov-item:last-child { border-bottom:none; }
    .prov-name { font-size:13px; color:var(--text); font-weight:500; }
    .prov-meta { font-size:11px; color:var(--text-3); }
    .prov-badge { font-size:10px; padding:2px 7px; border-radius:20px; background:var(--bg-3); color:var(--text-3); border:1px solid var(--border); }
    .prov-key-ok { color:#4ade80; } .prov-key-no { color:#f87171; }
    .btn-del { background:transparent; border:1px solid var(--border); color:var(--text-3); border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer; }
    .btn-del:hover { border-color:#ef4444; color:#ef4444; }

    @media (max-width:600px){ .row2{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

@php
    $textProviders  = $providers->filter(fn($p) => ($p->kind ?? 'text') === 'text')->values();
    $imageProviders = $providers->filter(fn($p) => ($p->kind ?? 'text') === 'image')->values();
@endphp

<div class="ai-header">
    <div>
        <h2>⚙️ Pengaturan AI</h2>
        <p>Provider teks, provider gambar &amp; penyimpanan — atur sekali, lalu pakai di AI Agent</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.ai-agent') }}" class="btn-back">← AI Agent</a>
        <a href="{{ route('admin.index') }}" class="btn-back">Panel Admin</a>
    </div>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- ===== PROVIDER TEKS ===== --}}
<details class="card" open>
    <summary>📝 Provider Teks — AI &amp; API Key ({{ $textProviders->count() }})</summary>
    <div class="card-body">
        @if($textProviders->count())
        <div style="margin-bottom:1rem;">
            @foreach($textProviders as $prov)
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
        <p style="font-size:12px;color:var(--text-3);margin-bottom:1rem;">Belum ada provider teks. Tambah di bawah (mulai dari preset gratis seperti Gemini / Groq).</p>
        @endif

        <form method="POST" action="{{ route('admin.ai-agent.provider.store') }}">
            @csrf
            <input type="hidden" name="kind" value="text">
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
            <button class="btn btn-primary" type="submit">Simpan Provider Teks</button>
        </form>
    </div>
</details>

{{-- ===== GENERATOR GAMBAR & PENYIMPANAN ===== --}}
<details class="card" open>
    <summary>🖼️ Generator Gambar &amp; Penyimpanan Cloudinary
        @if($cloudinary['cloud'] && $cloudinary['secret_set'])
            <span class="prov-key-ok" style="font-size:11px;margin-left:8px;">● aktif</span>
        @else
            <span class="prov-key-no" style="font-size:11px;margin-left:8px;">● belum diatur</span>
        @endif
    </summary>
    <div class="card-body">
        {{-- Kredensial Cloudinary --}}
        <div style="font-size:12px;color:var(--text-2);font-weight:600;margin-bottom:8px;">📦 Penyimpanan Cloudinary (gratis 25GB)</div>
        <p style="font-size:11px;color:var(--text-3);margin-bottom:10px;line-height:1.6;">
            Daftar di <b>cloudinary.com</b> → Dashboard → salin <b>Cloud Name</b>, <b>API Key</b>, <b>API Secret</b>.
            Gambar AI disimpan di Cloudinary (bukan hosting), DB cuma menyimpan URL. Server kamu tetap ringan.
        </p>
        <form method="POST" action="{{ route('admin.ai-agent.settings') }}">
            @csrf
            <div class="row2">
                <div class="fg"><label>Cloud Name</label><input type="text" name="cloudinary_cloud" class="fi" value="{{ $cloudinary['cloud'] }}" placeholder="dxxxxxx" autocomplete="off"></div>
                <div class="fg"><label>API Key</label><input type="text" name="cloudinary_key" class="fi" value="{{ $cloudinary['key'] }}" placeholder="1234567890" autocomplete="off"></div>
            </div>
            <div class="fg">
                <label>API Secret (terenkripsi){{ $cloudinary['secret_set'] ? ' — sudah tersimpan, isi untuk ganti' : '' }}</label>
                <input type="password" name="cloudinary_secret" class="fi" placeholder="{{ $cloudinary['secret_set'] ? '••••••••• (biarkan kosong jika tidak diganti)' : 'API secret' }}" autocomplete="off">
            </div>
            <button class="btn btn-primary" type="submit">Simpan Cloudinary</button>
        </form>

        <hr style="border:none;border-top:1px solid var(--border);margin:1.25rem 0;">

        {{-- Provider gambar --}}
        <div style="font-size:12px;color:var(--text-2);font-weight:600;margin-bottom:8px;">🎨 Provider Generator Gambar</div>
        @if($imageProviders->count())
        <div style="margin-bottom:1rem;">
            @foreach($imageProviders as $prov)
            <div class="prov-item">
                <div style="flex:1;min-width:0;">
                    <span class="prov-name">{{ $prov->name }}</span>
                    <span class="prov-badge">{{ $prov->format }}</span>
                    <div class="prov-meta">{{ $prov->model ?: 'default' }}
                        @if(in_array($prov->format, ['dalle','imagen'])) · @if($prov->api_key)<span class="prov-key-ok">● key terisi</span>@else<span class="prov-key-no">● key kosong</span>@endif @endif
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
        <p style="font-size:12px;color:var(--text-3);margin-bottom:1rem;">Belum ada provider gambar. <b>Pollinations</b> (gratis, tanpa API key) otomatis dipakai sebagai default. Tambah di bawah untuk pilihan lain (DALL-E / Gemini Imagen).</p>
        @endif

        <form method="POST" action="{{ route('admin.ai-agent.provider.store') }}">
            @csrf
            <input type="hidden" name="kind" value="image">
            <div class="fg">
                <label>Preset cepat</label>
                <select class="fi" id="imgPresetSelect" onchange="applyImgPreset(this.value)">
                    <option value="">— Pilih preset / isi manual —</option>
                    <option value="pollinations">Pollinations Flux (GRATIS, tanpa key)</option>
                    <option value="pollinations-turbo">Pollinations Turbo (gratis, cepat)</option>
                    <option value="gemini-image">Gemini 2.5 Flash Image / Nano Banana (key)</option>
                    <option value="imagen3">Google Imagen 3 (key + billing)</option>
                    <option value="dalle">OpenAI DALL-E 3 (butuh key)</option>
                </select>
            </div>
            <div class="row2">
                <div class="fg"><label>Nama</label><input type="text" name="name" id="iName" class="fi" placeholder="Pollinations Flux" required></div>
                <div class="fg"><label>Model</label><input type="text" name="model" id="iModel" class="fi" placeholder="flux / dall-e-3 / imagen-3.0-generate-002"></div>
            </div>
            <div class="row2">
                <div class="fg"><label>Format</label>
                    <select name="format" id="iFormat" class="fi">
                        <option value="pollinations">pollinations (gratis)</option>
                        <option value="imagen">imagen (gemini)</option>
                        <option value="dalle">dalle (openai images)</option>
                    </select>
                </div>
                <div class="fg"><label>Base URL (DALL-E / Imagen)</label><input type="text" name="base_url" id="iUrl" class="fi" placeholder="https://..."></div>
            </div>
            <div class="fg"><label>API Key (DALL-E / Imagen — terenkripsi)</label><input type="password" name="api_key" id="iKey" class="fi" placeholder="sk-... / AIza..." autocomplete="off"></div>
            <button class="btn btn-primary" type="submit">Simpan Provider Gambar</button>
        </form>
    </div>
</details>

<script>
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

var IMG_PRESETS = {
    'pollinations':       {name:'Pollinations Flux',  model:'flux',  format:'pollinations', base_url:''},
    'pollinations-turbo': {name:'Pollinations Turbo', model:'turbo', format:'pollinations', base_url:''},
    'gemini-image':       {name:'Gemini Flash Image', model:'gemini-2.5-flash-image', format:'imagen', base_url:'https://generativelanguage.googleapis.com/v1beta'},
    'imagen3':            {name:'Imagen 3',           model:'imagen-3.0-generate-002', format:'imagen', base_url:'https://generativelanguage.googleapis.com/v1beta'},
    'dalle':              {name:'DALL-E 3',           model:'dall-e-3', format:'dalle', base_url:'https://api.openai.com/v1'},
};
function applyImgPreset(k) {
    if (!k || !IMG_PRESETS[k]) return;
    var p = IMG_PRESETS[k];
    document.getElementById('iName').value = p.name;
    document.getElementById('iModel').value = p.model;
    document.getElementById('iFormat').value = p.format;
    document.getElementById('iUrl').value = p.base_url;
}
</script>

@endsection
