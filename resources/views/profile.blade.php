@extends('layouts.fanbase')
@section('title', 'Profil')

@push('styles')
<style>
    .profile-hero {
        background: linear-gradient(145deg, var(--sky-dk) 0%, var(--sky) 60%, var(--sky-mid) 100%);
        border-radius: var(--radius-lg); padding: 2rem 1.5rem;
        text-align: center; position: relative; overflow: hidden;
        box-shadow: var(--shadow-lg); margin-bottom: 1.5rem;
    }
    .profile-hero::before {
        content: ''; position: absolute; top: -60px; right: -60px;
        width: 200px; height: 200px; border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .profile-hero::after {
        content: ''; position: absolute; bottom: -40px; left: -40px;
        width: 150px; height: 150px; border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .profile-avatar {
        width: 88px; height: 88px; border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255,255,255,0.5);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        margin-bottom: 1rem; position: relative; z-index: 1;
    }
    .profile-name {
        font-family: 'Sora', sans-serif;
        font-size: 1.25rem; font-weight: 700; color: #fff;
        margin-bottom: 4px; text-shadow: 0 2px 8px rgba(0,0,0,0.15);
        position: relative; z-index: 1;
    }
    .profile-email {
        font-size: 12px; color: rgba(255,255,255,0.65);
        margin-bottom: 4px; position: relative; z-index: 1;
    }
    .profile-joined {
        font-size: 11px; color: rgba(255,255,255,0.45);
        position: relative; z-index: 1;
    }

    .profile-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.25rem;
        margin-bottom: 1rem; box-shadow: var(--shadow-sm);
    }
    .profile-card-title {
        font-size: 10px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.15em;
        margin-bottom: 1rem; padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-lt);
    }
    .profile-link {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: var(--radius-sm);
        background: var(--surface); border: 1px solid var(--border-lt);
        color: var(--text-2); text-decoration: none; transition: 0.2s;
        font-size: 13px; font-weight: 500; margin-bottom: 8px;
    }
    .profile-link:hover {
        background: var(--sky-lt); border-color: var(--sky-mid); color: var(--sky-dk);
    }
    .profile-link:last-child { margin-bottom: 0; }
    .profile-link-icon { font-size: 16px; flex-shrink: 0; }
    .profile-link-sub { font-size: 11px; color: var(--text-4); font-weight: 400; margin-top: 1px; }

    .profile-soon {
        text-align: center; padding: 2rem 1rem;
        background: var(--card); border: 1px dashed var(--border);
        border-radius: var(--radius); color: var(--text-4); font-size: 13px;
    }
</style>
@endpush

@section('content')

<div class="profile-hero">
    <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
         alt="{{ $user->name }}" class="profile-avatar">
    <div class="profile-name">{{ $user->name }}</div>
    <div class="profile-email">{{ $user->email }}</div>
    <div class="profile-joined">
        Member sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
    </div>
</div>

<div class="profile-card">
    <div class="profile-card-title">Halaman Saya</div>
    <a href="{{ route('kamu') }}" class="profile-link">
        <span class="profile-link-icon">&#128100;</span>
        <div>
            <div>Halaman Kamu</div>
            <div class="profile-link-sub">Postingan, catatan pribadi, statistik</div>
        </div>
    </a>
    <a href="{{ route('kita') }}" class="profile-link">
        <span class="profile-link-icon">&#128172;</span>
        <div>
            <div>Kita — Feed Komunitas</div>
            <div class="profile-link-sub">Buat dan lihat postingan komunitas</div>
        </div>
    </a>
    <a href="{{ route('dia') }}" class="profile-link">
        <span class="profile-link-icon">&#128483;</span>
        <div>
            <div>Dia — Chat</div>
            <div class="profile-link-sub">Pesan pribadi dan grup</div>
        </div>
    </a>
</div>

<div class="profile-soon">
    &#127925; Fitur playlist personal dan pengaturan akun segera hadir.
</div>

@endsection
