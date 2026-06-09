@extends('layouts.app')

@push('styles')
<style>
    .community-hero {
        text-align: center; padding: 5rem 1rem 4rem;
        border-bottom: 1px solid #111;
    }
    .community-hero h1 {
        font-size: 1.8rem; font-weight: 300;
        letter-spacing: 0.15em; margin-bottom: 0.75rem;
    }
    .community-hero p {
        font-size: 14px; color: #555; max-width: 480px;
        margin: 0 auto 2rem; line-height: 1.7;
    }
    .coming-soon-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 1rem; margin-top: 3rem;
    }
    .coming-card {
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 12px; padding: 1.5rem; text-align: center;
    }
    .coming-card-icon { font-size: 28px; margin-bottom: 0.75rem; }
    .coming-card-title { font-size: 13px; font-weight: 500; color: #888; margin-bottom: 0.4rem; }
    .coming-card-desc { font-size: 12px; color: #444; line-height: 1.6; }
    .coming-badge {
        display: inline-block; font-size: 10px; letter-spacing: 0.15em;
        color: #4a6fa5; background: #0a0a1a; border: 1px solid #1a1a3a;
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
    <h1>Rumah Margonoandi</h1>
    <p>
        Tempat ngobrol, diskusi, dan kolaborasi langsung dengan Rakhman Andi.
        Komunitas sedang dibangun — jadilah yang pertama bergabung.
    </p>
    <a href="{{ route('home') }}" style="font-size:13px;color:#555;text-decoration:none;border:1px solid #1a1a1a;padding:8px 20px;border-radius:50px;transition:0.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#555'">
        ← Kembali ke beranda
    </a>
</div>

<div class="section">
    <p class="section-eyebrow" style="font-size:10px;letter-spacing:0.3em;color:#3a3a3a;text-transform:uppercase;margin-bottom:0.5rem;">Yang akan hadir</p>
    <div class="coming-soon-grid">
        <div class="coming-card">
            <div class="coming-card-icon">&#128172;</div>
            <div class="coming-card-title">Diskusi Publik</div>
            <div class="coming-card-desc">Ngobrol bebas tentang lagu, cerita di baliknya, atau apapun yang ingin kamu bagikan.</div>
        </div>
        <div class="coming-card">
            <div class="coming-card-icon">&#127908;</div>
            <div class="coming-card-title">Room Kolaborasi</div>
            <div class="coming-card-desc">Punya ide lagu? Ajak Rakhman Andi dan member lain untuk berkolaborasi dalam satu room privat.</div>
        </div>
        <div class="coming-card">
            <div class="coming-card-icon">&#128276;</div>
            <div class="coming-card-title">Notifikasi Lagu Baru</div>
            <div class="coming-card-desc">Dapat pemberitahuan pertama kali ketika ada lagu baru atau cerita baru yang ditulis.</div>
        </div>
    </div>
</div>

@endsection