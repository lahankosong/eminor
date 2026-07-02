@extends('layouts.app')

@push('styles')
<style>
    .sl-wrap { padding: 2rem 1rem 4rem; }
    .sl-header { margin-bottom: 1.5rem; }
    .sl-title { font-size: clamp(1.4rem,4vw,2rem); font-weight: 300; color: var(--text); margin-bottom: .35rem; }
    .sl-sub { font-size: 14px; color: var(--text-3); }

    /* EVENT META */
    .sl-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 1.5rem; }
    @media(max-width:560px){ .sl-meta-grid { grid-template-columns: 1fr; } }
    .sl-field { display: flex; flex-direction: column; gap: 5px; }
    .sl-label { font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--text-3); }
    .sl-input { background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 13.5px; padding: 9px 13px; outline: none; font-family: inherit; transition: .15s; }
    .sl-input:focus { border-color: var(--accent); }

    /* SONG LIST */
    .sl-list-header { display: grid; grid-template-columns: 30px 1fr 70px 60px 80px 60px; gap: 8px; align-items: center; padding: 0 10px 8px; border-bottom: 1px solid var(--border); margin-bottom: 6px; }
    .sl-col-label { font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--text-4); }
    @media(max-width:620px){
        .sl-list-header { grid-template-columns: 24px 1fr 60px 50px; }
        .sl-col-bpm, .sl-col-notes { display: none; }
    }

    #sl-songs { display: flex; flex-direction: column; gap: 6px; min-height: 40px; }

    .sl-song-row {
        display: grid; grid-template-columns: 30px 1fr 70px 60px 80px 60px; gap: 8px; align-items: center;
        background: var(--card-bg, rgba(15,23,42,.5)); border: 1px solid var(--border); border-radius: 12px; padding: 8px 10px;
        transition: border-color .15s, box-shadow .15s;
    }
    .sl-song-row:hover { border-color: var(--accent); }
    .sl-song-row.dragging { opacity: .5; box-shadow: 0 4px 24px rgba(56,168,204,.25); }
    .sl-song-row.drag-over { border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent-dim); }
    @media(max-width:620px){
        .sl-song-row { grid-template-columns: 24px 1fr 60px 50px; }
        .sl-song-row .sl-cell-bpm, .sl-song-row .sl-cell-notes { display: none; }
    }

    .sl-drag-handle { color: var(--text-4); font-size: 14px; cursor: grab; user-select: none; text-align: center; line-height: 1; }
    .sl-drag-handle:active { cursor: grabbing; }
    .sl-num { font-size: 11px; color: var(--text-4); font-weight: 600; text-align: center; }

    .sl-cell-title { display: flex; flex-direction: column; gap: 3px; }
    .sl-song-input { background: none; border: none; color: var(--text); font-size: 13.5px; font-family: inherit; outline: none; width: 100%; padding: 0; }
    .sl-song-input::placeholder { color: var(--text-4); }
    .sl-song-input.title { font-weight: 500; }

    .sl-key-select { background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 12.5px; padding: 5px 4px; outline: none; font-family: inherit; width: 100%; transition: .15s; cursor: pointer; }
    .sl-key-select:focus { border-color: var(--accent); }

    .sl-bpm-input { background: none; border: none; color: var(--text-3); font-size: 12px; font-family: inherit; outline: none; width: 100%; padding: 0; text-align: center; }
    .sl-bpm-input::placeholder { color: var(--text-4); font-size: 11px; }

    .sl-note-input { background: none; border: none; color: var(--text-3); font-size: 12px; font-family: inherit; outline: none; width: 100%; padding: 0; }
    .sl-note-input::placeholder { color: var(--text-4); font-size: 11px; }

    .sl-del-btn { background: none; border: none; color: var(--text-4); font-size: 15px; cursor: pointer; padding: 4px; border-radius: 6px; transition: color .15s, background .15s; font-family: inherit; text-align: center; }
    .sl-del-btn:hover { color: #f87171; background: rgba(248,113,113,.1); }

    /* ACTIONS */
    .sl-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 1.2rem; }
    .sl-btn { padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; border: none; transition: .15s; }
    .sl-btn-add { background: var(--accent); color: #fff; }
    .sl-btn-add:hover { opacity: .88; }
    .sl-btn-sec { background: none; border: 1px solid var(--border); color: var(--text-3); }
    .sl-btn-sec:hover { border-color: var(--accent); color: var(--accent); }
    .sl-btn-print { background: none; border: 1px solid var(--border); color: var(--text-2); }
    .sl-btn-print:hover { border-color: var(--accent); color: var(--accent); }

    /* TOTALS BAR */
    .sl-totals { display: flex; gap: 18px; flex-wrap: wrap; padding: .8rem 1rem; background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; margin-top: .75rem; font-size: 12.5px; color: var(--text-3); }
    .sl-totals span b { color: var(--text-2); }

    /* PRINT PREVIEW */
    .sl-preview-box { margin-top: 1.5rem; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .sl-preview-bar { display: flex; justify-content: space-between; align-items: center; padding: .7rem 1rem; background: var(--bg-2); border-bottom: 1px solid var(--border); }
    .sl-preview-bar-title { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--text-3); }
    .sl-preview-content { padding: 1.5rem; background: var(--bg); }

    .print-setlist { font-family: 'Courier New', monospace; color: var(--text); }
    .print-event-name { font-size: 1.2rem; font-weight: 700; margin-bottom: .15rem; }
    .print-event-meta { font-size: 12px; color: var(--text-3); margin-bottom: 1rem; }
    .print-divider { border: none; border-top: 1px solid var(--border); margin: .75rem 0; }
    .print-song-row { display: grid; grid-template-columns: 28px 1fr 55px 45px; gap: 8px; padding: 5px 0; border-bottom: 1px dashed var(--border-2); font-size: 13px; }
    .print-song-row:last-child { border-bottom: none; }
    .print-num { color: var(--text-4); font-weight: 700; }
    .print-title { color: var(--text); font-weight: 500; }
    .print-key { color: var(--accent); font-size: 11px; font-weight: 700; text-align: center; }
    .print-bpm { color: var(--text-3); font-size: 11px; text-align: right; }
    .print-note { font-size: 11px; color: var(--text-3); grid-column: 2 / 5; padding-top: 1px; font-style: italic; }

    .sl-share-row { margin-top: .75rem; display: flex; gap: 8px; flex-wrap: wrap; }

    /* INFO */
    .sl-tip { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 1rem 1.1rem; margin-top: 1.5rem; font-size: 12.5px; color: var(--text-3); line-height: 1.7; }
    .sl-tip b { color: var(--text-2); }

    @media print {
        body > *:not(.sl-wrap) { display: none !important; }
        .sl-wrap > *:not(.sl-preview-box) { display: none !important; }
        .sl-preview-box { border: none !important; }
        .sl-preview-bar { display: none !important; }
        .print-setlist { color: #000 !important; }
        .print-event-name, .print-title { color: #000 !important; }
        .print-divider { border-color: #ccc !important; }
        .print-song-row { border-color: #ddd !important; }
        .print-event-meta, .print-num, .print-bpm, .print-note { color: #555 !important; }
        .print-key { color: #0077aa !important; }
    }
</style>
@endpush

@section('content')
<div class="page-rail-wrap">
<div class="sl-wrap">
    <div class="sl-header">
        <h1 class="sl-title">🎵 Setlist Builder Musisi</h1>
        <p class="sl-sub">Susun urutan lagu, catat kunci & BPM, tambah catatan — cetak atau simpan PDF sebelum manggung.</p>
    </div>

    @include('partials.tool-share', ['origin' => $origin])

    {{-- EVENT META --}}
    <div class="sl-meta-grid">
        <div class="sl-field">
            <label class="sl-label">Nama Acara / Venue</label>
            <input type="text" id="slEvent" class="sl-input" placeholder="cth: Open Mic Kedai Tempo, Yogyakarta">
        </div>
        <div class="sl-field">
            <label class="sl-label">Tanggal & Waktu</label>
            <input type="text" id="slDate" class="sl-input" placeholder="cth: 28 Juni 2026, 20:00 WIB">
        </div>
        <div class="sl-field">
            <label class="sl-label">Nama Artis / Band</label>
            <input type="text" id="slArtist" class="sl-input" placeholder="cth: EMINOR" value="EMINOR">
        </div>
        <div class="sl-field">
            <label class="sl-label">Durasi per Lagu (menit, default)</label>
            <input type="number" id="slDefaultDur" class="sl-input" value="4" min="1" max="30" style="width:100px;">
        </div>
    </div>

    {{-- SONG LIST --}}
    <div class="sl-list-header">
        <div></div>
        <div class="sl-col-label">Judul Lagu</div>
        <div class="sl-col-label">Kunci</div>
        <div class="sl-col-label sl-col-bpm">BPM</div>
        <div class="sl-col-label sl-col-notes">Catatan</div>
        <div></div>
    </div>

    <div id="sl-songs"></div>

    <div class="sl-actions">
        <button type="button" class="sl-btn sl-btn-add" onclick="addSong()">+ Tambah Lagu</button>
        <button type="button" class="sl-btn sl-btn-sec" onclick="addBreak()">⏸ Jeda / Intro</button>
        <button type="button" class="sl-btn sl-btn-sec" onclick="clearAll()">🗑 Kosongkan</button>
        <button type="button" class="sl-btn sl-btn-print" onclick="printSetlist()">🖨 Cetak / PDF</button>
    </div>

    <div class="sl-totals" id="sl-totals">
        <span>Lagu: <b id="t-songs">0</b></span>
        <span>Durasi est.: <b id="t-dur">0 mnt</b></span>
        <span>BPM rata-rata: <b id="t-bpm">—</b></span>
    </div>

    {{-- PRINT PREVIEW --}}
    <div class="sl-preview-box">
        <div class="sl-preview-bar">
            <span class="sl-preview-bar-title">Preview Setlist</span>
            <div style="display:flex;gap:8px;">
                <button type="button" class="sl-btn sl-btn-sec" style="padding:5px 14px;font-size:12px;" onclick="copyText()">📋 Salin teks</button>
                <button type="button" class="sl-btn sl-btn-sec" style="padding:5px 14px;font-size:12px;" onclick="downloadTxt()">⬇ .txt</button>
            </div>
        </div>
        <div class="sl-preview-content">
            <div class="print-setlist" id="print-setlist">
                <div class="print-event-name" id="pr-event">— Setlist Baru —</div>
                <div class="print-event-meta" id="pr-meta"></div>
                <hr class="print-divider">
                <div id="pr-songs"></div>
            </div>
        </div>
    </div>

    <div class="sl-tip">
        <b>Tips manggung:</b><br>
        • Urutkan dari lagu <em>mid-energy</em>, naik ke puncak energi di tengah, turun ke ballad, lalu tutup kuat.<br>
        • Beri catatan singkat: "intro panjang", "capo 2", "tanpa drum intro", dll — kamu akan berterima kasih saat di atas panggung.<br>
        • Cetak 3 salinan: satu kamu, satu ke sound engineer, satu ditempel di lantai panggung (setlist tape).
    </div>

</div>{{-- .sl-wrap --}}

<aside class="page-rail-aside">
    @include('partials.content-rail')
</aside>

</div>{{-- .page-rail-wrap --}}
@endsection

@push('scripts')
<script>
var songs  = [];
var dragSrc = null;
var uid    = 0;

var KEYS = ['—','C','Db','D','Eb','E','F','F#','G','Ab','A','Bb','B',
            'Cm','Dm','Em','Fm','Gm','Am','Bm'];

function makeSong(title, isBreak) {
    return { id: ++uid, title: title || '', key: '—', bpm: '', notes: '', dur: '', isBreak: !!isBreak };
}

function addSong(title) { songs.push(makeSong(title || '')); render(); }
function addBreak()     { songs.push(makeSong('— Jeda / Tuning —', true)); render(); }

function deleteSong(id) {
    songs = songs.filter(function(s){ return s.id !== id; });
    render();
}

function clearAll() {
    if (songs.length && !confirm('Kosongkan semua lagu?')) return;
    songs = [];
    render();
}

function render() {
    var list = document.getElementById('sl-songs');
    list.innerHTML = '';
    songs.forEach(function(s, i) {
        var row = document.createElement('div');
        row.className = 'sl-song-row' + (s.isBreak ? ' sl-break-row' : '');
        row.setAttribute('data-id', s.id);
        row.draggable = true;

        var keyOpts = KEYS.map(function(k){
            return '<option value="'+k+'"'+(s.key===k?' selected':'')+'>'+k+'</option>';
        }).join('');

        row.innerHTML =
            '<div class="sl-drag-handle" title="Seret untuk urutkan">⠿</div>' +
            '<div class="sl-cell-title">' +
                '<input type="text" class="sl-song-input title" placeholder="Judul lagu #'+(i+1)+'" value="'+esc(s.title)+'" data-field="title" data-id="'+s.id+'">' +
            '</div>' +
            '<select class="sl-key-select" data-field="key" data-id="'+s.id+'">'+keyOpts+'</select>' +
            '<div class="sl-cell-bpm"><input type="number" class="sl-bpm-input" placeholder="BPM" min="40" max="250" value="'+esc(s.bpm)+'" data-field="bpm" data-id="'+s.id+'"></div>' +
            '<div class="sl-cell-notes"><input type="text" class="sl-note-input" placeholder="catatan..." value="'+esc(s.notes)+'" data-field="notes" data-id="'+s.id+'"></div>' +
            '<button type="button" class="sl-del-btn" data-del="'+s.id+'" title="Hapus">✕</button>';

        // drag events
        row.addEventListener('dragstart', function(e){
            dragSrc = this;
            setTimeout(function(){ row.classList.add('dragging'); }, 0);
            e.dataTransfer.effectAllowed = 'move';
        });
        row.addEventListener('dragend', function(){
            row.classList.remove('dragging');
            document.querySelectorAll('.sl-song-row').forEach(function(r){ r.classList.remove('drag-over'); });
        });
        row.addEventListener('dragover', function(e){
            e.preventDefault(); e.dataTransfer.dropEffect = 'move';
            if (this !== dragSrc) this.classList.add('drag-over');
        });
        row.addEventListener('dragleave', function(){ this.classList.remove('drag-over'); });
        row.addEventListener('drop', function(e){
            e.preventDefault(); this.classList.remove('drag-over');
            if (!dragSrc || dragSrc === this) return;
            var fromId = parseInt(dragSrc.getAttribute('data-id'));
            var toId   = parseInt(this.getAttribute('data-id'));
            var fromIdx = songs.findIndex(function(s){ return s.id === fromId; });
            var toIdx   = songs.findIndex(function(s){ return s.id === toId; });
            if (fromIdx < 0 || toIdx < 0) return;
            var moved = songs.splice(fromIdx, 1)[0];
            songs.splice(toIdx, 0, moved);
            render();
        });

        list.appendChild(row);
    });

    // field change listeners
    list.querySelectorAll('[data-field][data-id]').forEach(function(el){
        el.addEventListener('input', function(){
            var id    = parseInt(this.getAttribute('data-id'));
            var field = this.getAttribute('data-field');
            var s     = songs.find(function(s){ return s.id === id; });
            if (s) { s[field] = this.value; updatePreview(); updateTotals(); }
        });
        el.addEventListener('change', function(){
            var id    = parseInt(this.getAttribute('data-id'));
            var field = this.getAttribute('data-field');
            var s     = songs.find(function(s){ return s.id === id; });
            if (s) { s[field] = this.value; updatePreview(); updateTotals(); }
        });
    });

    // delete listeners
    list.querySelectorAll('[data-del]').forEach(function(btn){
        btn.addEventListener('click', function(){
            deleteSong(parseInt(this.getAttribute('data-del')));
        });
    });

    updatePreview();
    updateTotals();
}

function esc(s) {
    return (s || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function updateTotals() {
    var real = songs.filter(function(s){ return !s.isBreak; });
    var defaultDur = parseFloat(document.getElementById('slDefaultDur').value) || 4;
    var totalDur = real.reduce(function(acc, s){
        return acc + (parseFloat(s.dur) || defaultDur);
    }, 0);
    var bpms = real.map(function(s){ return parseInt(s.bpm); }).filter(function(b){ return !isNaN(b) && b > 0; });
    var avgBpm = bpms.length ? Math.round(bpms.reduce(function(a,b){ return a+b; }, 0) / bpms.length) : null;

    document.getElementById('t-songs').textContent = real.length;
    document.getElementById('t-dur').textContent   = totalDur.toFixed(0) + ' mnt (~' + (totalDur/60).toFixed(1) + ' jam)';
    document.getElementById('t-bpm').textContent   = avgBpm ? avgBpm + ' BPM' : '—';
}

function updatePreview() {
    var event  = document.getElementById('slEvent').value  || '— Setlist —';
    var date   = document.getElementById('slDate').value;
    var artist = document.getElementById('slArtist').value;

    document.getElementById('pr-event').textContent = event;
    var metaParts = [];
    if (artist) metaParts.push(artist);
    if (date)   metaParts.push(date);
    document.getElementById('pr-meta').textContent = metaParts.join(' · ');

    var container = document.getElementById('pr-songs');
    container.innerHTML = '';
    var realIdx = 0;
    songs.forEach(function(s){
        if (s.isBreak) {
            var br = document.createElement('div');
            br.style.cssText = 'text-align:center;font-size:11px;color:var(--text-4);padding:6px 0;letter-spacing:.1em;';
            br.textContent = '— ' + (s.title || 'JEDA') + ' —';
            container.appendChild(br);
            return;
        }
        realIdx++;
        var row = document.createElement('div');
        row.className = 'print-song-row';
        row.innerHTML =
            '<span class="print-num">'+realIdx+'</span>' +
            '<span class="print-title">'+(s.title || '(belum diisi)')+'</span>' +
            '<span class="print-key">'+(s.key !== '—' ? s.key : '')+'</span>' +
            '<span class="print-bpm">'+(s.bpm ? s.bpm+' BPM' : '')+'</span>';
        if (s.notes) {
            var note = document.createElement('span');
            note.className = 'print-note';
            note.textContent = '↳ ' + s.notes;
            row.appendChild(note);
        }
        container.appendChild(row);
    });
}

function buildText() {
    var event  = document.getElementById('slEvent').value  || 'Setlist';
    var date   = document.getElementById('slDate').value;
    var artist = document.getElementById('slArtist').value;
    var lines  = [event];
    if (artist || date) lines.push([artist, date].filter(Boolean).join(' · '));
    lines.push('---');
    var realIdx = 0;
    songs.forEach(function(s){
        if (s.isBreak) { lines.push('    [' + (s.title || 'JEDA') + ']'); return; }
        realIdx++;
        var line = realIdx + '. ' + (s.title || '(kosong)');
        if (s.key !== '—') line += '  [' + s.key + ']';
        if (s.bpm)         line += '  ' + s.bpm + ' BPM';
        if (s.notes)       line += '  — ' + s.notes;
        lines.push(line);
    });
    return lines.join('\n');
}

function copyText() {
    navigator.clipboard.writeText(buildText()).then(function(){
        var btn = event.currentTarget || document.querySelector('[onclick="copyText()"]');
        var orig = btn.textContent;
        btn.textContent = '✓ Tersalin!';
        setTimeout(function(){ btn.textContent = orig; }, 1800);
    });
}

function downloadTxt() {
    var blob = new Blob([buildText()], { type: 'text/plain;charset=utf-8' });
    var a    = document.createElement('a');
    a.href   = URL.createObjectURL(blob);
    a.download = 'setlist-' + (document.getElementById('slEvent').value || 'EMINOR').replace(/\s+/g,'-').toLowerCase() + '.txt';
    a.click();
    URL.revokeObjectURL(a.href);
}

function printSetlist() {
    updatePreview();
    window.print();
}

// meta input listeners
['slEvent','slDate','slArtist','slDefaultDur'].forEach(function(id){
    document.getElementById(id).addEventListener('input', function(){
        updatePreview(); updateTotals();
    });
});

// start with 5 empty songs
for (var i = 0; i < 5; i++) addSong();
</script>
@endpush

