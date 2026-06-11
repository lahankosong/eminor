<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#38A8CC">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Margonoandi">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/Margonoandi.jpeg">
    <title>Margonoandi — @yield('title', 'Fanbase')</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        /* ===== DESIGN TOKENS ===== */
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
            --orange-glow:rgba(240,112,64,0.2);
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
            --shadow-xl:  0 16px 48px rgba(56,168,204,0.22);
            --radius-sm:  10px;
            --radius:     16px;
            --radius-lg:  24px;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        html { scroll-behavior:smooth; }

        body {
            font-family:'DM Sans', sans-serif;
            background:var(--cream);
            color:var(--text-1);
            min-height:100vh;
            overflow-x:hidden;
        }

        /* ===== AMBIENT BACKGROUND ===== */
        .fb-bg-orb {
            position:fixed; pointer-events:none; z-index:0;
            border-radius:50%; filter:blur(80px);
        }
        .fb-bg-orb-1 {
            width:500px; height:500px;
            top:-150px; left:-100px;
            background:radial-gradient(circle, rgba(56,168,204,0.12) 0%, transparent 70%);
        }
        .fb-bg-orb-2 {
            width:400px; height:400px;
            bottom:-100px; right:-80px;
            background:radial-gradient(circle, rgba(240,112,64,0.08) 0%, transparent 70%);
        }
        .fb-bg-orb-3 {
            width:300px; height:300px;
            top:40%; left:30%;
            background:radial-gradient(circle, rgba(56,168,204,0.06) 0%, transparent 70%);
        }

        /* ===== TOP BAR ===== */
        .fb-topbar {
            position:fixed; top:0; left:0; right:0; height:56px;
            background:linear-gradient(135deg, rgba(30,127,168,0.92) 0%, rgba(56,168,204,0.88) 100%);
            backdrop-filter:blur(24px) saturate(200%);
            -webkit-backdrop-filter:blur(24px) saturate(200%);
            border-bottom:1px solid rgba(255,255,255,0.2);
            z-index:200;
            display:flex; align-items:center; padding:0 1.5rem; gap:14px;
            box-shadow:0 2px 20px rgba(30,127,168,0.25);
        }
        .fb-brand {
            font-family:'Sora',sans-serif;
            font-size:13px; font-weight:700; letter-spacing:0.14em;
            color:#fff; text-decoration:none; flex-shrink:0;
            text-shadow:0 1px 4px rgba(0,0,0,0.15);
        }
        .fb-brand span { font-weight:300; opacity:0.65; font-size:11px; letter-spacing:0.1em; }
        .fb-search-wrap { flex:1; max-width:280px; position:relative; }
        .fb-search {
            width:100%;
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.25);
            border-radius:24px; color:#fff; font-size:12px;
            padding:7px 14px 7px 32px; outline:none;
            font-family:'DM Sans',sans-serif; transition:0.25s;
            letter-spacing:0.02em;
        }
        .fb-search::placeholder { color:rgba(255,255,255,0.45); }
        .fb-search:focus {
            background:rgba(255,255,255,0.22);
            border-color:rgba(255,255,255,0.45);
            box-shadow:0 0 0 3px rgba(255,255,255,0.1);
        }
        .fb-search-icon {
            position:absolute; left:11px; top:50%; transform:translateY(-50%);
            font-size:11px; color:rgba(255,255,255,0.4); pointer-events:none;
        }
        .fb-topbar-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
        .fb-notif-btn {
            width:34px; height:34px; border-radius:50%;
            background:rgba(255,255,255,0.12);
            border:1px solid rgba(255,255,255,0.2);
            color:#fff; font-size:15px; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:0.2s;
        }
        .fb-notif-btn:hover { background:rgba(255,255,255,0.22); }
        .fb-notif-wrap { position:relative; }
        .fb-notif-badge {
            position:absolute; top:-3px; right:-3px;
            min-width:16px; height:16px; border-radius:8px;
            background:#f43f5e; color:#fff; font-size:9px; font-weight:700;
            display:flex; align-items:center; justify-content:center; padding:0 3px;
            border:2px solid rgba(30,127,168,0.9);
            line-height:1; pointer-events:none;
        }
        .fb-notif-dropdown {
            display:none; position:absolute; top:calc(100% + 10px); right:0;
            width:320px; max-height:420px; overflow-y:auto;
            background:#fff; border:1px solid var(--border);
            border-radius:var(--radius); box-shadow:var(--shadow-lg);
            z-index:1000;
        }
        .fb-notif-dropdown.open { display:flex; flex-direction:column; }
        .fb-notif-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:12px 16px 10px; border-bottom:1px solid var(--border-lt);
            font-family:'Sora',sans-serif; font-size:12px; font-weight:600; color:var(--text-1);
            position:sticky; top:0; background:#fff; z-index:1;
        }
        .fb-notif-readall {
            font-size:10px; color:var(--sky-dk); cursor:pointer; background:none; border:none;
            font-family:inherit; padding:0; font-weight:500;
        }
        .fb-notif-readall:hover { text-decoration:underline; }
        .fb-notif-item {
            display:flex; gap:10px; padding:10px 16px;
            border-bottom:1px solid var(--border-lt); cursor:pointer; transition:0.15s;
        }
        .fb-notif-item:hover { background:var(--sky-lt); }
        .fb-notif-item.unread { background:rgba(56,168,204,0.05); }
        .fb-notif-item.unread:hover { background:var(--sky-lt); }
        .fb-notif-avatar { width:34px; height:34px; border-radius:50%; object-fit:cover; flex-shrink:0; }
        .fb-notif-body { flex:1; min-width:0; }
        .fb-notif-title { font-size:12px; font-weight:600; color:var(--text-1); margin-bottom:2px; line-height:1.3; }
        .fb-notif-msg { font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .fb-notif-time { font-size:10px; color:var(--text-4); margin-top:3px; }
        .fb-notif-empty { text-align:center; padding:2rem 1rem; font-size:12px; color:var(--text-4); }
        @media(max-width:480px){
            .fb-notif-dropdown { width:290px; right:-40px; }
        }
        .fb-avatar {
            width:32px; height:32px; border-radius:50%;
            object-fit:cover;
            border:2px solid rgba(255,255,255,0.5);
            box-shadow:0 2px 8px rgba(0,0,0,0.15);
            cursor:pointer; transition:0.2s;
        }
        .fb-avatar:hover { border-color:#fff; transform:scale(1.05); }
        .fb-logout {
            display:flex; align-items:center; gap:6px;
            color:rgba(255,255,255,0.7); text-decoration:none;
            padding:5px 14px; border:1px solid rgba(255,255,255,0.25);
            border-radius:20px; transition:0.2s; font-weight:500;
        }
        .fb-logout:hover { color:#fff; border-color:rgba(255,255,255,0.5); background:rgba(255,255,255,0.1); }
        .fb-logout-text { font-size:11px; }
        .fb-logout svg { flex-shrink:0; }

        /* ===== LAYOUT GRID ===== */
        .fb-layout {
            display:grid;
            grid-template-columns:220px 1fr 200px;
            max-width:1140px;
            margin:0 auto;
            padding-top:56px;
            min-height:100vh;
            position:relative; z-index:1;
        }

        /* ===== LEFT SIDEBAR ===== */
        .fb-sidebar-left {
            position:sticky; top:56px; height:calc(100vh - 56px);
            overflow:hidden;
            border-right:1px solid var(--border-lt);
            display:flex; flex-direction:column;
        }
        .fb-sidebar-scroll {
            flex:1; overflow-y:auto; overflow-x:hidden;
            padding:1.5rem 1rem;
            scrollbar-width:none;
        }
        .fb-sidebar-scroll::-webkit-scrollbar { display:none; }

        /* Desktop mini player */
        .fb-desk-player {
            flex-shrink:0;
            padding:8px 12px 10px;
            border-top:1px solid var(--border-lt);
            background:var(--card);
        }
        .fb-dp-top { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
        .fb-dp-thumb { width:32px; height:32px; border-radius:6px; object-fit:cover; background:var(--surface); flex-shrink:0; box-shadow:var(--shadow-sm); }
        .fb-dp-info { flex:1; min-width:0; }
        .fb-dp-title { font-size:11px; font-weight:600; color:var(--text-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .fb-dp-era   { font-size:10px; color:var(--text-4); }
        .fb-dp-top { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .fb-dp-controls { display:flex; justify-content:center; align-items:center; gap:6px; margin-bottom:8px; }
        .fb-dp-btn {
            background:transparent; border:none; cursor:pointer;
            color:var(--text-3); padding:6px; border-radius:8px;
            line-height:0; display:flex; align-items:center; justify-content:center;
            transition:all 0.18s;
        }
        .fb-dp-btn:hover { background:var(--sky-lt); color:var(--sky-dk); }
        .fb-dp-play {
            width:36px; height:36px; border-radius:50%; padding:0;
            background:linear-gradient(145deg, var(--sky) 0%, var(--sky-dk) 100%);
            color:#fff; box-shadow:0 3px 10px var(--sky-glow);
        }
        .fb-dp-play:hover { transform:scale(1.08); box-shadow:0 4px 14px var(--sky-glow); background:linear-gradient(145deg, var(--sky-dk) 0%, #1a6f8f 100%); }
        .fb-dp-play.playing { background:linear-gradient(145deg, var(--orange) 0%, var(--orange-dk) 100%); box-shadow:0 3px 10px var(--orange-glow); }
        .fb-dp-play.playing:hover { box-shadow:0 4px 14px var(--orange-glow); }
        .fb-dp-stop {
            width:26px; height:26px; border-radius:6px; padding:0; flex-shrink:0;
            background:var(--surface); border:1px solid var(--border);
            color:var(--text-3);
        }
        .fb-dp-stop:hover { background:#fef2f2; color:#ef4444; border-color:#fecaca; }
        .fb-dp-bar { height:4px; background:var(--border-lt); border-radius:4px; cursor:pointer; position:relative; overflow:hidden; margin-top:4px; }
        .fb-dp-bar:hover .fb-dp-fill { filter:brightness(1.15); }
        .fb-dp-fill { height:100%; background:linear-gradient(90deg,var(--sky),var(--sky-dk)); border-radius:4px; pointer-events:none; transition:width 0.25s linear; }

        /* Profile card */
        .fb-profile-card {
            background:linear-gradient(145deg, var(--sky-lt) 0%, #fff 60%, var(--cream) 100%);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:1.5rem 1rem;
            text-align:center;
            margin-bottom:1.25rem;
            box-shadow:var(--shadow-sm);
            position:relative; overflow:hidden;
        }
        .fb-profile-card::before {
            content:'';
            position:absolute; top:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg, var(--sky), var(--orange));
        }
        .fb-profile-avatar {
            width:58px; height:58px; border-radius:50%;
            object-fit:cover; margin-bottom:10px;
            border:3px solid #fff;
            box-shadow:0 0 0 3px var(--sky), var(--shadow);
        }
        .fb-profile-name {
            font-family:'Sora',sans-serif;
            font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:3px;
        }
        .fb-profile-sub { font-size:11px; color:var(--text-3); letter-spacing:0.03em; }

        /* Nav */
        .fb-nav { display:flex; flex-direction:column; gap:3px; }
        .fb-nav-item {
            display:flex; align-items:center; gap:10px;
            padding:10px 14px; border-radius:var(--radius-sm);
            text-decoration:none; color:var(--text-2);
            font-size:13px; font-weight:500;
            transition:all 0.2s; cursor:pointer;
            background:transparent; border:none;
            font-family:'DM Sans',sans-serif; width:100%; text-align:left;
        }
        .fb-nav-item:hover {
            background:var(--sky-lt); color:var(--sky-dk);
            transform:translateX(2px);
        }
        .fb-nav-item.active {
            background:linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
            color:#fff;
            box-shadow:0 4px 14px var(--sky-glow);
        }
        .fb-nav-item.active .fb-nav-icon { filter:drop-shadow(0 0 4px rgba(255,255,255,0.5)); }
        .fb-nav-icon { font-size:17px; width:22px; text-align:center; flex-shrink:0; }
        .fb-nav-divider { height:1px; background:var(--border-lt); margin:8px 4px; }
        .fb-sidebar-section {
            font-size:9px; color:var(--text-4); letter-spacing:0.2em;
            text-transform:uppercase; padding:0 14px; margin:14px 0 6px;
            font-weight:600;
        }

        /* Song items sidebar */
        .fb-song-item {
            display:flex; align-items:center; gap:8px;
            padding:7px 14px; border-radius:var(--radius-sm);
            cursor:pointer; transition:0.18s;
        }
        .fb-song-item:hover { background:var(--sky-lt); }
        .fb-song-item.playing {
            background:var(--sky-lt);
            border-left:2px solid var(--sky);
        }
        .fb-song-thumb {
            width:30px; height:30px; border-radius:6px;
            object-fit:cover; background:var(--surface); flex-shrink:0;
            box-shadow:var(--shadow-sm);
        }
        .fb-song-info { flex:1; min-width:0; }
        .fb-song-title { font-size:11px; color:var(--text-2); font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .fb-song-era   { font-size:10px; color:var(--text-4); }
        .fb-song-item.playing .fb-song-title { color:var(--sky-dk); }

        /* ===== MAIN ===== */
        .fb-main {
            min-width:0; padding:1.5rem;
            background:transparent;
        }

        /* ===== RIGHT SIDEBAR ===== */
        .fb-sidebar-right {
            position:sticky; top:56px; height:calc(100vh - 56px);
            overflow-y:auto; padding:1.5rem 1rem;
            scrollbar-width:none;
            border-left:1px solid var(--border-lt);
        }
        .fb-sidebar-right::-webkit-scrollbar { display:none; }

        .fb-widget {
            background:var(--card);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:1rem; margin-bottom:1rem;
            box-shadow:var(--shadow-sm);
        }
        .fb-widget-title {
            font-size:9px; color:var(--text-3); letter-spacing:0.2em;
            text-transform:uppercase; margin-bottom:0.875rem;
            padding-bottom:0.5rem; border-bottom:1px solid var(--border-lt);
            font-weight:700;
        }
        .fb-online-item {
            display:flex; align-items:center; gap:9px;
            padding:6px 4px; cursor:pointer; transition:0.15s;
            border-radius:10px;
        }
        .fb-online-item:hover { background:var(--sky-lt); padding-left:8px; }
        .fb-online-avatar { position:relative; flex-shrink:0; }
        .fb-online-avatar img { width:30px; height:30px; border-radius:50%; object-fit:cover; border:1px solid var(--border); }
        .fb-online-dot {
            position:absolute; bottom:0; right:0;
            width:9px; height:9px; border-radius:50%;
            background:#d1d5db; border:2px solid var(--card);
        }
        .fb-online-dot.online { background:#22c55e; }
        .fb-online-info { flex:1; min-width:0; }
        .fb-online-name { font-size:12px; color:var(--text-2); font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .fb-online-status { font-size:10px; color:var(--text-4); margin-top:1px; }
        .fb-online-status.online { color:#16a34a; }

        /* Member search widget (right sidebar desktop) */
        .fb-msearch-input {
            width:100%; background:var(--surface);
            border:1px solid var(--border); border-radius:10px;
            color:var(--text-1); font-size:11px; padding:7px 10px 7px 28px;
            outline:none; font-family:'DM Sans',sans-serif; transition:0.2s;
        }
        .fb-msearch-input:focus { border-color:var(--sky); box-shadow:0 0 0 2px var(--sky-glow); }
        .fb-msearch-input::placeholder { color:var(--text-4); }
        .fb-msearch-wrap { position:relative; margin-bottom:6px; }
        .fb-msearch-icon {
            position:absolute; left:8px; top:50%; transform:translateY(-50%);
            color:var(--text-4); pointer-events:none; line-height:0;
        }
        .fb-msearch-results { min-height:0; }
        .fb-msearch-empty { font-size:11px; color:var(--text-4); padding:6px 4px; text-align:center; }

        /* ===== GLOBAL ALERT ===== */
        .fb-alert { padding:10px 16px; border-radius:var(--radius-sm); margin-bottom:1rem; font-size:13px; }
        .fb-alert-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
        .fb-alert-error   { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

        /* ===== BOTTOM NAV MOBILE — FUTURISTIC FLOATING ===== */
        .fb-bottom-nav {
            display:none;
            position:fixed; bottom:0; left:0; right:0;
            z-index:400;
            padding:8px 16px;
            padding-bottom:calc(10px + env(safe-area-inset-bottom, 0px));
        }
        .fb-bottom-inner {
            display:flex; align-items:center;
            background:rgba(22,32,48,0.7);
            backdrop-filter:blur(32px) saturate(180%);
            -webkit-backdrop-filter:blur(32px) saturate(180%);
            border:1px solid rgba(255,255,255,0.18);
            border-radius:28px;
            height:66px;
            padding:0 10px;
            box-shadow:
                0 8px 32px rgba(30,127,168,0.3),
                0 2px 8px rgba(0,0,0,0.15),
                inset 0 1px 0 rgba(255,255,255,0.15),
                inset 0 -1px 0 rgba(0,0,0,0.1);
        }
        .fb-bnav-item {
            flex:1; display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            gap:4px; text-decoration:none;
            color:rgba(255,255,255,0.35);
            transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            background:transparent; border:none;
            font-family:'DM Sans',sans-serif; cursor:pointer;
            position:relative; padding:6px 4px;
            border-radius:18px;
        }
        .fb-bnav-item:hover { color:rgba(255,255,255,0.7); }
        .fb-bnav-item.active {
            color:#fff;
            background:rgba(255,255,255,0.1);
        }
        .fb-bnav-item.active .fb-bnav-icon { transform:translateY(-3px) scale(1.15); }
        .fb-bnav-icon { font-size:21px; line-height:1; transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1); }
        .fb-bnav-label { font-size:9px; letter-spacing:0.04em; font-weight:500; transition:0.3s; }

        /* Glowing active line */
        .fb-bnav-item.active::after {
            content:'';
            position:absolute; bottom:4px; left:50%; transform:translateX(-50%);
            width:20px; height:2px; border-radius:2px;
            background:linear-gradient(90deg, var(--sky-mid), rgba(255,255,255,0.8), var(--sky-mid));
            box-shadow:0 0 10px rgba(126,200,227,0.9), 0 0 20px rgba(126,200,227,0.4);
        }

        /* PLAY button — hero of the nav */
        .fb-bnav-play {
            flex:0 0 70px; display:flex; align-items:center; justify-content:center;
            background:transparent; border:none; cursor:pointer; padding:0; margin:0 4px;
        }
        .fb-bnav-play-btn {
            width:54px; height:54px; border-radius:50%;
            background:linear-gradient(145deg, var(--orange) 0%, #E05028 50%, var(--orange-dk) 100%);
            color:#fff; border:none; font-size:19px; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            box-shadow:
                0 6px 20px var(--orange-glow),
                0 2px 8px rgba(0,0,0,0.2),
                0 0 0 3px rgba(255,255,255,0.12),
                inset 0 1px 0 rgba(255,255,255,0.35);
            transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            position:relative;
        }
        .fb-bnav-play-btn::before {
            content:'';
            position:absolute; inset:-4px;
            border-radius:50%;
            background:conic-gradient(var(--orange), var(--sky-mid), var(--orange));
            z-index:-1; opacity:0;
            transition:opacity 0.3s;
        }
        .fb-bnav-play-btn:hover {
            transform:scale(1.1) translateY(-2px);
            box-shadow:0 10px 28px var(--orange-glow), 0 4px 12px rgba(0,0,0,0.25), 0 0 0 4px rgba(255,255,255,0.15);
        }
        .fb-bnav-play-btn.playing {
            background:linear-gradient(145deg, var(--sky) 0%, var(--sky-dk) 100%);
            box-shadow:0 6px 20px var(--sky-glow), 0 2px 8px rgba(0,0,0,0.2), 0 0 0 3px rgba(255,255,255,0.12);
            animation:navPlayPulse 2.5s ease-in-out infinite;
        }
        .fb-bnav-play-btn.playing::before { opacity:0.4; animation:rotateConic 3s linear infinite; }
        @keyframes navPlayPulse {
            0%,100% { box-shadow:0 6px 20px var(--sky-glow), 0 2px 8px rgba(0,0,0,0.2), 0 0 0 3px rgba(255,255,255,0.12); }
            50%      { box-shadow:0 8px 28px rgba(56,168,204,0.6), 0 2px 8px rgba(0,0,0,0.2), 0 0 0 6px rgba(255,255,255,0.08); }
        }
        @keyframes rotateConic { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

        /* ===== PLAYLIST POPUP MOBILE ===== */
        .fb-playlist-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(22,32,48,0.5);
            backdrop-filter:blur(6px);
            -webkit-backdrop-filter:blur(6px);
            z-index:500;
        }
        .fb-playlist-overlay.open { display:block; }

        .fb-playlist-popup {
            display:none; position:fixed;
            bottom:88px; left:14px; right:14px;
            background:rgba(253,248,243,0.92);
            backdrop-filter:blur(28px) saturate(200%);
            -webkit-backdrop-filter:blur(28px) saturate(200%);
            border:1px solid rgba(255,255,255,0.7);
            border-radius:28px;
            z-index:501;
            max-height:70vh; overflow:hidden;
            box-shadow:
                0 20px 60px rgba(30,127,168,0.2),
                0 8px 24px rgba(0,0,0,0.08),
                inset 0 1px 0 rgba(255,255,255,0.9);
            transform:translateY(24px) scale(0.95);
            opacity:0;
            transition:all 0.4s cubic-bezier(0.34,1.56,0.64,1);
            flex-direction:column;
        }
        .fb-playlist-popup.open {
            display:flex;
            transform:translateY(0) scale(1); opacity:1;
        }

        .fb-playlist-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:1rem 1.25rem 0.75rem; flex-shrink:0;
            border-bottom:1px solid var(--border-lt);
        }
        .fb-playlist-header-title {
            font-family:'Sora',sans-serif;
            font-size:13px; font-weight:700; color:var(--text-1);
            letter-spacing:0.02em;
        }
        .fb-playlist-close {
            width:28px; height:28px; border-radius:50%;
            background:var(--surface); border:1px solid var(--border);
            color:var(--text-3); font-size:13px; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:0.2s;
        }
        .fb-playlist-close:hover { background:var(--sky-lt); color:var(--sky-dk); }

        /* Now playing */
        .fb-playlist-now {
            padding:1rem 1.25rem; flex-shrink:0;
            background:linear-gradient(135deg, var(--sky-lt) 0%, rgba(255,255,255,0.5) 100%);
            border-bottom:1px solid var(--border-lt);
        }
        .fb-playlist-now-row {
            display:flex; align-items:center; gap:12px; margin-bottom:12px;
        }
        .fb-playlist-now-thumb {
            width:46px; height:46px; border-radius:10px;
            object-fit:cover; background:var(--surface);
            box-shadow:0 4px 12px var(--sky-glow);
            flex-shrink:0;
        }
        .fb-playlist-now-title {
            font-family:'Sora',sans-serif;
            font-size:13px; font-weight:600; color:var(--text-1);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .fb-playlist-now-era { font-size:11px; color:var(--text-3); margin-top:2px; }
        .fb-playlist-controls {
            display:flex; align-items:center; justify-content:center; gap:20px;
        }
        .fb-playlist-ctrl {
            background:transparent; border:none; color:var(--text-3);
            font-size:20px; cursor:pointer; padding:4px; transition:0.2s;
        }
        .fb-playlist-ctrl:hover { color:var(--sky-dk); transform:scale(1.1); }
        .fb-playlist-play-main {
            width:46px; height:46px; border-radius:50%;
            background:linear-gradient(145deg, var(--orange) 0%, var(--orange-dk) 100%);
            color:#fff; border:none; font-size:17px; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            box-shadow:0 4px 14px var(--orange-glow); transition:0.25s;
        }
        .fb-playlist-play-main:hover { transform:scale(1.06); }

        /* Progress */
        .fb-playlist-progress-wrap {
            padding:0 1.25rem 0.75rem; flex-shrink:0;
        }
        .fb-playlist-progress-track {
            height:4px; background:var(--border); border-radius:4px;
            overflow:hidden; cursor:pointer; margin-bottom:6px;
            position:relative;
        }
        .fb-playlist-progress-fill {
            height:100%; width:0%;
            background:linear-gradient(90deg, var(--sky) 0%, var(--orange) 100%);
            border-radius:4px; transition:none;
        }
        .fb-playlist-time {
            display:flex; justify-content:space-between;
            font-size:10px; color:var(--text-4); font-weight:500;
        }

        /* Track list */
        .fb-playlist-tracks { overflow-y:auto; flex:1; }
        .fb-playlist-track {
            display:flex; align-items:center; gap:10px;
            padding:9px 1.25rem; cursor:pointer; transition:0.15s;
            border-bottom:1px solid rgba(216,234,242,0.4);
        }
        .fb-playlist-track:hover { background:var(--sky-lt); }
        .fb-playlist-track.active { background:linear-gradient(90deg, var(--sky-lt) 0%, rgba(255,255,255,0.5) 100%); }
        .fb-playlist-track-num {
            font-size:11px; color:var(--text-4); min-width:18px;
            text-align:center; font-weight:600;
        }
        .fb-playlist-track.active .fb-playlist-track-num { color:var(--sky); }
        .fb-playlist-track-thumb {
            width:34px; height:34px; border-radius:8px;
            object-fit:cover; background:var(--surface);
            box-shadow:var(--shadow-sm);
        }
        .fb-playlist-track-title {
            font-size:12px; color:var(--text-2); font-weight:500;
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
            flex:1; min-width:0;
        }
        .fb-playlist-track.active .fb-playlist-track-title { color:var(--sky-dk); font-weight:600; }
        .fb-playlist-track-era { font-size:10px; color:var(--text-4); flex-shrink:0; }
        .fb-playlist-track-wave {
            color:var(--sky);
            animation:wavePop 0.8s ease-in-out infinite alternate;
        }
        @keyframes wavePop { from{opacity:0.3;transform:scale(0.9)} to{opacity:1;transform:scale(1.1)} }

        /* Hide on desktop */
        @media (min-width:769px) {
            .fb-playlist-popup   { display:none !important; }
            .fb-playlist-overlay { display:none !important; }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width:1060px) {
            .fb-layout { grid-template-columns:200px 1fr 0; }
            .fb-sidebar-right { display:none; }
        }
        @media (max-width:768px) {
            .fb-layout { grid-template-columns:1fr; padding-top:56px; }
            .fb-sidebar-left  { display:none; }
            .fb-sidebar-right { display:none; }
            .fb-main { padding:1rem; padding-bottom:96px; }
            .fb-bottom-nav { display:block; }
            .fb-topbar { padding:0 1rem; }
            .fb-logout { padding:0; width:34px; height:34px; border-radius:50%; justify-content:center; }
            .fb-logout-text { display:none; }
            .fb-search-wrap { display:none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Ambient orbs -->
<div class="fb-bg-orb fb-bg-orb-1"></div>
<div class="fb-bg-orb fb-bg-orb-2"></div>
<div class="fb-bg-orb fb-bg-orb-3"></div>

{{-- TOP BAR --}}
<div class="fb-topbar">
    <a href="{{ route('aku') }}" class="fb-brand">MARGONOANDI <span>· fanbase</span></a>
    <div class="fb-topbar-right">
        <div class="fb-notif-wrap">
            <button class="fb-notif-btn" id="fbNotifBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="fb-notif-badge" id="fbNotifBadge" style="display:none;"></span>
            </button>
            <div class="fb-notif-dropdown" id="fbNotifDropdown">
                <div class="fb-notif-header">
                    <span>Notifikasi</span>
                    <button class="fb-notif-readall" onclick="fbMarkAllRead()">Baca semua</button>
                </div>
                <div id="fbNotifList">
                    <div class="fb-notif-empty">Memuat...</div>
                </div>
            </div>
        </div>
        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" class="fb-avatar" alt="">
        <a href="{{ route('logout') }}" class="fb-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="fb-logout-text">Keluar</span>
        </a>
    </div>
</div>

<div class="fb-layout">

    {{-- LEFT SIDEBAR --}}
    <aside class="fb-sidebar-left">
    <div class="fb-sidebar-scroll">
        <div class="fb-profile-card">
            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" class="fb-profile-avatar" alt="">
            <div class="fb-profile-name">{{ Auth::user()->name }}</div>
            <div class="fb-profile-sub">&#10022; Member Margonoandi</div>
        </div>

        <nav class="fb-nav">
            <a href="{{ route('aku') }}"
               class="fb-nav-item {{ request()->routeIs('aku') ? 'active' : '' }}">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </span>
                <span>Aku</span>
            </a>
            <a href="{{ route('kamu') }}"
               class="fb-nav-item {{ request()->routeIs('kamu') ? 'active' : '' }}">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span>Kamu</span>
            </a>
            <a href="{{ route('kita') }}"
               class="fb-nav-item {{ request()->routeIs('kita') ? 'active' : '' }}">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span>Kita</span>
            </a>
            <a href="{{ route('dia') }}"
               class="fb-nav-item {{ request()->routeIs('dia*') ? 'active' : '' }}">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </span>
                <span>Dia</span>
            </a>
            <div class="fb-nav-divider"></div>
            <a href="{{ route('home') }}" class="fb-nav-item">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </span>
                <span>Beranda</span>
            </a>
            @if(Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS',''))))
            <a href="{{ route('admin.index') }}" class="fb-nav-item">
                <span class="fb-nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                </span>
                <span>Admin</span>
            </a>
            @endif
        </nav>

        @php
            $sidebarSongs = \App\Models\Song::whereNotNull('audio_file')
                ->where('audio_file','!=','')->where('is_active',true)
                ->orderBy('track_number')
                ->get(['id','title','era','audio_file','youtube_id']);
        @endphp
        @if($sidebarSongs->count() > 0)
        <p class="fb-sidebar-section" style="margin-top:1.5rem;">&#9834; Playlist</p>
        @foreach($sidebarSongs as $i => $s)
        <div class="fb-song-item" id="sbTrack{{ $i }}" onclick="fbPlayTrack({{ $i }})">
            <img src="https://img.youtube.com/vi/{{ $s->youtube_id }}/mqdefault.jpg"
                 class="fb-song-thumb" alt="">
            <div class="fb-song-info">
                <div class="fb-song-title">{{ $s->title }}</div>
                <div class="fb-song-era">{{ $s->era ?? '' }}</div>
            </div>
        </div>
        @endforeach
        @endif
    </div>{{-- /.fb-sidebar-scroll --}}

    {{-- Desktop mini player --}}
    <div class="fb-desk-player" id="fbDeskPlayer">
        <div class="fb-dp-top">
            <img id="fbDpThumb" src="" class="fb-dp-thumb" alt="">
            <div class="fb-dp-info">
                <div id="fbDpTitle" class="fb-dp-title">Pilih lagu</div>
                <div id="fbDpEra" class="fb-dp-era"></div>
            </div>
            {{-- Stop button —— tampil saat ada lagu dipilih --}}
            <button class="fb-dp-btn fb-dp-stop" id="fbDpStopBtn" onclick="fbStopDesk()" title="Berhenti"
                    style="display:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
            </button>
        </div>
        <div class="fb-dp-controls">
            <button class="fb-dp-btn" onclick="fbPrev()" title="Sebelumnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="19 20 9 12 19 4 19 20"/><line x1="5" y1="19" x2="5" y2="5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </button>
            <button class="fb-dp-btn fb-dp-play" id="fbDpPlayBtn" onclick="fbTogglePlay()" title="Play/Pause">
                <svg id="fbDpPlayIcon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </button>
            <button class="fb-dp-btn" onclick="fbNext()" title="Selanjutnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 4 15 12 5 20 5 4"/><line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </button>
        </div>
        <div class="fb-dp-bar" id="fbDpBar" onclick="fbSeekDesk(event)">
            <div class="fb-dp-fill" id="fbDpFill" style="width:0%"></div>
        </div>
    </div>

    </aside>

    {{-- MAIN --}}
    <main class="fb-main">
        @if(session('success'))
        <div class="fb-alert fb-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="fb-alert fb-alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    {{-- RIGHT SIDEBAR --}}
    <aside class="fb-sidebar-right">
        @php
            $onlineMembers = \App\Models\User::where('id','!=',Auth::id())->get()
                ->filter(fn($u) => $u->isOnline())->values();
            try {
                $allMembersForSearch = \App\Models\User::where('id','!=',Auth::id())
                    ->orderBy('name')->get(['id','name','avatar'])
                    ->map(function($u) use ($onlineMembers) {
                        return [
                            'id'     => $u->id,
                            'name'   => $u->name,
                            'first'  => explode(' ', $u->name)[0],
                            'avatar' => $u->avatar ?: '',
                            'online' => $onlineMembers->contains('id', $u->id),
                        ];
                    });
            } catch (\Throwable $e) {
                $allMembersForSearch = collect();
            }
        @endphp

        {{-- Search member untuk ngobrol --}}
        <div class="fb-widget">
            <p class="fb-widget-title">&#128172; Cari untuk ngobrol</p>
            <div class="fb-msearch-wrap">
                <span class="fb-msearch-icon">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" class="fb-msearch-input" id="fbMsearchInput"
                       placeholder="Ketik nama member..."
                       oninput="fbMemberSearch(this.value)"
                       autocomplete="off">
            </div>
            <div id="fbMsearchResults" class="fb-msearch-results">
                <p class="fb-msearch-empty" style="margin:0;">Ketik nama untuk mencari</p>
            </div>
        </div>

        {{-- Online members --}}
        <div class="fb-widget">
            <p class="fb-widget-title">
                &#128100; Online
                @if($onlineMembers->count() > 0)
                <span style="float:right;font-size:10px;color:#16a34a;font-weight:600;letter-spacing:0;">
                    &#128994; {{ $onlineMembers->count() }}
                </span>
                @endif
            </p>
            @forelse($onlineMembers as $u)
            <form method="POST" action="{{ route('dia.start', $u->id) }}" style="margin:0;">
                @csrf
                <button type="submit" class="fb-online-item" style="width:100%;cursor:pointer;">
                    <div class="fb-online-avatar">
                        <img src="{{ $u->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $u->name }}">
                        <div class="fb-online-dot online"></div>
                    </div>
                    <div class="fb-online-info">
                        <div class="fb-online-name">{{ explode(' ',$u->name)[0] }}</div>
                        <div class="fb-online-status online">Online</div>
                    </div>
                </button>
            </form>
            @empty
            <p style="font-size:11px;color:var(--text-4);text-align:center;padding:0.75rem 0;">Tidak ada yang online.</p>
            @endforelse
        </div>

        {{-- Obrolan & Grup (tampil di semua halaman) --}}
        @php
            try {
                $sbConvs = \App\Models\Conversation::with(['userOne','userTwo'])
                    ->where('user_one_id', Auth::id())
                    ->orWhere('user_two_id', Auth::id())
                    ->orderByDesc('last_message_at')
                    ->take(6)->get();
                $sbGroups = \App\Models\Group::whereHas('members', fn($q) => $q->where('user_id', Auth::id()))
                    ->orderByDesc('last_message_at')
                    ->take(4)->get();
            } catch (\Throwable $e) {
                $sbConvs = collect(); $sbGroups = collect();
            }
        @endphp

        @if($sbConvs->count() > 0 || $sbGroups->count() > 0)
        <div class="fb-widget" style="padding:0.75rem;">

            @if($sbConvs->count() > 0)
            <p class="fb-widget-title" style="margin-bottom:0.5rem;">&#128172; Obrolan</p>
            @foreach($sbConvs as $sc)
            @php $scOther = $sc->getOtherUser(Auth::id()); @endphp
            <a href="{{ route('dia.conversation', $sc->id) }}"
               class="fb-online-item" style="text-decoration:none;">
                <div class="fb-online-avatar">
                    <img src="{{ $scOther->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $scOther->name }}">
                    <div class="fb-online-dot {{ $scOther->isOnline() ? 'online' : '' }}"></div>
                </div>
                <div class="fb-online-info">
                    <div class="fb-online-name">{{ explode(' ',$scOther->name)[0] }}</div>
                    <div class="fb-online-status" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90px;">
                        {{ $sc->last_message ? \Illuminate\Support\Str::limit($sc->last_message, 18) : '—' }}
                    </div>
                </div>
            </a>
            @endforeach
            @endif

            @if($sbGroups->count() > 0)
            <p class="fb-widget-title" style="margin-top:0.75rem;margin-bottom:0.5rem;">&#128101; Grup</p>
            @foreach($sbGroups as $sg)
            <a href="{{ route('dia.group', $sg->id) }}"
               class="fb-online-item" style="text-decoration:none;">
                <div class="fb-online-avatar" style="background:var(--sky-lt);color:var(--sky-dk);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="fb-online-info">
                    <div class="fb-online-name">{{ \Illuminate\Support\Str::limit($sg->name, 14) }}</div>
                    <div class="fb-online-status">
                        {{ $sg->last_message ? \Illuminate\Support\Str::limit($sg->last_message, 18) : '—' }}
                    </div>
                </div>
            </a>
            @endforeach
            @endif

        </div>
        @endif
        <div class="fb-widget">
            <p class="fb-widget-title">Tentang</p>
            <p style="font-size:11px;color:var(--text-3);line-height:1.8;">
                Fanbase resmi Margonoandi<br>
                15 lagu · 2000–2026<br>
                <span style="color:var(--sky-dk);font-weight:600;">by Rakhman Andi</span>
            </p>
        </div>
    </aside>

</div>

{{-- PLAYLIST POPUP (mobile) --}}
<div class="fb-playlist-overlay" id="fbPlaylistOverlay" onclick="fbClosePlaylist()"></div>
<div class="fb-playlist-popup" id="fbPlaylistPopup">
    <div class="fb-playlist-header">
        <span class="fb-playlist-header-title">&#9834; Playlist</span>
        <button class="fb-playlist-close" onclick="fbClosePlaylist()">&#10005;</button>
    </div>
    <div class="fb-playlist-now">
        <div class="fb-playlist-now-row">
            <img id="fbPopupThumb" src="" class="fb-playlist-now-thumb" alt="">
            <div style="flex:1;min-width:0;">
                <div class="fb-playlist-now-title" id="fbPopupTitle">Pilih lagu...</div>
                <div class="fb-playlist-now-era"   id="fbPopupEra">Margonoandi</div>
            </div>
        </div>
        <div class="fb-playlist-controls">
            <button class="fb-playlist-ctrl" onclick="fbPrev()">&#9664;&#9664;</button>
            <button class="fb-playlist-play-main" onclick="fbTogglePlay()" id="fbPopupPlayBtn">&#9654;</button>
            <button class="fb-playlist-ctrl" onclick="fbNext()">&#9654;&#9654;</button>
        </div>
    </div>
    <div class="fb-playlist-progress-wrap">
        <div class="fb-playlist-progress-track" onclick="fbSeekPopup(event)" id="fbPopupBar">
            <div class="fb-playlist-progress-fill" id="fbPopupFill"></div>
        </div>
        <div class="fb-playlist-time">
            <span id="fbPopupCur">0:00</span>
            <span id="fbPopupDur">0:00</span>
        </div>
    </div>
    <div class="fb-playlist-tracks" id="fbPlaylistTracks"></div>
</div>

{{-- BOTTOM NAV (mobile) --}}
<nav class="fb-bottom-nav">
    <div class="fb-bottom-inner">
        <a href="{{ route('aku') }}"
           class="fb-bnav-item {{ request()->routeIs('aku') ? 'active' : '' }}">
            <span class="fb-bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </span>
            <span class="fb-bnav-label">Aku</span>
        </a>
        <a href="{{ route('kamu') }}"
           class="fb-bnav-item {{ request()->routeIs('kamu') ? 'active' : '' }}">
            <span class="fb-bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="fb-bnav-label">Kamu</span>
        </a>
        <button class="fb-bnav-play" onclick="fbTogglePlaylist()">
            <div class="fb-bnav-play-btn" id="fbPlayBtnNav">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </div>
        </button>
        <a href="{{ route('kita') }}"
           class="fb-bnav-item {{ request()->routeIs('kita') ? 'active' : '' }}">
            <span class="fb-bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="fb-bnav-label">Kita</span>
        </a>
        <a href="{{ route('dia') }}"
           class="fb-bnav-item {{ request()->routeIs('dia*') ? 'active' : '' }}">
            <span class="fb-bnav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span class="fb-bnav-label">Dia</span>
        </a>
    </div>
</nav>

<audio id="fbAudio" preload="none"></audio>

@stack('scripts')

<script>
@php
$fbTracksData = \App\Models\Song::whereNotNull('audio_file')
    ->where('audio_file','!=','')->where('is_active',true)
    ->orderBy('track_number')
    ->get(['title','era','audio_file','youtube_id'])
    ->map(function($s){
        return ['title'=>$s->title,'era'=>$s->era??'','audio'=>asset($s->audio_file),'thumb'=>'https://img.youtube.com/vi/'.$s->youtube_id.'/mqdefault.jpg'];
    });
@endphp
var fbTracks=@json($fbTracksData),fbTotal=fbTracks.length,fbCurrent=-1,fbPlaying=false,fbAudio=document.getElementById('fbAudio');
var fbLastSaveTs=0;

// ── Simpan posisi & status ke localStorage ──────────────────
function fbSaveState(){
    if(!fbTotal||fbCurrent<0)return;
    try{localStorage.setItem('fb_state',JSON.stringify({idx:fbCurrent,time:fbAudio.currentTime||0,playing:fbPlaying}));}catch(e){}
}
function fbClearState(){try{localStorage.removeItem('fb_state');}catch(e){}}

// ── Resume otomatis dari localStorage ──────────────────────
function fbTryResume(){
    try{
        var s=JSON.parse(localStorage.getItem('fb_state')||'null');
        if(!s||!s.playing||s.idx<0||s.idx>=fbTotal)return;
        fbCurrent=s.idx;
        var seekTo=parseFloat(s.time)||0;
        var doPlay=function(){
            if(seekTo>1)fbAudio.currentTime=seekTo;
            fbAudio.play()
                .then(function(){fbPlaying=true;fbUpdateUI();})
                .catch(function(){fbShowResumeToast();fbUpdateUI();});
        };
        // Listener harus ditambah SEBELUM set src agar tidak melewatkan canplay dari cache
        fbAudio.addEventListener('canplay',function onCp(){
            fbAudio.removeEventListener('canplay',onCp);
            doPlay();
        });
        fbAudio.src=fbTracks[fbCurrent].audio;
        fbAudio.load(); // paksa load meski preload="none"
        fbUpdateUI();
    }catch(e){}
}
function fbShowResumeToast(){
    if(document.getElementById('fbResumeToast'))return;
    var t=fbTracks[fbCurrent];if(!t)return;
    var el=document.createElement('div');
    el.id='fbResumeToast';
    el.setAttribute('style','position:fixed;bottom:96px;left:50%;transform:translateX(-50%);background:var(--sky-dk);color:#fff;padding:8px 18px;border-radius:20px;font-size:12px;font-family:inherit;z-index:9999;display:flex;align-items:center;gap:8px;box-shadow:0 4px 16px rgba(0,0,0,0.25);cursor:pointer;white-space:nowrap;');
    el.innerHTML='<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg> Lanjut: '+escHtml(t.title);
    el.onclick=function(){fbAudio.play().then(function(){fbPlaying=true;fbUpdateUI();}).catch(function(){});el.remove();};
    document.body.appendChild(el);
    setTimeout(function(){if(el.parentNode)el.remove();},8000);
}

document.addEventListener('DOMContentLoaded',function(){
    var list=document.getElementById('fbPlaylistTracks');
    if(list&&fbTracks.length){
        fbTracks.forEach(function(t,i){
            var d=document.createElement('div');
            d.className='fb-playlist-track';
            d.id='fbPopupTrack'+i;
            d.onclick=function(){fbPlayTrack(i);};
            d.innerHTML='<span class="fb-playlist-track-num" id="fbPopupNum'+i+'">'+(i+1)+'</span>'+
                '<img src="'+t.thumb+'" class="fb-playlist-track-thumb" alt="">'+
                '<div style="flex:1;min-width:0;"><div class="fb-playlist-track-title">'+escHtml(t.title)+'</div>'+
                '<div class="fb-playlist-track-era">'+escHtml(t.era)+'</div></div>';
            list.appendChild(d);
        });
    }
    fbTryResume();
});

function fbPlayTrack(i){
    if(!fbTotal)return;
    fbCurrent=i;
    var t=fbTracks[i];
    fbAudio.src=t.audio;
    fbAudio.play().then(function(){fbPlaying=true;fbSaveState();fbUpdateUI();}).catch(function(){});
    document.querySelectorAll('.fb-song-item').forEach(function(el,j){el.classList.toggle('playing',j===i);});
}
function fbTogglePlay(){
    if(fbCurrent<0){fbPlayTrack(0);return;}
    if(fbPlaying){fbAudio.pause();fbPlaying=false;}else{fbAudio.play();fbPlaying=true;}
    fbSaveState();fbUpdateUI();
}
function fbNext(){if(fbTotal)fbPlayTrack((fbCurrent+1)%fbTotal);}
function fbPrev(){
    if(!fbTotal)return;
    if(fbAudio.currentTime>3){fbAudio.currentTime=0;return;}
    fbPlayTrack((fbCurrent-1+fbTotal)%fbTotal);
}
function fbUpdateUI(){
    if(fbCurrent<0||!fbTracks[fbCurrent])return;
    var t=fbTracks[fbCurrent],el;
    el=document.getElementById('fbPopupThumb');if(el)el.src=t.thumb;
    el=document.getElementById('fbPopupTitle');if(el)el.textContent=t.title;
    el=document.getElementById('fbPopupEra');if(el)el.textContent=t.era;
    el=document.getElementById('fbPopupPlayBtn');if(el)el.innerHTML=fbPlaying?'&#9646;&#9646;':'&#9654;';
    // Desktop player
    el=document.getElementById('fbDpThumb');if(el)el.src=t.thumb;
    el=document.getElementById('fbDpTitle');if(el)el.textContent=t.title;
    el=document.getElementById('fbDpEra');if(el)el.textContent=t.era;
    el=document.getElementById('fbDpPlayBtn');
    if(el){
        el.classList.toggle('playing',fbPlaying);
        var ico=document.getElementById('fbDpPlayIcon');
        if(ico)ico.outerHTML=fbPlaying
            ?'<svg id="fbDpPlayIcon" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>'
            :'<svg id="fbDpPlayIcon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
    }
    el=document.getElementById('fbDpStopBtn');if(el)el.style.display=fbCurrent>=0?'flex':'none';
    el=document.getElementById('fbPlayBtnNav');
    if(el){
        el.innerHTML=fbPlaying
            ?'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>'
            :'<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
        el.classList.toggle('playing',fbPlaying);
    }
    for(var i=0;i<fbTotal;i++){
        var tr=document.getElementById('fbPopupTrack'+i);if(tr)tr.classList.toggle('active',i===fbCurrent);
        var sb=document.getElementById('sbTrack'+i);if(sb)sb.classList.toggle('playing',i===fbCurrent);
        var nm=document.getElementById('fbPopupNum'+i);
        if(nm)nm.innerHTML=(i===fbCurrent&&fbPlaying)?'<span class="fb-playlist-track-wave">&#9836;</span>':(i+1);
    }
}
fbAudio.addEventListener('timeupdate',function(){
    if(!fbAudio.duration)return;
    var pct=(fbAudio.currentTime/fbAudio.duration*100).toFixed(1)+'%';
    var el=document.getElementById('fbPopupFill');if(el)el.style.width=pct;
    el=document.getElementById('fbPopupCur');if(el)el.textContent=fbFmt(fbAudio.currentTime);
    el=document.getElementById('fbDpFill');if(el)el.style.width=pct;
    // Simpan posisi tiap 4 detik
    var now=Date.now();
    if(fbPlaying&&now-fbLastSaveTs>4000){fbLastSaveTs=now;fbSaveState();}
});
fbAudio.addEventListener('loadedmetadata',function(){
    var el=document.getElementById('fbPopupDur');if(el)el.textContent=fbFmt(fbAudio.duration);
});
fbAudio.addEventListener('pause',function(){fbPlaying=false;fbSaveState();});
fbAudio.addEventListener('play',function(){fbPlaying=true;fbSaveState();});
fbAudio.addEventListener('ended',fbNext);
// Simpan posisi TEPAT saat pindah halaman agar tidak rollback
window.addEventListener('beforeunload',function(){if(fbCurrent>=0)fbSaveState();});
function fbFmt(s){if(!s||isNaN(s))return'0:00';var m=Math.floor(s/60),sec=Math.floor(s%60);return m+':'+(sec<10?'0':'')+sec;}
function fbSeekPopup(e){
    var bar=document.getElementById('fbPopupBar');
    if(!bar||!fbAudio.duration)return;
    var rect=bar.getBoundingClientRect();
    fbAudio.currentTime=Math.max(0,Math.min(1,(e.clientX-rect.left)/rect.width))*fbAudio.duration;
}
function fbSeekDesk(e){
    var bar=document.getElementById('fbDpBar');
    if(!bar||!fbAudio.duration)return;
    var rect=bar.getBoundingClientRect();
    fbAudio.currentTime=Math.max(0,Math.min(1,(e.clientX-rect.left)/rect.width))*fbAudio.duration;
}
function fbStopDesk(){
    fbAudio.pause();
    fbAudio.currentTime=0;
    fbPlaying=false;
    fbClearState();
    var fill=document.getElementById('fbDpFill');if(fill)fill.style.width='0%';
    var stop=document.getElementById('fbDpStopBtn');if(stop)stop.style.display='none';
    fbUpdateUI();
}
function fbTogglePlaylist(){
    var p=document.getElementById('fbPlaylistPopup');
    if(!p)return;
    p.classList.contains('open')?fbClosePlaylist():fbOpenPlaylist();
}
function fbOpenPlaylist(){
    document.getElementById('fbPlaylistPopup').classList.add('open');
    document.getElementById('fbPlaylistOverlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function fbClosePlaylist(){
    document.getElementById('fbPlaylistPopup').classList.remove('open');
    document.getElementById('fbPlaylistOverlay').classList.remove('open');
    document.body.style.overflow='';
}
function escHtml(t){var d=document.createElement('div');d.appendChild(document.createTextNode(t));return d.innerHTML;}

// ===== SOUND SYSTEM =====
var fbSndCtx = null;
function fbSnd() {
    if (!fbSndCtx) {
        try { fbSndCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) { return null; }
    }
    return fbSndCtx;
}
function fbTone(freqs, durs, vol, type) {
    var ctx = fbSnd(); if (!ctx) return;
    type = type || 'sine'; vol = vol || 0.22;
    var t = ctx.currentTime;
    freqs.forEach(function(f, i) {
        var off = durs.slice(0, i).reduce(function(a,b){return a+b;}, 0);
        var osc = ctx.createOscillator();
        var g   = ctx.createGain();
        osc.type = type; osc.frequency.value = f;
        g.gain.setValueAtTime(vol, t + off);
        g.gain.exponentialRampToValueAtTime(0.001, t + off + durs[i]);
        osc.connect(g); g.connect(ctx.destination);
        osc.start(t + off); osc.stop(t + off + durs[i]);
    });
}
function fbSoundLike()        { fbTone([880, 1109], [0.08, 0.18], 0.20); }
function fbSoundComment()     { fbTone([659, 880, 1047], [0.07, 0.07, 0.18], 0.18); }
function fbSoundMessage()     { fbTone([523, 659, 784], [0.08, 0.08, 0.22], 0.25); }
function fbSoundInvite()      { fbTone([523, 659, 784, 1047], [0.07,0.07,0.07,0.28], 0.22); }
function fbSoundTunerInTune() { fbTone([1000, 1250], [0.15, 0.45], 0.30); }

// ===== NOTIFICATION BELL =====
var fbNotifOpen = false;
var fbNotifLoaded = false;
var fbPrevBadgeCount = -1; // -1 = first load, jangan bunyi

document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('fbNotifBtn');
    if (!btn) return;
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        fbNotifOpen ? fbCloseNotif() : fbOpenNotif();
    });
    document.addEventListener('click', function(e) {
        var dd = document.getElementById('fbNotifDropdown');
        if (dd && !dd.contains(e.target) && e.target !== btn) fbCloseNotif();
    });
    fbPollNotifCount();
    setInterval(fbPollNotifCount, 30000);
});

function fbOpenNotif() {
    var dd = document.getElementById('fbNotifDropdown');
    if (!dd) return;
    dd.classList.add('open');
    fbNotifOpen = true;
    fbLoadNotifs();
}
function fbCloseNotif() {
    var dd = document.getElementById('fbNotifDropdown');
    if (dd) dd.classList.remove('open');
    fbNotifOpen = false;
}

function fbCsrfToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
}

function fbPollNotifCount() {
    fetch('/notifications/unread-count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.ok ? r.json() : null; })
    .then(function(d) {
        if (!d || typeof d.count === 'undefined') return;
        var count = d.count;
        var badge = document.getElementById('fbNotifBadge');
        if (badge) {
            if (count > 0) { badge.textContent = count > 99 ? '99+' : count; badge.style.display = 'flex'; }
            else badge.style.display = 'none';
        }
        // Bunyi hanya saat ada notif BARU (bukan saat pertama load)
        if (fbPrevBadgeCount >= 0 && count > fbPrevBadgeCount) fbSoundMessage();
        fbPrevBadgeCount = count;
    }).catch(function(){});
}

function fbLoadNotifs() {
    var list = document.getElementById('fbNotifList');
    if (!list) return;
    list.innerHTML = '<div class="fb-notif-empty">Memuat...</div>';

    fetch('/notifications', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.ok ? r.json() : Promise.reject('HTTP ' + r.status); })
    .then(function(d) {
        if (!d.notifications || d.notifications.length === 0) {
            list.innerHTML = '<div class="fb-notif-empty">Belum ada notifikasi.</div>';
            return;
        }
        var defaultAvatar = '{{ asset("images/default-avatar.png") }}';
        list.innerHTML = d.notifications.map(function(n) {
            var avatar  = (n.from_user && n.from_user.avatar) ? n.from_user.avatar : defaultAvatar;
            var unread  = !n.read_at ? ' unread' : '';
            var isInvite = n.type === 'invite';

            if (isInvite) {
                // Extract invite ID from accept URL: /dia/invite/{id}/accept
                var inviteMatch = (n.url || '').match(/\/dia\/invite\/(\d+)\/accept/);
                var inviteId = inviteMatch ? inviteMatch[1] : null;
                var acceptBtn = inviteId
                    ? '<button onclick="fbAcceptInvite('+inviteId+','+n.id+',event)" style="margin-top:6px;padding:4px 12px;font-size:11px;font-weight:600;background:linear-gradient(135deg,var(--sky),var(--sky-dk));color:#fff;border:none;border-radius:6px;cursor:pointer;margin-right:4px;">Terima</button>'
                    + '<button onclick="fbDeclineInvite('+inviteId+','+n.id+',event)" style="padding:4px 10px;font-size:11px;background:transparent;color:var(--text-3);border:1px solid var(--border);border-radius:6px;cursor:pointer;">Tolak</button>'
                    : '';
                return '<div class="fb-notif-item'+unread+'" id="fbNotif'+n.id+'">'
                    + '<img class="fb-notif-avatar" src="'+escHtml(avatar)+'" onerror="this.src=\''+defaultAvatar+'\'" alt="">'
                    + '<div class="fb-notif-body">'
                    + '<div class="fb-notif-title">'+escHtml(n.title||'')+'</div>'
                    + '<div class="fb-notif-msg">'+escHtml(n.body||'')+'</div>'
                    + acceptBtn
                    + '<div class="fb-notif-time">'+escHtml(n.created_at_diff||'')+'</div>'
                    + '</div></div>';
            }

            // Pakai data-attribute — JSON.stringify bikin double-quote di dalam onclick HTML dan merusak parser
            return '<div class="fb-notif-item'+unread+'" id="fbNotif'+n.id
                +'" data-nid="'+n.id+'" data-nurl="'+escHtml(n.url||'')+'" onclick="fbNotifClickEl(this)">'
                + '<img class="fb-notif-avatar" src="'+escHtml(avatar)+'" onerror="this.src=\''+defaultAvatar+'\'" alt="">'
                + '<div class="fb-notif-body">'
                + '<div class="fb-notif-title">'+escHtml(n.title||'')+'</div>'
                + '<div class="fb-notif-msg">'+escHtml(n.body||'')+'</div>'
                + '<div class="fb-notif-time">'+escHtml(n.created_at_diff||'')+'</div>'
                + '</div></div>';
        }).join('');
        fbPollNotifCount();
    })
    .catch(function(err) {
        list.innerHTML = '<div class="fb-notif-empty">Gagal memuat notifikasi.</div>';
    });
}

function fbNotifClickEl(el) {
    var id  = el.getAttribute('data-nid');
    var url = el.getAttribute('data-nurl') || '';
    // Tandai baca di server
    fetch('/notifications/'+id+'/read', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':fbCsrfToken(),'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    }).catch(function(){});
    // Kurangi badge langsung tanpa tunggu server/reload
    if (el.classList.contains('unread')) {
        el.classList.remove('unread');
        var badge = document.getElementById('fbNotifBadge');
        if (badge && badge.style.display !== 'none') {
            var cnt = parseInt(badge.textContent) || 0;
            cnt--;
            if (cnt > 0) { badge.textContent = cnt; }
            else { badge.style.display = 'none'; }
        }
    }
    fbCloseNotif();
    if (url && url !== '#' && url !== '') window.location.href = url;
}

function fbAcceptInvite(inviteId, notifId, e) {
    e.stopPropagation();
    var btn = e.target;
    btn.disabled = true; btn.textContent = '...';
    fetch('/dia/invite/'+inviteId+'/accept', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':fbCsrfToken(),'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    })
    .then(function(r){
        if (r.redirected) { window.location.href = r.url; return; }
        return r.json();
    })
    .then(function(d){
        fbCloseNotif();
        // Redirect handled above; fallback
        window.location.href = '/dia';
    })
    .catch(function(){
        // On redirect (302) fetch follows — location.href done above
        fbCloseNotif();
        window.location.href = '/dia';
    });
}

function fbDeclineInvite(inviteId, notifId, e) {
    e.stopPropagation();
    var btn = e.target;
    btn.disabled = true; btn.textContent = '...';
    fetch('/dia/invite/'+inviteId+'/decline', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':fbCsrfToken(),'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    })
    .then(function(){ return fetch('/notifications/'+notifId+'/read', {
        method:'POST', headers:{'X-CSRF-TOKEN':fbCsrfToken(),'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    }); })
    .then(function(){ fbLoadNotifs(); })
    .catch(function(){ fbLoadNotifs(); });
}

function fbMarkAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': fbCsrfToken(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function() { fbLoadNotifs(); }).catch(function(){});
}

// ===== PWA SERVICE WORKER =====
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').catch(function(){});
    });
}

// ===== MEMBER SEARCH (right sidebar desktop) =====
var fbAllMembers = {!! json_encode($allMembersForSearch ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) !!};
function fbMemberSearch(q){
    var box=document.getElementById('fbMsearchResults');
    if(!box)return;
    q=(q||'').trim().toLowerCase();
    if(!q){
        box.innerHTML='<p class="fb-msearch-empty" style="margin:0;">Ketik nama untuk mencari</p>';
        return;
    }
    var members=Array.isArray(fbAllMembers)?fbAllMembers:Object.values(fbAllMembers);
    var results=members.filter(function(m){return m.name.toLowerCase().indexOf(q)>=0;}).slice(0,8);
    if(!results.length){
        box.innerHTML='<p class="fb-msearch-empty" style="margin:0;">Member tidak ditemukan</p>';
        return;
    }
    var csrf=document.querySelector("meta[name='csrf-token']").content;
    var html='';
    results.forEach(function(m){
        var dot=m.online?'<span style="width:7px;height:7px;border-radius:50%;background:var(--sky);flex-shrink:0;display:inline-block;margin-left:auto;"></span>':'';
        var fallback="https://ui-avatars.com/api/?name="+encodeURIComponent(m.first)+"&background=38A8CC&color=fff&size=28";
        var av=m.avatar
            ?'<img src="'+m.avatar+'" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;" onerror="this.src=\''+fallback+'\'">'
            :'<img src="'+fallback+'" style="width:28px;height:28px;border-radius:50%;flex-shrink:0;">';
        html+='<form method="POST" action="/dia/start/'+m.id+'" style="margin:0;">'
            +'<input type="hidden" name="_token" value="'+csrf+'">'
            +'<button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;background:none;border:none;padding:5px 6px;border-radius:8px;cursor:pointer;text-align:left;color:var(--text-1);transition:background 0.15s;" onmouseover="this.style.background=\'var(--sky-lt)\'" onmouseout="this.style.background=\'none\'">'
            +av
            +'<span style="font-size:13px;font-weight:500;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+m.first+'</span>'
            +dot
            +'</button>'
            +'</form>';
    });
    box.innerHTML=html;
}
</script>
</body>
</html>