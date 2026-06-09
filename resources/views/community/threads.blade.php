@extends('layouts.app')

@push('styles')
<style>
    .community-nav {
        display: flex; gap: 4px; margin-bottom: 1.5rem;
        border-bottom: 1px solid #111; padding-bottom: 0;
        overflow-x: auto; -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .community-nav::-webkit-scrollbar { display: none; }
    .community-nav-btn {
        padding: 8px 16px; font-size: 12px; color: #444;
        background: transparent; border: none; cursor: pointer;
        border-bottom: 2px solid transparent; transition: 0.15s;
        margin-bottom: -1px; text-decoration: none;
        display: inline-block; white-space: nowrap;
    }
    .community-nav-btn:hover { color: #888; }
    .community-nav-btn.active { color: #fff; border-bottom-color: #fff; }

    .threads-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .threads-header h2 { font-size: 1rem; font-weight: 500; }
    .btn-new-thread {
        padding: 7px 16px; border-radius: 8px; font-size: 12px;
        font-weight: 500; background: #fff; color: #000;
        text-decoration: none; transition: 0.2s; border: none;
        white-space: nowrap;
    }
    .btn-new-thread:hover { background: #ddd; }

    .category-filter {
        display: flex; gap: 6px; flex-wrap: nowrap;
        margin-bottom: 1.25rem; overflow-x: auto;
        -webkit-overflow-scrolling: touch; scrollbar-width: none;
        padding-bottom: 4px;
    }
    .category-filter::-webkit-scrollbar { display: none; }
    .cat-btn {
        padding: 5px 14px; border-radius: 20px; font-size: 11px;
        border: 1px solid #1a1a1a; color: #555; background: transparent;
        cursor: pointer; text-decoration: none; transition: 0.15s;
        display: inline-block; white-space: nowrap; flex-shrink: 0;
    }
    .cat-btn:hover { border-color: #333; color: #aaa; }
    .cat-btn.active { background: #fff; color: #000; border-color: #fff; }

    .thread-list { display: flex; flex-direction: column; gap: 4px; }
    .thread-item {
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 10px; padding: 1rem;
        display: flex; gap: 10px; align-items: flex-start;
        transition: 0.15s;
    }
    .thread-item:hover { border-color: #1e1e1e; }
    .thread-item.pinned { border-color: #2a2a1a; background: #0a0a08; }
    .thread-item.locked { opacity: 0.7; }

    .thread-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        object-fit: cover; background: #111; flex-shrink: 0;
    }
    .thread-content { flex: 1; min-width: 0; }
    .thread-title-row {
        display: flex; align-items: flex-start; gap: 6px;
        margin-bottom: 4px; flex-wrap: wrap;
    }
    .thread-title {
        font-size: 13px; font-weight: 500; color: #ccc;
        text-decoration: none; transition: 0.15s; line-height: 1.4;
        flex: 1; min-width: 0;
    }
    .thread-title:hover { color: #fff; }
    .thread-badge {
        font-size: 10px; padding: 2px 7px; border-radius: 10px;
        font-weight: 500; flex-shrink: 0;
    }
    .badge-pinned { background: #1c1400; color: #a16207; border: 1px solid #2a1f00; }
    .badge-locked { background: #1a1a1a; color: #555; border: 1px solid #2a2a2a; }
    .badge-cat { background: #0a0a1a; color: #4a6fa5; border: 1px solid #1a1a3a; text-transform: capitalize; }

    .thread-excerpt {
        font-size: 12px; color: #3a3a3a; line-height: 1.5;
        margin-top: 3px; overflow: hidden;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }
    .thread-meta {
        font-size: 11px; color: #2a2a2a; margin-top: 5px;
        display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
    }
    .thread-stats-inline {
        display: flex; gap: 10px; margin-left: auto;
    }
    .thread-stat-inline { font-size: 11px; color: #333; }
    .thread-stat-inline strong { color: #555; }

    /* Hide right column stats on mobile, show inline */
    .thread-stats-col {
        display: flex; flex-direction: column; align-items: flex-end;
        gap: 4px; flex-shrink: 0; min-width: 72px;
    }
    .thread-stat { font-size: 11px; color: #333; text-align: right; }
    .thread-stat strong { color: #555; }
    .thread-last-reply { font-size: 10px; color: #2a2a2a; text-align: right; }

    .empty-threads {
        text-align: center; padding: 3rem 1rem;
        color: #333; font-size: 13px;
    }
    .pagination-wrap {
        display: flex; justify-content: center; gap: 8px; margin-top: 1.5rem;
    }
    .page-btn {
        padding: 6px 14px; border-radius: 8px; font-size: 12px;
        border: 1px solid #1a1a1a; color: #555; text-decoration: none;
        transition: 0.15s; background: transparent;
    }
    .page-btn:hover { border-color: #333; color: #aaa; }
    .page-btn.active { background: #fff; color: #000; border-color: #fff; }
    .alert-success {
        background: #0d2e1a; color: #4ade80; border: 1px solid #166534;
        padding: 10px 16px; border-radius: 8px; margin-bottom: 1rem; font-size: 13px;
    }

    @media (max-width: 768px) {
        .thread-stats-col { display: none; }
        .thread-stats-inline { display: flex !important; }
        .thread-item { padding: 0.875rem; }
    }
    @media (min-width: 769px) {
        .thread-stats-inline { display: none; }
        .thread-stats-col { display: flex; }
    }
</style>
@endpush

@section('content')

{{-- NAV --}}
<div class="community-nav">
    <a href="{{ route('community.index') }}" class="community-nav-btn">&#128247; Feed</a>
    <a href="{{ route('community.threads') }}" class="community-nav-btn active">&#128172; Diskusi</a>
    <a href="{{ route('chat.index') }}" class="community-nav-btn">&#9993; Chat</a>
    <a href="{{ route('home') }}" class="community-nav-btn">&#8592; Beranda</a>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<div class="threads-header">
    <h2>Forum Diskusi</h2>
    @auth
    <a href="{{ route('community.thread.create') }}" class="btn-new-thread">+ Buat Thread</a>
    @endauth
</div>

<div class="category-filter">
    <a href="{{ route('community.threads') }}" class="cat-btn {{ !request('cat') ? 'active' : '' }}">Semua</a>
    @foreach($categories as $cat)
    <a href="{{ route('community.threads', ['cat' => $cat]) }}"
       class="cat-btn {{ request('cat') === $cat ? 'active' : '' }}">
        {{ ucfirst($cat) }}
    </a>
    @endforeach
</div>

@if($threads->count() > 0)
<div class="thread-list">
    @foreach($threads as $thread)
    <div class="thread-item {{ $thread->is_pinned ? 'pinned' : '' }} {{ $thread->is_locked ? 'locked' : '' }}">

        <img src="{{ $thread->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
             class="thread-avatar" alt="{{ $thread->user->name }}">

        <div class="thread-content">
            <div class="thread-title-row">
                <a href="{{ route('community.thread.show', $thread->id) }}" class="thread-title">
                    @if($thread->is_pinned)<span class="thread-badge badge-pinned">&#128204;</span> @endif
                    {{ $thread->title }}
                </a>
                @if($thread->is_locked)
                <span class="thread-badge badge-locked">&#128274;</span>
                @endif
                <span class="thread-badge badge-cat">{{ $thread->category }}</span>
            </div>
            <div class="thread-excerpt">{{ $thread->body }}</div>
            <div class="thread-meta">
                <span>{{ $thread->user->name }}</span>
                <span>{{ $thread->created_at->diffForHumans() }}</span>
                {{-- Stats inline untuk mobile --}}
                <div class="thread-stats-inline">
                    <span class="thread-stat-inline"><strong>{{ $thread->replies_count }}</strong> balas</span>
                    <span class="thread-stat-inline"><strong>{{ $thread->views_count }}</strong> lihat</span>
                </div>
            </div>
        </div>

        {{-- Stats kolom untuk desktop --}}
        <div class="thread-stats-col">
            <div class="thread-stat"><strong>{{ $thread->replies_count }}</strong> balasan</div>
            <div class="thread-stat"><strong>{{ $thread->views_count }}</strong> dilihat</div>
            @if($thread->last_reply_at)
            <div class="thread-last-reply">{{ $thread->last_reply_at->diffForHumans() }}</div>
            @endif
        </div>

    </div>
    @endforeach
</div>

@if($threads->hasPages())
<div class="pagination-wrap">
    @if($threads->onFirstPage())
    <span class="page-btn" style="opacity:0.3;">← Sebelumnya</span>
    @else
    <a href="{{ $threads->previousPageUrl() }}" class="page-btn">← Sebelumnya</a>
    @endif
    <span class="page-btn active">{{ $threads->currentPage() }}</span>
    @if($threads->hasMorePages())
    <a href="{{ $threads->nextPageUrl() }}" class="page-btn">Berikutnya →</a>
    @else
    <span class="page-btn" style="opacity:0.3;">Berikutnya →</span>
    @endif
</div>
@endif

@else
<div class="empty-threads">
    <p style="font-size:24px;margin-bottom:0.75rem;">&#128172;</p>
    <p>Belum ada diskusi. Mulai thread pertama!</p>
    @auth
    <a href="{{ route('community.thread.create') }}" style="display:inline-block;margin-top:1rem;padding:8px 20px;border-radius:8px;background:#fff;color:#000;text-decoration:none;font-size:13px;">
        + Buat Thread
    </a>
    @endauth
</div>
@endif

@endsection
