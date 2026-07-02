@extends('layouts.app')

@section('content')
@php
    $u     = $profile->user;
    $roles = $profile->rolesArray();
    $genres = $profile->genresArray();
@endphp

<div class="section" style="max-width:480px;margin:0 auto;text-align:center;">
    <p class="section-eyebrow">Profil Musisi · EMINOR</p>

    <div class="ms-pub-card" style="background:var(--card-bg);border:1px solid var(--border);border-radius:22px;padding:2rem 1.5rem;margin-top:1rem;transition:transform .15s ease;transform-style:preserve-3d;will-change:transform;">
        <img src="{{ $profile->photoUrl() }}"
             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'"
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

    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:1.25rem;">
        <button type="button" id="msCardBtn" onclick="msShareCard()" class="btn-ghost" style="cursor:pointer;">📸 Bagikan sebagai gambar</button>
        <a href="{{ route('home') }}" class="btn-ghost" style="text-decoration:none;">← Jelajahi EMINOR</a>
    </div>
</div>

<script>
/* Kartu profil "menghadap" kursor (desktop) / jari (HP) / kemiringan HP (giroskop) */
(function(){
    var card = document.querySelector('.ms-pub-card');
    if (!card) return;
    function tilt(rx, ry){ card.style.transform = 'perspective(900px) rotateX(' + rx.toFixed(2) + 'deg) rotateY(' + ry.toFixed(2) + 'deg)'; }
    function fromPoint(x, y){
        var r = card.getBoundingClientRect();
        tilt((0.5 - (y - r.top) / r.height) * 10, ((x - r.left) / r.width - 0.5) * 10);
    }
    card.addEventListener('mousemove', function(e){ fromPoint(e.clientX, e.clientY); });
    card.addEventListener('mouseleave', function(){ card.style.transform = ''; });
    card.addEventListener('touchstart', function(e){ var t=e.touches[0]; if(t) fromPoint(t.clientX, t.clientY); }, {passive:true});
    card.addEventListener('touchmove',  function(e){ var t=e.touches[0]; if(t) fromPoint(t.clientX, t.clientY); }, {passive:true});
    card.addEventListener('touchend',   function(){ card.style.transform = ''; });
    card.addEventListener('touchcancel',function(){ card.style.transform = ''; });
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', function(e){
            if (e.gamma == null && e.beta == null) return;
            tilt(Math.max(-9, Math.min(9, ((e.beta || 0) - 45) * 0.35)), Math.max(-9, Math.min(9, (e.gamma || 0) * 0.4)));
        }, true);
    }
})();
</script>

@php
$cardData = [
    'name'     => $u->name ?? 'Musisi',
    'avatar'   => $profile->photoUrl(),
    'roles'    => array_map('ucfirst', $roles),
    'genres'   => $genres,
    'location' => $profile->location,
    'skill'    => $profile->skill_level ? ucfirst($profile->skill_level) : '',
    'bio'      => \Illuminate\Support\Str::limit(strip_tags((string) $profile->bio), 170),
    'url'      => route('musisi.show', $profile->id),
];
@endphp
<script>window.MS_CARD = {{ Illuminate\Support\Js::from($cardData) }};</script>
<script src="{{ asset('js/musician-card.js') }}?v={{ @filemtime(public_path('js/musician-card.js')) ?: 1 }}"></script>

@endsection
