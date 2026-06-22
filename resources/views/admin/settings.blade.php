?@extends('layouts.admin')

@push('styles')
<style>
    .settings-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .settings-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .settings-header p  { font-size: 12px; color: var(--text-3); margin-top: 2px; }

    .settings-nav {
        display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 2rem;
        border-bottom: 1px solid var(--border); padding-bottom: 0;
    }
    .settings-tab {
        padding: 8px 16px; font-size: 12px; color: var(--text-3);
        background: transparent; border: none; cursor: pointer;
        border-bottom: 2px solid transparent; transition: 0.15s;
        margin-bottom: -1px;
    }
    .settings-tab:hover { color: var(--text-2); }
    .settings-tab.active { color: var(--text); border-bottom-color: var(--accent); }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .form-section {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem;
    }
    .form-section-title {
        font-size: 11px; color: var(--text-3); letter-spacing: 0.15em;
        text-transform: uppercase; margin-bottom: 1.25rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
    }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
    .form-hint  { font-size: 11px; color: var(--text-3); margin-top: 2px; }
    .form-input {
        background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: inherit; width: 100%;
    }
    .form-input:focus { border-color: var(--text-3); }
    .form-textarea {
        background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: inherit; resize: vertical;
        min-height: 100px; line-height: 1.7; width: 100%;
    }
    .form-textarea:focus { border-color: var(--text-3); }

    /* PHOTO UPLOAD */
    .photo-upload-wrap { display: flex; align-items: center; gap: 16px; }
    .photo-preview {
        width: 80px; height: 80px; border-radius: 50%;
        object-fit: cover; background: var(--bg-3); border: 1px solid var(--border);
        flex-shrink: 0;
    }
    .photo-upload-btn {
        padding: 7px 16px; border-radius: 8px; font-size: 12px;
        border: 1px solid var(--border); color: var(--text-2); background: transparent;
        cursor: pointer; transition: 0.15s;
    }
    .photo-upload-btn:hover { color: var(--text); border-color: var(--text-3); }

    /* TAGLINE PREVIEW */
    .tagline-preview {
        background: var(--bg-3); border: 1px solid var(--border-2); border-radius: 8px;
        padding: 1.25rem; margin-top: 12px;
    }
    .tagline-preview p { font-size: 11px; color: var(--text-3); margin-bottom: 8px; letter-spacing: 0.1em; text-transform: uppercase; }
    .tagline-preview-text {
        font-size: 1.4rem; font-weight: 300; letter-spacing: 0.1em;
        color: var(--text); line-height: 1.4;
    }

    .alert-success {
        background: #0d2e1a; color: #4ade80; border: 1px solid #166534;
        padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 13px;
    }

    .form-actions {
        display: flex; gap: 10px; padding-top: 1rem;
        border-top: 1px solid var(--border); margin-top: 1rem;
    }
    .btn-save {
        padding: 9px 24px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: var(--text); color: var(--bg);
        border: none; cursor: pointer; transition: 0.2s;
    }
    .btn-save:hover { filter: brightness(0.88); }
    .btn-back {
        padding: 9px 20px; border-radius: 8px; font-size: 13px;
        border: 1px solid var(--border); color: var(--text-2); background: transparent;
        text-decoration: none; transition: 0.15s;
    }
    .btn-back:hover { color: var(--text); border-color: var(--text-3); }

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
    <button class="settings-tab" onclick="showTab('feat-screenshots')">📱 Screenshot Fitur</button>
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
                <label class="form-label">Bio</label>
                @php
                    $bioRaw = $settings['bio']->value ?? '';
                    // konversi sisa HTML lama (Quill) ke teks biasa
                    $bioText = trim(html_entity_decode(strip_tags(
                        str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>'], "\n", $bioRaw)
                    )));
                @endphp
                <textarea name="bio" class="form-textarea" style="min-height:180px;"
                    placeholder="Tulis biografi / deskripsi project di sini...">{{ $bioText }}</textarea>
                <span class="form-hint">Teks biasa. Pisahkan paragraf dengan baris kosong.</span>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
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

    {{-- TAB: SCREENSHOT FITUR --}}
    <div class="tab-panel" id="tab-feat-screenshots">
        <div class="form-section">
            <p class="form-section-title">Screenshot Fitur di Landing Page</p>
            <p class="form-hint" style="margin-bottom:1.25rem;">Upload screenshot asli dari aplikasi untuk ditampilkan di dalam bingkai HP pada halaman utama. Format JPG/PNG, rasio 9:16 (portrait) disarankan, maks 2MB. Jika kosong, tampilan CSS otomatis digunakan.</p>

            @php
            $featMeta = [
                ['key'=>'feat_screenshot_0','label'=>'Maftuner','desc'=>'Tampilan tuner gitar'],
                ['key'=>'feat_screenshot_1','label'=>'Belajar Chord','desc'=>'Kamus chord gitar/piano/ukulele'],
                ['key'=>'feat_screenshot_2','label'=>'Portofolio Musisi','desc'=>'Halaman profil musisi'],
                ['key'=>'feat_screenshot_3','label'=>'Cari Personil','desc'=>'Direktori & filter musisi'],
                ['key'=>'feat_screenshot_4','label'=>'Chat','desc'=>'Percakapan antar musisi'],
                ['key'=>'feat_screenshot_5','label'=>'Posting','desc'=>'Feed komunitas'],
            ];
            @endphp

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;">
                @foreach($featMeta as $fm)
                @php $currentPath = $settings[$fm['key']]->value ?? ''; @endphp
                <div style="border:1px solid var(--border);border-radius:12px;padding:1rem;background:var(--bg-2);text-align:center;">
                    {{-- Phone mini preview --}}
                    <div style="width:80px;height:148px;margin:0 auto 0.75rem;background:#0d0d0f;border-radius:14px;border:2px solid #2a2a30;overflow:hidden;position:relative;box-shadow:0 8px 24px -8px rgba(0,0,0,0.5);">
                        @if($currentPath)
                            <img id="prev_{{ $fm['key'] }}"
                                 src="{{ asset($currentPath) }}?v={{ filemtime(public_path($currentPath)) }}"
                                 alt="{{ $fm['label'] }}"
                                 style="width:100%;height:100%;object-fit:cover;display:block;">
                        @else
                            <img id="prev_{{ $fm['key'] }}"
                                 src="" alt=""
                                 style="width:100%;height:100%;object-fit:cover;display:{{ $currentPath ? 'block' : 'none' }};">
                            <div id="empty_{{ $fm['key'] }}" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.2);font-size:22px;">📱</div>
                        @endif
                    </div>

                    <div style="font-size:12px;font-weight:600;color:var(--text);margin-bottom:2px;">{{ $fm['label'] }}</div>
                    <div style="font-size:10px;color:var(--text-3);margin-bottom:0.75rem;">{{ $fm['desc'] }}</div>

                    <input type="file" name="{{ $fm['key'] }}" id="fi_{{ $fm['key'] }}"
                           accept="image/*" style="display:none"
                           onchange="previewFeat('{{ $fm['key'] }}', this)">
                    <button type="button"
                            onclick="document.getElementById('fi_{{ $fm['key'] }}').click()"
                            style="font-size:11px;padding:5px 12px;border-radius:8px;border:1px solid var(--border);background:var(--surface);color:var(--text-2);cursor:pointer;width:100%;">
                        {{ $currentPath ? '↩ Ganti' : '⬆ Upload' }}
                    </button>
                    @if($currentPath)
                    <div style="margin-top:6px;">
                        <label style="font-size:10px;color:var(--text-3);cursor:pointer;">
                            <input type="checkbox" name="clear_{{ $fm['key'] }}" value="1" style="margin-right:3px;">
                            Hapus screenshot ini
                        </label>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan Screenshot</button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
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

function previewFeat(key, input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img   = document.getElementById('prev_' + key);
            var empty = document.getElementById('empty_' + key);
            if (img) { img.src = e.target.result; img.style.display = 'block'; }
            if (empty) empty.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Feedback tombol saat menyimpan
document.querySelector('form').addEventListener('submit', function() {
    document.querySelectorAll('.btn-save').forEach(function(b){
        b.textContent = 'Menyimpan…'; b.disabled = true; b.style.opacity = '0.7';
    });
});
</script>
@endpush

