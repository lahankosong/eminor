<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') · Margonoandi</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        /* ===== TOKENS (same as fanbase) ===== */
        :root {
            --sky:        #38A8CC;
            --sky-lt:     #E6F4FA;
            --sky-dk:     #1E7FA8;
            --sky-mid:    #6FC5E0;
            --sky-glow:   rgba(56,168,204,0.18);
            --cream:      #FDF8F3;
            --cream-dk:   #F5EDE0;
            --orange:     #F07040;
            --orange-lt:  #FFF0E8;
            --orange-dk:  #C84E20;
            --text-1:     #162030;
            --text-2:     #3A5468;
            --text-3:     #7A9DB0;
            --text-4:     #B0C8D4;
            --border:     #D8EAF2;
            --border-lt:  #EBF5F9;
            --surface:    #EEF7FB;
            --card:       #FFFFFF;
            --shadow-sm:  0 1px 4px rgba(56,168,204,0.08);
            --shadow:     0 4px 16px rgba(56,168,204,0.12);
            --shadow-lg:  0 8px 32px rgba(56,168,204,0.18);
            --radius-sm:  10px;
            --radius:     16px;

            /* Alias token untuk halaman AI (ai-agent, audio-cut, ai-settings, calendar)
               — agar background solid, tidak transparan */
            --bg:         #FFFFFF;
            --bg-2:       #FFFFFF;
            --bg-3:       #EEF7FB;
            --text:       #162030;
            --border-2:   #EBF5F9;
            --accent:     #1E7FA8;
            --accent-dim: #E6F4FA;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            font-family:'DM Sans', sans-serif;
            background:var(--cream);
            color:var(--text-1);
            min-height:100vh;
        }

        /* ===== TOPBAR ===== */
        .adm-topbar {
            position:fixed; top:0; left:0; right:0; height:56px;
            background:linear-gradient(135deg, rgba(22,32,48,0.97) 0%, rgba(30,127,168,0.92) 100%);
            backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(255,255,255,0.1);
            z-index:200;
            display:flex; align-items:center; padding:0 1.25rem; gap:12px;
            box-shadow:0 2px 20px rgba(0,0,0,0.2);
        }
        .adm-brand {
            font-family:'Sora',sans-serif;
            font-size:12px; font-weight:700; letter-spacing:0.15em;
            color:#fff; text-decoration:none; flex-shrink:0;
        }
        .adm-brand span {
            background:var(--sky); color:#fff;
            font-size:9px; font-weight:600; letter-spacing:0.1em;
            padding:2px 6px; border-radius:4px; margin-left:6px;
            vertical-align:middle;
        }
        .adm-topbar-right { margin-left:auto; display:flex; align-items:center; gap:8px; }
        .adm-back {
            display:flex; align-items:center; gap:5px;
            color:rgba(255,255,255,0.6); text-decoration:none;
            font-size:12px; padding:5px 12px;
            border:1px solid rgba(255,255,255,0.15); border-radius:20px;
            transition:0.2s;
        }
        .adm-back:hover { color:#fff; border-color:rgba(255,255,255,0.35); background:rgba(255,255,255,0.08); }
        .adm-avatar {
            width:30px; height:30px; border-radius:50%;
            object-fit:cover; border:2px solid rgba(255,255,255,0.3);
        }
        .adm-logout {
            color:rgba(255,255,255,0.5); text-decoration:none;
            font-size:11px; padding:4px 10px;
            border:1px solid rgba(255,255,255,0.12); border-radius:16px; transition:0.2s;
        }
        .adm-logout:hover { color:rgba(255,255,255,0.85); border-color:rgba(255,255,255,0.3); }
        .adm-avatar { cursor:pointer; transition:0.2s; }
        .adm-avatar:hover { border-color:#fff; transform:scale(1.05); }

        /* Menu akun admin (foto profil → Profil / Fanbase / Keluar) */
        .adm-profile-menu { position:relative; }
        .adm-profile-dropdown {
            display:none; position:absolute; top:calc(100% + 10px); right:0;
            width:230px; background:#fff; border:1px solid var(--border);
            border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.25);
            z-index:300; overflow:hidden;
        }
        .adm-profile-dropdown.open { display:block; }
        .adm-profile-dd-head {
            display:flex; align-items:center; gap:10px; padding:12px 14px;
            border-bottom:1px solid var(--border);
        }
        .adm-profile-dd-head img { width:38px; height:38px; border-radius:50%; object-fit:cover; flex-shrink:0; }
        .adm-profile-dd-name { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .adm-profile-dd-email { font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .adm-profile-dd-item {
            display:block; padding:11px 14px; font-size:13px;
            color:var(--text); text-decoration:none; transition:0.15s;
        }
        .adm-profile-dd-item:hover { background:var(--bg-3); }
        .adm-profile-dd-item.danger { color:#e11d48; border-top:1px solid var(--border); }
        .adm-profile-dd-item.danger:hover { background:#fef2f2; }

        /* ===== LAYOUT ===== */
        .adm-layout {
            display:grid;
            grid-template-columns:210px 1fr;
            max-width:1120px;
            margin:0 auto;
            padding-top:56px;
            min-height:100vh;
        }

        /* ===== LEFT SIDEBAR ===== */
        .adm-sidebar {
            position:sticky; top:56px; height:calc(100vh - 56px);
            overflow-y:auto; overflow-x:hidden;
            border-right:1px solid var(--border-lt);
            padding:1.25rem 0.875rem;
            scrollbar-width:none;
        }
        .adm-sidebar::-webkit-scrollbar { display:none; }

        .adm-sidebar-label {
            font-size:9px; font-weight:600; letter-spacing:0.12em;
            color:var(--text-4); text-transform:uppercase;
            padding:0 10px; margin-bottom:6px; margin-top:1rem;
        }
        .adm-sidebar-label:first-child { margin-top:0; }

        .adm-nav { display:flex; flex-direction:column; gap:2px; }
        .adm-nav-item {
            display:flex; align-items:center; gap:9px;
            padding:9px 10px; border-radius:var(--radius-sm);
            text-decoration:none; color:var(--text-2);
            font-size:13px; font-weight:500; transition:all 0.18s;
        }
        .adm-nav-item:hover { background:var(--sky-lt); color:var(--sky-dk); }
        .adm-nav-item.active {
            background:var(--sky-lt); color:var(--sky-dk); font-weight:600;
            box-shadow:inset 3px 0 0 var(--sky);
        }
        .adm-nav-item .adm-nav-icon {
            width:18px; height:18px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-size:14px; line-height:1;
        }
        .adm-nav-divider { height:1px; background:var(--border-lt); margin:10px 0; }
        .adm-nav-item.adm-nav-back { color:var(--text-3); font-size:12px; }
        .adm-nav-item.adm-nav-back:hover { color:var(--text-2); background:var(--surface); }

        /* ===== MAIN CONTENT ===== */
        .adm-main {
            padding:1.5rem 1.5rem 4rem;
            min-width:0;
        }

        /* ===== MOBILE NAV ===== */
        .adm-mobile-nav {
            display:none;
            position:fixed; bottom:0; left:0; right:0;
            background:#fff; border-top:1px solid var(--border);
            z-index:150; padding:8px 0 max(8px, env(safe-area-inset-bottom));
        }
        .adm-mobile-nav-inner {
            display:flex; justify-content:space-around;
        }
        .adm-mnav-item {
            display:flex; flex-direction:column; align-items:center; gap:3px;
            text-decoration:none; color:var(--text-3);
            font-size:10px; padding:4px 8px; border-radius:8px; transition:0.15s;
            min-width:48px;
        }
        .adm-mnav-item.active, .adm-mnav-item:hover { color:var(--sky-dk); }
        .adm-mnav-icon { font-size:18px; line-height:1; }

        @media (max-width:768px) {
            .adm-layout { grid-template-columns:1fr; }
            .adm-sidebar { display:none; }
            .adm-main { padding:1rem 1rem 5rem; }
            .adm-mobile-nav { display:block; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- TOPBAR -->
<header class="adm-topbar">
    <a href="{{ route('admin.index') }}" class="adm-brand">
        MARGONOANDI <span>ADMIN</span>
    </a>
    <div class="adm-topbar-right">
        <a href="{{ route('aku') }}" class="adm-back">← Fanbase</a>
        <div class="adm-profile-menu">
            <img class="adm-avatar" id="admProfileBtn" role="button" tabindex="0" title="Menu akun"
                 src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}"
                 alt="{{ Auth::user()->name }}"
                 onerror="this.src='{{ asset('images/default-avatar.png') }}'">
            <div class="adm-profile-dropdown" id="admProfileDropdown">
                <div class="adm-profile-dd-head">
                    <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" alt=""
                         onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                    <div style="min-width:0;">
                        <div class="adm-profile-dd-name">{{ Auth::user()->name }}</div>
                        <div class="adm-profile-dd-email">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <a href="{{ route('profile') }}" class="adm-profile-dd-item">👤 Profil</a>
                <a href="{{ route('aku') }}" class="adm-profile-dd-item">← Kembali ke Fanbase</a>
                <a href="{{ route('logout') }}" class="adm-profile-dd-item danger">🚪 Keluar</a>
            </div>
        </div>
    </div>
</header>

<script>
(function(){
    var b = document.getElementById('admProfileBtn');
    var d = document.getElementById('admProfileDropdown');
    if (!b || !d) return;
    b.addEventListener('click', function(e){ e.stopPropagation(); d.classList.toggle('open'); });
    document.addEventListener('click', function(e){ if (!d.contains(e.target) && e.target !== b) d.classList.remove('open'); });
})();
</script>

<!-- LAYOUT -->
<div class="adm-layout">

    <!-- LEFT SIDEBAR -->
    <aside class="adm-sidebar">
        <div class="adm-sidebar-label">Utama</div>
        <nav class="adm-nav">
            <a href="{{ route('admin.index') }}"
               class="adm-nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <span class="adm-nav-icon">📊</span> Dashboard
            </a>
            <a href="{{ route('admin.index') }}#songs"
               class="adm-nav-item {{ request()->routeIs('admin.index') && request()->has('tab') == 'songs' ? 'active' : '' }}">
                <span class="adm-nav-icon">🎵</span> Kelola Lagu
            </a>
        </nav>

        <div class="adm-sidebar-label">Konten</div>
        <nav class="adm-nav">
            <a href="{{ route('admin.ai-agent') }}"
               class="adm-nav-item {{ request()->routeIs('admin.ai-agent') ? 'active' : '' }}">
                <span class="adm-nav-icon">✨</span> AI Agent
            </a>
            <a href="{{ route('admin.audio-cut') }}"
               class="adm-nav-item {{ request()->routeIs('admin.audio-cut') ? 'active' : '' }}">
                <span class="adm-nav-icon">✂️</span> Pemotong Lagu
            </a>
            <a href="{{ route('admin.video-builder') }}"
               class="adm-nav-item {{ request()->routeIs('admin.video-builder') ? 'active' : '' }}">
                <span class="adm-nav-icon">🎬</span> Video Builder
            </a>
            <a href="{{ route('admin.calendar') }}"
               class="adm-nav-item {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                <span class="adm-nav-icon">📅</span> Kalender
            </a>
            <a href="{{ route('admin.promo') }}"
               class="adm-nav-item {{ request()->routeIs('admin.promo') ? 'active' : '' }}">
                <span class="adm-nav-icon">📋</span> Promo
            </a>
        </nav>

        <div class="adm-sidebar-label">Sistem</div>
        <nav class="adm-nav">
            <a href="{{ route('admin.settings') }}"
               class="adm-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <span class="adm-nav-icon">⚙️</span> Pengaturan
            </a>
            <a href="{{ route('admin.create') }}"
               class="adm-nav-item {{ request()->routeIs('admin.create') ? 'active' : '' }}">
                <span class="adm-nav-icon">➕</span> Tambah Lagu
            </a>
        </nav>

        <div class="adm-nav-divider"></div>
        <nav class="adm-nav">
            <a href="{{ route('aku') }}" class="adm-nav-item adm-nav-back">
                <span class="adm-nav-icon">←</span> Kembali ke Fanbase
            </a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="adm-main">
        @yield('content')
    </main>
</div>

<!-- MOBILE BOTTOM NAV -->
<nav class="adm-mobile-nav">
    <div class="adm-mobile-nav-inner">
        <a href="{{ route('admin.index') }}"
           class="adm-mnav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <span class="adm-mnav-icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.index') }}#songs"
           class="adm-mnav-item">
            <span class="adm-mnav-icon">🎵</span>
            <span>Lagu</span>
        </a>
        <a href="{{ route('admin.ai-agent') }}"
           class="adm-mnav-item {{ request()->routeIs('admin.ai-agent') ? 'active' : '' }}">
            <span class="adm-mnav-icon">✨</span>
            <span>AI Agent</span>
        </a>
        <a href="{{ route('admin.calendar') }}"
           class="adm-mnav-item {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
            <span class="adm-mnav-icon">📅</span>
            <span>Kalender</span>
        </a>
        <a href="{{ route('admin.settings') }}"
           class="adm-mnav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <span class="adm-mnav-icon">⚙️</span>
            <span>Setting</span>
        </a>
    </div>
</nav>

@stack('scripts')
</body>
</html>
