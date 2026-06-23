@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); }
    .th-page { max-width:760px; margin:0 auto; padding:1.75rem 1rem 4rem; }
    .th-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .th-back:hover { color:var(--text,#f0f0f0); }
    .th-hero { text-align:center;margin-bottom:1.75rem; }
    .th-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .th-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.5rem,5vw,2.1rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .th-hero p { font-size:13.5px;color:var(--text-3,#94a3b8);max-width:540px;margin:0 auto;line-height:1.7; }
    .th-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:12px;margin-top:.5rem; }
    .th-card { display:flex;gap:13px;align-items:flex-start;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:16px;padding:1.1rem;text-decoration:none;transition:.18s; }
    .th-card:hover { border-color:var(--ac);transform:translateY(-3px);box-shadow:0 16px 34px -20px var(--ac); }
    .th-ic { font-size:1.8rem;flex-shrink:0;line-height:1; }
    .th-t { font-weight:700;font-size:14px;color:var(--text,#f0f0f0);line-height:1.25; }
    .th-d { font-size:12px;color:var(--text-3,#94a3b8);margin-top:4px;line-height:1.5; }
    .th-arrow { margin-left:auto;color:var(--ac);font-weight:700;flex-shrink:0; }
    .th-cta { margin-top:2.25rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg,#0f172a));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .th-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.4rem; }
    .th-cta p { font-size:12.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:460px;margin:0 auto .9rem; }
    .th-cta-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
</style>
@endpush

@section('content')
<div class="th-page">
    <a href="{{ route('home') }}" class="th-back">← Beranda</a>
    @include('partials.tool-share')

    <div class="th-hero">
        <div class="th-badge">🎛️ Studio Gratis</div>
        <h1>Alat Gratis untuk Musisi</h1>
        <p>Semua yang kamu butuhkan dari kamar — potong lagu, bikin karaoke, desain cover, sampai promo rilis. <b>Di browser, tanpa upload, tanpa daftar.</b></p>
    </div>

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

    <div class="th-cta">
        <h2>🎸 Lebih dari sekadar alat</h2>
        <p>Margonoandi adalah <b>ekosistem musisi Indonesia</b> — buat profil portofolio gratis (kartu + QR), temukan personil &amp; gig lewat matchmaking, dan tumbuh bareng komunitas. Dimulai dari kamarmu.</p>
        <a href="{{ route('google.login') }}" class="th-cta-btn">Buat profil musisi gratis →</a>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> — komunitas musisi Indonesia 🎸
    </p>
</div>
@endsection
