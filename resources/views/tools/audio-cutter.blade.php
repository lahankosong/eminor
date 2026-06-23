@extends('layouts.app')

@push('head')
<meta name="description" content="{{ $seo['description'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Pemotong Lagu Online",
  "url": "{{ $seo['canonical'] }}",
  "description": "{{ $seo['description'] }}",
  "applicationCategory": "MultimediaApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "IDR" }
}
</script>
@endpush

@section('title', $seo['title'])

@push('styles')
<style>
    :root {
        --ac: #38bdf8;
        --ac-dk: #0ea5e9;
        --ac-lt: rgba(56,189,248,.12);
        --or: #f59e0b;
        --rd: #ef4444;
        --green: #22c55e;
    }

    /* ── Page layout ── */
    .ac-page { max-width: 780px; margin: 0 auto; padding: 1.5rem 1rem 4rem; }

    /* ── Hero ── */
    .ac-hero { text-align: center; margin-bottom: 2rem; }
    .ac-hero-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--ac-dk, #0ea5e9); background: var(--ac-lt, rgba(56,189,248,.1)); border: 1px solid rgba(56,189,248,.3); border-radius: 20px; padding: 4px 12px; margin-bottom: 1rem; }
    .ac-hero h1 { font-family: 'Space Grotesk','Sora','Inter',sans-serif; font-size: clamp(1.5rem, 5vw, 2.2rem); font-weight: 700; color: var(--text, #f0f0f0); line-height: 1.2; margin-bottom: .6rem; }
    .ac-hero p { font-size: 14px; color: var(--text-3, #94a3b8); max-width: 520px; margin: 0 auto; line-height: 1.7; }

    /* ── Drop zone ── */
    .ac-drop { border: 2px dashed var(--border, #334155); border-radius: 20px; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer; transition: .2s; background: var(--card-bg, #0f172a); }
    .ac-drop:hover, .ac-drop.drag-over { border-color: var(--ac); background: var(--ac-lt); }
    .ac-drop-icon { font-size: 2.5rem; margin-bottom: .75rem; }
    .ac-drop-text { font-size: 15px; font-weight: 600; color: var(--text, #f0f0f0); margin-bottom: .3rem; }
    .ac-drop-sub { font-size: 12px; color: var(--text-3, #94a3b8); }
    #acFileInput { display: none; }

    /* ── Editor card ── */
    .ac-editor { background: var(--card-bg, #0f172a); border: 1px solid var(--border, #334155); border-radius: 20px; overflow: hidden; margin-top: 1.25rem; display: none; }
    .ac-editor.show { display: block; }
    .ac-editor-head { padding: .875rem 1.25rem; border-bottom: 1px solid var(--border, #334155); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .ac-file-name { font-weight: 600; font-size: 14px; color: var(--text, #f0f0f0); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px; }
    .ac-file-meta { font-size: 11px; color: var(--text-3, #94a3b8); }
    .ac-editor-body { padding: 1.25rem; }

    /* ── Waveform ── */
    .ac-wave-wrap { position: relative; border-radius: 12px; overflow: hidden; background: #0a0e1a; margin-bottom: .75rem; cursor: crosshair; }
    #acWave { display: block; width: 100%; height: 120px; }
    .ac-playhead { position: absolute; top: 0; bottom: 0; width: 2px; background: #fff; opacity: .8; pointer-events: none; left: 0; }

    /* ── Time labels ── */
    .ac-time-row { display: flex; justify-content: space-between; font-size: 11px; color: var(--text-3, #94a3b8); font-variant-numeric: tabular-nums; margin-bottom: 1rem; padding: 0 2px; }

    /* ── Sliders ── */
    .ac-sliders { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1rem; }
    .ac-slider-row { display: flex; align-items: center; gap: 10px; }
    .ac-slider-label { font-size: 11px; font-weight: 700; color: var(--text-3, #94a3b8); width: 36px; flex-shrink: 0; }
    .ac-slider-row input[type=range] { flex: 1; accent-color: var(--ac); cursor: pointer; height: 4px; }
    .ac-slider-val { font-size: 12px; font-variant-numeric: tabular-nums; color: var(--text, #f0f0f0); width: 52px; text-align: right; flex-shrink: 0; }

    /* ── Selection info ── */
    .ac-sel-info { display: flex; align-items: center; justify-content: center; gap: 16px; background: var(--ac-lt); border: 1px solid rgba(56,189,248,.25); border-radius: 12px; padding: .6rem 1rem; margin-bottom: 1rem; font-size: 13px; }
    .ac-sel-item { display: flex; flex-direction: column; align-items: center; gap: 2px; }
    .ac-sel-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: var(--text-3, #94a3b8); }
    .ac-sel-val { font-weight: 700; font-variant-numeric: tabular-nums; color: var(--ac); }

    /* ── Controls ── */
    .ac-controls { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.1rem; }
    .ac-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: .15s; font-family: inherit; }
    .ac-btn:disabled { opacity: .45; cursor: not-allowed; }
    .ac-btn-play  { background: var(--card-bg, #1e293b); border: 1px solid var(--border, #334155); color: var(--text, #f0f0f0); }
    .ac-btn-play:not(:disabled):hover  { border-color: var(--ac); color: var(--ac); }
    .ac-btn-prev  { background: var(--ac-lt); border: 1px solid rgba(56,189,248,.3); color: var(--ac); }
    .ac-btn-prev:not(:disabled):hover  { background: rgba(56,189,248,.2); }
    .ac-btn-stop  { background: var(--card-bg, #1e293b); border: 1px solid var(--border, #334155); color: var(--text-3, #94a3b8); }
    .ac-btn-stop:not(:disabled):hover  { border-color: var(--rd); color: var(--rd); }
    .ac-btn-cut   { background: linear-gradient(135deg, var(--ac), var(--ac-dk)); color: #fff; box-shadow: 0 4px 14px rgba(56,189,248,.3); flex: 1; justify-content: center; }
    .ac-btn-cut:not(:disabled):hover   { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(56,189,248,.4); }

    /* ── Result ── */
    .ac-result { background: rgba(34,197,94,.06); border: 1px solid rgba(34,197,94,.3); border-radius: 14px; padding: 1rem 1.25rem; display: none; }
    .ac-result.show { display: block; }
    .ac-result-head { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--green); margin-bottom: .75rem; }
    .ac-result audio { width: 100%; border-radius: 8px; margin-bottom: .75rem; }
    .ac-dl { display: inline-flex; align-items: center; gap: 8px; background: var(--green); color: #fff; padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; transition: .15s; }
    .ac-dl:hover { opacity: .88; transform: translateY(-1px); }
    .ac-reset { display: inline-flex; align-items: center; gap: 6px; background: transparent; border: 1px solid var(--border, #334155); color: var(--text-3, #94a3b8); padding: 10px 18px; border-radius: 10px; font-size: 13px; cursor: pointer; margin-left: 8px; font-family: inherit; }
    .ac-reset:hover { border-color: var(--text-3); color: var(--text); }

    /* ── Status ── */
    .ac-status { font-size: 12px; color: var(--text-3, #94a3b8); margin-top: 8px; min-height: 16px; }
    .ac-status.err { color: var(--rd); }

    /* ── Info cards (SEO) ── */
    .ac-info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: 12px; margin-top: 2.5rem; }
    .ac-info-card { background: var(--card-bg, #0f172a); border: 1px solid var(--border, #334155); border-radius: 16px; padding: 1.1rem; }
    .ac-info-icon { font-size: 1.5rem; margin-bottom: .5rem; }
    .ac-info-title { font-weight: 700; font-size: 13px; color: var(--text, #f0f0f0); margin-bottom: .35rem; }
    .ac-info-body { font-size: 12px; color: var(--text-3, #94a3b8); line-height: 1.6; }

    /* ── Back link ── */
    .ac-back { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-3, #94a3b8); text-decoration: none; margin-bottom: 1.5rem; }
    .ac-back:hover { color: var(--text); }

    @media(max-width:480px) {
        .ac-hero h1 { font-size: 1.4rem; }
        .ac-controls { flex-direction: column; }
        .ac-btn-cut { flex: none; }
    }
</style>
@endpush

@section('content')
<div class="ac-page">

    <a href="{{ route('home') }}" class="ac-back">← Kembali ke Beranda</a>

    {{-- Hero --}}
    <div class="ac-hero">
        <div class="ac-hero-badge">✂️ Tool Gratis</div>
        <h1>Pemotong Lagu Online</h1>
        <p>Potong bagian lagu yang kamu mau langsung di browser &mdash; tanpa upload ke server, tanpa install aplikasi, dan tanpa biaya.</p>
    </div>

    {{-- Drop zone --}}
    <div class="ac-drop" id="acDrop" onclick="document.getElementById('acFileInput').click()">
        <div class="ac-drop-icon">🎵</div>
        <div class="ac-drop-text">Seret file audio ke sini atau klik untuk pilih</div>
        <div class="ac-drop-sub">MP3 · WAV · OGG · FLAC · AAC · M4A &nbsp;|&nbsp; Maks 300 MB &nbsp;|&nbsp; Tidak diunggah ke server</div>
    </div>
    <input type="file" id="acFileInput" accept="audio/*">

    {{-- Editor --}}
    <div class="ac-editor" id="acEditor">
        <div class="ac-editor-head">
            <div>
                <div class="ac-file-name" id="acFileName">—</div>
                <div class="ac-file-meta" id="acFileMeta">—</div>
            </div>
            <button class="ac-btn ac-btn-stop" onclick="acChangeFile()" style="font-size:12px;padding:6px 12px;">🔄 Ganti File</button>
        </div>
        <div class="ac-editor-body">

            {{-- Waveform --}}
            <div class="ac-wave-wrap" id="acWaveWrap">
                <canvas id="acWave"></canvas>
                <div class="ac-playhead" id="acPlayhead" style="display:none;"></div>
            </div>
            <div class="ac-time-row">
                <span>0:00</span>
                <span id="acDurLabel">0:00</span>
            </div>

            {{-- Sliders --}}
            <div class="ac-sliders">
                <div class="ac-slider-row">
                    <span class="ac-slider-label" style="color:#22d3ee;">Mulai</span>
                    <input type="range" id="acStart" min="0" max="100" step="0.01" value="0">
                    <span class="ac-slider-val" id="acStartVal" style="color:#22d3ee;">0:00</span>
                </div>
                <div class="ac-slider-row">
                    <span class="ac-slider-label" style="color:#f59e0b;">Akhir</span>
                    <input type="range" id="acEnd" min="0" max="100" step="0.01" value="100">
                    <span class="ac-slider-val" id="acEndVal" style="color:#f59e0b;">0:00</span>
                </div>
            </div>

            {{-- Selection info --}}
            <div class="ac-sel-info">
                <div class="ac-sel-item">
                    <span class="ac-sel-lbl">Mulai</span>
                    <span class="ac-sel-val" id="acSelStart">0:00</span>
                </div>
                <div style="color:var(--text-3,#94a3b8);font-size:18px;">→</div>
                <div class="ac-sel-item">
                    <span class="ac-sel-lbl">Akhir</span>
                    <span class="ac-sel-val" id="acSelEnd">0:00</span>
                </div>
                <div style="color:var(--text-3,#94a3b8);font-size:18px;">|</div>
                <div class="ac-sel-item">
                    <span class="ac-sel-lbl">Durasi Pilihan</span>
                    <span class="ac-sel-val" id="acSelDur">0:00</span>
                </div>
            </div>

            {{-- Controls --}}
            <div class="ac-controls">
                <button class="ac-btn ac-btn-play" id="acBtnPlay" onclick="acPlay()">▶ Play Semua</button>
                <button class="ac-btn ac-btn-prev" id="acBtnPrev" onclick="acPreview()">▶ Preview Pilihan</button>
                <button class="ac-btn ac-btn-stop" id="acBtnStop" onclick="acStop()">⏹ Stop</button>
                <button class="ac-btn ac-btn-cut" id="acBtnCut" onclick="acCut()">✂️ Potong &amp; Unduh</button>
            </div>
            <div class="ac-status" id="acStatus"></div>

            {{-- Result --}}
            <div class="ac-result" id="acResult">
                <div class="ac-result-head">✅ Potongan siap diunduh</div>
                <audio id="acClipPlayer" controls></audio>
                <div>
                    <a id="acDlLink" class="ac-dl" download>⬇️ Unduh WAV</a>
                    <button class="ac-reset" onclick="acCutAgain()">✂️ Potong Lagi</button>
                </div>
            </div>

        </div>
    </div>

    {{-- Info cards (SEO + UX) --}}
    <section style="margin-top:3rem;">
        <h2 style="font-family:'Space Grotesk','Sora',sans-serif;font-size:1.1rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:1rem;">Tentang Tool Ini</h2>
        <div class="ac-info-grid">
            <div class="ac-info-card">
                <div class="ac-info-icon">🔒</div>
                <div class="ac-info-title">Privasi 100%</div>
                <div class="ac-info-body">File audio tidak pernah dikirim ke server. Semua pemrosesan terjadi di dalam browser kamu menggunakan Web Audio API.</div>
            </div>
            <div class="ac-info-card">
                <div class="ac-info-icon">⚡</div>
                <div class="ac-info-title">Cepat &amp; Langsung</div>
                <div class="ac-info-body">Tidak perlu tunggu upload. Geser slider untuk pilih bagian yang kamu mau, lalu klik Potong — selesai dalam hitungan detik.</div>
            </div>
            <div class="ac-info-card">
                <div class="ac-info-icon">🎵</div>
                <div class="ac-info-title">Format Didukung</div>
                <div class="ac-info-body">MP3, WAV, OGG, FLAC, AAC, M4A — semua format yang didukung browser. Output diunduh sebagai file WAV berkualitas tinggi.</div>
            </div>
            <div class="ac-info-card">
                <div class="ac-info-icon">🎸</div>
                <div class="ac-info-title">Cocok untuk Musisi</div>
                <div class="ac-info-body">Potong bagian intro, verse, atau chorus untuk latihan, preview, atau konten media sosial. Presisi hingga 0.01 detik.</div>
            </div>
        </div>
    </section>

    <section style="margin-top:2.5rem;">
        <h2 style="font-family:'Space Grotesk','Sora',sans-serif;font-size:1.1rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.75rem;">Cara Menggunakan</h2>
        <ol style="font-size:13px;color:var(--text-3,#94a3b8);line-height:2;padding-left:1.25rem;">
            <li>Klik area upload atau seret file audio dari komputer ke kotak di atas</li>
            <li>Geser slider <span style="color:#22d3ee;font-weight:700;">Mulai</span> dan <span style="color:#f59e0b;font-weight:700;">Akhir</span> untuk memilih bagian yang diinginkan</li>
            <li>Klik <b>Preview Pilihan</b> untuk mendengarkan hasil sebelum memotong</li>
            <li>Klik <b>✂️ Potong &amp; Unduh</b> — file WAV langsung terunduh</li>
        </ol>
    </section>

    <p style="text-align:center;margin-top:3rem;font-size:12px;color:var(--text-3,#94a3b8);">
        Bagian dari komunitas musik <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> &mdash;
        platform musisi Indonesia 🎸
    </p>

</div>
@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════
//  PEMOTONG LAGU — Web Audio API
// ══════════════════════════════════════════

var _ctx    = null;   // AudioContext
var _buf    = null;   // AudioBuffer (decoded)
var _src    = null;   // AudioBufferSourceNode (playing)
var _startT = 0;      // selection start (seconds)
var _endT   = 0;      // selection end (seconds)
var _dur    = 0;      // total duration
var _playCtxTime = 0; // AudioContext.currentTime when play started
var _playOffset  = 0; // where in buffer we started playing
var _playing     = false;
var _prevStop    = null;
var _rafId       = null;
var _fileName    = 'lagu';
var _resultUrl   = null;

/* ── Utils ── */
function fmtT(s) {
    s = Math.max(0, s || 0);
    var m = Math.floor(s / 60), x = s % 60;
    return m + ':' + (x < 10 ? '0' : '') + x.toFixed(2);
}
function fmtShort(s) {
    s = Math.max(0, s || 0);
    var m = Math.floor(s / 60), x = Math.floor(s % 60);
    return m + ':' + (x < 10 ? '0' : '') + x;
}
function setStatus(msg, err) {
    var el = document.getElementById('acStatus');
    el.textContent = msg;
    el.className = 'ac-status' + (err ? ' err' : '');
}

/* ── File loading ── */
var drop = document.getElementById('acDrop');
drop.addEventListener('dragover', function(e) { e.preventDefault(); drop.classList.add('drag-over'); });
drop.addEventListener('dragleave', function() { drop.classList.remove('drag-over'); });
drop.addEventListener('drop', function(e) {
    e.preventDefault(); drop.classList.remove('drag-over');
    var f = e.dataTransfer.files[0];
    if (f) loadAudioFile(f);
});
document.getElementById('acFileInput').addEventListener('change', function() {
    if (this.files[0]) loadAudioFile(this.files[0]);
});

function loadAudioFile(file) {
    var mb = (file.size / 1024 / 1024).toFixed(1);
    if (file.size > 300 * 1024 * 1024) { alert('File terlalu besar (maks 300 MB).'); return; }

    _fileName = file.name.replace(/\.[^.]+$/, '');
    document.getElementById('acFileName').textContent = file.name;
    document.getElementById('acFileMeta').textContent = mb + ' MB · memuat…';
    setStatus('Membaca file dan menggambar waveform…');

    if (_ctx) { try { _ctx.close(); } catch(e){} }
    _ctx = new (window.AudioContext || window.webkitAudioContext)();

    var reader = new FileReader();
    reader.onload = function(e) {
        _ctx.decodeAudioData(e.target.result, function(buffer) {
            _buf  = buffer;
            _dur  = buffer.duration;
            _startT = 0;
            _endT   = _dur;

            var sr = buffer.sampleRate;
            var ch = buffer.numberOfChannels;
            document.getElementById('acFileMeta').textContent =
                mb + ' MB · ' + fmtShort(_dur) + ' · ' + sr + ' Hz · ' + ch + 'ch';
            document.getElementById('acDurLabel').textContent = fmtShort(_dur);

            var sl = document.getElementById('acStart');
            var el = document.getElementById('acEnd');
            sl.max = el.max = _dur.toFixed(2);
            sl.step = el.step = (_dur / 1000).toFixed(4);
            sl.value = 0;
            el.value = _dur.toFixed(2);

            drawWaveform();
            updateDisplay();
            document.getElementById('acEditor').classList.add('show');
            document.getElementById('acResult').classList.remove('show');
            setStatus('');
        }, function() {
            setStatus('Gagal membaca file — pastikan format audio yang valid.', true);
        });
    };
    reader.readAsArrayBuffer(file);
}

function acChangeFile() {
    acStop();
    document.getElementById('acEditor').classList.remove('show');
    document.getElementById('acResult').classList.remove('show');
    document.getElementById('acFileInput').value = '';
    _buf = null; _dur = 0;
    setStatus('');
}

/* ── Waveform drawing ── */
function drawWaveform() {
    var canvas = document.getElementById('acWave');
    var wrap   = document.getElementById('acWaveWrap');
    var W = wrap.clientWidth || 680;
    var H = 120;
    canvas.width  = W;
    canvas.height = H;
    var ctx = canvas.getContext('2d');

    // Background
    ctx.fillStyle = '#0a0e1a';
    ctx.fillRect(0, 0, W, H);

    if (!_buf) return;

    var data = _buf.getChannelData(0);
    var step = Math.ceil(data.length / W);
    var sx = (_startT / _dur) * W;
    var ex = (_endT   / _dur) * W;

    // Selection fill
    ctx.fillStyle = 'rgba(56,189,248,0.08)';
    ctx.fillRect(sx, 0, ex - sx, H);

    // Waveform bars
    for (var i = 0; i < W; i++) {
        var max = 0;
        for (var j = 0; j < step; j++) {
            var v = Math.abs(data[i * step + j] || 0);
            if (v > max) max = v;
        }
        var barH = Math.max(1, max * H * 0.92);
        var y    = (H - barH) / 2;

        if (i >= sx && i <= ex) {
            ctx.fillStyle = i >= sx + 2 && i <= ex - 2 ? '#38bdf8' : '#7dd3fc';
        } else {
            ctx.fillStyle = '#1e3a4a';
        }
        ctx.fillRect(i, y, 1, barH);
    }

    // Start handle line + triangle
    ctx.fillStyle = '#22d3ee';
    ctx.fillRect(sx, 0, 2, H);
    ctx.beginPath(); ctx.moveTo(sx, 0); ctx.lineTo(sx + 12, 0); ctx.lineTo(sx, 16); ctx.closePath(); ctx.fill();

    // End handle line + triangle
    ctx.fillStyle = '#f59e0b';
    ctx.fillRect(ex - 2, 0, 2, H);
    ctx.beginPath(); ctx.moveTo(ex, 0); ctx.lineTo(ex - 12, 0); ctx.lineTo(ex, 16); ctx.closePath(); ctx.fill();
}

/* ── Sliders ── */
document.getElementById('acStart').addEventListener('input', function() {
    _startT = parseFloat(this.value);
    if (_startT >= _endT - 0.1) { _startT = _endT - 0.1; this.value = _startT.toFixed(4); }
    drawWaveform(); updateDisplay();
});
document.getElementById('acEnd').addEventListener('input', function() {
    _endT = parseFloat(this.value);
    if (_endT <= _startT + 0.1) { _endT = _startT + 0.1; this.value = _endT.toFixed(4); }
    drawWaveform(); updateDisplay();
});

function updateDisplay() {
    document.getElementById('acStartVal').textContent = fmtShort(_startT);
    document.getElementById('acEndVal').textContent   = fmtShort(_endT);
    document.getElementById('acSelStart').textContent = fmtT(_startT);
    document.getElementById('acSelEnd').textContent   = fmtT(_endT);
    document.getElementById('acSelDur').textContent   = fmtT(_endT - _startT);
}

/* ── Click waveform to seek ── */
document.getElementById('acWaveWrap').addEventListener('click', function(e) {
    if (!_dur) return;
    var rect = this.getBoundingClientRect();
    var t = ((e.clientX - rect.left) / rect.width) * _dur;
    // snap to nearest handle
    var dStart = Math.abs(t - _startT);
    var dEnd   = Math.abs(t - _endT);
    if (dStart < dEnd) {
        _startT = Math.max(0, Math.min(t, _endT - 0.1));
        document.getElementById('acStart').value = _startT.toFixed(4);
    } else {
        _endT = Math.max(_startT + 0.1, Math.min(t, _dur));
        document.getElementById('acEnd').value = _endT.toFixed(4);
    }
    drawWaveform(); updateDisplay();
});

/* ── Playback ── */
function acPlay() {
    if (!_buf) return;
    acStop();
    _ctx.resume();
    _src = _ctx.createBufferSource();
    _src.buffer = _buf;
    _src.connect(_ctx.destination);
    _playOffset  = 0;
    _playCtxTime = _ctx.currentTime;
    _src.start(0, 0);
    _playing = true;
    rafTick();
}

function acPreview() {
    if (!_buf) return;
    acStop();
    _ctx.resume();
    _src = _ctx.createBufferSource();
    _src.buffer = _buf;
    _src.connect(_ctx.destination);
    _playOffset  = _startT;
    _playCtxTime = _ctx.currentTime;
    var selDur   = _endT - _startT;
    _src.start(0, _startT, selDur);
    _playing = true;
    _prevStop = setTimeout(acStop, selDur * 1000 + 200);
    rafTick();
}

function acStop() {
    if (_prevStop) { clearTimeout(_prevStop); _prevStop = null; }
    if (_rafId) { cancelAnimationFrame(_rafId); _rafId = null; }
    if (_src) { try { _src.stop(); } catch(e){} _src = null; }
    _playing = false;
    var ph = document.getElementById('acPlayhead');
    ph.style.display = 'none';
}

function rafTick() {
    if (!_playing) return;
    var elapsed = _ctx.currentTime - _playCtxTime;
    var pos = (_playOffset + elapsed) / _dur;
    if (pos > 1) { acStop(); return; }
    var canvas = document.getElementById('acWave');
    var ph = document.getElementById('acPlayhead');
    ph.style.display = 'block';
    ph.style.left = (pos * canvas.width) + 'px';
    _rafId = requestAnimationFrame(rafTick);
}

/* ── Export WAV ── */
function acCut() {
    if (!_buf) return;
    var sel = _endT - _startT;
    if (sel < 0.1) { setStatus('Pilihan terlalu pendek — minimal 0.1 detik.', true); return; }

    setStatus('Memproses…');
    document.getElementById('acBtnCut').disabled = true;

    setTimeout(function() {
        try {
            var blob = bufferToWav(_buf, _startT, _endT);
            if (_resultUrl) URL.revokeObjectURL(_resultUrl);
            _resultUrl = URL.createObjectURL(blob);

            var player = document.getElementById('acClipPlayer');
            player.src = _resultUrl;

            var dl = document.getElementById('acDlLink');
            dl.href = _resultUrl;
            dl.download = _fileName + '_' + fmtShort(_startT).replace(':', 'm') + 's-' + fmtShort(_endT).replace(':', 'm') + 's.wav';

            document.getElementById('acResult').classList.add('show');
            setStatus('');
            document.getElementById('acResult').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } catch(e) {
            setStatus('Gagal: ' + (e.message || e), true);
        } finally {
            document.getElementById('acBtnCut').disabled = false;
        }
    }, 50);
}

function acCutAgain() {
    document.getElementById('acResult').classList.remove('show');
}

/* ── WAV encoder (PCM 16-bit stereo/mono) ── */
function bufferToWav(buffer, startSec, endSec) {
    var sr         = buffer.sampleRate;
    var nCh        = buffer.numberOfChannels;
    var startSamp  = Math.floor(startSec * sr);
    var endSamp    = Math.min(Math.ceil(endSec * sr), buffer.length);
    var nSamp      = endSamp - startSamp;
    var dataLen    = nSamp * nCh * 2;
    var ab         = new ArrayBuffer(44 + dataLen);
    var v          = new DataView(ab);

    function ws(off, str) { for (var i = 0; i < str.length; i++) v.setUint8(off + i, str.charCodeAt(i)); }
    ws(0,  'RIFF'); v.setUint32(4,  36 + dataLen, true);
    ws(8,  'WAVE'); ws(12, 'fmt ');
    v.setUint32(16, 16, true);
    v.setUint16(20, 1, true);               // PCM
    v.setUint16(22, nCh, true);
    v.setUint32(24, sr, true);
    v.setUint32(28, sr * nCh * 2, true);
    v.setUint16(32, nCh * 2, true);
    v.setUint16(34, 16, true);
    ws(36, 'data'); v.setUint32(40, dataLen, true);

    var offset = 44;
    for (var i = 0; i < nSamp; i++) {
        for (var ch = 0; ch < nCh; ch++) {
            var s = buffer.getChannelData(ch)[startSamp + i];
            s = Math.max(-1, Math.min(1, s));
            v.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            offset += 2;
        }
    }
    return new Blob([ab], { type: 'audio/wav' });
}

// Redraw waveform on window resize
window.addEventListener('resize', function() { if (_buf) drawWaveform(); });
</script>
@endpush
