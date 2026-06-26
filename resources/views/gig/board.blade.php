@extends('layouts.app')

@push('styles')
<style>
    .gb-wrap { max-width: 820px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .gb-hero { text-align: center; margin-bottom: 2rem; }
    .gb-eyebrow { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--accent); background: var(--accent-dim); border: 1px solid rgba(56,168,204,.3); border-radius: 20px; padding: 4px 12px; margin-bottom: .75rem; }
    .gb-title { font-size: clamp(1.5rem,4vw,2.2rem); font-weight: 300; color: var(--text); margin-bottom: .4rem; }
    .gb-sub { font-size: 14px; color: var(--text-3); max-width: 500px; margin: 0 auto 1.5rem; line-height: 1.6; }

    /* FILTER */
    .gb-filter { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 1.5rem; justify-content: center; }
    .gb-filter-chip { padding: 6px 14px; border-radius: 20px; border: 1px solid var(--border); background: none; color: var(--text-3); font-size: 12.5px; cursor: pointer; font-family: inherit; transition: .15s; text-decoration: none; display: inline-block; }
    .gb-filter-chip:hover { border-color: var(--accent); color: var(--accent); }
    .gb-filter-chip.active { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* CARDS */
    .gb-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
    .gb-card { background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border); border-radius: 16px; padding: 1.1rem 1.25rem; position: relative; transition: border-color .2s; }
    .gb-card:hover { border-color: var(--accent); }
    .gb-card-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: .7rem; }
    .gb-type-badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 12px; background: var(--accent-dim); color: var(--accent); white-space: nowrap; flex-shrink: 0; }
    .gb-card-title { font-size: 15px; font-weight: 600; color: var(--text); line-height: 1.4; flex: 1; }
    .gb-meta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: .75rem; }
    .gb-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-3); }
    .gb-desc { font-size: 13px; color: var(--text-2); line-height: 1.65; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .gb-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; gap: 10px; flex-wrap: wrap; }
    .gb-poster { display: flex; align-items: center; gap: 7px; }
    .gb-av { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border); background: var(--accent-dim); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--accent); flex-shrink: 0; }
    .gb-poster-name { font-size: 12px; color: var(--text-3); }
    .gb-apply-btn { padding: 8px 18px; border-radius: 20px; background: var(--accent); color: #fff; font-size: 12.5px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-family: inherit; transition: .15s; }
    .gb-apply-btn:hover { opacity: .88; }
    .gb-apply-locked { padding: 8px 18px; border-radius: 20px; background: none; color: var(--text-3); font-size: 12.5px; font-weight: 600; border: 1px solid var(--border); cursor: pointer; font-family: inherit; transition: .15s; text-decoration: none; display: inline-block; }
    .gb-apply-locked:hover { border-color: var(--accent); color: var(--accent); }

    /* BLUR gate on last 2 cards for guests */
    .gb-blur { filter: blur(5px); pointer-events: none; user-select: none; }

    /* GATE */
    .gb-gate { position: relative; margin-top: -80px; padding: 2rem 1rem 0; text-align: center; z-index: 2; }
    .gb-gate-box { background: var(--card-bg, rgba(15,23,42,.95)); border: 1px solid var(--accent-dim); border-radius: 20px; padding: 1.5rem 1.5rem; display: inline-block; max-width: 420px; width: 100%; }
    .gb-gate-title { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: .4rem; }
    .gb-gate-sub { font-size: 13px; color: var(--text-3); margin-bottom: 1rem; line-height: 1.6; }
    .gb-gate-cta { display: inline-block; padding: 10px 24px; border-radius: 20px; background: var(--accent); color: #fff; font-size: 13.5px; font-weight: 600; text-decoration: none; }

    .gb-empty { text-align: center; padding: 3rem 1rem; color: var(--text-4); }
    .gb-empty-icon { font-size: 2.5rem; margin-bottom: .75rem; }
    .gb-post-cta { display: inline-block; margin-top: 1rem; padding: 10px 22px; border-radius: 20px; background: var(--accent); color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; }

    .gb-kota-input { padding: 8px 13px; border-radius: 20px; border: 1px solid var(--border); background: var(--card-bg, rgba(15,23,42,.6)); color: var(--text); font-size: 13px; font-family: inherit; outline: none; transition: .15s; width: 150px; }
    .gb-kota-input:focus { border-color: var(--accent); }
</style>
@endpush

@section('content')
<div class="gb-wrap">

    <div class="gb-hero">
        <div class="gb-eyebrow">🎪 Live Terus</div>
        <h1 class="gb-title">Papan Gig Musisi Indonesia</h1>
        <p class="gb-sub">Audisi band, open mic, session player, rekaman — semua peluang untuk musisi indie di satu tempat.</p>
    </div>

    {{-- FILTER --}}
    <div class="gb-filter">
        <a href="{{ route('gig.board') }}" class="gb-filter-chip {{ $type === '' ? 'active' : '' }}">Semua</a>
        @foreach(GigPost::types() as $key => $label)
        <a href="{{ route('gig.board', ['type' => $key]) }}" class="gb-filter-chip {{ $type === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
        <form method="GET" action="{{ route('gig.board') }}" style="display:flex;align-items:center;gap:6px;">
            @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
            <input type="text" name="kota" class="gb-kota-input" placeholder="📍 Filter kota..." value="{{ $location }}" autocomplete="off">
        </form>
    </div>

    @php $isGuest = !auth()->check(); $total = $gigs->count(); @endphp

    @if($total === 0)
    <div class="gb-empty">
        <div class="gb-empty-icon">🎸</div>
        <p>Belum ada gig yang tersedia saat ini{{ $type ? ' untuk kategori ini' : '' }}.</p>
        @auth
        <a href="{{ route('gig.create') }}" class="gb-post-cta">+ Pasang Gig</a>
        @else
        <a href="{{ route('google.login') }}" class="gb-post-cta">Gabung & Pasang Gig</a>
        @endauth
    </div>
    @else

    <div class="gb-grid">
        @foreach($gigs as $i => $gig)
        @php $blurred = $isGuest && $i >= 3; @endphp
        <div class="gb-card {{ $blurred ? 'gb-blur' : '' }}">
            <div class="gb-card-top">
                <span class="gb-type-badge">{{ GigPost::typeLabel($gig->type) }}</span>
                <div class="gb-card-title">{{ $gig->title }}</div>
            </div>
            <div class="gb-meta">
                @if($gig->location)
                <span class="gb-meta-item">📍 {{ $gig->location }}</span>
                @endif
                @if($gig->date_event)
                <span class="gb-meta-item">🗓 {{ $gig->date_event->format('d M Y') }}</span>
                @endif
                <span class="gb-meta-item">🕐 {{ $gig->created_at->diffForHumans() }}</span>
            </div>
            @if($gig->description)
            <p class="gb-desc">{{ $gig->description }}</p>
            @endif
            <div class="gb-card-footer">
                <div class="gb-poster">
                    @if($gig->user?->avatar)
                    <img src="{{ $gig->user->avatar }}" class="gb-av" alt="">
                    @else
                    <div class="gb-av">{{ strtoupper(substr($gig->user?->name ?? '?', 0, 1)) }}</div>
                    @endif
                    <span class="gb-poster-name">{{ $gig->user?->name ?? 'Anggota' }}</span>
                </div>
                @auth
                <a href="{{ route('musisi.index', ['tab' => 'gig']) }}" class="gb-apply-btn">Lihat & Hubungi →</a>
                @else
                <a href="{{ route('google.login') }}" class="gb-apply-locked" onclick="gtag && gtag('event','cta_click',{event_category:'gig_board',button:'apply_locked'})">🔒 Login untuk melamar</a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    @guest
    @if($total > 3)
    <div class="gb-gate">
        <div class="gb-gate-box">
            <div class="gb-gate-title">🎪 {{ $total - 3 }} gig lainnya menunggu</div>
            <p class="gb-gate-sub">Masuk untuk melihat semua gig, detail kontak poster, dan pasang pengumumanmu sendiri — gratis.</p>
            <a href="{{ route('google.login') }}" class="gb-gate-cta" onclick="gtag && gtag('event','cta_click',{event_category:'gig_board',button:'gate_login'})">Masuk dengan Google — gratis</a>
        </div>
    </div>
    @endif
    @endguest

    @auth
    <div style="text-align:center;margin-top:2rem;">
        <a href="{{ route('gig.create') }}" class="gb-post-cta">+ Pasang Gig Baru</a>
    </div>
    @endauth

    @endif

</div>
@endsection
