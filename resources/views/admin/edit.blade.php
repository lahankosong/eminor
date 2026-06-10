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

    .toggle-wrap { display: flex; align-items: center; gap: 10px; }
    .toggle-wrap input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; }
    .toggle-label { font-size: 13px; color: var(--text-2); }

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

    .chord-detector {
        background: var(--bg-3); border: 1px solid var(--accent-dim);
        border-radius: 8px; padding: 1rem; margin-bottom: 12px;
    }
    .detector-title {
        font-size: 11px; color: var(--text-3); letter-spacing: 0.1em;
        text-transform: uppercase; margin-bottom: 10px;
    }
    .detector-upload { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .btn-upload {
        padding: 6px 16px; border-radius: 6px; font-size: 12px;
        border: 1px solid var(--accent-dim); color: var(--accent); background: transparent;
        cursor: pointer; transition: 0.15s; white-space: nowrap;
    }
    .btn-upload:hover { background: var(--accent-dim); }
    .file-name { font-size: 11px; color: var(--text-3); }
    .progress-bar {
        background: var(--bg-4); border-radius: 4px;
        height: 4px; margin: 10px 0 6px; overflow: hidden;
    }
    .progress-fill {
        height: 100%; background: var(--accent);
        width: 0%; transition: width 0.3s;
    }
    .progress-text { font-size: 11px; color: var(--text-3); }
    .detector-result { margin-top: 10px; }
    .result-row { display: flex; gap: 16px; margin-bottom: 10px; }
    .result-item { display: flex; flex-direction: column; gap: 3px; }
    .result-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.1em; }
    .result-value { font-size: 18px; font-weight: 500; color: var(--accent); }
    .btn-apply {
        padding: 6px 16px; border-radius: 6px; font-size: 12px;
        border: 1px solid rgba(74,222,128,0.3); color: #4ade80; background: transparent;
        cursor: pointer; transition: 0.15s;
    }
    .btn-apply:hover { background: rgba(74,222,128,0.08); }
</style>
@endpush

@section('content')

<div class="form-header">
    <a href="{{ route('admin.index') }}" class="btn-back">← Kembali</a>
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

<form method="POST" action="{{ route('admin.update', $song->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- INFO DASAR --}}
    <div class="form-section">
        <p class="form-section-title">Informasi Lagu</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Judul Lagu *</label>
                <input type="text" name="title" class="form-input"
                    value="{{ old('title', $song->title) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Track Number</label>
                <input type="number" name="track_number" class="form-input"
                    value="{{ old('track_number', $song->track_number) }}">
            </div>
            <div class="form-group">
                <label class="form-label">YouTube ID *</label>
                <div class="form-group full">
                    <label class="form-label">File Audio MP3 (untuk player komunitas)</label>
                    <div style="display:flex;align-items:center;gap:12px;">
                        @if($song->audio_file)
                        <audio controls style="height:32px;flex:1;">
                            <source src="{{ asset($song->audio_file) }}" type="audio/mpeg">
                        </audio>
                        @else
                        <span style="font-size:12px;color:var(--text-3);">Belum ada file audio</span>
                        @endif
                        <input type="file" name="audio_file" accept="audio/mp3,audio/*" class="form-input" style="flex:1;">
                    </div>
                    <span class="form-hint">MP3, max 20MB. Berbeda dari versi YouTube — bisa versi akustik atau demo.</span>
                </div>
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
            <div class="form-group full">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-input"
                    value="{{ old('description', $song->description) }}"
                    placeholder="Cerita singkat di balik lagu ini">
            </div>
            <div class="form-group full">
                <label class="form-label">Hook Cerita</label>
                <input type="text" name="story_hook" class="form-input"
                    value="{{ old('story_hook', $song->story_hook) }}"
                    placeholder="Kalimat pendek yang memancing rasa ingin tahu. Contoh: Lagu ini lahir dari pertengkaran yang tidak pernah selesai.">
                <span class="form-hint">Maksimal 120 karakter. Tampil sebagai CTA di halaman utama.</span>
            </div>
            <div class="form-group">
                <div class="toggle-wrap">
                    <input type="checkbox" name="featured" id="isFeatured" value="1"
                        {{ $song->featured ? 'checked' : '' }}>
                    <label class="toggle-label" for="isFeatured">Tampilkan sebagai CTA di halaman utama</label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Era</label>
                <select name="era" class="form-input">
                    <option value="">Pilih era</option>
                    <option value="Papsi Class" {{ $song->era == 'Papsi Class' ? 'selected' : '' }}>Papsi Class (SMA)</option>
                    <option value="Papsi Class → Senyawa" {{ $song->era == 'Papsi Class → Senyawa' ? 'selected' : '' }}>Papsi Class → Senyawa</option>
                    <option value="Senyawa" {{ $song->era == 'Senyawa' ? 'selected' : '' }}>Senyawa (Kuliah)</option>
                    <option value="Solo" {{ $song->era == 'Solo' ? 'selected' : '' }}>Solo (2012-2014)</option>
                    <option value="AI Revival" {{ $song->era == 'AI Revival' ? 'selected' : '' }}>AI Revival (2026)</option>
                </select>
            </div>
            <div class="form-group full">
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
        </div>
    </div>

    {{-- CHORD & KEY --}}
    <div class="form-section">
        <p class="form-section-title">Chord & Nada Dasar</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nada Dasar (Key)</label>
                <input type="text" name="key_signature" class="form-input"
                    value="{{ old('key_signature', $song->key_signature) }}"
                    placeholder="Contoh: C, Am, G">
            </div>
            <div class="form-group">
                <label class="form-label">Tempo (BPM)</label>
                <input type="number" name="tempo" class="form-input"
                    value="{{ old('tempo', $song->tempo) }}"
                    placeholder="Contoh: 72">
            </div>
            <div class="form-group full">
                <label class="form-label">Chord</label>
                <textarea name="chords" class="form-textarea"
                    placeholder="Tulis chord di sini...">{{ old('chords', $song->chords) }}</textarea>

                {{-- CHORD DETECTOR --}}
                <div class="chord-detector">
                    <p class="detector-title">Chord Detector Otomatis</p>
                    <div class="detector-upload">
                        <input type="file" id="audioFile" accept=".mp3,.wav,.m4a" style="display:none">
                        <button type="button" class="btn-upload" onclick="document.getElementById('audioFile').click()">
                            &#9836; Upload MP3 untuk deteksi chord
                        </button>
                        <span class="file-name" id="fileName">Belum ada file dipilih</span>
                    </div>
                    <div class="detector-progress" id="detectorProgress" style="display:none">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <p class="progress-text" id="progressText">Memuat library analisis...</p>
                    </div>
                    <div class="detector-result" id="detectorResult" style="display:none">
                        <div class="result-row">
                            <div class="result-item">
                                <span class="result-label">Nada Dasar Terdeteksi</span>
                                <span class="result-value" id="resultKey">—</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Scale</span>
                                <span class="result-value" id="resultScale">—</span>
                            </div>
                        </div>
                        <button type="button" class="btn-apply" onclick="applyResult()">
                            &#10003; Terapkan ke form
                        </button>
                    </div>
                </div>

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

    {{-- LIRIK --}}
    <div class="form-section">
        <p class="form-section-title">Lirik</p>
        <div class="form-group">
            <label class="form-label">Lirik Lengkap</label>
            <textarea name="lyrics" class="form-textarea" style="min-height:300px;"
                placeholder="Tulis lirik lengkap di sini...">{{ old('lyrics', $song->lyrics) }}</textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-save">Simpan Perubahan</button>
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

// CHORD DETECTOR
var detectedKey = '';
var detectedScale = '';

document.getElementById('audioFile').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) return;

    document.getElementById('fileName').textContent = file.name;
    document.getElementById('detectorProgress').style.display = 'block';
    document.getElementById('detectorResult').style.display = 'none';
    setProgress(10, 'Membaca file audio...');

    var reader = new FileReader();
    reader.onload = function(evt) {
        setProgress(30, 'Mendekode audio...');
        var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        audioCtx.decodeAudioData(evt.target.result, function(buffer) {
            setProgress(60, 'Menganalisis nada dasar...');
            analyzeKey(buffer, audioCtx.sampleRate);
        }, function(err) {
            setProgress(0, 'Gagal membaca file. Coba format MP3 atau WAV.');
        });
    };
    reader.readAsArrayBuffer(file);
});

function setProgress(pct, text) {
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressText').textContent = text;
}

function analyzeKey(buffer, sampleRate) {
    var channelData = buffer.getChannelData(0);
    var chroma = computeChroma(channelData, sampleRate);
    var result  = detectKeyFromChroma(chroma);

    detectedKey   = result.key;
    detectedScale = result.scale;

    setProgress(100, 'Analisis selesai!');
    document.getElementById('resultKey').textContent   = result.key;
    document.getElementById('resultScale').textContent = result.scale;
    document.getElementById('detectorResult').style.display = 'block';
    setProgress(100, 'Selesai — periksa hasilnya dan klik Terapkan.');
}

function computeChroma(samples, sampleRate) {
    var chroma = new Array(12).fill(0);
    var A4 = 440;
    var step = Math.floor(sampleRate / 100);
    var count = 0;

    for (var i = 0; i < samples.length - step; i += step) {
        var sum = 0;
        for (var j = 0; j < step; j++) sum += samples[i + j] * samples[i + j];
        var rms = Math.sqrt(sum / step);
        if (rms < 0.01) continue;

        // Simple autocorrelation pitch detection
        var best = -1, bestLag = -1;
        var minLag = Math.floor(sampleRate / 1000);
        var maxLag = Math.floor(sampleRate / 50);
        for (var lag = minLag; lag < maxLag && lag < step; lag++) {
            var corr = 0;
            for (var k = 0; k < step - lag; k++) {
                corr += samples[i + k] * samples[i + k + lag];
            }
            if (corr > best) { best = corr; bestLag = lag; }
        }
        if (bestLag > 0) {
            var freq = sampleRate / bestLag;
            var semitones = Math.round(12 * Math.log2(freq / A4));
            var pitchClass = ((semitones % 12) + 12) % 12;
            chroma[pitchClass] += rms;
            count++;
        }
    }
    if (count > 0) chroma = chroma.map(function(v) { return v / count; });
    return chroma;
}

function detectKeyFromChroma(chroma) {
    var noteNames = ['A','Bb','B','C','C#','D','Eb','E','F','F#','G','Ab'];
    var majorProfile = [6.35,2.23,3.48,2.33,4.38,4.09,2.52,5.19,2.39,3.66,2.29,2.88];
    var minorProfile = [6.33,2.68,3.52,5.38,2.60,3.53,2.54,4.75,3.98,2.69,3.34,3.17];

    var bestScore = -Infinity, bestKey = 0, bestScale = 'major';

    for (var shift = 0; shift < 12; shift++) {
        var majScore = 0, minScore = 0;
        for (var i = 0; i < 12; i++) {
            majScore += chroma[i] * majorProfile[(i - shift + 12) % 12];
            minScore += chroma[i] * minorProfile[(i - shift + 12) % 12];
        }
        if (majScore > bestScore) { bestScore = majScore; bestKey = shift; bestScale = 'major'; }
        if (minScore > bestScore) { bestScore = minScore; bestKey = shift; bestScale = 'minor'; }
    }

    return {
        key:   noteNames[bestKey],
        scale: bestScale === 'major' ? 'Mayor' : 'Minor'
    };
}

function applyResult() {
    var keyInput = document.querySelector('input[name="key_signature"]');
    if (keyInput) keyInput.value = detectedKey + ' ' + detectedScale;

    var chordsArea = document.querySelector('textarea[name="chords"]');
    if (chordsArea) {
        chordsArea.value = generateChordTemplate(detectedKey, detectedScale);
    }
}

function generateChordTemplate(key, scale) {
    var notes = ['A','Bb','B','C','C#','D','Eb','E','F','F#','G','Ab'];
    var idx = notes.indexOf(key);

    var chords;
    if (scale === 'Mayor') {
        var steps = [0,2,4,5,7,9,11];
        var types = ['','m','m','','','m','dim'];
        chords = {
            'I'  : notes[(idx+steps[0])%12] + types[0],
            'II' : notes[(idx+steps[1])%12] + types[1],
            'III': notes[(idx+steps[2])%12] + types[2],
            'IV' : notes[(idx+steps[3])%12] + types[3],
            'V'  : notes[(idx+steps[4])%12] + types[4],
            'VI' : notes[(idx+steps[5])%12] + types[5],
            'VII': notes[(idx+steps[6])%12] + types[6],
        };
    } else {
        var steps = [0,2,3,5,7,8,10];
        var types = ['m','dim','','m','m','',''];
        chords = {
            'i'  : notes[(idx+steps[0])%12] + types[0],
            'ii' : notes[(idx+steps[1])%12] + types[1],
            'III': notes[(idx+steps[2])%12] + types[2],
            'iv' : notes[(idx+steps[3])%12] + types[3],
            'v'  : notes[(idx+steps[4])%12] + types[4],
            'VI' : notes[(idx+steps[5])%12] + types[5],
            'VII': notes[(idx+steps[6])%12] + types[6],
        };
    }

    var c = chords;
    var keys = Object.values(c);
    var i=keys[0], ii=keys[1], iii=keys[2],
        iv=keys[3], v=keys[4], vi=keys[5], vii=keys[6];

    return '[Intro]\n' + i + '  ' + iii + '  ' + vi + '  ' + v + '\n\n' +
           '[Verse]\n' + i + '  ' + iii + '  ' + vi + '  ' + v + '\n' +
           'Tulis lirik di sini\n' + i + '  ' + iv + '  ' + v + '  ' + i + '\n' +
           'Tulis lirik di sini\n\n' +
           '[Bridge]\n' + vi + '  ' + iii + '  ' + iv + '  ' + v + '\n' +
           'Tulis lirik di sini\n\n' +
           '[Chorus]\n' + iv + '  ' + i + '  ' + v + '  ' + vi + '\n' +
           'Tulis lirik di sini\n' + iv + '  ' + v + '  ' + i + '\n\n' +
           '[Outro]\n' + i + '  ' + iii + '  ' + vi + '  ' + i + '\n\n' +
           '// Key: ' + key + ' ' + scale + '\n' +
           '// Chord tersedia: ' + Object.entries(c).map(function(e){ return e[0]+'='+e[1]; }).join(', ') + '\n' +
           '// Silakan sesuaikan chord per baris lirik';
}
</script>

@endsection
