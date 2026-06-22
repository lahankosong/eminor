@extends('layouts.fanbase')
@section('title', $profile->user->name ?? 'Musisi')

@push('styles')
<style>
    .ms-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; display: inline-block; margin-bottom: 1rem; }
    .ms-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-sm); }
    .ms-top { display: flex; align-items: center; gap: 14px; margin-bottom: 1rem; }
    .ms-avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--border); }
    .ms-name { font-family: 'Sora',sans-serif; font-size: 1.3rem; font-weight: 700; color: var(--text-1); }
    .ms-loc { font-size: 13px; color: var(--text-3); margin-top: 2px; }
    .ms-tags { display: flex; flex-wrap: wrap; gap: 6px; margin: 4px 0 1rem; }
    .ms-tag { font-size: 12px; padding: 3px 10px; border-radius: 20px; background: var(--surface); color: var(--sky-dk); border: 1px solid var(--border-lt); }
    .ms-tag.g { color: var(--text-2); }
    .ms-tag.lv { background: var(--sky); color: #fff; border-color: var(--sky); }
    .ms-sec { margin-top: 1rem; }
    .ms-sec-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-3); margin-bottom: 4px; }
    .ms-bio { font-size: 14px; color: var(--text-1); line-height: 1.6; white-space: pre-wrap; }
    .ms-look { font-size: 14px; color: var(--text-1); }
    .ms-links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .ms-link { font-size: 12px; padding: 6px 12px; border-radius: 10px; background: var(--surface); border: 1px solid var(--border); color: var(--text-2); text-decoration: none; }
    .ms-link:hover { border-color: var(--sky); color: var(--sky-dk); }
    .ms-actions { margin-top: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .ms-btn { padding: 11px 22px; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }
    .ms-btn-primary { background: var(--sky); color: #fff; }
    .ms-btn-primary:hover { background: var(--sky-dk); }
    .ms-btn-ghost { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
</style>
@endpush

@section('content')

<a href="{{ route('musisi.index') }}" class="ms-back">← Direktori Musisi</a>

<div class="ms-card">
    <div class="ms-top">
        <img class="ms-avatar" src="{{ $profile->photoUrl() }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'" alt="">
        <div style="min-width:0;">
            <div class="ms-name">{{ $profile->user->name ?? 'Musisi' }}</div>
            @if($profile->location)<div class="ms-loc">📍 {{ $profile->location }}</div>@endif
        </div>
    </div>

    <div class="ms-tags">
        @if($profile->skill_level)<span class="ms-tag lv">{{ ucfirst($profile->skill_level) }}</span>@endif
        @foreach($profile->rolesArray() as $role)<span class="ms-tag">{{ $role }}</span>@endforeach
        @foreach($profile->genresArray() as $g)<span class="ms-tag g">{{ $g }}</span>@endforeach
    </div>

    @if($profile->looking_for)
    <div class="ms-sec">
        <div class="ms-sec-label">Sedang mencari</div>
        <div class="ms-look">🔎 {{ $profile->looking_for }}</div>
    </div>
    @endif

    @if($profile->bio)
    <div class="ms-sec">
        <div class="ms-sec-label">Bio</div>
        <div class="ms-bio">{{ $profile->bio }}</div>
    </div>
    @endif

    @if($profile->spotify_url || $profile->youtube_url || $profile->instagram)
    <div class="ms-sec">
        <div class="ms-sec-label">Portofolio</div>
        <div class="ms-links">
            @if($profile->spotify_url)<a class="ms-link" href="{{ $profile->spotify_url }}" target="_blank" rel="noopener">🎧 Spotify</a>@endif
            @if($profile->youtube_url)<a class="ms-link" href="{{ $profile->youtube_url }}" target="_blank" rel="noopener">▶️ YouTube</a>@endif
            @if($profile->instagram)<a class="ms-link" href="https://instagram.com/{{ ltrim($profile->instagram,'@') }}" target="_blank" rel="noopener">📸 Instagram</a>@endif
        </div>
    </div>
    @endif

    <div class="ms-actions">
        @if($profile->tip_url)
        <a href="{{ $profile->tip_url }}" target="_blank" rel="noopener"
           class="ms-btn" style="background:linear-gradient(135deg,var(--orange),var(--orange-dk));color:#fff;">☕ Dukung / Saweran</a>
        @endif
        @if($profile->user_id === auth()->id())
        <a href="{{ route('musisi.edit') }}" class="ms-btn ms-btn-primary">✏️ Edit profilku</a>
        @else
        <form method="POST" action="{{ route('dia.start', $profile->user_id) }}">
            @csrf
            <button type="submit" class="ms-btn ms-btn-primary">💬 Hubungi</button>
        </form>
        @endif
        <button type="button" class="ms-btn ms-btn-ghost" onclick="msShareProfile()">🔗 Bagikan</button>
        <button type="button" class="ms-btn ms-btn-ghost" id="msCardBtn" onclick="msShareCard()">📸 Kartu Gambar</button>
        <a href="{{ route('musisi.index') }}" class="ms-btn ms-btn-ghost">← Kembali</a>
    </div>
</div>

<script>
function msShareProfile(){
    var url = window.location.href;
    var title = {{ Illuminate\Support\Js::from(($profile->user->name ?? 'Musisi') . ' — Margonoandi') }};
    if (navigator.share) {
        navigator.share({ title: title, text: 'Cek profil musisi ini di Margonoandi', url: url }).catch(function(){});
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){ alert('Link profil disalin!'); }).catch(function(){ prompt('Salin link:', url); });
    } else { prompt('Salin link:', url); }
}
</script>

@php
$cardData = [
    'name'     => $profile->user->name ?? 'Musisi',
    'avatar'   => $profile->photoUrl(),
    'roles'    => array_map('ucfirst', $profile->rolesArray()),
    'genres'   => $profile->genresArray(),
    'location' => $profile->location,
    'skill'    => $profile->skill_level ? ucfirst($profile->skill_level) : '',
    'bio'      => \Illuminate\Support\Str::limit(strip_tags((string) $profile->bio), 170),
    'url'      => route('musisi.show', $profile->id),
];
@endphp
<script>window.MS_CARD = {{ Illuminate\Support\Js::from($cardData) }};</script>
<script src="{{ asset('js/musician-card.js') }}?v={{ @filemtime(public_path('js/musician-card.js')) ?: 1 }}"></script>

@endsection
