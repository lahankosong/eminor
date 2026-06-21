@extends('layouts.fanbase')
@section('title', 'Cari Personil')

@push('styles')
<style>
    .bp-hero { background: linear-gradient(145deg, var(--orange), #f0a050); border-radius: 20px; padding: 1.5rem; color: #fff; margin-bottom: 1.25rem; box-shadow: var(--shadow-lg); }
    .bp-hero h2 { font-family: 'Sora',sans-serif; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
    .bp-hero p { font-size: 13px; color: rgba(255,255,255,0.85); }
    .bp-hero-actions { margin-top: 1rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .bp-btn { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
    .bp-btn-solid { background: #fff; color: #c2410c; }
    .bp-btn-ghost { background: rgba(255,255,255,0.2); color: #fff; }

    .bp-list { display: flex; flex-direction: column; gap: 12px; }
    .bp-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 1rem 1.1rem; text-decoration: none; display: block; transition: 0.15s; box-shadow: var(--shadow-sm); }
    .bp-card:hover { border-color: var(--orange); box-shadow: var(--shadow-md); }
    .bp-card-head { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .bp-title { font-family: 'Sora',sans-serif; font-weight: 700; font-size: 15px; color: var(--text-1); flex: 1; }
    .bp-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 700; }
    .bp-badge.urgent { background: #fee2e2; color: #b91c1c; }
    .bp-badge.open { background: #dcfce7; color: #15803d; }
    [data-theme="dark"] .bp-badge.urgent { background: rgba(239,68,68,0.18); color: #fca5a5; }
    [data-theme="dark"] .bp-badge.open { background: rgba(34,197,94,0.18); color: #86efac; }
    .bp-badge.closed { background: var(--surface); color: var(--text-3); }
    .bp-tags { display: flex; flex-wrap: wrap; gap: 5px; margin: 6px 0; }
    .bp-tag { font-size: 11px; padding: 2px 9px; border-radius: 20px; background: var(--surface); color: var(--orange); border: 1px solid var(--border-lt); font-weight: 600; }
    .bp-tag.g { color: var(--text-3); font-weight: 400; }
    .bp-foot { display: flex; align-items: center; gap: 8px; margin-top: 8px; font-size: 12px; color: var(--text-3); }
    .bp-foot img { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
    .bp-empty { text-align: center; color: var(--text-3); padding: 2.5rem 1rem; font-size: 13px; }
</style>
@endpush

@section('content')

<div class="bp-hero">
    <h2>🎯 Cari Personil</h2>
    <p>Pasang lowongan atau temukan band yang sedang mencari personil.</p>
    <div class="bp-hero-actions">
        <a href="{{ route('band.create') }}" class="bp-btn bp-btn-solid">+ Pasang lowongan</a>
        <a href="{{ route('musisi.index') }}" class="bp-btn bp-btn-ghost">🎸 Direktori musisi</a>
    </div>
</div>

@if(session('success'))
<div style="background:#0d2e1a;color:#4ade80;border:1px solid #166534;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">{{ session('success') }}</div>
@endif

<div class="bp-list">
    @forelse($posts as $bp)
    <a href="{{ route('band.show', $bp->id) }}" class="bp-card">
        <div class="bp-card-head">
            <span class="bp-title">{{ $bp->title }}</span>
            @if($bp->urgent && $bp->status === 'open')<span class="bp-badge urgent">URGENT</span>@endif
            <span class="bp-badge {{ $bp->status }}">{{ $bp->status === 'open' ? 'Dibuka' : 'Ditutup' }}</span>
        </div>
        <div class="bp-tags">
            @foreach($bp->rolesArray() as $r)<span class="bp-tag">🎵 {{ $r }}</span>@endforeach
            @foreach($bp->genresArray() as $g)<span class="bp-tag g">{{ $g }}</span>@endforeach
        </div>
        <div class="bp-foot">
            <img src="{{ $bp->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
            <span>{{ $bp->user->name ?? 'Member' }}</span>
            @if($bp->location)<span>· 📍 {{ $bp->location }}</span>@endif
            <span>· {{ $bp->created_at->diffForHumans() }}</span>
        </div>
    </a>
    @empty
    <div class="bp-empty">Belum ada lowongan.<br>Jadilah yang pertama pasang — klik "Pasang lowongan".</div>
    @endforelse
</div>

@endsection
