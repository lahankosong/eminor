@extends('layouts.app')

@push('styles')
<style>
    .profile-card {
        background: #111; border: 1px solid #222;
        border-radius: 16px; padding: 2rem; margin-bottom: 2rem;
        display: flex; align-items: center; gap: 1.5rem;
    }
    .profile-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
    .profile-name { font-size: 1.3rem; font-weight: 500; margin-bottom: 4px; }
    .profile-email { color: #666; font-size: 14px; }
    .profile-joined { color: #555; font-size: 12px; margin-top: 6px; }
</style>
@endpush

@section('content')

<div class="profile-card">
    <img src="{{ $user->avatar }}" alt="avatar" class="profile-avatar">
    <div>
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-email">{{ $user->email }}</div>
        <div class="profile-joined">Bergabung sejak {{ $user->created_at->format('d M Y') }}</div>
    </div>
</div>

<p style="color:#555;font-size:14px;">Fitur playlist personal segera hadir.</p>

@endsection