@extends('layouts.app')

@push('styles')
<style>
    .form-header {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .btn-back {
        padding: 6px 14px; border-radius: 8px; font-size: 12px;
        border: 1px solid var(--border); color: var(--text-2); text-decoration: none;
        transition: 0.15s;
    }
    .btn-back:hover { color: var(--text); border-color: var(--text-3); }
    .form-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .form-header p { font-size: 12px; color: var(--text-3); margin-top: 2px; }

    .form-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 16px; margin-bottom: 1.5rem;
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { font-size: 11px; color: var(--text-3); letter-spacing: 0.05em; text-transform: uppercase; }
    .form-input {
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: inherit;
    }
    .form-input:focus { border-color: var(--text-3); }
    .form-textarea {
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; padding: 9px 12px; outline: none;
        transition: 0.15s; font-family: 'Courier New', monospace;
        resize: vertical; min-height: 200px; line-height: 1.8;
    }
    .form-textarea:focus { border-color: var(--text-3); }
    .form-hint { font-size: 11px; color: var(--text-3); margin-top: 2px; }

    .form-section {
        margin-bottom: 2rem; padding: 1.25rem;
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 10px;
    }
    .form-section-title {
        font-size: 11px; color: var(--text-3); letter-spacing: 0.15em;
        text-transform: uppercase; margin-bottom: 1.25rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
    }

    .preview-thumb {
        width: 100%; aspect-ratio: 16/9; border-radius: 8px;
        background: var(--bg-3); object-fit: cover; display: block;
    }

    .form-actions {
        display: flex; gap: 10px; padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    .btn-save {
        padding: 9px 24px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: var(--text); color: var(--bg);
        border: none; cursor: pointer; transition: 0.2s;
    }
    .btn-save:hover { filter: brightness(0.88); }
    .btn-cancel {
        padding: 9px 20px; border-radius: 8px; font-size: 13px;
        border: 1px solid var(--border); color: var(--text-2); background: transparent;
        text-decoration: none; transition: 0.15s;
    }
    .btn-cancel:hover { color: var(--text); border-color: var(--text-3); }

    .chord-guide {
        background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px;
        padding: 1rem; margin-top: 8px;
    }
    .chord-guide p { font-size: 11px; color: var(--text-3); margin-bottom: 6px; }
    .chord-guide code { font-size: 11px; color: var(--accent); font-family: monospace; line-height: 1.8; display: block; }

    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="form-header">
    <a href="{{ route('admin.index') }}" class="btn-back">← Kembali</a>
    <div>
        <h2>Tambah Lagu Baru</h2>
        <p>Isi informasi lagu yang ingin ditambahkan</p>
    </div>
</div>

@if($errors->any())
<div style="background:#2e0d0d;color:#f87171;border:1px solid #991b1b;padding:10px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:13px;">
    @foreach($errors->all() as $error)
    <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('admin.store') }}">
    @csrf

    <div class="form-section">
        <p class="form-section-title">Informasi Lagu</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Judul Lagu *</label>
                <input type="text" name="title" class="form-input"
                    value="{{ old('title') }}" required placeholder="Judul lagu">
            </div>
            <div class="form-group">
                <label class="form-label">YouTube ID *</label>
                <input type="text" name="youtube_id" class="form-input"
                    value="{{ old('youtube_id') }}" id="ytInput"
                    oninput="updateThumb(this.value)" required
                    placeholder="Contoh: TG8oAcVRnzA">
                <span class="form-hint">Ambil dari URL youtube.com/watch?v=XXXX</span>
            </div>
            <div class="form-group full">
                <label class="form-label">Preview Thumbnail</label>
                <img id="thumbPreview"
                    src="https://img.youtube.com/vi/default/mqdefault.jpg"
                    class="preview-thumb" alt="thumbnail">
            </div>
            <div class="form-group">
                <label class="form-label">Spotify URL</label>
                <input type="text" name="spotify_url" class="form-input"
                    value="{{ old('spotify_url') }}" placeholder="https://open.spotify.com/track/...">
            </div>
            <div class="form-group">
                <label class="form-label">Apple Music URL</label>
                <input type="text" name="apple_music_url" class="form-input"
                    value="{{ old('apple_music_url') }}" placeholder="https://music.apple.com/...">
            </div>
            <div class="form-group full">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-input"
                    value="{{ old('description') }}"
                    placeholder="Cerita singkat di balik lagu ini">
            </div>
        </div>
    </div>

    <div class="form-section">
        <p class="form-section-title">Chord & Nada Dasar</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nada Dasar (Key)</label>
                <input type="text" name="key_signature" class="form-input"
                    value="{{ old('key_signature') }}" placeholder="Contoh: C, Am, G">
            </div>
            <div class="form-group">
                <label class="form-label">Tempo (BPM)</label>
                <input type="number" name="tempo" class="form-input"
                    value="{{ old('tempo') }}" placeholder="Contoh: 72">
            </div>
            <div class="form-group full">
                <label class="form-label">Chord</label>
                <textarea name="chords" class="form-textarea"
                    placeholder="Tulis chord di sini...">{{ old('chords') }}</textarea>
                <div class="chord-guide">
                    <p>Format penulisan chord:</p>
                    <code>[Intro]
C  G  Am  F

[Verse]
C              G
Padamkan sejenak bara egomu

[Chorus]
F                    C
Aku coba memohon, agar engkau mengerti</code>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <p class="form-section-title">Lirik</p>
        <div class="form-group">
            <label class="form-label">Lirik Lengkap</label>
            <textarea name="lyrics" class="form-textarea" style="min-height:300px;"
                placeholder="Tulis lirik lengkap di sini...">{{ old('lyrics') }}</textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-save">Simpan Lagu</button>
        <a href="{{ route('admin.index') }}" class="btn-cancel">Batal</a>
    </div>
</form>

<script>
function updateThumb(val) {
    if (val.length > 5) {
        document.getElementById('thumbPreview').src =
            'https://img.youtube.com/vi/' + val + '/mqdefault.jpg';
    }
}
</script>

@endsection
