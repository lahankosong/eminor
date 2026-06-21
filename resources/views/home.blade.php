@extends('layouts.app')

@push('styles')
<style>
    /* HERO */
    .hero {
        min-height: 90vh; display: flex; flex-direction: column;
        justify-content: flex-end; padding: 0 2rem 4rem;
        position: relative; overflow: hidden;
    }
    .hero-bg {
        position: absolute; inset: 0; z-index: 0;
        background: linear-gradient(160deg, var(--bg) 0%, var(--bg-2) 55%, var(--bg) 100%);
        transition: background 0.4s;
    }
    /* warm accent bleed at bottom of hero */
    .hero-bg::after {
        content: '';
        position: absolute; bottom: 0; left: 0; right: 0; height: 200px;
        background: linear-gradient(to top, var(--accent-glow), transparent);
    }
    .hero-photo {
        position: absolute; right: -2%; top: 0; height: 100%;
        width: 52%; object-fit: cover; object-position: top center;
        opacity: 0.18;
        mask-image: linear-gradient(to left, rgba(0,0,0,0.5) 0%, transparent 80%);
        -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.5) 0%, transparent 80%);
    }
    [data-theme="light"] .hero-photo { opacity: 0.12; }

    .hero-content { position: relative; z-index: 1; }
    .hero-byline {
        font-size: 10px; letter-spacing: 0.35em; color: var(--text-3);
        text-transform: uppercase; margin-bottom: 1.25rem;
    }
    .hero-byline span { color: var(--text-2); }
    .hero-title {
        font-size: clamp(1.8rem, 5vw, 3.8rem); font-weight: 300;
        letter-spacing: 0.1em; line-height: 1.2;
        margin-bottom: 1.5rem; color: var(--text);
    }
    .hero-story {
        font-size: 14px; color: var(--text-2); line-height: 1.9;
        max-width: 480px; margin-bottom: 2rem;
    }
    .hero-story em { color: var(--text-2); font-style: normal; }
    .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

    .btn-primary {
        padding: 11px 28px; border-radius: 50px; font-size: 13px;
        font-weight: 600; background: var(--text); color: var(--bg);
        border: none; cursor: pointer; transition: opacity 0.2s, transform 0.2s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        letter-spacing: 0.04em;
    }
    .btn-primary:hover { opacity: 0.88; transform: translateY(-1px); }

    .btn-ghost {
        padding: 11px 24px; border-radius: 50px; font-size: 13px;
        font-weight: 500; background: transparent; color: var(--text-2);
        border: 1px solid var(--border); cursor: pointer; transition: 0.2s;
        text-decoration: none; display: inline-flex; align-items: center;
        letter-spacing: 0.04em;
    }
    .btn-ghost:hover { color: var(--text); border-color: var(--text-3); }

    .scroll-hint {
        position: absolute; bottom: 1.5rem; left: 2rem; z-index: 1;
        display: flex; align-items: center; gap: 10px;
    }
    .scroll-line { width: 40px; height: 1px; background: var(--bg-4); }
    .scroll-hint span { font-size: 10px; color: var(--text-4); letter-spacing: 0.2em; }

    .divider { border: none; border-top: 1px solid var(--border-2); margin: 0; }
    .section { padding: 3rem 2rem; }
    .section-eyebrow {
        font-size: 10px; letter-spacing: 0.35em; color: var(--accent);
        text-transform: uppercase; margin-bottom: 0.5rem; opacity: 0.7;
    }
    .section-heading {
        font-size: 1.1rem; font-weight: 400; color: var(--text-2);
        margin-bottom: 2rem; line-height: 1.5;
    }

    /* FEATURED */
    .featured-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; }
    .featured-player {
        border-radius: 12px; overflow: hidden; background: var(--bg-2);
        box-shadow: var(--shadow);
    }
    .featured-player iframe { width: 100%; height: 280px; display: block; border: none; }
    .featured-era-tag {
        display: inline-block; font-size: 10px; letter-spacing: 0.2em; color: var(--accent);
        text-transform: uppercase; border: 1px solid var(--accent-dim);
        padding: 3px 10px; border-radius: 20px; margin-bottom: 1rem;
    }
    .featured-title { font-size: 1.4rem; font-weight: 300; margin-bottom: 0.75rem; color: var(--text); transition: 0.3s; }
    .featured-hook  { font-size: 13px; color: var(--text-2); line-height: 1.7; margin-bottom: 1.5rem; font-style: italic; min-height: 40px; }
    .featured-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .feat-btn {
        font-size: 12px; padding: 6px 16px; border-radius: 20px; text-decoration: none;
        border: 1px solid var(--border); transition: 0.15s; display: inline-flex;
        align-items: center; gap: 6px; cursor: pointer; background: transparent;
        color: var(--text-2);
    }
    .feat-btn:hover  { border-color: var(--text-3); }
    .feat-spotify { color: #1DB954; }
    .feat-youtube { color: #FF0000; }
    .feat-apple   { color: #fc3c44; }
    .feat-chord   { color: var(--accent); }

    /* STORY CARDS */
    .story-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .story-card {
        background: var(--card-bg); border: 1px solid var(--border-2);
        border-radius: 14px; overflow: hidden; transition: 0.25s; cursor: pointer;
    }
    .story-card:hover {
        border-color: var(--border);
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }
    .story-card-thumb {
        width: 100%; aspect-ratio: 16/9; object-fit: cover;
        background: var(--bg-3); display: block; opacity: 0.75; transition: 0.25s;
    }
    .story-card:hover .story-card-thumb { opacity: 1; }
    .story-card-body   { padding: 1rem; }
    .story-card-era    { font-size: 10px; color: var(--accent); letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 6px; opacity: 0.6; }
    .story-card-title  { font-size: 14px; font-weight: 500; color: var(--text); margin-bottom: 8px; }
    .story-card-hook   { font-size: 12px; color: var(--text-2); line-height: 1.6; margin-bottom: 10px; font-style: italic; }
    .story-card-cta    { font-size: 11px; color: var(--text-3); transition: 0.15s; }
    .story-card:hover .story-card-cta { color: var(--text-2); }

    /* PLATFORMS */
    .platforms { display: flex; gap: 10px; flex-wrap: wrap; }
    .platform-btn {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 20px; border-radius: 50px; font-size: 13px;
        font-weight: 500; text-decoration: none; transition: 0.2s;
        border: 1px solid var(--border); color: var(--text-2);
    }
    .platform-btn:hover { border-color: var(--text-3); background: var(--card-bg); }
    .platform-spotify { color: #1DB954; }
    .platform-youtube { color: #FF0000; }
    .platform-apple   { color: #fc3c44; }

    /* COMMUNITY CTA */
    .community-cta {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 18px; padding: 2.5rem 2rem; text-align: center;
        position: relative; overflow: hidden;
    }
    .community-cta::before {
        content: '';
        position: absolute; top: -60px; left: 50%; transform: translateX(-50%);
        width: 300px; height: 200px;
        background: radial-gradient(ellipse, var(--accent-glow) 0%, transparent 70%);
        pointer-events: none;
    }
    .community-cta h3 { font-size: 1.3rem; font-weight: 300; margin-bottom: 0.75rem; color: var(--text); }
    .community-cta p  { font-size: 14px; color: var(--text-2); line-height: 1.7; max-width: 500px; margin: 0 auto 1.75rem; }
    .community-features { display: flex; justify-content: center; gap: 2rem; margin-bottom: 1.75rem; flex-wrap: wrap; }
    .comm-feat { font-size: 12px; color: var(--text-3); display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .comm-feat span:first-child { font-size: 20px; }

    /* FANBASE TICKER */
    .fb-ticker {
        display: block; text-decoration: none;
        background: var(--card-bg);
        border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
        overflow: hidden; white-space: nowrap; padding: 11px 0;
    }
    .fb-ticker:hover .fb-ticker-track { animation-play-state: paused; }
    .fb-ticker-track { display: inline-block; white-space: nowrap; animation: fbticker 30s linear infinite; will-change: transform; }
    .fb-ticker-track span { display: inline-block; margin: 0 1.4rem; font-size: 13px; color: var(--text-2); font-weight: 500; }
    .fb-ticker-track span b { color: var(--accent); font-weight: 600; }
    @keyframes fbticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* FANBASE PROMO */
    .fb-promo { text-align: center; }
    .fb-promo-intro { font-size: 14px; color: var(--text-2); max-width: 520px; margin: 0 auto 2rem; line-height: 1.7; }
    .fb-promo-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px; max-width: 760px; margin: 0 auto 2rem;
    }
    .fb-promo-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem 1rem; text-align: left; }
    .fb-promo-card .ic { font-size: 26px; line-height: 1; }
    .fb-promo-card h4 { font-size: 14px; font-weight: 600; color: var(--text); margin: 0.6rem 0 0.25rem; }
    .fb-promo-card p { font-size: 12px; color: var(--text-3); line-height: 1.5; margin: 0; }
    .fb-promo-cta { text-decoration: none; display: inline-block; font-size: 15px; padding: 13px 28px; }
    .fb-promo-note { font-size: 12px; color: var(--text-3); margin-top: 0.85rem; }

    /* STICKY PLAYER */
    .sticky-player {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: var(--bottom-bg); backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        border-top: 1px solid var(--border); z-index: 500;
        transform: translateY(100%); transition: transform 0.35s ease;
        padding: 10px 2rem;
    }
    .sticky-player.visible { transform: translateY(0); }
    .sticky-inner { max-width: 900px; margin: 0 auto; display: flex; align-items: center; gap: 14px; }
    .sticky-thumb { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background: var(--bg-3); flex-shrink: 0; }
    .sticky-info  { flex: 1; min-width: 0; }
    .sticky-title { font-size: 13px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sticky-era   { font-size: 11px; color: var(--text-3); margin-top: 1px; }
    .sticky-nav   { display: flex; align-items: center; gap: 8px; }
    .sticky-btn {
        width: 30px; height: 30px; border-radius: 50%;
        background: var(--bg-3); border: 1px solid var(--border);
        color: var(--text); font-size: 16px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: 0.15s; padding: 0;
    }
    .sticky-btn:hover { background: var(--bg-4); }
    .sticky-counter { font-size: 11px; color: var(--text-3); min-width: 36px; text-align: center; }
    .sticky-close { background: transparent; border: none; color: var(--text-3); font-size: 16px; cursor: pointer; padding: 4px; transition: 0.15s; }
    .sticky-close:hover { color: var(--text-2); }

    /* STORY POPUP */
    .story-popup {
        display: none; position: fixed; inset: 0; z-index: 2000;
        background: rgba(0,0,0,0); transition: background 0.4s ease;
        align-items: center; justify-content: center;
    }
    .story-popup.open {
        display: flex;
        background: rgba(0,0,0,0.88);
    }
    .story-popup-inner {
        position: relative; width: 100%; max-width: 640px;
        max-height: 88vh; overflow-y: auto;
        margin: 1rem; z-index: 2;
    }
    .story-content {
        background: var(--bg-2); backdrop-filter: blur(24px);
        border: 1px solid var(--border);
        border-radius: 20px; padding: 2rem; position: relative;
    }
    .story-popup-close {
        position: absolute; top: 1rem; right: 1rem;
        background: var(--card-bg); border: 1px solid var(--border);
        color: var(--text-3); width: 30px; height: 30px; border-radius: 50%;
        font-size: 13px; cursor: pointer; display: flex;
        align-items: center; justify-content: center; transition: 0.2s;
    }
    .story-popup-close:hover { color: var(--text); background: var(--bg-3); }
    .story-popup-eyebrow { font-size: 10px; letter-spacing: 0.3em; color: var(--accent); text-transform: uppercase; margin-bottom: 0.5rem; opacity: 0.7; }
    .story-popup-title   { font-size: 1.6rem; font-weight: 300; letter-spacing: 0.05em; margin-bottom: 0.5rem; color: var(--text); }
    .story-popup-era     { font-size: 11px; color: var(--text-3); margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--border-2); }

    /* Waveform */
    .waveform { display: flex; align-items: center; gap: 3px; height: 28px; margin-bottom: 1.25rem; }
    .wave-bar  { width: 3px; background: var(--border); border-radius: 2px; height: 4px; }
    .waveform.playing .wave-bar { animation: wavePulse 1.2s ease-in-out infinite; background: var(--accent); }
    .waveform.playing .wave-bar:nth-child(1)  { animation-delay:0s;    --h:12px; }
    .waveform.playing .wave-bar:nth-child(2)  { animation-delay:0.1s;  --h:24px; }
    .waveform.playing .wave-bar:nth-child(3)  { animation-delay:0.2s;  --h:18px; }
    .waveform.playing .wave-bar:nth-child(4)  { animation-delay:0.15s; --h:28px; }
    .waveform.playing .wave-bar:nth-child(5)  { animation-delay:0.3s;  --h:16px; }
    .waveform.playing .wave-bar:nth-child(6)  { animation-delay:0.05s; --h:22px; }
    .waveform.playing .wave-bar:nth-child(7)  { animation-delay:0.25s; --h:14px; }
    .waveform.playing .wave-bar:nth-child(8)  { animation-delay:0.35s; --h:26px; }
    .waveform.playing .wave-bar:nth-child(9)  { animation-delay:0.1s;  --h:20px; }
    .waveform.playing .wave-bar:nth-child(10) { animation-delay:0.2s;  --h:10px; }
    .waveform.playing .wave-bar:nth-child(11) { animation-delay:0.4s;  --h:24px; }
    .waveform.playing .wave-bar:nth-child(12) { animation-delay:0.15s; --h:18px; }
    @keyframes wavePulse {
        0%,100% { height: 4px; }
        50%      { height: var(--h, 20px); }
    }

    .story-popup-body { font-size: 14px; color: var(--text-2); line-height: 1.9; margin-bottom: 1.5rem; }
    .story-popup-body p { margin-bottom: 1rem; }
    .story-popup-actions {
        display: flex; gap: 10px; flex-wrap: wrap;
        padding-top: 1.25rem; border-top: 1px solid var(--border-2);
    }
    .story-action-btn {
        padding: 8px 18px; border-radius: 50px; font-size: 12px;
        font-weight: 500; text-decoration: none; border: 1px solid var(--border);
        color: var(--text-2); transition: 0.15s; cursor: pointer; background: transparent;
    }
    .story-action-btn.primary { background: var(--text); color: var(--bg); border-color: var(--text); }
    .story-action-btn.primary:hover { opacity: 0.88; }
    .story-action-btn:hover { border-color: var(--text-3); color: var(--text); }
    .story-action-spotify { color: #1DB954; }
    .story-action-youtube { color: #FF0000; }

    /* CHORD POPUP */
    .popup-overlay {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.88);
        z-index: 3000; align-items: center; justify-content: center; padding: 1rem;
    }
    .popup-overlay.open { display: flex; }
    .popup-box {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 18px; width: 100%;
        max-width: 680px; max-height: 88vh; overflow-y: auto; position: relative;
    }
    .popup-close {
        position: absolute; top: 12px; right: 12px;
        background: var(--card-bg); border: 1px solid var(--border);
        color: var(--text-3); width: 28px; height: 28px; border-radius: 50%;
        font-size: 13px; cursor: pointer; display: flex;
        align-items: center; justify-content: center; transition: 0.15s;
    }
    .popup-close:hover { color: var(--text); background: var(--bg-3); }
    .chord-header { padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid var(--border-2); }
    .chord-header h3 { font-size: 1rem; font-weight: 500; color: var(--text); margin-bottom: 3px; }
    .chord-header p  { font-size: 12px; color: var(--text-3); }
    .chord-body { padding: 1.5rem; font-family: 'Courier New', monospace; font-size: 13px; line-height: 2.3; color: var(--text-2); white-space: pre-wrap; }
    .chord-mark   { color: var(--accent); font-weight: 700; }
    .section-mark { color: var(--text-3); font-size: 11px; }

    /* MOBILE */
    @media (max-width: 768px) {
        .hero         { min-height: 75vh; padding: 0 1rem 3rem; }
        .hero-photo   { display: none; }
        .hero-title   { font-size: 2rem; }
        .section      { padding: 2.5rem 1rem; }
        .featured-wrap { grid-template-columns: 1fr; }
        .featured-player iframe { height: 220px; }
        .sticky-player { padding: 8px 1rem; bottom: 60px; }
        .story-popup-inner { margin: 0.5rem; }
        .story-content { padding: 1.5rem; border-radius: 14px; }
    }
    @media (max-width: 560px) {
        .story-cards { grid-template-columns: 1fr; }
        .community-features { gap: 1rem; }
    }
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero">
    <div class="hero-bg"></div>
    @if(file_exists(public_path('images/margonoandi.jpg')))
    <img src="{{ asset('images/margonoandi.jpg') }}" class="hero-photo" alt="Rakhman Andi">
    @endif
    <div class="hero-content">
        <p class="hero-byline">
            {{ $settings['artist_name'] ?? 'Rakhman Andi' }} ·
            <span>{{ $settings['artist_role'] ?? 'Songwriter' }}</span> ·
            Project <span>{{ $settings['artist_project'] ?? 'Margonoandi' }}</span>
        </p>
        <h1 class="hero-title">
            {{ $settings['tagline_1'] ?? 'Tiga chord.' }}<br>
            {{ $settings['tagline_2'] ?? 'Satu rindu.' }}<br>
            {{ $settings['tagline_3'] ?? 'Dua puluh tahun.' }}
        </h1>
        <p class="hero-story">{!! nl2br(e($settings['hero_story'] ?? '')) !!}</p>
        <div class="hero-actions">
            <button class="btn-primary" onclick="document.getElementById('featuredSection').scrollIntoView({behavior:'smooth'})">
                &#9654; Dengarkan
            </button>
            <button class="btn-ghost" onclick="document.getElementById('storySection').scrollIntoView({behavior:'smooth'})">
                Baca ceritanya
            </button>
        </div>
    </div>
    <div class="scroll-hint">
        <div class="scroll-line"></div>
        <span>scroll</span>
    </div>
</div>

@php $fbEntry = auth()->check() ? route('aku') : route('google.login'); @endphp

{{-- FANBASE TICKER --}}
<a href="{{ $fbEntry }}" class="fb-ticker" aria-label="Masuk fanbase">
    <div class="fb-ticker-track">
        @php
            $ticks = [
                '&#127928; Stem gitar <b>gratis</b>',
                '&#127932; Belajar <b>chord gitar &amp; piano</b>',
                '&#127911; Dengar <b>semua lagu</b>',
                '&#128172; Ngobrol bareng <b>sesama fans</b>',
                '&#127908; <b>Cari personil</b> band',
                '&#128221; Catatan lirik &amp; ide lagu',
                '&#128205; Temukan <b>musisi terdekat</b>',
                '&#128640; <b>Masuk fanbase &rarr;</b>',
            ];
        @endphp
        @for ($i = 0; $i < 2; $i++)
            @foreach ($ticks as $t)<span>{!! $t !!}</span>@endforeach
        @endfor
    </div>
</a>

<hr class="divider">

{{-- FANBASE PROMO CTA --}}
<div class="section fb-promo">
    <p class="section-eyebrow">Gratis untuk semua fans</p>
    <p class="section-heading">Bukan cuma dengar lagu.</p>
    <p class="fb-promo-intro">Di dalam fanbase ada alat &amp; ruang buat kamu yang main musik &mdash; semuanya gratis, cukup login.</p>
    <div class="fb-promo-grid">
        <div class="fb-promo-card"><div class="ic">&#127928;</div><h4>Tuner Gitar</h4><p>Stem gitar akurat langsung dari HP.</p></div>
        <div class="fb-promo-card"><div class="ic">&#127932;</div><h4>Belajar Chord</h4><p>Kamus chord gitar &amp; piano, chord geser + suara.</p></div>
        <div class="fb-promo-card"><div class="ic">&#127911;</div><h4>Putar Semua Lagu</h4><p>Dengar koleksi lagu Margonoandi kapan saja.</p></div>
        <div class="fb-promo-card"><div class="ic">&#128172;</div><h4>Komunitas</h4><p>Diskusi, curhat &amp; ngobrol bareng fans lain.</p></div>
        <div class="fb-promo-card"><div class="ic">&#127908;</div><h4>Cari Personil</h4><p>Direktori musisi &amp; lowongan band terdekat.</p></div>
        <div class="fb-promo-card"><div class="ic">&#128221;</div><h4>Catatan Pribadi</h4><p>Simpan lirik, ide lagu &amp; chord favoritmu.</p></div>
    </div>
    <a href="{{ $fbEntry }}" class="btn-primary fb-promo-cta">&#128640; Masuk Fanbase &mdash; Gratis</a>
    <p class="fb-promo-note">@auth Kamu sudah login &mdash; lanjut ke dalam. @else Cukup login pakai Google, langsung bisa dipakai. @endauth</p>
</div>

<hr class="divider">

{{-- FEATURED SONG --}}
@if($featuredSong)
<div class="section" id="featuredSection">
    <p class="section-eyebrow">Sedang diputar</p>
    <p class="section-heading">Mulai dari sini.</p>
    <div class="featured-wrap">
        <div class="featured-player">
            <iframe id="mainPlayer"
                src="https://www.youtube.com/embed/{{ $featuredSong->youtube_id }}?rel=0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
        <div id="featuredInfo">
            <span class="featured-era-tag" id="featuredEraTag">{{ $featuredSong->era ?? '' }}</span>
            <h2 class="featured-title" id="featuredTitle">{{ $featuredSong->title }}</h2>
            <p class="featured-hook" id="featuredHook">
                @if($featuredSong->story_hook)"{{ $featuredSong->story_hook }}"
                @elseif($featuredSong->description){{ $featuredSong->description }}
                @endif
            </p>
            <div class="featured-actions" id="featuredActions">
                @if($featuredSong->spotify_url)
                <a href="{{ $featuredSong->spotify_url }}" target="_blank" class="feat-btn feat-spotify">&#9834; Spotify</a>
                @endif
                <a href="https://youtu.be/{{ $featuredSong->youtube_id }}" target="_blank" class="feat-btn feat-youtube">&#9658; YouTube</a>
                @if($featuredSong->apple_music_url)
                <a href="{{ $featuredSong->apple_music_url }}" target="_blank" class="feat-btn feat-apple">&#9835; Apple Music</a>
                @endif
                @if($featuredSong->chords)
                <button class="feat-btn feat-chord" onclick="window.openChord(0)">&#9833; Chord</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<hr class="divider">

{{-- CTA STORY CARDS --}}
@if($ctaSongs->count() > 0)
<div class="section" id="storySection">
    <p class="section-eyebrow">Cerita di balik lagu</p>
    <p class="section-heading">Setiap lagu punya ceritanya sendiri.</p>
    <div class="story-cards">
        @foreach($ctaSongs as $ctaSong)
        @php $ctaIdx = $songs->search(fn($s) => $s->id === $ctaSong->id); @endphp
        <div class="story-card" onclick="window.openStoryPopup({{ $ctaIdx ?? 0 }})">
            <img class="story-card-thumb"
                src="https://img.youtube.com/vi/{{ $ctaSong->youtube_id }}/mqdefault.jpg"
                alt="{{ $ctaSong->title }}">
            <div class="story-card-body">
                @if($ctaSong->era)
                <p class="story-card-era">{{ $ctaSong->era }}</p>
                @endif
                <p class="story-card-title">{{ $ctaSong->title }}</p>
                @if($ctaSong->story_hook)
                <p class="story-card-hook">"{{ $ctaSong->story_hook }}"</p>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                    <span class="story-card-cta">Dengarkan ceritanya &rarr;</span>
                    @if($ctaSong->slug)
                    <a href="{{ route('song.show', $ctaSong->slug) }}"
                       onclick="event.stopPropagation()"
                       style="font-size:11px;color:#333;text-decoration:none;"
                       onmouseover="this.style.color='#888'" onmouseout="this.style.color='#333'">
                        Baca →
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<hr class="divider">
@endif

<hr class="divider">

{{-- STICKY PLAYER --}}
<div class="sticky-player" id="stickyPlayer">
    <div class="sticky-inner">
        <img id="stickyThumb" src="" class="sticky-thumb" alt="">
        <div class="sticky-info">
            <div class="sticky-title" id="stickyTitle">—</div>
            <div class="sticky-era"   id="stickyEra">—</div>
        </div>
        <div class="sticky-nav">
            <button class="sticky-btn" onclick="window.changeTrack(-1)">&#8249;</button>
            <span class="sticky-counter" id="stickyCounter">—</span>
            <button class="sticky-btn" onclick="window.changeTrack(1)">&#8250;</button>
        </div>
        <button class="sticky-close" onclick="window.closePlayer()">&#10005;</button>
    </div>
</div>

{{-- STORY POPUP - canvas DI LUAR popup --}}
<canvas id="storyVisualizer"
    style="display:none;position:fixed;inset:0;z-index:1999;pointer-events:none;"></canvas>
<div id="floatingNotes"
    style="display:none;position:fixed;inset:0;z-index:1999;pointer-events:none;overflow:hidden;"></div>

<div class="story-popup" id="storyPopup">
    <div class="story-popup-inner">
        <div class="story-content">
            <button class="story-popup-close" onclick="window.closeStoryPopup()">&#10005;</button>
            <p class="story-popup-eyebrow" id="popupEyebrow">Cerita di balik lagu</p>
            <h2 class="story-popup-title" id="popupTitle">—</h2>
            <p class="story-popup-era"    id="popupEra">—</p>
            <div class="waveform" id="popupWaveform">
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
            </div>
            <div class="story-popup-body"    id="popupBody"></div>
            <div class="story-popup-actions" id="popupActions"></div>
        </div>
    </div>
</div>

{{-- CHORD POPUP --}}
<div class="popup-overlay" id="chordPopup" onclick="window.closeChordPopup()">
    <div class="popup-box" onclick="event.stopPropagation()">
        <button class="popup-close" onclick="window.closeChordPopup()">&#10005;</button>
        <div class="chord-header">
            <h3 id="chordTitle"></h3>
            <p id="chordMeta">Margonoandi</p>
        </div>
        <div class="chord-body" id="chordBody"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

@php
$songsArray = $songs->values()->map(function($s) {
    return [
        'id'              => $s->id,
        'youtube_id'      => $s->youtube_id,
        'title'           => $s->title,
        'era'             => $s->era,
        'story_hook'      => $s->story_hook,
        'description'     => $s->description,
        'chords'          => $s->chords,
        'key_signature'   => $s->key_signature,
        'tempo'           => $s->tempo,
        'spotify_url'     => $s->spotify_url,
        'apple_music_url' => $s->apple_music_url,
    ];
});
@endphp

var songs   = @json($songsArray);
var total   = songs.length;
var current = -1;
var notesInterval       = null;
var isVisualizerRunning = false;
var visAnimId           = null;

/* ===== PLAYER ===== */
window.playSong = function(index) {
    if (!songs[index]) return;
    current = index;
    var song = songs[index];
    var el;

    el = document.getElementById('mainPlayer');
    if (el) el.src = 'https://www.youtube.com/embed/' + song.youtube_id + '?rel=0&autoplay=1';

    el = document.getElementById('featuredEraTag'); if (el) el.textContent = song.era || '';
    el = document.getElementById('featuredTitle');  if (el) el.textContent = song.title;
    el = document.getElementById('featuredHook');
    if (el) el.textContent = song.story_hook ? '"' + song.story_hook + '"' : (song.description || '');

    var actions = document.getElementById('featuredActions');
    if (actions) {
        var h = '';
        if (song.spotify_url) h += '<a href="' + song.spotify_url + '" target="_blank" class="feat-btn feat-spotify">&#9834; Spotify</a>';
        h += '<a href="https://youtu.be/' + song.youtube_id + '" target="_blank" class="feat-btn feat-youtube">&#9658; YouTube</a>';
        if (song.apple_music_url) h += '<a href="' + song.apple_music_url + '" target="_blank" class="feat-btn feat-apple">&#9835; Apple Music</a>';
        if (song.chords) h += '<button class="feat-btn feat-chord" onclick="window.openChord(' + index + ')">&#9833; Chord</button>';
        actions.innerHTML = h;
    }

    el = document.getElementById('stickyThumb');   if (el) el.src = 'https://img.youtube.com/vi/' + song.youtube_id + '/mqdefault.jpg';
    el = document.getElementById('stickyTitle');   if (el) el.textContent = song.title;
    el = document.getElementById('stickyEra');     if (el) el.textContent = song.era || 'Margonoandi';
    el = document.getElementById('stickyCounter'); if (el) el.textContent = (index + 1) + ' / ' + total;
    el = document.getElementById('stickyPlayer');  if (el) el.classList.add('visible');

    el = document.getElementById('featuredSection');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};

window.changeTrack = function(dir) {
    if (current < 0) current = 0;
    var n = current + dir;
    if (n >= total) n = 0;
    if (n < 0) n = total - 1;
    window.playSong(n);
};

window.closePlayer = function() {
    var el = document.getElementById('stickyPlayer');
    if (el) el.classList.remove('visible');
    el = document.getElementById('mainPlayer');
    if (el) el.src = 'https://www.youtube.com/embed/{{ $featuredSong->youtube_id ?? "" }}?rel=0';
    current = -1;
};

/* ===== STORY POPUP ===== */
window.openStoryPopup = function(index) {
    var song = songs[index];
    if (!song) return;
    window.playSong(index);

    var el;
    el = document.getElementById('popupEyebrow');
    if (el) el.textContent = song.era ? 'Era ' + song.era : 'Cerita di balik lagu';
    el = document.getElementById('popupTitle');
    if (el) el.textContent = song.title;

    var meta = song.era || 'Margonoandi';
    if (song.key_signature) meta += ' · Key ' + song.key_signature;
    if (song.tempo) meta += ' · ' + song.tempo + ' bpm';
    el = document.getElementById('popupEra');
    if (el) el.textContent = meta;

    var body = song.description || song.story_hook || '';
    el = document.getElementById('popupBody');
    if (el) el.innerHTML = body
        ? '<p>' + body.replace(/\n\n/g,'</p><p>').replace(/\n/g,'<br>') + '</p>'
        : '<p style="color:#333;font-style:italic;">Cerita lagu ini sedang ditulis.</p>';

    var actHtml = '<button class="story-action-btn primary" onclick="window.closeStoryPopup()">&#9654; Lanjut mendengarkan</button>';
    if (song.spotify_url) actHtml += '<a href="' + song.spotify_url + '" target="_blank" class="story-action-btn story-action-spotify">&#9834; Spotify</a>';
    actHtml += '<a href="https://youtu.be/' + song.youtube_id + '" target="_blank" class="story-action-btn story-action-youtube">&#9658; YouTube</a>';
    el = document.getElementById('popupActions');
    if (el) el.innerHTML = actHtml;

    // Tampilkan canvas & notes
    var canvas = document.getElementById('storyVisualizer');
    var notes  = document.getElementById('floatingNotes');
    if (canvas) { canvas.style.display = 'block'; canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    if (notes)    notes.style.display = 'block';

    var popup = document.getElementById('storyPopup');
    if (popup) { popup.style.display = 'flex'; setTimeout(function(){ popup.classList.add('open'); }, 10); }

    el = document.getElementById('popupWaveform');
    if (el) el.classList.add('playing');

    isVisualizerRunning = false;
    if (visAnimId) { cancelAnimationFrame(visAnimId); visAnimId = null; }
    startVisualizer();
    startFloatingNotes();
    document.body.style.overflow = 'hidden';
};

window.closeStoryPopup = function() {
    isVisualizerRunning = false;
    if (visAnimId) { cancelAnimationFrame(visAnimId); visAnimId = null; }

    var popup = document.getElementById('storyPopup');
    if (popup) { popup.classList.remove('open'); setTimeout(function(){ popup.style.display = 'none'; }, 400); }

    var canvas = document.getElementById('storyVisualizer');
    if (canvas) { var ctx = canvas.getContext('2d'); if (ctx) ctx.clearRect(0,0,canvas.width,canvas.height); canvas.style.display = 'none'; }

    var notes = document.getElementById('floatingNotes');
    if (notes) { notes.style.display = 'none'; notes.innerHTML = ''; }

    var el = document.getElementById('popupWaveform');
    if (el) el.classList.remove('playing');

    stopFloatingNotes();
    document.body.style.overflow = '';
};

/* ===== FLOATING NOTES ===== */
var noteSymbols = ['&#9833;','&#9834;','&#9835;','&#9836;','&#9837;'];

function startFloatingNotes() {
    var c = document.getElementById('floatingNotes');
    if (!c) return;
    c.innerHTML = '';
    if (notesInterval) clearInterval(notesInterval);
    notesInterval = setInterval(function() {
        var n = document.createElement('div');
        n.style.cssText = 'position:absolute;pointer-events:none;animation:floatNote linear infinite;opacity:0;';
        n.style.left = Math.random() * 100 + 'vw';
        n.style.fontSize = (14 + Math.random() * 20) + 'px';
        n.style.color = 'rgba(255,255,255,0.04)';
        n.style.animationDuration = (6 + Math.random() * 8) + 's';
        n.innerHTML = noteSymbols[Math.floor(Math.random() * noteSymbols.length)];
        c.appendChild(n);
        setTimeout(function(){ if (n.parentNode) n.parentNode.removeChild(n); }, 14000);
    }, 700);
}

function stopFloatingNotes() {
    if (notesInterval) clearInterval(notesInterval);
    notesInterval = null;
}

/* CSS for floating notes */
var styleEl = document.createElement('style');
styleEl.textContent = '@keyframes floatNote{0%{transform:translateY(100vh) rotate(0deg);opacity:0}10%{opacity:1}90%{opacity:0.4}100%{transform:translateY(-100px) rotate(360deg);opacity:0}}';
document.head.appendChild(styleEl);

/* ===== VISUALIZER ===== */
function startVisualizer() {
    var canvas = document.getElementById('storyVisualizer');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    isVisualizerRunning = true;

    var bars    = 50;
    var heights = Array.from({length: bars}, function(){ return Math.random() * 30 + 5; });
    var targets = Array.from({length: bars}, function(){ return Math.random() * 50 + 10; });

    function draw() {
        if (!isVisualizerRunning) { ctx.clearRect(0,0,canvas.width,canvas.height); return; }
        var popup = document.getElementById('storyPopup');
        if (!popup || !popup.classList.contains('open')) { isVisualizerRunning = false; ctx.clearRect(0,0,canvas.width,canvas.height); return; }

        ctx.clearRect(0,0,canvas.width,canvas.height);
        var bw = canvas.width / bars;
        for (var i = 0; i < bars; i++) {
            heights[i] += (targets[i] - heights[i]) * 0.05;
            if (Math.abs(heights[i] - targets[i]) < 1) targets[i] = Math.random() * 70 + 10;
            var alpha = 0.03 + (heights[i] / 80) * 0.07;
            ctx.fillStyle = 'rgba(96,165,250,' + alpha + ')';
            ctx.fillRect(i * bw, canvas.height - heights[i], bw - 2, heights[i]);
        }
        visAnimId = requestAnimationFrame(draw);
    }
    draw();
}

window.addEventListener('resize', function() {
    var c = document.getElementById('storyVisualizer');
    if (c && c.style.display !== 'none') { c.width = window.innerWidth; c.height = window.innerHeight; }
});

/* ===== CHORD POPUP ===== */
window.openChord = function(index) {
    var song = songs[index];
    if (!song) return;
    var el;
    el = document.getElementById('chordTitle'); if (el) el.textContent = song.title;
    var m = song.era || 'Margonoandi';
    if (song.key_signature) m += ' · Key: ' + song.key_signature;
    if (song.tempo) m += ' · ' + song.tempo + ' bpm';
    el = document.getElementById('chordMeta'); if (el) el.textContent = m;
    el = document.getElementById('chordBody'); if (el) el.innerHTML = renderChord(song);
    el = document.getElementById('chordPopup'); if (el) el.classList.add('open');
};

window.closeChordPopup = function() {
    var el = document.getElementById('chordPopup'); if (el) el.classList.remove('open');
};

function renderChord(song) {
    if (!song || !song.chords) return '<span style="color:#333;">Chord belum tersedia.</span>';
    return song.chords
        .replace(/\[([^\]]+)\]/g, '<span class="section-mark">[$1]</span>')
        .replace(/\b([A-G][#b]?(?:m|maj|min|dim|aug|sus|add)?[0-9]?(?:\/[A-G][#b]?)?)\b/g,
            '<span class="chord-mark">$1</span>');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { window.closeStoryPopup(); window.closeChordPopup(); }
});

console.log('Home loaded:', songs.length, 'songs');
});
</script>
@endpush