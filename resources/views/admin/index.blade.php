@extends('layouts.app')

@push('styles')
<style>
    .admin-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
        margin-bottom: 1.5rem; padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .admin-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .admin-header p { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .quick-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-add {
        padding: 8px 16px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: var(--text); color: var(--bg);
        text-decoration: none; transition: 0.2s; border: none; cursor: pointer;
        white-space: nowrap;
    }
    .btn-add:hover { filter: brightness(0.88); }
    .btn-ghost { background: var(--bg-2); color: var(--text-2); border: 1px solid var(--border); }
    .btn-accent { background: var(--accent-dim); color: var(--accent); border: 1px solid transparent; }

    .alert { padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 13px; }
    .alert-success { background: #0d2e1a; color: #4ade80; border: 1px solid #166534; }
    .alert-error { background: #2e0d0d; color: #f87171; border: 1px solid #991b1b; }

    /* STATS (clickable) */
    .stats-row {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 12px; margin-bottom: 1.25rem;
    }
    .stat-card {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 10px; padding: 1rem; cursor: pointer; transition: 0.15s;
        text-align: left;
    }
    .stat-card:hover { border-color: var(--text-3); }
    .stat-card.active { border-color: var(--accent); background: var(--accent-dim); }
    .stat-num   { font-size: 22px; font-weight: 500; color: var(--text); }
    .stat-label { font-size: 11px; color: var(--text-3); margin-top: 2px; }

    /* SEARCH + FILTER */
    .toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
    .search-box { position: relative; flex: 1; min-width: 200px; }
    .search-box input {
        width: 100%; background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 8px; color: var(--text); font-size: 13px;
        padding: 9px 12px 9px 32px; outline: none; transition: 0.15s; font-family: inherit;
    }
    .search-box input:focus { border-color: var(--text-3); }
    .search-box::before {
        content: '🔍'; position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); font-size: 12px; opacity: 0.5;
    }
    .filter-chips { display: flex; gap: 6px; flex-wrap: wrap; }
    .chip {
        padding: 6px 12px; border-radius: 20px; font-size: 12px;
        background: var(--bg-2); border: 1px solid var(--border); color: var(--text-2);
        cursor: pointer; transition: 0.15s; white-space: nowrap;
    }
    .chip:hover { border-color: var(--text-3); color: var(--text); }
    .chip.active { background: var(--text); color: var(--bg); border-color: var(--text); }
    .era-select {
        padding: 6px 10px; border-radius: 8px; font-size: 12px;
        background: var(--bg-2); border: 1px solid var(--border); color: var(--text-2);
        cursor: pointer; outline: none; font-family: inherit;
    }

    .result-count { font-size: 12px; color: var(--text-3); margin-bottom: 10px; }

    /* TABLE */
    .songs-table { width: 100%; border-collapse: collapse; }
    .songs-table th {
        font-size: 11px; color: var(--text-3); letter-spacing: 0.1em;
        text-transform: uppercase; padding: 8px 12px;
        border-bottom: 1px solid var(--border); text-align: left;
    }
    .songs-table td {
        padding: 12px; border-bottom: 1px solid var(--border-2);
        font-size: 13px; color: var(--text-2); vertical-align: middle;
    }
    .songs-table tr:hover td { background: var(--card-bg); }
    .song-num { color: var(--text-3); font-size: 12px; }
    .song-ytid {
        font-family: monospace; font-size: 11px;
        color: var(--text-3); background: var(--bg-3); padding: 2px 6px; border-radius: 4px;
    }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; }
    .badge-active  { background: #0d2e1a; color: #4ade80; }
    .badge-inactive{ background: var(--bg-3); color: var(--text-3); }
    .badge-chord   { background: var(--accent-dim); color: var(--accent); }
    .badge-nochord { background: var(--bg-3); color: var(--text-3); }

    .tbl-actions { display: flex; gap: 6px; }
    .btn-edit {
        padding: 4px 12px; border-radius: 6px; font-size: 11px;
        font-weight: 500; background: transparent; border: 1px solid var(--border);
        color: var(--text-2); cursor: pointer; text-decoration: none; transition: 0.15s;
    }
    .btn-edit:hover { border-color: var(--text-3); color: var(--text); }
    .btn-delete {
        padding: 4px 12px; border-radius: 6px; font-size: 11px;
        font-weight: 500; background: transparent; border: 1px solid var(--border);
        color: var(--text-3); cursor: pointer; transition: 0.15s;
    }
    .btn-delete:hover { border-color: #ef4444; color: #ef4444; }

    .empty-row td { text-align: center; color: var(--text-3); padding: 2rem; font-size: 13px; }

    /* MOBILE: tabel jadi kartu */
    @media (max-width: 768px) {
        .songs-table thead { display: none; }
        .songs-table, .songs-table tbody, .songs-table tr, .songs-table td { display: block; width: 100%; }
        .songs-table tr {
            margin-bottom: 12px; border: 1px solid var(--border);
            border-radius: 10px; padding: 6px 4px; background: var(--bg-2);
        }
        .songs-table tr:hover td { background: transparent; }
        .songs-table td {
            border: none; padding: 7px 14px; display: flex;
            justify-content: space-between; align-items: center; gap: 12px;
        }
        .songs-table td::before {
            content: attr(data-label); font-size: 10px; text-transform: uppercase;
            letter-spacing: 0.08em; color: var(--text-3); font-weight: 600; flex-shrink: 0;
        }
        .songs-table td[data-label="Judul"] { font-size: 15px; color: var(--text); font-weight: 600; }
        .tbl-actions { justify-content: flex-end; }
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')

<div class="admin-header">
    <div>
        <h2>Panel Admin</h2>
        <p>Kelola lagu, lirik, dan chord — Margonoandi</p>
    </div>
    <div class="quick-actions">
        <a href="{{ route('admin.settings') }}" class="btn-add btn-ghost">&#9881; Pengaturan</a>
        <a href="{{ route('admin.promo') }}" class="btn-add btn-ghost">&#128203; Promo</a>
        <a href="{{ route('admin.ai-agent') }}" class="btn-add btn-accent">&#10024; AI Agent</a>
        <a href="{{ route('admin.create') }}" class="btn-add">+ Tambah Lagu</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

@php
    $totalSongs = $songs->count();
    $activeSongs = $songs->where('is_active', 1)->count();
    $inactiveSongs = $songs->where('is_active', 0)->count();
    $noChordSongs = $songs->filter(fn($s) => empty($s->chords))->count();
    $eras = $songs->pluck('era')->filter()->unique()->values();
@endphp

{{-- STATS (klik untuk filter) --}}
<div class="stats-row">
    <button type="button" class="stat-card" data-filter="all" onclick="setStatus('all', this)">
        <div class="stat-num">{{ $totalSongs }}</div>
        <div class="stat-label">Total lagu</div>
    </button>
    <button type="button" class="stat-card" data-filter="active" onclick="setStatus('active', this)">
        <div class="stat-num">{{ $activeSongs }}</div>
        <div class="stat-label">Lagu aktif</div>
    </button>
    <button type="button" class="stat-card" data-filter="inactive" onclick="setStatus('inactive', this)">
        <div class="stat-num">{{ $inactiveSongs }}</div>
        <div class="stat-label">Nonaktif</div>
    </button>
    <button type="button" class="stat-card" data-filter="nochord" onclick="setStatus('nochord', this)">
        <div class="stat-num">{{ $noChordSongs }}</div>
        <div class="stat-label">Tanpa chord</div>
    </button>
</div>

{{-- SEARCH + FILTER --}}
<div class="toolbar">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari judul lagu..." oninput="applyFilters()">
    </div>
    @if($eras->count())
    <select class="era-select" id="eraSelect" onchange="applyFilters()">
        <option value="">Semua era</option>
        @foreach($eras as $era)
        <option value="{{ $era }}">{{ $era }}</option>
        @endforeach
    </select>
    @endif
</div>
<div class="filter-chips" id="filterChips">
    <span class="chip active" data-filter="all" onclick="setStatus('all', this)">Semua</span>
    <span class="chip" data-filter="active" onclick="setStatus('active', this)">Aktif</span>
    <span class="chip" data-filter="inactive" onclick="setStatus('inactive', this)">Nonaktif</span>
    <span class="chip" data-filter="chord" onclick="setStatus('chord', this)">Ada chord</span>
    <span class="chip" data-filter="nochord" onclick="setStatus('nochord', this)">Tanpa chord</span>
</div>

<div class="result-count" id="resultCount"></div>

<table class="songs-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Judul</th>
            <th>YouTube ID</th>
            <th>Key</th>
            <th>Chord</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody id="songsBody">
        @foreach($songs as $song)
        <tr class="song-row"
            data-title="{{ strtolower($song->title) }}"
            data-active="{{ $song->is_active ? 1 : 0 }}"
            data-chord="{{ empty($song->chords) ? 0 : 1 }}"
            data-era="{{ $song->era }}">
            <td class="song-num" data-label="#">{{ $song->track_number }}</td>
            <td data-label="Judul" style="color:var(--text);">{{ $song->title }}</td>
            <td data-label="YouTube ID"><span class="song-ytid">{{ $song->youtube_id }}</span></td>
            <td data-label="Key" style="color:var(--text-2);">{{ $song->key_signature ?? '—' }}</td>
            <td data-label="Chord">
                @if($song->chords)
                    <span class="badge badge-chord">Ada chord</span>
                @else
                    <span class="badge badge-nochord">Belum</span>
                @endif
            </td>
            <td data-label="Status">
                @if($song->is_active)
                    <span class="badge badge-active">Aktif</span>
                @else
                    <span class="badge badge-inactive">Nonaktif</span>
                @endif
            </td>
            <td data-label="Aksi">
                <div class="tbl-actions">
                    <a href="{{ route('admin.edit', $song->id) }}" class="btn-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.destroy', $song->id) }}"
                          onsubmit="return confirm('Hapus lagu {{ $song->title }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        <tr class="empty-row" id="emptyRow" style="display:none;">
            <td colspan="7">Tidak ada lagu yang cocok dengan filter.</td>
        </tr>
    </tbody>
</table>

<script>
var statusFilter = 'all';

function setStatus(val, el) {
    statusFilter = val;
    // sinkron highlight chip
    document.querySelectorAll('#filterChips .chip').forEach(function(c){
        c.classList.toggle('active', c.getAttribute('data-filter') === val);
    });
    // sinkron highlight stat card
    document.querySelectorAll('.stat-card').forEach(function(c){
        c.classList.toggle('active', c.getAttribute('data-filter') === val);
    });
    applyFilters();
}

function applyFilters() {
    var q   = (document.getElementById('searchInput').value || '').toLowerCase().trim();
    var era = (document.getElementById('eraSelect') || {}).value || '';
    var rows = document.querySelectorAll('.song-row');
    var shown = 0;

    rows.forEach(function(row) {
        var title  = row.getAttribute('data-title');
        var active = row.getAttribute('data-active') === '1';
        var chord  = row.getAttribute('data-chord') === '1';
        var rEra   = row.getAttribute('data-era') || '';

        var okSearch = !q || title.indexOf(q) !== -1;
        var okEra    = !era || rEra === era;
        var okStatus = true;
        if (statusFilter === 'active')   okStatus = active;
        else if (statusFilter === 'inactive') okStatus = !active;
        else if (statusFilter === 'chord')    okStatus = chord;
        else if (statusFilter === 'nochord')  okStatus = !chord;

        var visible = okSearch && okEra && okStatus;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });

    document.getElementById('emptyRow').style.display = shown === 0 ? '' : 'none';
    document.getElementById('resultCount').textContent =
        'Menampilkan ' + shown + ' dari {{ $totalSongs }} lagu';
}

applyFilters();
</script>

@endsection
