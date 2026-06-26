@extends('layouts.app')

@push('styles')
<style>
    .tk-wrap { max-width: 720px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .tk-title { font-size: clamp(1.4rem,4vw,2rem); font-weight: 300; color: var(--text); margin-bottom: .35rem; }
    .tk-sub { font-size: 14px; color: var(--text-3); margin-bottom: 2rem; }

    .tk-controls { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1.2rem; }
    .tk-ctrl-group { display: flex; flex-direction: column; gap: 5px; }
    .tk-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-3); }
    .tk-select { background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 14px; padding: 9px 36px 9px 13px; outline: none; font-family: inherit; transition: .15s; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M0 0l6 8 6-8z' fill='%2394a3b8'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; min-width: 110px; }
    .tk-select:focus { border-color: var(--accent); }
    .tk-btn { padding: 10px 22px; border-radius: 10px; background: var(--accent); color: #fff; border: none; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; }
    .tk-btn:hover { opacity: .88; }
    .tk-btn-clear { padding: 10px 16px; border-radius: 10px; background: none; color: var(--text-3); border: 1px solid var(--border); font-size: 13px; cursor: pointer; font-family: inherit; transition: .15s; }
    .tk-btn-clear:hover { border-color: var(--text-3); color: var(--text-2); }

    .tk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media(max-width:600px){ .tk-grid { grid-template-columns: 1fr; } }

    .tk-box-label { font-size: 10px; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: var(--text-4); margin-bottom: .5rem; }
    .tk-textarea { width: 100%; min-height: 320px; background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 14px; color: var(--text); font-family: 'Courier New', monospace; font-size: 14px; line-height: 2; padding: 14px 16px; outline: none; resize: vertical; transition: .15s; box-sizing: border-box; }
    .tk-textarea:focus { border-color: var(--accent); }
    .tk-result { width: 100%; min-height: 320px; background: var(--bg-2, rgba(15,23,42,.9)); border: 1px solid var(--border); border-radius: 14px; color: var(--text); font-family: 'Courier New', monospace; font-size: 14px; line-height: 2; padding: 14px 16px; box-sizing: border-box; overflow: auto; white-space: pre-wrap; }
    .chord-hi { color: var(--accent, #38bdf8); font-weight: 700; }

    .tk-copy-btn { margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; background: none; border: 1px solid var(--border); border-radius: 8px; color: var(--text-3); font-size: 12px; padding: 6px 13px; cursor: pointer; font-family: inherit; transition: .15s; }
    .tk-copy-btn:hover { border-color: var(--accent); color: var(--accent); }

    .tk-quick { margin-top: 1.5rem; }
    .tk-quick-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-3); margin-bottom: .6rem; }
    .tk-quick-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .tk-chip { padding: 5px 13px; border-radius: 20px; border: 1px solid var(--border); background: none; color: var(--text-3); font-size: 12px; cursor: pointer; font-family: inherit; transition: .15s; }
    .tk-chip:hover { border-color: var(--accent); color: var(--accent); }

    .tk-info { background: var(--card-bg, rgba(15,23,42,.5)); border: 1px solid var(--border); border-radius: 14px; padding: 1rem 1.1rem; margin-top: 1.5rem; font-size: 12.5px; color: var(--text-3); line-height: 1.7; }
    .tk-info b { color: var(--text-2); }
</style>
@endpush

@section('content')
<div class="tk-wrap">
    <h1 class="tk-title">🔀 Transpose Kunci Gitar Online</h1>
    <p class="tk-sub">Tempel chord, pilih kunci asal dan tujuan — chord langsung berpindah kunci. Gratis, tanpa daftar.</p>

    @include('partials.tool-share', ['origin' => $origin])

    <div class="tk-controls">
        <div class="tk-ctrl-group">
            <label class="tk-label">Kunci Asal</label>
            <select id="tkFrom" class="tk-select" onchange="doTranspose()">
                @foreach(['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','Db','Eb','Gb','Ab','Bb'] as $n)
                <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <span style="font-size:20px;color:var(--text-4);padding-bottom:9px;">→</span>
        <div class="tk-ctrl-group">
            <label class="tk-label">Kunci Tujuan</label>
            <select id="tkTo" class="tk-select" onchange="doTranspose()">
                @foreach(['G','G#','A','A#','B','C','C#','D','D#','E','F','F#','Ab','Bb','Db','Eb','Gb'] as $n)
                <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="tk-btn" onclick="doTranspose()">Transpose</button>
        <button type="button" class="tk-btn-clear" onclick="clearAll()">Reset</button>
    </div>

    <div class="tk-grid">
        <div>
            <div class="tk-box-label">Chord asli — paste di sini</div>
            <textarea id="tkInput" class="tk-textarea"
                placeholder="Contoh:&#10;[Intro]&#10;C  Am  F  G&#10;&#10;[Verse]&#10;C          Am&#10;Langit senja memerah&#10;F            G&#10;Di ujung jalan itu"
                oninput="doTranspose()"></textarea>
        </div>
        <div>
            <div class="tk-box-label">Hasil transpose</div>
            <div id="tkOutput" class="tk-result"><span style="color:var(--text-4);font-family:inherit;font-size:13px;">Hasil transpose akan muncul di sini...</span></div>
            <button type="button" class="tk-copy-btn" onclick="copyResult()">📋 Salin hasil</button>
        </div>
    </div>

    <div class="tk-quick">
        <div class="tk-quick-label">Contoh cepat — klik untuk load</div>
        <div class="tk-quick-chips">
            <button type="button" class="tk-chip" onclick="loadExample('C')">Progresi C Mayor</button>
            <button type="button" class="tk-chip" onclick="loadExample('Am')">Progresi Am</button>
            <button type="button" class="tk-chip" onclick="loadExample('G')">Progresi G</button>
            <button type="button" class="tk-chip" onclick="loadExample('Dm')">Progresi Dm minor</button>
        </div>
    </div>

    <div class="tk-info">
        <b>Cara pakai:</b><br>
        1. Paste chord lagu (bisa berformat bebas — C, Am, F/A, Cmaj7, dll)<br>
        2. Pilih kunci asal dan kunci tujuan<br>
        3. Chord otomatis berpindah — teks lain (lirik, section mark) tetap utuh<br><br>
        <b>Tip:</b> Kalau kamu tidak yakin kunci aslinya, coba cek di Spotify/YouTube Music → tiga titik → "About this song" (di beberapa versi tersedia key info). Atau pakai telinga — chord pertama biasanya adalah kunci lagunya.
    </div>
</div>
@endsection

@push('scripts')
<script>
var NOTES_SHARP = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
var FLAT_MAP    = {Db:'C#',Eb:'D#',Gb:'F#',Ab:'G#',Bb:'A#'};
var EXAMPLES = {
    'C': '[Intro]\nC  Am  F  G\n\n[Verse]\nC          Am\nLangit senja memerah\nF            G\nDi ujung jalan itu\n\n[Chorus]\nF   G   Am   C\nKamu tetap di sini',
    'Am': '[Verse]\nAm  F  C  G\n\n[Bridge]\nDm  G  Am  E\n\n[Chorus]\nAm  F  C  E',
    'G': '[Intro]\nG  D  Em  C\n\n[Verse]\nG          D\nBerjalan sendiri\nEm         C\nDi bawah bintang\n\n[Chorus]\nC  D  G  Em',
    'Dm': '[Verse]\nDm  Bb  F  C\n\n[Bridge]\nGm  A  Dm\n\n[Chorus]\nBb  F  C  Dm',
};

function toSharp(n) { return FLAT_MAP[n] || n; }

function noteIndex(n) {
    var s = toSharp(n);
    return NOTES_SHARP.indexOf(s);
}

function transposeNote(note, semitones) {
    var idx = noteIndex(note);
    if (idx === -1) return note;
    var newIdx = ((idx + semitones) % 12 + 12) % 12;
    return NOTES_SHARP[newIdx];
}

function calcSemitones(from, to) {
    var a = noteIndex(from), b = noteIndex(to);
    if (a === -1 || b === -1) return 0;
    return ((b - a) % 12 + 12) % 12;
}

function transposeText(text, semitones) {
    if (!semitones) return text;
    return text.replace(/\b([A-G][#b]?)(m|maj7|maj|min|dim|aug|sus4|sus2|add9|7|9|11|13)?(\/[A-G][#b]?)?\b/g,
        function(match, root, quality, bass) {
            var newRoot = transposeNote(root, semitones);
            var newBass = bass ? '/' + transposeNote(bass.slice(1), semitones) : '';
            return newRoot + (quality || '') + newBass;
        });
}

function highlightChords(text) {
    return text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\[([^\]]+)\]/g,'<span style="color:var(--text-4);font-size:12px;">[$1]</span>')
        .replace(/\b([A-G][#b]?(?:m|maj7|maj|min|dim|aug|sus4|sus2|add9|7|9|11|13)?(?:\/[A-G][#b]?)?)\b/g,
            '<span class="chord-hi">$1</span>');
}

function doTranspose() {
    var input = document.getElementById('tkInput').value;
    var from  = document.getElementById('tkFrom').value;
    var to    = document.getElementById('tkTo').value;
    var semi  = calcSemitones(from, to);
    var result = transposeText(input, semi);
    document.getElementById('tkOutput').innerHTML = result
        ? highlightChords(result)
        : '<span style="color:var(--text-4);font-family:inherit;font-size:13px;">Hasil transpose akan muncul di sini...</span>';
}

function clearAll() {
    document.getElementById('tkInput').value = '';
    document.getElementById('tkOutput').innerHTML = '<span style="color:var(--text-4);font-family:inherit;font-size:13px;">Hasil transpose akan muncul di sini...</span>';
}

function copyResult() {
    var text = document.getElementById('tkOutput').innerText;
    if (!text || text.includes('muncul di sini')) return;
    navigator.clipboard.writeText(text).then(function() {
        var b = document.querySelector('.tk-copy-btn');
        b.textContent = '✓ Tersalin!';
        setTimeout(function(){ b.textContent = '📋 Salin hasil'; }, 1800);
    });
}

function loadExample(key) {
    document.getElementById('tkInput').value = EXAMPLES[key] || '';
    document.getElementById('tkFrom').value = key === 'Am' || key === 'Dm' ? key.replace('m','') : key;
    doTranspose();
}
</script>
@endpush
