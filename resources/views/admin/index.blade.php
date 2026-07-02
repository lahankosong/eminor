@extends('layouts.admin')

@section('title', 'Production Dashboard')

@push('styles')
<style>
    /* ===== PAGE HEADER ===== */
    .dash-header {
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:10px; margin-bottom:1.5rem;
    }
    .dash-title {
        font-family:'Sora',sans-serif;
        font-size:1.1rem; font-weight:600; color:var(--text-1);
    }
    .dash-date { font-size:12px; color:var(--text-3); margin-top:2px; }
    .btn-primary {
        display:inline-flex; align-items:center; gap:6px;
        padding:8px 16px; border-radius:20px; font-size:13px; font-weight:500;
        background:var(--sky); color:#fff; text-decoration:none;
        border:none; cursor:pointer; transition:0.18s;
    }
    .btn-primary:hover { background:var(--sky-dk); }
    .btn-ghost {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 14px; border-radius:20px; font-size:12px; font-weight:500;
        background:transparent; color:var(--text-2);
        border:1px solid var(--border); text-decoration:none; transition:0.18s;
    }
    .btn-ghost:hover { border-color:var(--text-3); color:var(--text-1); }

    /* ===== METRIC CARDS ===== */
    .metric-grid {
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:12px;
        margin-bottom:1.5rem;
    }
    .metric-card {
        border-radius:var(--radius);
        padding:1.1rem 1.25rem;
        color:#fff;
        position:relative; overflow:hidden;
    }
    .metric-card::after {
        content:''; position:absolute;
        bottom:-20px; right:-20px;
        width:80px; height:80px; border-radius:50%;
        background:rgba(255,255,255,0.08);
    }
    .metric-card.sky    { background:#38A8CC; }
    .metric-card.green  { background:#22c55e; }
    .metric-card.orange { background:#F07040; }
    .metric-card.purple { background:#a855f7; }
    .metric-num {
        font-family:'Sora',sans-serif;
        font-size:2rem; font-weight:700; line-height:1;
        margin-bottom:4px;
    }
    .metric-label { font-size:12px; opacity:0.85; font-weight:500; }
    .metric-sub   { font-size:10px; opacity:0.65; margin-top:2px; }

    /* ===== 2-COL GRID ===== */
    .dash-body {
        display:grid;
        grid-template-columns:1fr 320px;
        gap:1.25rem;
        margin-bottom:2rem;
    }

    /* ===== CARD CONTAINER ===== */
    .dash-card {
        background:var(--card);
        border:1px solid var(--border-lt);
        border-radius:var(--radius);
        overflow:hidden;
        box-shadow:var(--shadow-sm);
    }
    .dash-card-head {
        display:flex; align-items:center; justify-content:space-between;
        padding:1rem 1.25rem 0.75rem;
        border-bottom:1px solid var(--border-lt);
    }
    .dash-card-title {
        font-family:'Sora',sans-serif;
        font-size:13px; font-weight:600; color:var(--text-1);
    }
    .dash-card-link {
        font-size:11px; color:var(--sky-dk); text-decoration:none;
    }
    .dash-card-link:hover { text-decoration:underline; }

    /* ===== ACTIVITY FEED ===== */
    .activity-list { padding:0.5rem 0; }
    .activity-item {
        display:flex; align-items:flex-start; gap:10px;
        padding:10px 1.25rem; transition:0.15s;
    }
    .activity-item:hover { background:var(--surface); }
    .activity-avatar {
        width:32px; height:32px; border-radius:50%;
        object-fit:cover; flex-shrink:0; border:2px solid var(--border-lt);
    }
    .activity-avatar-placeholder {
        width:32px; height:32px; border-radius:50%; flex-shrink:0;
        background:var(--surface); border:2px solid var(--border-lt);
        display:flex; align-items:center; justify-content:center;
        font-size:14px;
    }
    .activity-body { flex:1; min-width:0; }
    .activity-name {
        font-size:12px; font-weight:600; color:var(--text-1);
    }
    .activity-text {
        font-size:11px; color:var(--text-3);
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        margin-top:1px;
    }
    .activity-time { font-size:10px; color:var(--text-4); flex-shrink:0; margin-top:2px; }
    .activity-badge {
        display:inline-block; padding:1px 6px; border-radius:10px;
        font-size:9px; font-weight:600; letter-spacing:0.04em;
        margin-right:4px; vertical-align:middle;
    }
    .badge-post   { background:#EEF7FB; color:#1E7FA8; }
    .badge-member { background:#f0fdf4; color:#16a34a; }
    .activity-empty { text-align:center; padding:2rem 1rem; font-size:12px; color:var(--text-4); }

    /* ===== RIGHT COLUMN ===== */
    /* Quick Actions */
    .qa-list { padding:0.75rem 1.25rem 1rem; display:flex; flex-direction:column; gap:8px; }
    .qa-item {
        display:flex; align-items:center; gap:10px;
        padding:9px 12px; border-radius:10px;
        text-decoration:none; color:var(--text-2);
        font-size:12px; font-weight:500;
        border:1px solid var(--border-lt); transition:0.15s;
        background:var(--surface);
    }
    .qa-item:hover { border-color:var(--sky); color:var(--sky-dk); background:var(--sky-lt); }
    .qa-item-icon { font-size:16px; flex-shrink:0; }
    .qa-item-sub { font-size:10px; color:var(--text-4); margin-top:1px; }

    /* Top Songs */
    .topsong-list { padding:0.5rem 0 0.75rem; }
    .topsong-item {
        display:flex; align-items:center; gap:10px;
        padding:8px 1.25rem; transition:0.15s;
    }
    .topsong-item:hover { background:var(--surface); }
    .topsong-rank {
        width:20px; text-align:center;
        font-size:11px; font-weight:600; color:var(--text-4); flex-shrink:0;
    }
    .topsong-rank.top1 { color:#F07040; }
    .topsong-rank.top2 { color:#7A9DB0; }
    .topsong-rank.top3 { color:#B0C8D4; }
    .topsong-info { flex:1; min-width:0; }
    .topsong-title {
        font-size:12px; font-weight:600; color:var(--text-1);
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .topsong-meta { font-size:10px; color:var(--text-4); margin-top:1px; }
    .topsong-badge {
        font-size:10px; padding:2px 7px; border-radius:10px;
        background:var(--sky-lt); color:var(--sky-dk); font-weight:500; flex-shrink:0;
    }
    .topsong-badge.featured { background:#FFF0E8; color:#C84E20; }

    /* ===== SONGS TABLE SECTION ===== */
    .songs-section { margin-top:0.5rem; }
    .songs-section-head {
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:8px; margin-bottom:1rem;
    }
    .section-title {
        font-family:'Sora',sans-serif;
        font-size:14px; font-weight:600; color:var(--text-1);
    }

    /* Stats bar */
    .stats-row {
        display:grid; grid-template-columns:repeat(4,1fr);
        gap:10px; margin-bottom:1rem;
    }
    .stat-card {
        background:var(--card); border:1px solid var(--border);
        border-radius:10px; padding:0.875rem 1rem;
        cursor:pointer; transition:0.15s; text-align:left;
    }
    .stat-card:hover { border-color:var(--sky-mid); }
    .stat-card.active { border-color:var(--sky); background:var(--sky-lt); }
    .stat-num   { font-size:1.25rem; font-weight:600; color:var(--text-1); }
    .stat-label { font-size:11px; color:var(--text-3); margin-top:2px; }

    /* Toolbar */
    .toolbar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:0.75rem; }
    .search-box { position:relative; flex:1; min-width:180px; }
    .search-box input {
        width:100%; background:var(--card); border:1px solid var(--border);
        border-radius:24px; color:var(--text-1); font-size:13px;
        padding:8px 14px 8px 34px; outline:none; transition:0.15s; font-family:inherit;
    }
    .search-box input:focus { border-color:var(--sky-mid); box-shadow:0 0 0 3px var(--sky-glow); }
    .search-box::before {
        content:'🔍'; position:absolute; left:12px; top:50%;
        transform:translateY(-50%); font-size:12px; opacity:0.4; pointer-events:none;
    }
    .filter-chips { display:flex; gap:6px; flex-wrap:wrap; }
    .chip {
        padding:5px 12px; border-radius:20px; font-size:12px;
        background:var(--card); border:1px solid var(--border); color:var(--text-3);
        cursor:pointer; transition:0.15s; white-space:nowrap;
    }
    .chip:hover { border-color:var(--sky-mid); color:var(--text-2); }
    .chip.active { background:var(--text-1); color:#fff; border-color:var(--text-1); }
    .era-select {
        padding:6px 10px; border-radius:8px; font-size:12px;
        background:var(--card); border:1px solid var(--border); color:var(--text-2);
        cursor:pointer; outline:none; font-family:inherit;
    }
    .result-count { font-size:11px; color:var(--text-4); margin-bottom:8px; }

    /* Table */
    .songs-table { width:100%; border-collapse:collapse; }
    .songs-table th {
        font-size:10px; color:var(--text-3); letter-spacing:0.1em;
        text-transform:uppercase; padding:8px 12px;
        border-bottom:1px solid var(--border-lt); text-align:left; background:var(--surface);
    }
    .songs-table td {
        padding:11px 12px; border-bottom:1px solid var(--border-lt);
        font-size:13px; color:var(--text-2); vertical-align:middle;
    }
    .songs-table tr:last-child td { border-bottom:none; }
    .songs-table tr:hover td { background:var(--surface); }
    .song-num { color:var(--text-4); font-size:12px; }
    .song-ytid {
        font-family:monospace; font-size:11px;
        color:var(--text-3); background:var(--surface); padding:2px 6px; border-radius:4px;
    }
    .badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:500; }
    .badge-active   { background:#dcfce7; color:#15803d; }
    .badge-inactive { background:var(--surface); color:var(--text-3); }
    .badge-chord    { background:var(--sky-lt); color:var(--sky-dk); }
    .badge-nochord  { background:var(--surface); color:var(--text-3); }
    .tbl-actions { display:flex; gap:6px; }
    .btn-edit {
        padding:4px 12px; border-radius:6px; font-size:11px; font-weight:500;
        background:transparent; border:1px solid var(--border);
        color:var(--text-2); cursor:pointer; text-decoration:none; transition:0.15s;
    }
    .btn-edit:hover { border-color:var(--sky-mid); color:var(--sky-dk); }
    .btn-delete {
        padding:4px 12px; border-radius:6px; font-size:11px; font-weight:500;
        background:transparent; border:1px solid var(--border);
        color:var(--text-3); cursor:pointer; transition:0.15s;
    }
    .btn-delete:hover { border-color:#fca5a5; color:#ef4444; }
    .empty-row td { text-align:center; color:var(--text-4); padding:2rem; font-size:13px; }

    /* ===== RESPONSIVE ===== */
    @media (max-width:900px) {
        .metric-grid { grid-template-columns:repeat(2,1fr); }
        .dash-body { grid-template-columns:1fr; }
    }
    @media (max-width:640px) {
        .metric-grid { grid-template-columns:repeat(2,1fr); gap:8px; }
        .metric-num { font-size:1.5rem; }
        .stats-row { grid-template-columns:repeat(2,1fr); }
        .songs-table thead { display:none; }
        .songs-table, .songs-table tbody, .songs-table tr, .songs-table td { display:block; width:100%; }
        .songs-table tr {
            margin-bottom:10px; border:1px solid var(--border);
            border-radius:10px; padding:4px; background:var(--card);
        }
        .songs-table tr:hover td { background:transparent; }
        .songs-table td {
            border:none; padding:7px 14px; display:flex;
            justify-content:space-between; align-items:center; gap:12px;
        }
        .songs-table td::before {
            content:attr(data-label); font-size:10px; text-transform:uppercase;
            letter-spacing:0.08em; color:var(--text-3); font-weight:600; flex-shrink:0;
        }
        .songs-table td[data-label="Judul"] { font-size:14px; color:var(--text-1); font-weight:600; }
        .tbl-actions { justify-content:flex-end; }
    }
</style>
@endpush

@section('content')

{{-- PAGE HEADER --}}
<div class="dash-header">
    <div>
        <div class="dash-title">Production Dashboard</div>
        <div class="dash-date">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.ai-agent') }}" class="btn-ghost">✨ AI Agent</a>
        <a href="{{ route('admin.create') }}" class="btn-primary">+ Tambah Lagu</a>
    </div>
</div>

{{-- METRIC CARDS --}}
<div class="metric-grid">
    <div class="metric-card sky">
        <div class="metric-num">{{ $totalMembers }}</div>
        <div class="metric-label">Total Member</div>
        <div class="metric-sub">terdaftar di platform</div>
    </div>
    <div class="metric-card green">
        <div class="metric-num">{{ $dau }}</div>
        <div class="metric-label">Aktif 24 Jam</div>
        <div class="metric-sub">daily active users</div>
    </div>
    <div class="metric-card orange">
        <div class="metric-num">{{ $totalSongs }}</div>
        <div class="metric-label">Total Lagu</div>
        <div class="metric-sub">{{ $activeSongs }} aktif dipublikasi</div>
    </div>
    <div class="metric-card purple">
        <div class="metric-num">{{ $totalPosts }}</div>
        <div class="metric-label">Post Kita</div>
        <div class="metric-sub">total postingan komunitas</div>
    </div>
</div>

{{-- TRAFFIC SECTION --}}
@php
    $todayHp  = $traffic['today_hp'] ?? 0;
    $todayFb  = $traffic['today_fb'] ?? 0;
    $totalHp  = $traffic['total_hp'] ?? 0;
    $totalFb  = $traffic['total_fb'] ?? 0;
    $convRate = $totalHp > 0 ? round(($totalFb / $totalHp) * 100, 1) : 0;
    $trend    = $traffic['trend'] ?? [];
    $maxBar   = collect($trend)->max(fn($d) => max($d['hp'], $d['fb'])) ?: 1;
@endphp
<div class="dash-card" style="margin-bottom:1.25rem;">
    <div class="dash-card-head">
        <div class="dash-card-title">📈 Lalu Lintas Trafik</div>
        <span style="font-size:11px;color:var(--text-4);">Per sesi unik · reset saat browser ditutup</span>
    </div>
    <div style="padding:1rem 1.25rem;">
        {{-- Funnel cards --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:1.25rem;">
            <div style="background:var(--sky-lt);border:1px solid var(--border);border-radius:12px;padding:1rem 1.1rem;">
                <div style="font-size:1.6rem;font-weight:700;color:var(--sky-dk);font-family:'Sora',sans-serif;">{{ $todayHp }}</div>
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:2px;">Landing Page Hari Ini</div>
                <div style="font-size:10px;color:var(--text-4);margin-top:1px;">Total all-time: {{ number_format($totalHp) }}</div>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem 1.1rem;">
                <div style="font-size:1.6rem;font-weight:700;color:#15803d;font-family:'Sora',sans-serif;">{{ $todayFb }}</div>
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:2px;">Masuk Ekosistem Hari Ini</div>
                <div style="font-size:10px;color:var(--text-4);margin-top:1px;">Total all-time: {{ number_format($totalFb) }}</div>
            </div>
            <div style="background:#fdf4ff;border:1px solid #e9d5ff;border-radius:12px;padding:1rem 1.1rem;">
                <div style="font-size:1.6rem;font-weight:700;color:#9333ea;font-family:'Sora',sans-serif;">{{ $convRate }}%</div>
                <div style="font-size:12px;font-weight:600;color:var(--text-2);margin-top:2px;">Conversion Rate</div>
                <div style="font-size:10px;color:var(--text-4);margin-top:1px;">Landing → masuk Ekosistem</div>
            </div>
        </div>

        {{-- Bar chart 7 hari --}}
        @if(!empty($trend))
        <div style="font-size:11px;font-weight:600;color:var(--text-3);margin-bottom:8px;letter-spacing:0.04em;">7 HARI TERAKHIR</div>
        <div style="display:flex;gap:6px;align-items:flex-end;height:80px;">
            @foreach($trend as $day)
            @php
                $hpH = $maxBar > 0 ? round(($day['hp'] / $maxBar) * 70) : 0;
                $fbH = $maxBar > 0 ? round(($day['fb'] / $maxBar) * 70) : 0;
            @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                <div style="width:100%;display:flex;gap:2px;align-items:flex-end;height:70px;">
                    <div style="flex:1;height:{{ $hpH }}px;background:var(--sky);border-radius:3px 3px 0 0;min-height:{{ $day['hp'] > 0 ? 3 : 0 }}px;" title="Landing: {{ $day['hp'] }}"></div>
                    <div style="flex:1;height:{{ $fbH }}px;background:#22c55e;border-radius:3px 3px 0 0;min-height:{{ $day['fb'] > 0 ? 3 : 0 }}px;" title="Ekosistem: {{ $day['fb'] }}"></div>
                </div>
                <div style="font-size:9px;color:var(--text-4);white-space:nowrap;">{{ $day['label'] }}</div>
            </div>
            @endforeach
        </div>
        <div style="display:flex;gap:12px;margin-top:8px;">
            <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--text-3);">
                <div style="width:10px;height:10px;background:var(--sky);border-radius:2px;"></div> Landing Page
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:10px;color:var(--text-3);">
                <div style="width:10px;height:10px;background:#22c55e;border-radius:2px;"></div> Ekosistem
            </div>
        </div>
        @endif
    </div>
</div>

{{-- DASHBOARD BODY: 2 cols --}}
<div class="dash-body">

    {{-- LEFT: RECENT ACTIVITY --}}
    <div class="dash-card">
        <div class="dash-card-head">
            <div class="dash-card-title">Aktivitas Terbaru</div>
            <button type="button" class="dash-card-link" style="background:none;border:none;cursor:pointer;font:inherit;"
                onclick="var l=document.getElementById('activityList');var h=(l.style.display==='none');l.style.display=h?'':'none';this.textContent=h?'Sembunyikan':'Tampilkan';">Sembunyikan</button>
        </div>
        <div class="activity-list" id="activityList">
            @forelse($recentActivity as $item)
            <div class="activity-item">
                @if($item->user?->avatar)
                    <img class="activity-avatar"
                         src="{{ $item->user->avatar }}"
                         alt="{{ $item->user->name }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'">
                @else
                    <div class="activity-avatar-placeholder">
                        {{ $item->type === 'member' ? '🎉' : '📝' }}
                    </div>
                @endif
                <div class="activity-body">
                    <div class="activity-name">
                        <span class="activity-badge {{ $item->type === 'member' ? 'badge-member' : 'badge-post' }}">
                            {{ $item->type === 'member' ? 'Member' : 'Post' }}
                        </span>
                        {{ $item->user?->name ?? 'Pengguna' }}
                    </div>
                    <div class="activity-text">{{ $item->text }}</div>
                </div>
                <div class="activity-time">{{ $item->time_h }}</div>
            </div>
            @empty
            <div class="activity-empty">Belum ada aktivitas.</div>
            @endforelse
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- TOP SONGS --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-title">Paling Diputar</div>
                <a href="#songs" class="dash-card-link">{{ number_format($totalPlays ?? 0) }}× total</a>
            </div>
            <div class="topsong-list">
                @forelse($topSongs as $i => $song)
                <div class="topsong-item">
                    <div class="topsong-rank {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="topsong-info">
                        <div class="topsong-title">{{ $song->title }}</div>
                        <div class="topsong-meta">{{ $song->era ?? 'Era' }} · <b style="color:var(--accent,#38A8CC);">{{ number_format($song->play_count ?? 0) }}×</b> diputar</div>
                    </div>
                    @if($song->featured)
                        <span class="topsong-badge featured">⭐ Featured</span>
                    @else
                        <span class="topsong-badge">Aktif</span>
                    @endif
                </div>
                @empty
                <div style="text-align:center;padding:1.5rem;font-size:12px;color:var(--text-4);">
                    Belum ada lagu aktif.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- SONGS MANAGEMENT TABLE --}}
<div class="songs-section" id="songs">
    <div class="songs-section-head">
        <div class="section-title">Kelola Lagu</div>
        <a href="{{ route('admin.create') }}" class="btn-primary" style="font-size:12px;padding:7px 14px;">+ Tambah Lagu</a>
    </div>

    @php
        $totalSongsAll  = $songs->count();
        $activeSongsAll = $songs->where('is_active', 1)->count();
        $inactiveSongs  = $songs->where('is_active', 0)->count();
        $noChordSongs   = $songs->filter(fn($s) => empty($s->chords))->count();
        $eras           = $songs->pluck('era')->filter()->unique()->values();
    @endphp

    <div class="stats-row">
        <button type="button" class="stat-card" data-filter="all" onclick="setStatus('all',this)">
            <div class="stat-num">{{ $totalSongsAll }}</div>
            <div class="stat-label">Total lagu</div>
        </button>
        <button type="button" class="stat-card" data-filter="active" onclick="setStatus('active',this)">
            <div class="stat-num">{{ $activeSongsAll }}</div>
            <div class="stat-label">Aktif</div>
        </button>
        <button type="button" class="stat-card" data-filter="inactive" onclick="setStatus('inactive',this)">
            <div class="stat-num">{{ $inactiveSongs }}</div>
            <div class="stat-label">Nonaktif</div>
        </button>
        <button type="button" class="stat-card" data-filter="nochord" onclick="setStatus('nochord',this)">
            <div class="stat-num">{{ $noChordSongs }}</div>
            <div class="stat-label">Tanpa chord</div>
        </button>
    </div>

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
        <span class="chip active" data-filter="all"      onclick="setStatus('all',this)">Semua</span>
        <span class="chip"        data-filter="active"   onclick="setStatus('active',this)">Aktif</span>
        <span class="chip"        data-filter="inactive" onclick="setStatus('inactive',this)">Nonaktif</span>
        <span class="chip"        data-filter="chord"    onclick="setStatus('chord',this)">Ada chord</span>
        <span class="chip"        data-filter="nochord"  onclick="setStatus('nochord',this)">Tanpa chord</span>
    </div>
    <div class="result-count" id="resultCount" style="margin-top:8px;"></div>

    <div class="dash-card" style="margin-top:0.75rem;overflow:auto;">
        <table class="songs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>YouTube ID</th>
                    <th>Key</th>
                    <th>Chord</th>
                    <th>Diputar</th>
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
                    <td data-label="Judul" style="color:var(--text-1);font-weight:500;">{{ $song->title }}</td>
                    <td data-label="YouTube ID"><span class="song-ytid">{{ $song->youtube_id }}</span></td>
                    <td data-label="Key">{{ $song->key_signature ?? '—' }}</td>
                    <td data-label="Chord">
                        @if($song->chords)
                            <span class="badge badge-chord">Ada chord</span>
                        @else
                            <span class="badge badge-nochord">Belum</span>
                        @endif
                    </td>
                    <td data-label="Diputar" style="font-weight:600;color:var(--text-1);">{{ number_format($song->play_count ?? 0) }}<span style="color:var(--text-3);font-weight:400;">×</span></td>
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
                                  onsubmit="return confirm('Hapus lagu {{ addslashes($song->title) }}?')">
                                @csrf @method('DELETE')
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
    </div>
</div>

@endsection

@push('scripts')
<script>
var statusFilter = 'all';

function setStatus(val, el) {
    statusFilter = val;
    document.querySelectorAll('#filterChips .chip').forEach(function(c) {
        c.classList.toggle('active', c.getAttribute('data-filter') === val);
    });
    document.querySelectorAll('.stat-card').forEach(function(c) {
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
        if      (statusFilter === 'active')   okStatus = active;
        else if (statusFilter === 'inactive') okStatus = !active;
        else if (statusFilter === 'chord')    okStatus = chord;
        else if (statusFilter === 'nochord')  okStatus = !chord;

        var visible = okSearch && okEra && okStatus;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });

    document.getElementById('emptyRow').style.display = shown === 0 ? '' : 'none';
    document.getElementById('resultCount').textContent =
        'Menampilkan ' + shown + ' dari {{ $totalSongsAll }} lagu';
}

applyFilters();
</script>
@endpush
