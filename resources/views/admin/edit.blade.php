@extends('layouts.admin')

@push('styles')
<style>
    .form-header {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 1.5rem; padding-bottom: 1rem;
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

    /* Layout: form + preview sticky */
    .edit-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }

    .form-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 16px;
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

    /* Section collapsible (native <details>) */
    .form-section {
        margin-bottom: 1rem; padding: 0;
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 10px;
        overflow: hidden;
    }
    .form-section > summary {
        font-size: 11px; color: var(--text-2); letter-spacing: 0.12em;
        text-transform: uppercase; font-weight: 600;
        padding: 1rem 1.25rem; cursor: pointer; list-style: none;
        display: flex; align-items: center; justify-content: space-between;
        transition: 0.15s;
    }
    .form-section > summary::-webkit-details-marker { display: none; }
    .form-section > summary:hover { color: var(--text); background: var(--bg-3); }
    .form-section > summary::after {
        content: 'â–¾'; font-size: 14px; color: var(--text-3); transition: transform 0.2s;
    }
    .form-section[open] > summary::after { transform: rotate(180deg); }
    .form-section[open] > summary { border-bottom: 1px solid var(--border); }
    .section-body { padding: 1.25rem; }

    .toggle-wrap { display: flex; align-items: center; gap: 10px; }
    .toggle-wrap input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; }
    .toggle-label { font-size: 13px; color: var(--text-2); }

    .preview-thumb {
        width: 100%; aspect-ratio: 16/9; border-radius: 8px;
        background: var(--bg-3); object-fit: cover; display: block;
    }

    .form-actions {
        display: flex; gap: 10px; padding-top: 1rem; margin-top: 0.5rem;
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
    .chord-guide code { font-size: 11px; color: var(--accent); font-family: monospace; line-height: 1.8; display: block; white-space: pre-wrap; }

    /* PREVIEW PANEL */
    .edit-preview { position: sticky; top: 1rem; }
    .preview-card {
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px;
        overflow: hidden;
    }
    .preview-card-label {
        font-size: 10px; color: var(--text-3); letter-spacing: 0.15em; text-transform: uppercase;
        padding: 10px 14px; border-bottom: 1px solid var(--border);
    }
    .preview-card-body { padding: 14px; }
    .preview-card-body .preview-thumb { margin-bottom: 12px; }
    .pv-title { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 4px; line-height: 1.3; }
    .pv-desc  { font-size: 12px; color: var(--text-3); line-height: 1.5; margin-bottom: 12px; min-height: 18px; }
    .pv-meta  { display: flex; flex-wrap: wrap; gap: 6px; }
    .pv-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; padding: 3px 9px; border-radius: 20px;
        background: var(--bg-3); color: var(--text-2); border: 1px solid var(--border);
    }
    .pv-badge.accent { background: var(--accent-dim); color: var(--accent); border-color: transparent; }

    @media (max-width: 900px) {
        .edit-layout { grid-template-columns: 1fr; }
        .edit-preview { position: static; order: -1; }
    }
    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="form-header">
    <a href="{{ route('admin.index') }}" class="btn-back">â† Kembali</a>
    <div>
        <h2>Edit Lagu</h2>
        <p>{{ $song->title }}</p>
    </div>
</div>

@if($errors->any())
<div style="background:#2e0d0d;color:#f87171;border:1px solid #991b1b;padding:10px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:13px;">
    @foreach($errors->all() as $error)
    <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<div class="edit-layout">
<form method="POST" action="{{ route('admin.update', $song->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- INFO DASAR --}}
    <details class="form-section" open>
        <summary>Informasi Dasar</summary>
        <div class="section-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Judul Lagu *</label>
                    <input type="text" name="title" id="fTitle" class="form-input"
                        value="{{ old('title', $song->title) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Track Number</label>
                    <input type="number" name="track_number" class="form-input"
                        value="{{ old('track_number', $song->track_number) }}">
                </div>
                <div class="form-group full">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="description" id="fDesc" class="form-input"
                        value="{{ old('description', $song->description) }}"
                        placeholder="Cerita singkat di balik lagu ini">
                </div>
                <div class="form-group full">
                    <label class="form-label">Hook Cerita</label>
                    <input type="text" name="story_hook" class="form-input"
                        value="{{ old('story_hook', $song->story_hook) }}"
                        placeholder="Kalimat pendek yang memancing rasa ingin tahu.">
                    <span class="form-hint">Maksimal 120 karakter. Tampil sebagai CTA di halaman utama.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Era</label>
                    <select name="era" class="form-input">
                        <option value="">Pilih era</option>
                        <option value="Papsi Class" {{ $song->era == 'Papsi Class' ? 'selected' : '' }}>Papsi Class (SMA)</option>
                        <option value="Papsi Class â†’ Senyawa" {{ $song->era == 'Papsi Class â†’ Senyawa' ? 'selected' : '' }}>Papsi Class â†’ Senyawa</option>
                        <option value="Senyawa" {{ $song->era == 'Senyawa' ? 'selected' : '' }}>Senyawa (Kuliah)</option>
                        <option value="Solo" {{ $song->era == 'Solo' ? 'selected' : '' }}>Solo (2012-2014)</option>
                        <option value="AI Revival" {{ $song->era == 'AI Revival' ? 'selected' : '' }}>AI Revival (2026)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Cerita Era</label>
                    <input type="text" name="era_story" class="form-input"
                        value="{{ old('era_story', $song->era_story) }}"
                        placeholder="Cerita singkat lagu ini di eranya">
                </div>
                <div class="form-group">
                    <div class="toggle-wrap">
                        <input type="checkbox" name="is_active" id="isActive" value="1"
                            {{ $song->is_active ? 'checked' : '' }}>
                        <label class="toggle-label" for="isActive">Tampilkan di halaman utama</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="toggle-wrap">
                        <input type="checkbox" name="featured" id="isFeatured" value="1"
                            {{ $song->featured ? 'checked' : '' }}>
                        <label class="toggle-label" for="isFeatured">Jadikan CTA unggulan</label>
                    </div>
                </div>
            </div>
        </div>
    </details>

    {{-- MEDIA & TAUTAN --}}
    <details class="form-section" open>
        <summary>Media &amp; Tautan</summary>
        <div class="section-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">YouTube ID *</label>
                    <input type="text" name="youtube_id" class="form-input" id="ytInput"
                        value="{{ old('youtube_id', $song->youtube_id) }}"
                        oninput="updateThumb(this.value)" required>
                    <span class="form-hint">Contoh: TG8oAcVRnzA (bukan full URL)</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Preview Thumbnail</label>
                    <img id="thumbPreview"
                        src="https://img.youtube.com/vi/{{ $song->youtube_id }}/mqdefault.jpg"
                        class="preview-thumb" alt="thumbnail">
                </div>
                <div class="form-group full">
                    <label class="form-label">File Audio MP3 (untuk pemutar musik komunitas)</label>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        @if($song->audio_file)
                        <audio controls style="height:32px;flex:1;min-width:200px;">
                            <source src="{{ asset($song->audio_file) }}" type="audio/mpeg">
                        </audio>
                        @else
                        <span style="font-size:12px;color:var(--text-3);">Belum ada file audio</span>
                        @endif
                        <input type="file" name="audio_file" accept="audio/mp3,audio/*" class="form-input" style="flex:1;min-width:200px;">
                    </div>
                    <span class="form-hint">MP3/WAV/OGG, maks 10MB. Bisa versi akustik/demo â€” beda dari versi YouTube.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Spotify URL</label>
                    <input type="text" name="spotify_url" class="form-input"
                        value="{{ old('spotify_url', $song->spotify_url) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Apple Music URL</label>
                    <input type="text" name="apple_music_url" class="form-input"
                        value="{{ old('apple_music_url', $song->apple_music_url) }}">
                </div>
            </div>
        </div>
    </details>

    {{-- CHORD & KEY --}}
    <details class="form-section">
        <summary>Chord &amp; Nada Dasar</summary>
        <div class="section-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nada Dasar (Key)</label>
                    <input type="text" name="key_signature" id="fKey" class="form-input"
                        value="{{ old('key_signature', $song->key_signature) }}"
                        placeholder="Contoh: C, Am, G">
                </div>
                <div class="form-group">
                    <label class="form-label">Tempo (BPM)</label>
                    <input type="number" name="tempo" id="fTempo" class="form-input"
                        value="{{ old('tempo', $song->tempo) }}"
                        placeholder="Contoh: 72">
                </div>
                <div class="form-group full">
                    <label class="form-label">Chord</label>
                    <textarea name="chords" class="form-textarea"
                        placeholder="Tulis chord di sini...">{{ old('chords', $song->chords) }}</textarea>
                    <div class="chord-guide">
                        <p>Format penulisan chord:</p>
                        <code>[Intro]
C  G  Am  F

[Verse]
C              G
Padamkan sejenak bara egomu
Am             F
Bersihkan semua isi di hatimu

[Chorus]
F                    C
Aku coba memohon, agar engkau mengerti</code>
                    </div>
                </div>
            </div>
        </div>
    </details>

    {{-- LIRIK --}}
    <details class="form-section">
        <summary>Lirik</summary>
        <div class="section-body">
            <div class="form-group">
                <label class="form-label">Lirik Lengkap</label>
                <textarea name="lyrics" class="form-textarea" style="min-height:300px;"
                    placeholder="Tulis lirik lengkap di sini...">{{ old('lyrics', $song->lyrics) }}</textarea>
            </div>
        </div>
    </details>

    <div class="form-actions">
        <button type="submit" class="btn-save">Simpan Perubahan</button>
        <a href="{{ route('admin.index') }}" class="btn-cancel">Batal</a>
    </div>
</form>

    {{-- PREVIEW --}}
    <div class="edit-preview">
        <div class="preview-card">
            <div class="preview-card-label">Preview</div>
            <div class="preview-card-body">
                <img id="pvThumb"
                    src="https://img.youtube.com/vi/{{ $song->youtube_id }}/mqdefault.jpg"
                    class="preview-thumb" alt="preview">
                <div class="pv-title" id="pvTitle">{{ $song->title }}</div>
                <div class="pv-desc" id="pvDesc">{{ $song->description }}</div>
                <div class="pv-meta">
                    <span class="pv-badge accent" id="pvKey" style="{{ $song->key_signature ? '' : 'display:none' }}">â™ª {{ $song->key_signature }}</span>
                    <span class="pv-badge" id="pvTempo" style="{{ $song->tempo ? '' : 'display:none' }}">{{ $song->tempo }} BPM</span>
                    <span class="pv-badge" id="pvEra" style="{{ $song->era ? '' : 'display:none' }}">{{ $song->era }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateThumb(val) {
    if (val.length > 5) {
        var url = 'https://img.youtube.com/vi/' + val + '/mqdefault.jpg';
        document.getElementById('thumbPreview').src = url;
        document.getElementById('pvThumb').src = url;
    }
}

// Sinkron preview realtime
function bindPv(inputId, handler) {
    var el = document.getElementById(inputId);
    if (el) el.addEventListener('input', handler);
}
bindPv('fTitle', function(e){ document.getElementById('pvTitle').textContent = e.target.value || 'Tanpa judul'; });
bindPv('fDesc',  function(e){ document.getElementById('pvDesc').textContent = e.target.value; });
bindPv('fKey',   function(e){
    var b = document.getElementById('pvKey');
    if (e.target.value.trim()) { b.style.display = ''; b.textContent = 'â™ª ' + e.target.value; }
    else b.style.display = 'none';
});
bindPv('fTempo', function(e){
    var b = document.getElementById('pvTempo');
    if (e.target.value.trim()) { b.style.display = ''; b.textContent = e.target.value + ' BPM'; }
    else b.style.display = 'none';
});
</script>

@endsection

