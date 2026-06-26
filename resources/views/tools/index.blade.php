@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); }

    /* ===== LAYOUT ===== */
    main:has(.th-outer) { max-width: 1100px !important; }

    .th-outer { max-width: 1100px; margin: 0 auto; padding: 1.75rem 1.5rem 4rem; }

    .th-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 210px;
        gap: 28px;
        align-items: start;
    }
    @media(max-width: 860px) { .th-layout { grid-template-columns: 1fr; } .th-sidebar { display: none; } }

    /* ===== HEADER ===== */
    .th-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .th-back:hover { color:var(--text,#f0f0f0); }
    .th-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .th-hero { margin-bottom:1.5rem; }
    .th-hero h1 { font-size:clamp(1.4rem,5vw,1.9rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.4rem; }
    .th-hero p { font-size:13.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:560px; }

    /* ===== TOOLS GRID ===== */
    .th-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px; }
    .th-card { display:flex;gap:13px;align-items:flex-start;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:16px;padding:1.1rem;text-decoration:none;transition:.18s; }
    .th-card:hover { border-color:var(--ac);transform:translateY(-3px);box-shadow:0 16px 34px -20px var(--ac); }
    .th-ic { font-size:1.7rem;flex-shrink:0;line-height:1; }
    .th-t { font-weight:700;font-size:13.5px;color:var(--text,#f0f0f0);line-height:1.25; }
    .th-d { font-size:12px;color:var(--text-3,#94a3b8);margin-top:4px;line-height:1.5; }
    .th-arrow { margin-left:auto;color:var(--ac);font-weight:700;flex-shrink:0; }

    /* ===== RIGHT SIDEBAR ===== */
    .th-sidebar { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 14px; }
    .th-widget { background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border);border-radius:16px;padding:1rem; }
    .th-widget-title { font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--text-3);margin-bottom:.75rem; }

    .th-art-link { display:flex;flex-direction:column;gap:2px;padding:8px 0;border-bottom:1px solid var(--border);text-decoration:none;transition:.15s; }
    .th-art-link:last-child { border-bottom:none;padding-bottom:0; }
    .th-art-link:hover .th-art-title { color:var(--ac); }
    .th-art-title { font-size:12.5px;font-weight:500;color:var(--text);line-height:1.35; }
    .th-art-meta { font-size:10px;color:var(--text-3); }
    .th-see-all { display:block;text-align:center;font-size:11px;color:var(--ac);text-decoration:none;margin-top:.5rem;font-weight:600; }

    .th-gig-card { background:linear-gradient(135deg,rgba(56,189,248,.08),rgba(56,189,248,.02));border:1px solid rgba(56,189,248,.25);border-radius:16px;padding:1rem; }
    .th-gig-type { display:inline-block;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(56,189,248,.15);color:var(--ac);margin-bottom:5px; }
    .th-gig-title { font-size:13px;font-weight:600;color:var(--text);line-height:1.35;margin-bottom:4px; }
    .th-gig-meta { font-size:11px;color:var(--text-3);margin-bottom:.7rem; }
    .th-gig-cta { display:block;text-align:center;padding:7px 0;border-radius:20px;background:var(--ac);color:#fff;font-size:12px;font-weight:600;text-decoration:none; }

    .th-cta-widget { background:linear-gradient(135deg,var(--ac-lt),rgba(14,165,233,.02));border:1px solid rgba(56,189,248,.25);border-radius:16px;padding:1rem;text-align:center; }
    .th-cta-widget h4 { font-size:13px;font-weight:700;color:var(--text);margin-bottom:.3rem; }
    .th-cta-widget p { font-size:11.5px;color:var(--text-3);margin-bottom:.8rem;line-height:1.55; }
    .th-cta-btn { display:inline-block;padding:8px 18px;border-radius:20px;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;font-size:12px;font-weight:600;text-decoration:none; }

    .th-musisi-cta { background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:1rem;margin-top:1.5rem; }
    .th-musisi-cta h2 { font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.3rem; }
    .th-musisi-cta p { font-size:12px;color:var(--text-3);line-height:1.6;margin-bottom:.8rem; }
    .th-musisi-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:9px 20px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
</style>
@endpush

@section('content')
<div class="th-outer">
    <a href="{{ route('home') }}" class="th-back">← Beranda</a>
    @include('partials.tool-share')

    <div class="th-hero">
        <div class="th-badge">🎛️ Studio Gratis</div>
        <h1>Alat Gratis untuk Musisi</h1>
        <p>Semua yang kamu butuhkan dari kamar — potong lagu, bikin karaoke, desain cover, sampai promo rilis. <b>Di browser, tanpa upload, tanpa daftar.</b></p>
    </div>

    <div class="th-layout">

        {{-- ===== TOOLS GRID ===== --}}
        <div>
            <div class="th-grid">
                @foreach($tools as $t)
                <a href="{{ route($t['route']) }}" class="th-card">
                    <span class="th-ic">{{ $t['icon'] }}</span>
                    <span style="min-width:0;">
                        <span class="th-t" style="display:block;">{{ $t['name'] }}</span>
                        <span class="th-d" style="display:block;">{{ $t['desc'] }}</span>
                    </span>
                    <span class="th-arrow">→</span>
                </a>
                @endforeach
            </div>

            <div class="th-musisi-cta">
                <h2>🎸 Lebih dari sekadar alat</h2>
                <p>Margonoandi adalah <b>ekosistem musisi Indonesia</b> — buat profil portofolio gratis, temukan personil &amp; gig lewat matchmaking, dan tumbuh bareng komunitas.</p>
                <a href="{{ route('google.login') }}" class="th-musisi-btn">Buat profil musisi gratis →</a>
            </div>
        </div>

        {{-- ===== RIGHT SIDEBAR ===== --}}
        <aside class="th-sidebar">

            {{-- Materi terbaru --}}
            @if($featuredArticles->count())
            <div class="th-widget">
                <div class="th-widget-title">📚 Baca Materi</div>
                @foreach($featuredArticles as $a)
                <a href="{{ route('library.materi.show', $a->slug) }}" class="th-art-link">
                    <div class="th-art-title">{{ $a->title }}</div>
                    <div class="th-art-meta">🕐 {{ $a->reading_time }} mnt</div>
                </a>
                @endforeach
                <a href="{{ route('library.materi') }}" class="th-see-all">Semua materi →</a>
            </div>
            @endif

            {{-- Gig terbaru --}}
            @if($latestGig)
            <div class="th-gig-card">
                <div class="th-widget-title">🎪 Gig Terbaru</div>
                <div class="th-gig-type">{{ \App\Models\GigPost::typeLabel($latestGig->type) }}</div>
                <div class="th-gig-title">{{ $latestGig->title }}</div>
                <div class="th-gig-meta">
                    @if($latestGig->location)📍 {{ $latestGig->location }}@endif
                    @if($latestGig->date_event) · 🗓 {{ $latestGig->date_event->format('d M') }}@endif
                </div>
                @auth
                <a href="{{ route('gig.board') }}" class="th-gig-cta">Lihat gig →</a>
                @else
                <a href="{{ route('google.login') }}" class="th-gig-cta">🔒 Login untuk lamar</a>
                @endauth
            </div>
            @endif

            {{-- CTA komunitas --}}
            @guest
            <div class="th-cta-widget">
                <h4>🎵 Gabung Komunitas</h4>
                <p>Profil musisi, cari personil, diskusi &amp; gig — semua gratis.</p>
                <a href="{{ route('google.login') }}" class="th-cta-btn">Masuk Gratis</a>
            </div>
            @else
            <div class="th-cta-widget">
                <h4>👥 Cari Personil</h4>
                <p>Temukan kolaborator dan partner rekaman di kotamu.</p>
                <a href="{{ route('musisi.index') }}" class="th-cta-btn">Lihat Musisi →</a>
            </div>
            @endguest

        </aside>

    </div>

    <p style="text-align:center;margin-top:2rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> — komunitas musisi Indonesia 🎸
    </p>
</div>
@endsection
