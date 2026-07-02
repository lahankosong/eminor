@extends('layouts.fanbase')
@section('title', 'Materi Musik')

@push('styles')
<style>
    .fb-main { --card-bg: var(--card); --accent: var(--sky); --accent-dim: var(--sky-lt); --bg: var(--cream); --bg-3: var(--surface); --border-2: var(--border-lt); --text: var(--text-1); }

    /* ===== LAYOUT ===== */
    .mat-outer { padding: 0 0 2rem; }

    .mat-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .mat-back:hover { color:var(--text-1); }

    /* ===== COMMUNITY SNAPSHOT ===== */
    .cs-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 1.75rem;
    }
    @media(max-width: 680px) { .cs-strip { grid-template-columns: 1fr; } }

    .cs-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.1rem 1.1rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: border-color .2s;
    }
    .cs-card:hover { border-color: var(--accent); }
    .cs-card-accent { border-color: rgba(56,168,204,.3); background: rgba(56,168,204,.04); }

    .cs-label { font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-3); }
    .cs-avatars { display: flex; gap: -4px; margin: 2px 0; }
    .cs-av {
        width: 30px; height: 30px; border-radius: 50%; object-fit: cover;
        border: 2px solid var(--bg); margin-right: -6px; flex-shrink: 0;
        background: var(--bg-3);
    }
    .cs-av-more {
        width: 30px; height: 30px; border-radius: 50%; background: var(--accent-dim);
        border: 2px solid var(--bg); display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: var(--accent); margin-right: 0;
    }
    .cs-desc { font-size: 12px; color: var(--text-3); line-height: 1.55; flex: 1; }

    .cs-gig-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: var(--accent-dim); color: var(--accent); margin-bottom: 2px; }
    .cs-gig-title { font-size: 13.5px; font-weight: 600; color: var(--text); line-height: 1.35; }
    .cs-gig-meta { display: flex; gap: 10px; font-size: 11px; color: var(--text-3); flex-wrap: wrap; }

    .cs-post-author { font-size: 11px; font-weight: 600; color: var(--accent); }
    .cs-post-text { font-size: 12.5px; color: var(--text-3); line-height: 1.55; font-style: italic; flex: 1; }

    .cs-cta { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; margin-top: auto; transition: .15s; }
    .cs-cta:hover { opacity: .8; }
    .cs-cta.locked { color: var(--text-3); }
    .cs-cta.locked:hover { color: var(--accent); }

    /* Filter chips — always visible */
    .mat-filter-mobile { display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem; }
    .mat-chip { padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:none;color:var(--text-3);font-size:12px;font-weight:500;cursor:pointer;transition:.15s;font-family:inherit; }
    .mat-chip:hover { border-color:var(--sky);color:var(--sky-dk); }
    .mat-chip.active { background:var(--sky);color:#fff;border-color:var(--sky); }

    /* ===== MAIN ===== */
    .mat-section-label { font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);font-weight:700;margin:1.5rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border); }
    .mat-section-label:first-child { margin-top: 0; }

    .mat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-bottom:1.5rem; }
    .mat-card { background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:1.1rem;transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:8px; }
    .mat-card:hover { transform:translateY(-2px);box-shadow:var(--shadow);border-color:var(--accent); }
    .mat-card-top { display:flex;align-items:center;justify-content:space-between;gap:8px; }
    .mat-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff;letter-spacing:.04em;flex-shrink:0; }
    .mat-time { font-size:10px;color:var(--text-3);display:flex;align-items:center;gap:3px; }
    .mat-card-title { font-size:13.5px;font-weight:600;color:var(--text);line-height:1.4; }
    .mat-card-excerpt { font-size:12px;color:var(--text-3);line-height:1.6;flex:1; }
    .mat-card-footer { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:4px; }
    .mat-btn-read { font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px; }
    .mat-btn-dl { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--accent-dim);border:1px solid rgba(56,168,204,.25);color:var(--accent);font-weight:600;text-decoration:none;transition:.15s; }
    .mat-btn-dl:hover { background:var(--accent);color:#fff; }
    .mat-btn-dl-lock { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--card-bg);border:1px solid var(--border);color:var(--text-3);display:inline-flex;align-items:center;gap:4px; }

</style>
@endpush

@section('content')
<div class="mat-outer">

    <a href="{{ route('library') }}" class="mat-back">← Library</a>

    {{-- ===== COMMUNITY SNAPSHOT (mengganti hero) ===== --}}
    <div class="cs-strip">

        {{-- Kartu 1: Musisi Aktif --}}
        <div class="cs-card">
            <div class="cs-label">👥 Musisi Aktif</div>
            @if($musicians->count())
            <div class="cs-avatars">
                @foreach($musicians->take(4) as $m)
                <img src="{{ $m['avatar'] }}" class="cs-av" alt="{{ $m['name'] }}" title="{{ $m['name'] }}">
                @endforeach
                @if($musicians->count() > 4)
                <div class="cs-av cs-av-more">+{{ $musicians->count() - 4 }}</div>
                @endif
            </div>
            @else
            <div style="font-size:22px;margin:4px 0;">👤 👤 👤</div>
            @endif
            <p class="cs-desc">Temukan personil, kolaborator, dan partner rekaman di kotamu.</p>
            <a href="{{ route('kamu') }}" class="cs-cta">Cari personil →</a>
        </div>

        {{-- Kartu 2: Gig Terbaru --}}
        <div class="cs-card cs-card-accent">
            <div class="cs-label">🎪 Gig Terbaru</div>
            @if($latestGig)
            <div class="cs-gig-badge">{{ \App\Models\GigPost::typeLabel($latestGig->type) }}</div>
            <div class="cs-gig-title">{{ $latestGig->title }}</div>
            <div class="cs-gig-meta">
                @if($latestGig->location)<span>📍 {{ $latestGig->location }}</span>@endif
                @if($latestGig->date_event)<span>🗓 {{ $latestGig->date_event->format('d M Y') }}</span>@endif
            </div>
            @else
            <div class="cs-gig-title" style="color:var(--text-3);">Belum ada gig terbuka — jadilah yang pertama!</div>
            @endif
            <a href="{{ route('gig.board') }}" class="cs-cta">Lihat semua gig →</a>
        </div>

        {{-- Kartu 3: Diskusi Terbaru --}}
        <div class="cs-card">
            <div class="cs-label">💬 Diskusi Komunitas</div>
            @if($latestPost)
            <div class="cs-post-author">{{ $latestPost->user?->name ?? 'Musisi' }}</div>
            <div class="cs-post-text">"{{ \Illuminate\Support\Str::limit(strip_tags($latestPost->body ?? ''), 90) }}"</div>
            @else
            <div class="cs-post-text">Jadilah yang pertama memulai diskusi tentang musik...</div>
            @endif
            <a href="{{ route('aku') }}" class="cs-cta">Ikut diskusi →</a>
        </div>

    </div>{{-- .cs-strip --}}

    {{-- Mobile filter chips --}}
    <div class="mat-filter-mobile">
        <button class="mat-chip active" onclick="matFilter('all',this)" data-cat="all">Semua</button>
        <button class="mat-chip" onclick="matFilter('teori',this)" data-cat="teori">🎵 Teori</button>
        <button class="mat-chip" onclick="matFilter('produksi',this)" data-cat="produksi">🎛️ Produksi</button>
        <button class="mat-chip" onclick="matFilter('kolaborasi',this)" data-cat="kolaborasi">🤝 Kolaborasi</button>
        <button class="mat-chip" onclick="matFilter('rilis',this)" data-cat="rilis">🚀 Rilis</button>
        <button class="mat-chip" onclick="matFilter('karir',this)" data-cat="karir">💼 Karir</button>
    </div>

    @php
    $catLabels = ['teori'=>'Teori Musik','produksi'=>'Produksi & Recording','kolaborasi'=>'Kolaborasi','rilis'=>'Rilis & Branding','karir'=>'Karir & Bisnis Musik'];
    $catColors = ['teori'=>'#38A8CC','produksi'=>'#a855f7','kolaborasi'=>'#f59e0b','rilis'=>'#22c55e','karir'=>'#f97316'];
    $catIcons  = ['teori'=>'🎵','produksi'=>'🎛️','kolaborasi'=>'🤝','rilis'=>'🚀','karir'=>'💼'];
    @endphp

    @foreach($catLabels as $cat => $label)
        @if($grouped->has($cat))
        <div class="mat-section" data-cat="{{ $cat }}">
            <div class="mat-section-label">{{ $catIcons[$cat] }} {{ $label }}</div>
            <div class="mat-grid">
                @foreach($grouped[$cat] as $a)
                <a href="{{ route('library.materi.show', $a->slug) }}" class="mat-card">
                    <div class="mat-card-top">
                        <span class="mat-cat-pill" style="background:{{ $catColors[$cat] }}">{{ $label }}</span>
                        <span class="mat-time">🕐 {{ $a->reading_time }} mnt</span>
                    </div>
                    <div class="mat-card-title">{{ $a->title }}</div>
                    <div class="mat-card-excerpt">{{ $a->excerpt }}</div>
                    <div class="mat-card-footer" onclick="event.stopPropagation()">
                        <span class="mat-btn-read">Baca artikel →</span>
                        @auth
                        <a href="{{ route('library.materi.download', $a->slug) }}" class="mat-btn-dl" onclick="event.stopPropagation()">⬇ Unduh</a>
                        @else
                        <span class="mat-btn-dl-lock">🔒 Unduh</span>
                        @endauth
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        @guest
        <div style="background:linear-gradient(135deg,var(--sky-lt),rgba(56,168,204,.02));border:1px solid rgba(56,168,204,.2);border-radius:20px;padding:1.5rem;text-align:center;margin-top:2rem;">
            <h3 style="font-size:1rem;font-weight:700;color:var(--text-1);margin-bottom:.4rem;">🔒 Download Semua Artikel</h3>
            <p style="font-size:13px;color:var(--text-3);margin-bottom:1rem;">Login untuk download semua artikel dalam format Markdown.</p>
            <a href="{{ route('google.login') }}" style="display:inline-block;padding:10px 24px;border-radius:30px;background:var(--sky);color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Login dengan Google</a>
        </div>
        @endguest
</div>
@endsection

@push('scripts')
<script>
function matFilter(cat, btn) {
    document.querySelectorAll('.mat-nav-item[data-cat], .mat-chip[data-cat]').forEach(function(b){
        b.classList.toggle('active', b.dataset.cat === cat);
    });
    document.querySelectorAll('.mat-section').forEach(function(s){
        s.style.display = (cat === 'all' || s.dataset.cat === cat) ? '' : 'none';
    });
}
</script>
@endpush
