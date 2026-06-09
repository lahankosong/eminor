<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Margonoandi — Official Fanbase</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a; color: #fff;
            min-height: 100vh;
        }

        /* ===== TOP NAV ===== */
        nav.top-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem; background: rgba(0,0,0,0.85);
            backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 200;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .nav-brand {
            font-size: 1.1rem; font-weight: 600; letter-spacing: 0.12em;
            color: #fff; text-decoration: none; flex-shrink: 0;
        }
        .nav-center {
            display: flex; align-items: center; gap: 4px;
        }
        .nav-link {
            font-size: 12px; color: #555; text-decoration: none;
            padding: 6px 14px; border-radius: 20px; transition: 0.15s;
            letter-spacing: 0.05em;
        }
        .nav-link:hover  { color: #fff; background: #111; }
        .nav-link.active { color: #fff; }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .btn-login {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 18px; border-radius: 50px;
            background: #fff; color: #000; font-size: 13px;
            font-weight: 500; text-decoration: none; transition: 0.2s;
        }
        .btn-login:hover { background: #e0e0e0; }
        .btn-login img { width: 16px; height: 16px; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            object-fit: cover; border: 1px solid #2a2a2a;
        }
        .user-name { font-size: 13px; color: #888; }
        .btn-logout {
            font-size: 12px; color: #555; text-decoration: none;
            padding: 4px 12px; border: 1px solid #1a1a1a; border-radius: 20px;
            transition: 0.15s;
        }
        .btn-logout:hover { color: #fff; border-color: #555; }

        /* ===== MAIN ===== */
        main {
            max-width: 900px; margin: 0 auto;
            padding: 2rem 2rem 5rem;
        }

        /* ===== FOOTER ===== */
        footer {
            text-align: center; padding: 2rem;
            border-top: 1px solid #111;
            color: #444; font-size: 12px;
            margin-top: 2rem; margin-bottom: 60px;
        }
        footer a { text-decoration: none; transition: 0.15s; }
        footer a:hover { opacity: 0.7; }

        /* ===== BOTTOM NAV MOBILE ===== */
        .bottom-nav {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(6,6,6,0.98);
            backdrop-filter: blur(20px);
            border-top: 1px solid #1a1a1a;
            z-index: 300;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .bottom-nav-inner {
            display: flex; align-items: stretch; height: 58px;
        }
        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px; text-decoration: none; color: #2a2a2a;
            transition: color 0.2s; position: relative;
            background: transparent; border: none;
            font-family: inherit; cursor: pointer;
            padding: 0;
        }
        .bottom-nav-item:hover  { color: #666; }
        .bottom-nav-item.active { color: #fff; }
        .bnav-icon  { font-size: 19px; line-height: 1; }
        .bnav-label { font-size: 9px; letter-spacing: 0.03em; font-weight: 400; }

        /* Active dot */
        .bottom-nav-item.active::after {
            content: ''; position: absolute; bottom: 5px;
            width: 3px; height: 3px; border-radius: 50%; background: #fff;
        }

        /* Alert */
        .alert-session {
            background: #2e0d0d; color: #f87171;
            border: 1px solid #991b1b;
            padding: 10px 16px; border-radius: 8px;
            margin-bottom: 1rem; font-size: 13px;
        }

        /* ===== MOBILE BREAKPOINT ===== */
        @media (max-width: 768px) {
            .bottom-nav    { display: block; }
            .nav-center    { display: none; }
            .nav-brand     { font-size: 0.95rem; }
            .user-name     { display: none; }
            nav.top-nav    { padding: 0.75rem 1rem; }
            main           { padding: 0 0 5rem; }
            footer         { padding: 1.5rem 1rem; margin-bottom: 60px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- TOP NAV --}}
<nav class="top-nav">
    <a href="{{ route('home') }}" class="nav-brand">MARGONOANDI</a>

    <div class="nav-center">
        <a href="{{ route('home') }}"
           class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        @auth
        <a href="{{ route('community.index') }}"
           class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}">Komunitas</a>
        <a href="{{ route('chat.index') }}"
           class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">Chat</a>
        @endauth
        @if(Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS', ''))))
        <a href="{{ route('admin.index') }}"
           class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
        @endif
    </div>

    <div class="nav-right">
        @auth
        <div class="user-info">
            <img src="{{ Auth::user()->avatar }}" alt="" class="user-avatar">
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

<main>
    @if(session('error'))
    <div class="alert-session">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<footer>
    <p>© 2026 Margonoandi. Semua lagu dilindungi hak cipta.</p>
    <p style="margin-top:6px;">
        <a href="https://open.spotify.com/playlist/1lpXuXUd3wMbwWe0stM0dD" style="color:#1DB954;">Spotify</a>
        &nbsp;·&nbsp;
        <a href="https://www.youtube.com/channel/UCBTFgn31i3auH29qm81lDKA" style="color:#FF0000;">YouTube</a>
        &nbsp;·&nbsp;
        <a href="https://music.apple.com/us/artist/margonoandi/1850375782" style="color:#fc3c44;">Apple Music</a>
    </p>
</footer>



{{-- Music Player (komunitas & chat) --}}
@if(request()->routeIs('community.*') || request()->routeIs('chat.*') || request()->routeIs('profile'))
    @include('partials.music-player')
@endif

@stack('scripts')
</body>
</html>