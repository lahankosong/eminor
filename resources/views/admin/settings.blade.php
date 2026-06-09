@extends('layouts.app')

@push('styles')
<style>
    .settings-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid #1a1a1a;
    }
    .settings-header h2 { font-size: 1rem; font-weight: 500; }
    .settings-header p  { font-size: 12px; color: #555; margin-top: 2px; }

    .settings-nav {
        display: flex; gap: 4px; margin-bottom: 2rem;
        border-bottom: 1px solid #111; padding-bottom: 0;
    }
    .settings-tab {
        padding: 8px 16px; font-size: 12px; color: #555;
        background: transparent; border: none; cursor: pointer;
        border-bottom: 2px solid transparent; transition: 0.15s;
        margin-bottom: -1px;
    }
    .settings-tab:hover { color: #aaa; }
    .settings-tab.active { color: #fff; border-bottom-color: #fff; }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .form-section {
        background: #0d0d0d; border: 1px solid #1a1a1a;
        border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem;
    }
    .form-section-title {
        font-size: 11px; color: #555; letter-spacing: 0.15em;
        text-transform: uppercase; margin-bottom: 1.25rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid #1a1a1a;
    }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-hint  { font-size: 11px; color: #333; margin-top: 2px; }
    .form-input {
        background: #111; border: 1px solid #2a2a2a; border-radius: 8px;
        color: #fff; font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: inherit; width: 100%;
    }
    .form-input:focus { border-color: #555; }
    .form-textarea {
        background: #111; border: 1px solid #2a2a2a; border-radius: 8px;
        color: #fff; font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: inherit; resize: vertical;
        min-height: 100px; line-height: 1.7; width: 100%;
    }
    .form-textarea:focus { border-color: #555; }

    /* QUILL EDITOR */
    .quill-wrap { border-radius: 8px; overflow: hidden; }
    .ql-toolbar {
        background: #111 !important; border: 1px solid #2a2a2a !important;
        border-bottom: 1px solid #1a1a1a !important; border-radius: 8px 8px 0 0 !important;
    }
    .ql-container {
        background: #111 !important; border: 1px solid #2a2a2a !important;
        border-top: none !important; border-radius: 0 0 8px 8px !important;
        font-size: 13px !important; min-height: 150px;
    }
    .ql-editor { color: #ccc !important; min-height: 150px; line-height: 1.7; }
    .ql-toolbar .ql-stroke { stroke: #666 !important; }
    .ql-toolbar .ql-fill   { fill: #666 !important; }
    .ql-toolbar button:hover .ql-stroke { stroke: #fff !important; }
    .ql-toolbar button:hover .ql-fill   { fill: #fff !important; }
    .ql-toolbar .ql-picker-label { color: #666 !important; }

    /* PHOTO UPLOAD */
    .photo-upload-wrap { display: flex; align-items: center; gap: 16px; }
    .photo-preview {
        width: 80px; height: 80px; border-radius: 50%;
        object-fit: cover; background: #111; border: 1px solid #2a2a2a;
        flex-shrink: 0;
    }
    .photo-upload-btn {
        padding: 7px 16px; border-radius: 8px; font-size: 12px;
        border: 1px solid #2a2a2a; color: #888; background: transparent;
        cursor: pointer; transition: 0.15s;
    }
    .photo-upload-btn:hover { color: #fff; border-color: #555; }

    /* TAGLINE PREVIEW */
    .tagline-preview {
        background: #080808; border: 1px solid #111; border-radius: 8px;
        padding: 1.25rem; margin-top: 12px;
    }
    .tagline-preview p { font-size: 11px; color: #333; margin-bottom: 8px; letter-spacing: 0.1em; text-transform: uppercase; }
    .tagline-preview-text {
        font-size: 1.4rem; font-weight: 300; letter-spacing: 0.1em;
        color: #fff; line-height: 1.4;
    }

    .alert-success {
        background: #0d2e1a; color: #4ade80; border: 1px solid #166534;
        padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 13px;
    }

    .form-actions {
        display: flex; gap: 10px; padding-top: 1rem;
        border-top: 1px solid #1a1a1a; margin-top: 1rem;
    }
    .btn-save {
        padding: 9px 24px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: #fff; color: #000;
        border: none; cursor: pointer; transition: 0.2s;
    }
    .btn-save:hover { background: #ddd; }
    .btn-back {
        padding: 9px 20px; border-radius: 8px; font-size: 13px;
        border: 1px solid #2a2a2a; color: #888; background: transparent;
        text-decoration: none; transition: 0.15s;
    }
    .btn-back:hover { color: #fff; border-color: #555; }

    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="settings-header">
    <div>
        <h2>Pengaturan Website</h2>
        <p>Kelola identitas, konten, dan tampilan — tanpa sentuh kode</p>
    </div>
    <a href="{{ route('admin.index') }}" class="btn-back">← Panel Admin</a>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<div class="settings-nav">
    <button class="settings-tab active" onclick="showTab('identity')">Identitas</button>
    <button class="settings-tab" onclick="showTab('hero')">Hero & Tagline</button>
    <button class="settings-tab" onclick="showTab('bio')">Bio & Project</button>
    <button class="settings-tab" onclick="showTab('social')">Platform & Sosial</button>
    <button class="settings-tab" onclick="showTab('community')">Komunitas</button>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf

    {{-- TAB: IDENTITAS --}}
    <div class="tab-panel active" id="tab-identity">
        <div class="form-section">
            <p class="form-section-title">Foto & Nama</p>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Foto Profil</label>
                    <div class="photo-upload-wrap">
                        @php $photoVal = $settings['artist_photo']->value ?? ''; @endphp
                        <img id="photoPreview"
                            src="{{ $photoVal ? asset($photoVal) : asset('images/margonoandi.jpg') }}"
                            class="photo-preview" alt="foto">
                        <div>
                            <input type="file" name="artist_photo" id="photoInput"
                                accept="image/*" style="display:none"
                                onchange="previewPhoto(this)">
                            <button type="button" class="photo-upload-btn"
                                onclick="document.getElementById('photoInput').click()">
                                Ganti foto
                            </button>
                            <p class="form-hint" style="margin-top:6px;">JPG/PNG, maks 2MB. Akan disimpan sebagai foto profil utama.</p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Artis / Songwriter</label>
                    <input type="text" name="artist_name" class="form-input"
                        value="{{ $settings['artist_name']->value ?? 'Rakhman Andi' }}"
                        placeholder="Rakhman Andi">
                </div>
                <div class="form-group">
                    <label class="form-label">Role / Profesi</label>
                    <input type="text" name="artist_role" class="form-input"
                        value="{{ $settings['artist_role']->value ?? '' }}"
                        placeholder="Songwriter · Musisi Independent">
                </div>
                <div class="form-group full">
                    <label class="form-label">Project / Album Fokus</label>
                    <input type="text" name="artist_project" class="form-input"
                        value="{{ $settings['artist_project']->value ?? '' }}"
                        placeholder="Margonoandi — 15 Lagu 2000–2026">
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
        </div>
    </div>

    {{-- TAB: HERO & TAGLINE --}}
    <div class="tab-panel" id="tab-hero">
        <div class="form-section">
            <p class="form-section-title">Tagline Hero (3 baris)</p>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Baris 1</label>
                    <input type="text" name="tagline_1" class="form-input" id="tl1"
                        value="{{ $settings['tagline_1']->value ?? 'Tiga chord.' }}"
                        oninput="updateTaglinePreview()">
                </div>
                <div class="form-group">
                    <label class="form-label">Baris 2</label>
                    <input type="text" name="tagline_2" class="form-input" id="tl2"
                        value="{{ $settings['tagline_2']->value ?? 'Satu rindu.' }}"
                        oninput="updateTaglinePreview()">
                </div>
                <div class="form-group">
                    <label class="form-label">Baris 3</label>
                    <input type="text" name="tagline_3" class="form-input" id="tl3"
                        value="{{ $settings['tagline_3']->value ?? 'Dua puluh tahun.' }}"
                        oninput="updateTaglinePreview()">
                </div>
                <div class="form-group">
                    <label class="form-label">Preview</label>
                    <div class="tagline-preview">
                        <p>Tampilan di hero</p>
                        <div class="tagline-preview-text" id="taglinePreview">
                            {{ $settings['tagline_1']->value ?? 'Tiga chord.' }}<br>
                            {{ $settings['tagline_2']->value ?? 'Satu rindu.' }}<br>
                            {{ $settings['tagline_3']->value ?? 'Dua puluh tahun.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-section">
            <p class="form-section-title">Teks Cerita di Hero</p>
            <div class="form-group">
                <label class="form-label">Paragraf singkat (tampil di bawah tagline)</label>
                <textarea name="hero_story" class="form-textarea" style="min-height:120px;">{{ $settings['hero_story']->value ?? '' }}</textarea>
                <span class="form-hint">Gunakan *teks* untuk cetak miring (emphasis). Maksimal 3-4 kalimat.</span>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
        </div>
    </div>

    {{-- TAB: BIO & PROJECT --}}
    <div class="tab-panel" id="tab-bio">
        <div class="form-section">
            <p class="form-section-title">Biografi & Deskripsi Project</p>
            <div class="form-group full">
                <label class="form-label">Bio (tampil di halaman About)</label>
                <div class="quill-wrap">
                    <div id="bioEditor">{!! $settings['bio']->value ?? '' !!}</div>
                </div>
                <input type="hidden" name="bio" id="bioInput">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save" onclick="saveBio()">Simpan</button>
        </div>
    </div>

    {{-- TAB: PLATFORM --}}
    <div class="tab-panel" id="tab-social">
        <div class="form-section">
            <p class="form-section-title">Link Platform Streaming</p>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Spotify Playlist URL</label>
                    <input type="text" name="spotify_url" class="form-input"
                        value="{{ $settings['spotify_url']->value ?? '' }}"
                        placeholder="https://open.spotify.com/playlist/...">
                </div>
                <div class="form-group full">
                    <label class="form-label">YouTube Channel URL</label>
                    <input type="text" name="youtube_url" class="form-input"
                        value="{{ $settings['youtube_url']->value ?? '' }}"
                        placeholder="https://youtube.com/@...">
                </div>
                <div class="form-group full">
                    <label class="form-label">Apple Music URL</label>
                    <input type="text" name="apple_music_url" class="form-input"
                        value="{{ $settings['apple_music_url']->value ?? '' }}"
                        placeholder="https://music.apple.com/...">
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
        </div>
    </div>

    {{-- TAB: KOMUNITAS --}}
    <div class="tab-panel" id="tab-community">
        <div class="form-section">
            <p class="form-section-title">Ajakan Bergabung Komunitas</p>
            <div class="form-group">
                <label class="form-label">Tagline Komunitas</label>
                <textarea name="community_tagline" class="form-textarea">{{ $settings['community_tagline']->value ?? '' }}</textarea>
                <span class="form-hint">Tampil sebagai CTA ajakan bergabung di halaman utama.</span>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
var quill = new Quill('#bioEditor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['clean']
        ]
    }
});

function saveBio() {
    document.getElementById('bioInput').value = quill.root.innerHTML;
}

function showTab(name) {
    document.querySelectorAll('.tab-panel').forEach(function(el) {
        el.classList.remove('active');
    });
    document.querySelectorAll('.settings-tab').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById('tab-' + name).classList.add('active');
    event.target.classList.add('active');
}

function updateTaglinePreview() {
    var t1 = document.getElementById('tl1').value || '';
    var t2 = document.getElementById('tl2').value || '';
    var t3 = document.getElementById('tl3').value || '';
    document.getElementById('taglinePreview').innerHTML =
        t1 + '<br>' + t2 + '<br>' + t3;
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelector('form').addEventListener('submit', function() {
    if (document.getElementById('tab-bio').classList.contains('active')) {
        saveBio();
    }
});
</script>
@endpush