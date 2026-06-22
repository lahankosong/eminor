<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(config('services.google_site_verification'))
    <meta name="google-site-verification" content="{{ config('services.google_site_verification') }}">
    @endif

    {{-- ===== SEO ===== --}}
    @php
        $seoTitle = $seo['title'] ?? 'Margonoandi — Lagu, Chord & Komunitas Musik Indonesia';
        $seoDesc  = $seo['description'] ?? 'Dengarkan lagu Margonoandi, belajar chord gitar/piano/ukulele/bass + tuner, dan gabung komunitas musisi. Ekosistem musik Indonesia, dimulai dari kamar tidur.';
        $seoImage = $seo['image'] ?? asset('images/Margonoandi.jpeg');
        $seoUrl   = $seo['url'] ?? url()->current();
        $seoType  = $seo['type'] ?? 'website';
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="Margonoandi">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    @isset($seo['schema'])
    <script type="application/ld+json">{!! json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endisset
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    <style>
        /* ===== VARIABLES ===== */
        :root {
            --accent:       #38A8CC;
            --accent-2:     #F07040;
            --accent-dim:   rgba(56,168,204,0.15);
            --accent-glow:  rgba(56,168,204,0.08);

            --bg:           #0b1520;
            --bg-2:         #0f1e2e;
            --bg-3:         #162840;
            --bg-4:         #1e3450;
            --text:         #e8f4fa;
            --text-2:       #7A9DB0;
            --text-3:       #3A5468;
            --text-4:       #1e3450;
            --border:       rgba(56,168,204,0.14);
            --border-2:     rgba(56,168,204,0.07);
            --nav-bg:       rgba(11,21,32,0.85);
            --bottom-bg:    rgba(11,21,32,0.96);
            --card-bg:      rgba(56,168,204,0.05);
            --shadow:       0 8px 40px rgba(56,168,204,0.12);
        }

        [data-theme="light"] {
            --accent:       #38A8CC;
            --accent-2:     #F07040;
            --accent-dim:   rgba(56,168,204,0.15);
            --accent-glow:  rgba(56,168,204,0.06);

            --bg:           #FDF8F3;
            --bg-2:         #EEF7FB;
            --bg-3:         #E6F4FA;
            --bg-4:         #D8EAF2;
            --text:         #162030;
            --text-2:       #3A5468;
            --text-3:       #7A9DB0;
            --text-4:       #B0C8D4;
            --border:       #D8EAF2;
            --border-2:     #EBF5F9;
            --nav-bg:       rgba(253,248,243,0.88);
            --bottom-bg:    rgba(253,248,243,0.96);
            --card-bg:      rgba(56,168,204,0.04);
            --shadow:       0 4px 16px rgba(56,168,204,0.12);
        }

        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background 0.4s ease, color 0.4s ease;
            overflow-x: hidden;
        }

        /* ===== GRAIN OVERLAY ===== */
        body::after {
            content: '';
            position: fixed; inset: 0; z-index: 9999;
            pointer-events: none;
            opacity: 0.018;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 200px 200px;
        }

        /* ===== AMBIENT TOP GLOW ===== */
        .page-glow {
            position: fixed; top: -80px; left: 50%; z-index: 0;
            transform: translateX(-50%);
            width: 700px; height: 360px;
            background: radial-gradient(ellipse at 50% 0%, var(--accent-glow) 0%, transparent 65%);
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        /* ===== NAV ===== */
        nav.top-nav {
            position: sticky; top: 0; z-index: 500;
            display: flex; align-items: center; justify-content: space-between;
            height: 58px; padding: 0 2rem;
            background: var(--nav-bg);
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s, background 0.4s;
        }
        nav.top-nav.scrolled {
            border-bottom-color: var(--border);
        }

        /* Brand */
        .nav-brand {
            font-size: 0.95rem; font-weight: 600; letter-spacing: 0.22em;
            text-decoration: none; flex-shrink: 0;
            color: var(--text);
            transition: opacity 0.2s;
            position: relative;
        }
        .nav-brand span {
            /* italic italic Playfair for the "andi" suffix */
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic; font-weight: 400;
            letter-spacing: 0.08em;
            color: var(--accent);
        }
        .nav-brand::after {
            content: '';
            position: absolute; left: 0; bottom: -4px; width: 0; height: 1px;
            background: linear-gradient(90deg, var(--accent), transparent);
            transition: width 0.3s ease;
        }
        .nav-brand:hover::after { width: 100%; }

        /* Center links */
        .nav-center {
            display: flex; align-items: center; gap: 2px;
        }
        .nav-link {
            position: relative; font-size: 12px; color: var(--text-3);
            text-decoration: none; padding: 6px 14px; border-radius: 24px;
            letter-spacing: 0.07em; transition: color 0.2s, background 0.2s;
        }
        .nav-link:hover { color: var(--text); background: var(--card-bg); }
        .nav-link.active { color: var(--text); }
        .nav-link.active::after {
            content: '';
            position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%);
            width: 3px; height: 3px; border-radius: 50%;
            background: var(--accent);
        }

        /* Right side */
        .nav-right { display: flex; align-items: center; gap: 8px; }

        /* Theme toggle */
        .theme-toggle {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text-2); font-size: 15px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: color 0.2s, border-color 0.2s, background 0.2s;
            flex-shrink: 0;
        }
        .theme-toggle:hover { color: var(--text); border-color: var(--border-2); background: var(--bg-3); }

        /* Auth */
        .btn-login {
            display: flex; align-items: center; gap: 7px;
            padding: 7px 17px; border-radius: 50px;
            background: var(--text); color: var(--bg);
            font-size: 12px; font-weight: 600; letter-spacing: 0.04em;
            text-decoration: none; transition: opacity 0.2s, transform 0.2s;
        }
        .btn-login:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-login img { width: 13px; height: 13px; border-radius: 2px; }

        .user-info { display: flex; align-items: center; gap: 9px; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            object-fit: cover; border: 1.5px solid var(--border);
            transition: border-color 0.2s;
        }
        .user-avatar:hover { border-color: var(--accent); }
        .user-name { font-size: 12px; color: var(--text-2); }
        .btn-logout {
            font-size: 11px; color: var(--text-3); text-decoration: none;
            padding: 5px 13px; border: 1px solid var(--border); border-radius: 24px;
            letter-spacing: 0.04em; transition: color 0.2s, border-color 0.2s;
        }
        .btn-logout:hover { color: var(--text); border-color: var(--text-3); }

        /* ===== MAIN ===== */
        main {
            max-width: 900px; margin: 0 auto;
            padding: 2rem 2rem 5rem;
            position: relative; z-index: 1;
        }

        /* Alert */
        .alert-session {
            background: rgba(239,68,68,0.07);
            color: #f87171; border: 1px solid rgba(239,68,68,0.18);
            padding: 10px 16px; border-radius: 10px;
            margin-bottom: 1.25rem; font-size: 13px;
        }

        /* ===== FOOTER ===== */
        footer {
            position: relative; z-index: 1;
            margin-top: 3rem; margin-bottom: 60px;
            border-top: 1px solid var(--border-2);
            padding: 2.5rem 2rem 2rem;
        }
        .footer-inner {
            max-width: 900px; margin: 0 auto;
            display: flex; flex-direction: column; align-items: center; gap: 1.25rem;
        }
        .footer-brand-label {
            font-size: 10px; letter-spacing: 0.35em; color: var(--text-3);
            text-transform: uppercase; font-weight: 500;
        }
        .footer-sep {
            width: 48px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), var(--accent-2), transparent);
            opacity: 0.6;
        }
        .footer-links {
            display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: center;
        }
        .footer-link {
            font-size: 11px; font-weight: 500; text-decoration: none;
            padding: 5px 14px; border-radius: 20px;
            border: 1px solid var(--border);
            color: var(--text-3);
            transition: color 0.2s, border-color 0.2s, background 0.2s;
            display: flex; align-items: center; gap: 5px;
        }
        .footer-link:hover { background: var(--card-bg); }
        .footer-link.spotify:hover  { color: #1DB954; border-color: rgba(29,185,84,0.3); }
        .footer-link.youtube:hover  { color: #FF0000; border-color: rgba(255,0,0,0.25); }
        .footer-link.apple:hover    { color: #fc3c44; border-color: rgba(252,60,68,0.25); }
        .footer-copy {
            font-size: 11px; color: var(--text-4);
            letter-spacing: 0.03em;
        }

        /* ===== BOTTOM NAV MOBILE ===== */
        .bottom-nav {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 300;
            background: var(--bottom-bg);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-top: 1px solid var(--border);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            transition: background 0.4s;
        }
        .bottom-nav-inner {
            display: flex; align-items: stretch; height: 56px;
        }
        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 4px;
            text-decoration: none; color: var(--text-4);
            background: transparent; border: none;
            font-family: inherit; cursor: pointer; padding: 0;
            position: relative; transition: color 0.2s;
        }
        .bottom-nav-item:hover { color: var(--text-3); }
        .bottom-nav-item.active { color: var(--text); }
        .bottom-nav-item.active::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 24px; height: 2px; border-radius: 0 0 3px 3px;
            background: var(--accent);
        }
        .bnav-icon { font-size: 17px; line-height: 1; }
        .bnav-label { font-size: 9px; letter-spacing: 0.06em; font-weight: 500; text-transform: uppercase; }

        /* ===== MOBILE ===== */
        @media (max-width: 768px) {
            .bottom-nav { display: block; }
            .nav-center { display: none; }
            .user-name  { display: none; }
            nav.top-nav { padding: 0 1rem; height: 50px; }
            main        { padding: 0 0 5rem; }
            footer      { padding: 2rem 1rem 1.5rem; margin-bottom: 60px; }
        }

        /* ===== CURSOR GLOW ===== */
        .cursor-light {
            position: fixed; pointer-events: none; z-index: 3;
            width: 420px; height: 420px; border-radius: 50%;
            left: -9999px; top: -9999px;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle,
                rgba(56,168,204,0.13) 0%,
                rgba(56,168,204,0.06) 40%,
                transparent 75%);
            will-change: left, top;
            transition: opacity 0.5s ease;
        }
        [data-theme="light"] .cursor-light {
            background: radial-gradient(circle,
                rgba(56,168,204,0.09) 0%,
                rgba(56,168,204,0.035) 40%,
                transparent 75%);
        }
        @media (hover: none) { .cursor-light { display: none; } }
    </style>
    @stack('styles')
</head>
<body>

<div class="page-glow"></div>
<div class="cursor-light" id="cursorLight"></div>

{{-- NAV --}}
<nav class="top-nav" id="topNav">

    <a href="{{ route('home') }}" class="nav-brand">MARGONOANDI</a>

    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle tema">🌙</button>

        @auth
        <div class="user-info">
            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="user-avatar">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <a href="{{ route('logout') }}" class="btn-logout">Keluar</a>
        </div>
        @else
        <a href="{{ route('google.login') }}" class="btn-login">
            <img src="https://www.google.com/favicon.ico" alt="G"> Masuk
        </a>
        @endauth
    </div>
</nav>

{{-- MAIN CONTENT --}}
<main>
    @if(session('error'))
    <div class="alert-session">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

{{-- FOOTER --}}
<footer>
    <div class="footer-inner">
        <div class="footer-brand-label">Margonoandi</div>
        <div class="footer-sep"></div>
        <div class="footer-links">
            <a href="https://open.spotify.com/playlist/1lpXuXUd3wMbwWe0stM0dD"
               target="_blank" rel="noopener" class="footer-link spotify">&#9834; Spotify</a>
            <a href="https://www.youtube.com/channel/UCBTFgn31i3auH29qm81lDKA"
               target="_blank" rel="noopener" class="footer-link youtube">&#9658; YouTube</a>
            <a href="https://music.apple.com/us/artist/margonoandi/1850375782"
               target="_blank" rel="noopener" class="footer-link apple">&#9835; Apple Music</a>
        </div>
        <div class="footer-copy">© 2026 Margonoandi · Semua lagu dilindungi hak cipta</div>
    </div>
</footer>

{{-- BOTTOM NAV (mobile) — tersembunyi di landing page --}}
@if(!request()->routeIs('home'))
<nav class="bottom-nav" aria-label="Navigasi mobile">
    <div class="bottom-nav-inner">
        <a href="{{ route('home') }}"
           class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <span class="bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span class="bnav-label">Beranda</span>
        </a>
        @auth
        <a href="{{ route('community.index') }}"
           class="bottom-nav-item {{ request()->routeIs('community.*') ? 'active' : '' }}">
            <span class="bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="bnav-label">Komunitas</span>
        </a>
        <a href="{{ route('chat.index') }}"
           class="bottom-nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <span class="bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span class="bnav-label">Chat</span>
        </a>
        <a href="{{ route('profile') }}"
           class="bottom-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <span class="bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="bnav-label">Profil</span>
        </a>
        @else
        <a href="{{ route('google.login') }}" class="bottom-nav-item">
            <span class="bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            </span>
            <span class="bnav-label">Masuk</span>
        </a>
        @endauth
    </div>
</nav>
@endif

{{-- Music Player --}}
@if(request()->routeIs('community.*') || request()->routeIs('chat.*') || request()->routeIs('profile'))
    @include('partials.music-player')
@endif

@stack('scripts')

<script>
(function () {
    var KEY = 'margono-theme';
    var html   = document.documentElement;
    var toggle = document.getElementById('themeToggle');
    var nav    = document.getElementById('topNav');

    /* apply saved theme immediately (before paint) */
    var saved = localStorage.getItem(KEY) || 'dark';
    html.setAttribute('data-theme', saved);
    syncIcon(saved);

    toggle.addEventListener('click', function () {
        var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem(KEY, next);
        syncIcon(next);
    });

    function syncIcon(theme) {
        toggle.textContent    = theme === 'dark' ? '☀️' : '🌙';
        toggle.title          = theme === 'dark' ? 'Mode terang' : 'Mode gelap';
        toggle.setAttribute('aria-label', toggle.title);
    }

    /* scroll-aware navbar border */
    window.addEventListener('scroll', function () {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });

    /* cursor glow */
    var glowEl = document.getElementById('cursorLight');
    if (glowEl && !window.matchMedia('(hover: none)').matches) {
        var raf = null, cx = -9999, cy = -9999;
        document.addEventListener('mousemove', function (e) {
            cx = e.clientX; cy = e.clientY;
            if (!raf) raf = requestAnimationFrame(function () {
                glowEl.style.left = cx + 'px';
                glowEl.style.top  = cy + 'px';
                raf = null;
            });
        });
        document.addEventListener('mouseleave', function () { glowEl.style.opacity = '0'; });
        document.addEventListener('mouseenter', function () { glowEl.style.opacity = '1'; });
    }
})();
</script>
</body>
</html>
