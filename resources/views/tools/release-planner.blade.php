@extends('layouts.app')

@push('styles')
<style>
    .rp-wrap { max-width: 820px; margin: 0 auto; padding: 2rem 1rem 5rem; }
    .rp-title { font-size: clamp(1.4rem,4vw,2rem); font-weight: 300; color: var(--text); margin-bottom: .3rem; }
    .rp-sub { font-size: 14px; color: var(--text-3); margin-bottom: 1.75rem; }

    /* INPUT FORM */
    .rp-form { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 1.5rem; }
    @media(max-width:600px){ .rp-form { grid-template-columns: 1fr; } }
    .rp-field { display: flex; flex-direction: column; gap: 5px; }
    .rp-label { font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--text-3); }
    .rp-input { background: var(--card-bg,rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 13.5px; padding: 9px 13px; outline: none; font-family: inherit; transition: .15s; }
    .rp-input:focus { border-color: var(--accent); }

    /* PROGRESS */
    .rp-progress-bar-wrap { height: 5px; background: var(--bg-3); border-radius: 5px; margin-bottom: 1.5rem; overflow: hidden; }
    .rp-progress-bar { height: 5px; background: linear-gradient(90deg, var(--accent), #22c55e); border-radius: 5px; transition: width .4s; }
    .rp-progress-label { display: flex; justify-content: space-between; font-size: 11px; color: var(--text-3); margin-bottom: .4rem; }

    /* PHASES */
    .rp-phase { margin-bottom: 1.25rem; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .rp-phase-head { display: flex; align-items: center; gap: 12px; padding: .9rem 1.1rem; cursor: pointer; transition: background .15s; }
    .rp-phase-head:hover { background: var(--card-bg); }
    .rp-phase.past .rp-phase-head { opacity: .6; }
    .rp-phase-badge { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 12px; white-space: nowrap; flex-shrink: 0; }
    .rp-phase-title { font-size: 14px; font-weight: 600; color: var(--text); flex: 1; }
    .rp-phase-date { font-size: 11px; color: var(--text-3); white-space: nowrap; }
    .rp-phase-check-count { font-size: 11px; color: var(--text-3); }
    .rp-chevron { font-size: 12px; color: var(--text-4); transition: transform .2s; flex-shrink: 0; }
    .rp-phase.open .rp-chevron { transform: rotate(180deg); }

    .rp-tasks { display: none; padding: 0 1.1rem 1rem; border-top: 1px solid var(--border); }
    .rp-phase.open .rp-tasks { display: block; }

    .rp-task { display: flex; align-items: flex-start; gap: 10px; padding: .6rem 0; border-bottom: 1px solid var(--border-2); }
    .rp-task:last-child { border-bottom: none; }
    .rp-task input[type=checkbox] { margin-top: 2px; accent-color: var(--accent); width: 15px; height: 15px; flex-shrink: 0; cursor: pointer; }
    .rp-task-body { flex: 1; }
    .rp-task-text { font-size: 13.5px; color: var(--text-2); line-height: 1.5; }
    .rp-task input:checked + .rp-task-body .rp-task-text { text-decoration: line-through; color: var(--text-4); }
    .rp-task-tag { display: inline-block; font-size: 10px; padding: 2px 7px; border-radius: 8px; margin-top: 3px; font-weight: 600; }
    .rp-task-link { font-size: 11px; color: var(--accent); text-decoration: none; margin-left: 4px; }
    .rp-task-link:hover { text-decoration: underline; }

    /* ACTIONS */
    .rp-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.5rem; }
    .rp-btn { padding: 9px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; border: none; transition: .15s; }
    .rp-btn-primary { background: var(--accent); color: #fff; }
    .rp-btn-primary:hover { opacity: .88; }
    .rp-btn-sec { background: none; border: 1px solid var(--border); color: var(--text-3); }
    .rp-btn-sec:hover { border-color: var(--accent); color: var(--accent); }

    /* EMPTY STATE */
    .rp-empty { text-align: center; padding: 3rem 1rem; color: var(--text-3); }
    .rp-empty-icon { font-size: 2.5rem; margin-bottom: .75rem; }

    @media print {
        body > *:not(.rp-wrap) { display: none !important; }
        .rp-actions, .rp-form, .rp-title + p { display: none !important; }
        .rp-phase { page-break-inside: avoid; }
        .rp-tasks { display: block !important; }
    }
</style>
@endpush

@section('content')
<div class="rp-wrap">
    <h1 class="rp-title">📅 Music Release Planner</h1>
    <p class="rp-sub">Masukkan tanggal rilis — jadwal promo minggu per minggu langsung siap. Tandai checklist, cetak, selesai.</p>

    @include('partials.tool-share', ['origin' => $origin])

    <div class="rp-form">
        <div class="rp-field">
            <label class="rp-label">Judul Lagu / EP</label>
            <input type="text" id="rpTitle" class="rp-input" placeholder="cth: Bersamamu" oninput="generate()">
        </div>
        <div class="rp-field">
            <label class="rp-label">Nama Artis</label>
            <input type="text" id="rpArtist" class="rp-input" placeholder="cth: Margonoandi" value="Margonoandi" oninput="generate()">
        </div>
        <div class="rp-field">
            <label class="rp-label">Tanggal Rilis</label>
            <input type="date" id="rpDate" class="rp-input" oninput="generate()">
        </div>
    </div>

    <div class="rp-actions">
        <button type="button" class="rp-btn rp-btn-sec" onclick="expandAll()">⬇ Buka Semua</button>
        <button type="button" class="rp-btn rp-btn-sec" onclick="collapseAll()">⬆ Tutup Semua</button>
        <button type="button" class="rp-btn rp-btn-sec" onclick="resetChecks()">↺ Reset Checklist</button>
        <button type="button" class="rp-btn rp-btn-primary" onclick="window.print()">🖨 Cetak / PDF</button>
    </div>

    <div class="rp-progress-label">
        <span id="rp-prog-text">Pilih tanggal rilis untuk mulai</span>
        <span id="rp-prog-pct"></span>
    </div>
    <div class="rp-progress-bar-wrap"><div class="rp-progress-bar" id="rp-bar" style="width:0%"></div></div>

    <div id="rp-timeline">
        <div class="rp-empty">
            <div class="rp-empty-icon">🗓</div>
            <p>Masukkan tanggal rilis di atas untuk membuat jadwal promo otomatis.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var PHASES = [
    {
        key: 'p42', offsetDays: -42, label: '6 Minggu Sebelum', emoji: '🎙️',
        color: '#6366f1', bg: 'rgba(99,102,241,.12)',
        tasks: [
            { text: 'Finalisasi master audio — kirim ke mastering engineer jika belum', tag: 'audio' },
            { text: 'Buat artwork cover: 3000×3000px minimum, RGB, JPG/PNG, tidak ada teks terlalu kecil', tag: 'desain', link: { text: 'Buat Cover Art', route: '{{ route("tools.cover-art") }}' } },
            { text: 'Daftar atau login ke distributor (DistroKid / TuneCore / CD Baby / Labelku)', tag: 'distribusi' },
            { text: 'Submit lagu ke distributor — proses approval 3–7 hari kerja, jangan mepet', tag: 'distribusi' },
            { text: 'Tulis lirik resmi dan siapkan untuk upload ke Genius/AZLyrics', tag: 'konten' },
            { text: 'Lengkapi metadata: judul, artis, featuring, penulis lagu, ISRC', tag: 'metadata', link: { text: 'Edit Metadata', route: '{{ route("tools.edit-metadata") }}' } },
        ]
    },
    {
        key: 'p28', offsetDays: -28, label: '4 Minggu Sebelum', emoji: '🔗',
        color: '#8b5cf6', bg: 'rgba(139,92,246,.12)',
        tasks: [
            { text: 'Buat pre-save link di Spotify for Artists setelah lagu approved distributor', tag: 'promo' },
            { text: 'Pitch ke playlist editorial Spotify — wajib dilakukan 7 hari sebelum rilis (lewat Spotify for Artists)', tag: 'playlist' },
            { text: 'Hubungi kurator playlist indie independen (DM Instagram, email)', tag: 'playlist' },
            { text: 'Rekam teaser video 15–30 detik untuk Instagram Reels / TikTok', tag: 'konten' },
            { text: 'Siapkan jadwal posting konten — buat 5–7 konten sekarang untuk dijadwal', tag: 'konten' },
            { text: 'Hubungi media / blogger musik indie untuk review atau wawancara', tag: 'pr' },
        ]
    },
    {
        key: 'p21', offsetDays: -21, label: '3 Minggu Sebelum', emoji: '📢',
        color: '#f59e0b', bg: 'rgba(245,158,11,.12)',
        tasks: [
            { text: 'Announce tanggal rilis + pre-save link di semua sosial media', tag: 'promo' },
            { text: 'Aktifkan countdown link di bio Instagram / linktree', tag: 'promo', link: { text: 'Buat Countdown', route: '{{ route("tools.countdown") }}' } },
            { text: 'Buat atau update Electronic Press Kit (EPK) untuk media dan booker', tag: 'pr', link: { text: 'EPK Generator', route: '{{ route("tools.epk") }}' } },
            { text: 'Kirim email blast ke mailing list jika ada', tag: 'email' },
            { text: 'Posting konten "behind the scenes" — proses rekaman, nulis lagu, mixing', tag: 'konten' },
            { text: 'Follow up media yang belum balas dari minggu lalu', tag: 'pr' },
        ]
    },
    {
        key: 'p14', offsetDays: -14, label: '2 Minggu Sebelum', emoji: '🎬',
        color: '#f97316', bg: 'rgba(249,115,22,.12)',
        tasks: [
            { text: 'Rilis konten BTS intensif: story harian, behind the mic, cerita di balik lirik', tag: 'konten' },
            { text: 'Pin post/tweet tentang tanggal rilis di semua platform', tag: 'promo' },
            { text: 'Hubungi radio indie / podcast musik lokal untuk promo atau interview', tag: 'pr' },
            { text: 'Reminder ke semua kolaborator (co-writer, produser, musisi) untuk turut promo', tag: 'tim' },
            { text: 'Buat kartu promo rilis (feed 1:1 dan story 9:16)', tag: 'desain', link: { text: 'Kartu Promo Rilis', route: '{{ route("tools.kartu-rilis") }}' } },
            { text: 'Siapkan caption panjang (storytelling) tentang makna lagu untuk hari rilis', tag: 'konten' },
        ]
    },
    {
        key: 'p7', offsetDays: -7, label: '1 Minggu Sebelum', emoji: '🔥',
        color: '#ef4444', bg: 'rgba(239,68,68,.12)',
        tasks: [
            { text: 'Posting countdown harian (7, 6, 5, 4, 3, 2, 1 hari lagi)', tag: 'promo' },
            { text: 'Share cuplikan lirik atau kutipan bermakna setiap hari', tag: 'konten' },
            { text: 'Engage aktif dengan semua komentar dan DM yang masuk', tag: 'engagement' },
            { text: 'Hubungi fanbase terdekat secara personal (WhatsApp, DM) untuk support di hari-H', tag: 'komunitas' },
            { text: 'Test semua link: pre-save, sosmed bio, press kit', tag: 'teknis' },
            { text: 'Siapkan setlist kalau ada rencana live / siaran live hari rilis', tag: 'persiapan', link: { text: 'Setlist Builder', route: '{{ route("tools.setlist") }}' } },
        ]
    },
    {
        key: 'p0', offsetDays: 0, label: '🎉 Hari Rilis', emoji: '🚀',
        color: '#22c55e', bg: 'rgba(34,197,94,.12)',
        tasks: [
            { text: 'Posting di semua platform sesuai jadwal (Instagram, TikTok, Twitter/X, Facebook, YouTube)', tag: 'rilis' },
            { text: 'Update semua bio link ke lagu / pre-save → stream', tag: 'rilis' },
            { text: 'Balas SEMUA komentar dan DM dalam 2 jam pertama — algoritma menghargai ini', tag: 'engagement' },
            { text: 'Share ke grup WhatsApp, komunitas musik, dan teman terdekat', tag: 'komunitas' },
            { text: 'Minta teman dan kolaborator untuk share, add to playlist, dan save', tag: 'komunitas' },
            { text: 'Live / siaran langsung (opsional): first listen bersama atau akustik singkat', tag: 'live' },
            { text: 'Screenshot momen pertama "Selamat, lagumu sudah rilis!" dari distributor', tag: 'dokumentasi' },
        ]
    },
    {
        key: 'a7', offsetDays: 7, label: 'Minggu 1 Setelah Rilis', emoji: '📊',
        color: '#38A8CC', bg: 'rgba(56,168,204,.12)',
        tasks: [
            { text: 'Cek statistik pertama: Spotify for Artists, YouTube Studio, DistroKid Stats', tag: 'analisis' },
            { text: 'Screenshot angka streaming pertama dan share sebagai "update" ke followers', tag: 'promo' },
            { text: 'Repost dan reply ke semua listener yang share / cerita tentang lagumu', tag: 'engagement' },
            { text: 'Kirim terima kasih personal ke yang sudah aktif support', tag: 'komunitas' },
            { text: 'Submit ke Spotify untuk Fresh Finds dan playlist editorial lainnya yang terlewat', tag: 'playlist' },
            { text: 'Pertimbangkan lyric video atau visualizer murah di YouTube', tag: 'konten' },
        ]
    },
    {
        key: 'a14', offsetDays: 14, label: 'Minggu 2 Setelah Rilis', emoji: '🎸',
        color: '#0ea5e9', bg: 'rgba(14,165,233,.12)',
        tasks: [
            { text: 'Rilis konten sekunder: versi akustik, cover art timelapse, cerita rekaman panjang', tag: 'konten' },
            { text: 'Buat video tutorial chord atau cover lagu untuk YouTube / TikTok', tag: 'konten' },
            { text: 'Pitch ke kurator playlist independen kedua (ada ribuan di Spotify, bukan hanya editorial)', tag: 'playlist' },
            { text: 'Follow up semua media / blogger yang belum merespons', tag: 'pr' },
            { text: 'Cari gig / open mic untuk perform lagu baru secara live', tag: 'live', link: { text: 'Lihat Papan Gig', route: '{{ route("gig.board") }}' } },
        ]
    },
    {
        key: 'a28', offsetDays: 28, label: 'Bulan 1 Setelah Rilis', emoji: '🔄',
        color: '#6b7280', bg: 'rgba(107,114,128,.1)',
        tasks: [
            { text: 'Review performa lengkap: stream, save rate, playlist add, reach, engagement rate', tag: 'analisis' },
            { text: 'Identifikasi: konten apa yang paling banyak bring traffic? Platform mana terbaik?', tag: 'analisis' },
            { text: 'Dokumentasi semua pelajaran dari proses rilis ini untuk panduan rilis berikutnya', tag: 'dokumentasi' },
            { text: 'Mulai brainstorm materi baru — momentum masih hangat, jangan hilang', tag: 'kreatif' },
            { text: 'Update rate card kalau sudah ada streaming numbers sebagai social proof', tag: 'karir', link: { text: 'Rate Card Generator', route: '{{ route("tools.rate-card") }}' } },
        ]
    },
];

var TAG_COLORS = {
    audio: '#6366f1', desain: '#a855f7', distribusi: '#f59e0b', konten: '#22c55e',
    metadata: '#38A8CC', promo: '#ef4444', playlist: '#f97316', pr: '#ec4899',
    email: '#0ea5e9', tim: '#84cc16', engagement: '#14b8a6', komunitas: '#8b5cf6',
    teknis: '#64748b', persiapan: '#f59e0b', rilis: '#22c55e', live: '#f97316',
    dokumentasi: '#6b7280', analisis: '#38A8CC', kreatif: '#a855f7', karir: '#f59e0b',
};

var storageKey = '';

function generate() {
    var title  = document.getElementById('rpTitle').value.trim();
    var artist = document.getElementById('rpArtist').value.trim();
    var date   = document.getElementById('rpDate').value;

    if (!date) {
        document.getElementById('rp-timeline').innerHTML =
            '<div class="rp-empty"><div class="rp-empty-icon">🗓</div><p>Masukkan tanggal rilis di atas untuk membuat jadwal promo otomatis.</p></div>';
        document.getElementById('rp-prog-text').textContent = 'Pilih tanggal rilis untuk mulai';
        document.getElementById('rp-prog-pct').textContent = '';
        document.getElementById('rp-bar').style.width = '0%';
        return;
    }

    storageKey = 'rp_' + date + '_' + title.replace(/\s+/g,'_').toLowerCase();
    var saved  = getSaved();
    var releaseDate = new Date(date);
    var today  = new Date();
    today.setHours(0,0,0,0);

    var html = '';
    PHASES.forEach(function(phase) {
        var phaseDate = new Date(releaseDate);
        phaseDate.setDate(phaseDate.getDate() + phase.offsetDays);

        var isPast    = phaseDate < today && phase.offsetDays !== 0;
        var isCurrent = Math.abs(phaseDate - today) < 7 * 86400000 || (phase.offsetDays === 0 && phaseDate <= new Date(today.getTime() + 86400000));
        var openClass = isCurrent ? ' open' : (phase.offsetDays >= 0 && !isPast ? ' open' : '');

        var checkedCount = 0;
        phase.tasks.forEach(function(t, ti) {
            if (saved[phase.key + '_' + ti]) checkedCount++;
        });

        var dateStr = phaseDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        html += '<div class="rp-phase' + (isPast ? ' past' : '') + openClass + '" id="phase-' + phase.key + '">';
        html += '<div class="rp-phase-head" onclick="togglePhase(\'' + phase.key + '\')">';
        html += '<span class="rp-phase-badge" style="background:' + phase.bg + ';color:' + phase.color + '">' + phase.emoji + ' ' + phase.label + '</span>';
        html += '<span class="rp-phase-date">' + dateStr + '</span>';
        html += '<span class="rp-phase-check-count" style="margin-left:auto;margin-right:8px;">' + checkedCount + '/' + phase.tasks.length + '</span>';
        html += '<span class="rp-chevron">▼</span>';
        html += '</div>';

        html += '<div class="rp-tasks">';
        phase.tasks.forEach(function(t, ti) {
            var cbId  = 'cb_' + phase.key + '_' + ti;
            var ckd   = saved[phase.key + '_' + ti] ? ' checked' : '';
            var color = TAG_COLORS[t.tag] || '#6b7280';
            html += '<div class="rp-task">';
            html += '<input type="checkbox" id="' + cbId + '"' + ckd + ' onchange="saveCheck(\'' + phase.key + '\',' + ti + ',this.checked)">';
            html += '<div class="rp-task-body">';
            html += '<label for="' + cbId + '" class="rp-task-text" style="cursor:pointer;display:block;">' + t.text + '</label>';
            var tagHtml = '<span class="rp-task-tag" style="background:' + color + '22;color:' + color + '">' + t.tag + '</span>';
            if (t.link) tagHtml += '<a href="' + t.link.route + '" class="rp-task-link" target="_blank">→ ' + t.link.text + '</a>';
            html += '<div style="margin-top:3px;">' + tagHtml + '</div>';
            html += '</div></div>';
        });
        html += '</div></div>';
    });

    document.getElementById('rp-timeline').innerHTML = html;
    updateProgress();
}

function togglePhase(key) {
    var el = document.getElementById('phase-' + key);
    el.classList.toggle('open');
}

function expandAll() {
    document.querySelectorAll('.rp-phase').forEach(function(el){ el.classList.add('open'); });
}
function collapseAll() {
    document.querySelectorAll('.rp-phase').forEach(function(el){ el.classList.remove('open'); });
}

function getSaved() {
    try { return JSON.parse(localStorage.getItem(storageKey) || '{}'); } catch(e){ return {}; }
}

function saveCheck(phaseKey, taskIdx, checked) {
    var saved = getSaved();
    var k = phaseKey + '_' + taskIdx;
    if (checked) saved[k] = 1; else delete saved[k];
    try { localStorage.setItem(storageKey, JSON.stringify(saved)); } catch(e){}
    updatePhaseCount(phaseKey);
    updateProgress();
}

function updatePhaseCount(phaseKey) {
    var phase = PHASES.find(function(p){ return p.key === phaseKey; });
    if (!phase) return;
    var saved = getSaved();
    var count = 0;
    phase.tasks.forEach(function(t, ti){ if (saved[phaseKey + '_' + ti]) count++; });
    var el = document.querySelector('#phase-' + phaseKey + ' .rp-phase-check-count');
    if (el) el.textContent = count + '/' + phase.tasks.length;
}

function updateProgress() {
    var total = 0, checked = 0;
    var saved = getSaved();
    PHASES.forEach(function(phase){
        total += phase.tasks.length;
        phase.tasks.forEach(function(t, ti){ if (saved[phase.key + '_' + ti]) checked++; });
    });
    var pct = total ? Math.round(checked / total * 100) : 0;
    document.getElementById('rp-bar').style.width = pct + '%';
    document.getElementById('rp-prog-text').textContent = checked + ' dari ' + total + ' tugas selesai';
    document.getElementById('rp-prog-pct').textContent = pct + '%';
}

function resetChecks() {
    if (!storageKey || !confirm('Reset semua checklist?')) return;
    try { localStorage.removeItem(storageKey); } catch(e){}
    generate();
}

// Set default release date 4 minggu dari sekarang
(function(){
    var d = new Date();
    d.setDate(d.getDate() + 28);
    document.getElementById('rpDate').value = d.toISOString().split('T')[0];
    generate();
})();
</script>
@endpush
