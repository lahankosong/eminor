@extends('layouts.app')

@push('styles')
<style>
    .admin-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid #1a1a1a;
    }
    .admin-header h2 { font-size: 1rem; font-weight: 500; color: #fff; }
    .admin-header p { font-size: 12px; color: #555; margin-top: 2px; }
    .btn-add {
        padding: 8px 18px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: #fff; color: #000;
        text-decoration: none; transition: 0.2s; border: none; cursor: pointer;
    }
    .btn-add:hover { background: #ddd; }

    .alert {
        padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem;
        font-size: 13px;
    }
    .alert-success { background: #0d2e1a; color: #4ade80; border: 1px solid #166534; }
    .alert-error { background: #2e0d0d; color: #f87171; border: 1px solid #991b1b; }

    .songs-table { width: 100%; border-collapse: collapse; }
    .songs-table th {
        font-size: 11px; color: #555; letter-spacing: 0.1em;
        text-transform: uppercase; padding: 8px 12px;
        border-bottom: 1px solid #1a1a1a; text-align: left;
    }
    .songs-table td {
        padding: 12px; border-bottom: 1px solid #111;
        font-size: 13px; color: #ccc; vertical-align: middle;
    }
    .songs-table tr:hover td { background: #0d0d0d; }
    .song-num { color: #444; font-size: 12px; }
    .song-ytid {
        font-family: monospace; font-size: 11px;
        color: #555; background: #111; padding: 2px 6px; border-radius: 4px;
    }
    .badge {
        display: inline-block; padding: 2px 8px; border-radius: 20px;
        font-size: 11px; font-weight: 500;
    }
    .badge-active { background: #0d2e1a; color: #4ade80; }
    .badge-inactive { background: #1a1a1a; color: #555; }
    .badge-chord { background: #1a1a2e; color: #60a5fa; }
    .badge-nochord { background: #1a1a1a; color: #444; }

    .tbl-actions { display: flex; gap: 6px; }
    .btn-edit {
        padding: 4px 12px; border-radius: 6px; font-size: 11px;
        font-weight: 500; background: transparent; border: 1px solid #2a2a2a;
        color: #aaa; cursor: pointer; text-decoration: none; transition: 0.15s;
    }
    .btn-edit:hover { border-color: #fff; color: #fff; }
    .btn-delete {
        padding: 4px 12px; border-radius: 6px; font-size: 11px;
        font-weight: 500; background: transparent; border: 1px solid #2a2a2a;
        color: #666; cursor: pointer; transition: 0.15s;
    }
    .btn-delete:hover { border-color: #ef4444; color: #ef4444; }

    .stats-row {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 12px; margin-bottom: 2rem;
    }
    .stat-card {
        background: #0d0d0d; border: 1px solid #1a1a1a;
        border-radius: 10px; padding: 1rem;
    }
    .stat-num { font-size: 22px; font-weight: 500; color: #fff; }
    .stat-label { font-size: 11px; color: #555; margin-top: 2px; }
</style>
@endpush

@section('content')

<div class="admin-header">
    <div>
        <h2>Panel Admin</h2>
        <p>Kelola lagu, lirik, dan chord — Margonoandi</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.settings') }}" class="btn-add" style="background:#111;color:#888;border:1px solid #2a2a2a;">
            &#9881; Pengaturan
        </a>
        <a href="{{ route('admin.ai-agent') }}" class="btn-add" style="background:#0a0a1a;color:#60a5fa;border:1px solid #1a1a3a;">
            &#10024; AI Agent
        </a>
        <a href="{{ route('admin.create') }}" class="btn-add">+ Tambah Lagu</a>
    </div>
</div>


@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-num">{{ $songs->count() }}</div>
        <div class="stat-label">Total lagu</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $songs->where('is_active', 1)->count() }}</div>
        <div class="stat-label">Lagu aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $songs->whereNotNull('chords')->where('chords', '!=', '')->count() }}</div>
        <div class="stat-label">Sudah ada chord</div>
    </div>
</div>

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
    <tbody>
        @foreach($songs as $song)
        <tr>
            <td class="song-num">{{ $song->track_number }}</td>
            <td style="color:#fff;">{{ $song->title }}</td>
            <td><span class="song-ytid">{{ $song->youtube_id }}</span></td>
            <td style="color:#888;">{{ $song->key_signature ?? '—' }}</td>
            <td>
                @if($song->chords)
                    <span class="badge badge-chord">Ada chord</span>
                @else
                    <span class="badge badge-nochord">Belum</span>
                @endif
            </td>
            <td>
                @if($song->is_active)
                    <span class="badge badge-active">Aktif</span>
                @else
                    <span class="badge badge-inactive">Nonaktif</span>
                @endif
            </td>
            <td>
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
    </tbody>
</table>

@endsection