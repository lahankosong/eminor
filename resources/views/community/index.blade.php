@extends('layouts.app')

@push('styles')
<style>
    .community-hero {
        text-align: center; padding: 5rem 1rem 4rem;
        border-bottom: 1px solid var(--border-2);
    }
    .community-hero h1 {
        font-size: 1.8rem; font-weight: 300;
        letter-spacing: 0.15em; margin-bottom: 0.75rem;
    }
    .community-hero p {
        font-size: 14px; color: var(--text-3); max-width: 480px;
        margin: 0 auto 2rem; line-height: 1.7;
    }
    .coming-soon-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 1rem; margin-top: 3rem;
    }
    .coming-card {
        background: var(--bg); border: 1px solid var(--border-2);
        border-radius: 12px; padding: 1.5rem; text-align: center;
    }
    .coming-card-icon { font-size: 28px; margin-bottom: 0.75rem; }
    .coming-card-title { font-size: 13px; font-weight: 500; color: var(--text-2); margin-bottom: 0.4rem; }
    .coming-card-desc { font-size: 12px; color: var(--text-4); line-height: 1.6; }
    .coming-badge {
        display: inline-block; font-size: 10px; letter-spacing: 0.15em;
        color: var(--accent); background: var(--accent-glow); border: 1px solid var(--accent-dim);
        padding: 4px 12px; border-radius: 20px; margin-bottom: 1.5rem;
        text-transform: uppercase;
    }
    @media (max-width: 600px) {
        .coming-soon-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="community-hero">
    <span class="coming-badge">Segera hadir</span>
    <h1>Rumah EMINOR</h1>
    <p>
        Tempat ngobrol, diskusi, dan kolaborasi langsung dengan Eminor.
        Komunitas sedang dibangun — jadilah yang pertama bergabung.
    </p>
    <a href="{{ route('home') }}"
       style="font-size:13px;color:var(--text-3);text-decoration:none;border:1px solid var(--border);padding:8px 20px;border-radius:50px;transition:0.15s;"
       onmouseover="this.style.color='var(--text)'"
       onmouseout="this.style.color='var(--text-3)'">
        ← Kembali ke beranda
    </a>
</div>

<div class="section">
    <p class="section-eyebrow"
       style="font-size:10px;letter-spacing:0.3em;color:var(--text-4);text-transform:uppercase;margin-bottom:0.5rem;">
        Yang akan hadir
    </p>
    <div class="coming-soon-grid">
        <div class="coming-card">
            <div class="coming-card-icon">&#128172;</div>
            <div class="coming-card-title">Diskusi Publik</div>
            <div class="coming-card-desc">Ngobrol bebas tentang lagu, cerita di baliknya, atau apapun yang ingin kamu bagikan.</div>
        </div>
        <div class="coming-card">
            <div class="coming-card-icon">&#127908;</div>
            <div class="coming-card-title">Room Kolaborasi</div>
            <div class="coming-card-desc">Punya ide lagu? Ajak Eminor dan member lain untuk berkolaborasi dalam satu room privat.</div>
        </div>
        <div class="coming-card">
            <div class="coming-card-icon">&#128276;</div>
            <div class="coming-card-title">Notifikasi Lagu Baru</div>
            <div class="coming-card-desc">Dapat pemberitahuan pertama kali ketika ada lagu baru atau cerita baru yang ditulis.</div>
        </div>
    </div>
</div>

@endsection
