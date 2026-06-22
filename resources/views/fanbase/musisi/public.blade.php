@extends('layouts.app')

@section('content')
@php
    $u     = $profile->user;
    $roles = $profile->rolesArray();
    $genres = $profile->genresArray();
@endphp

<div class="section" style="max-width:480px;margin:0 auto;text-align:center;">
    <p class="section-eyebrow">Profil Musisi · Margonoandi</p>

    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:22px;padding:2rem 1.5rem;margin-top:1rem;">
        <img src="{{ $u->avatar ?? asset('images/default-avatar.png') }}"
             onerror="this.src='{{ asset('images/default-avatar.png') }}'"
             alt="{{ $u->name ?? 'Musisi' }}"
             style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
        <h1 style="font-family:'Space Grotesk','Inter',sans-serif;font-size:1.5rem;font-weight:600;color:var(--text);margin-top:0.85rem;">{{ $u->name ?? 'Musisi' }}</h1>
        @if($profile->location)<p style="font-size:13px;color:var(--text-3);margin-top:3px;">📍 {{ $profile->location }}</p>@endif

        <div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;margin:1rem 0;">
            @if($profile->skill_level)
            <span style="font-size:12px;padding:4px 12px;border-radius:20px;background:var(--accent);color:#fff;font-weight:600;">{{ ucfirst($profile->skill_level) }}</span>
            @endif
            @foreach($roles as $r)
            <span style="font-size:12px;padding:4px 12px;border-radius:20px;background:var(--accent-dim);color:var(--accent);">{{ $r }}</span>
            @endforeach
            @foreach($genres as $g)
            <span style="font-size:12px;padding:4px 12px;border-radius:20px;background:var(--card-bg);border:1px solid var(--border);color:var(--text-2);">🎶 {{ $g }}</span>
            @endforeach
        </div>

        @if($profile->bio)
        <p style="font-size:14px;color:var(--text-2);line-height:1.7;white-space:pre-wrap;text-align:left;">{{ \Illuminate\Support\Str::limit($profile->bio, 280) }}</p>
        @endif

        {{-- Detail (portofolio, kontak, dukungan) HANYA untuk member --}}
        <div style="background:var(--bg-2);border:1px dashed var(--border);border-radius:14px;padding:1rem;margin-top:1.25rem;">
            <p style="font-size:13px;color:var(--text-3);">🔒 Portofolio, kontak &amp; dukungan musisi ini hanya untuk member.</p>
        </div>

        <a href="{{ route('google.login') }}" class="btn-primary" style="text-decoration:none;display:block;text-align:center;margin-top:1rem;">Masuk untuk lihat lengkap &amp; hubungi</a>
    </div>

    <a href="{{ route('home') }}" class="btn-ghost" style="text-decoration:none;display:inline-block;margin-top:1.25rem;">← Jelajahi Margonoandi</a>
</div>

@endsection
