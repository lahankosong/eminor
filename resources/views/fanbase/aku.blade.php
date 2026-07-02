@extends('layouts.fanbase')
@section('title', 'Aku — Ekosistem Musik Indie Indonesia')

@push('styles')
<style>
    /* PAGE HEADER */
    .aku-page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    .aku-page-title {
        font-family: 'Sora', sans-serif;
        font-size: 1.1rem; font-weight: 700; color: var(--text-1);
    }
    .aku-page-sub { font-size: 12px; color: var(--text-3); margin-top: 3px; }

    /* ADMIN BADGE */
    .admin-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
        background: linear-gradient(135deg, var(--sky-lt), #fff);
        color: var(--sky-dk); border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    /* POST FORM */
    .aku-form {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; padding: 1.25rem; margin-bottom: 1.5rem;
        box-shadow: var(--shadow);
        position: relative; overflow: hidden;
    }
    .aku-form::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--sky), var(--orange));
    }
    .aku-form-label {
        font-size: 10px; color: var(--text-3); letter-spacing: 0.2em;
        text-transform: uppercase; font-weight: 700; margin-bottom: 0.875rem;
        display: block;
    }
    .aku-form-input {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-1); font-size: 13px;
        padding: 9px 14px; outline: none; font-family: 'DM Sans', sans-serif;
        transition: 0.2s; margin-bottom: 8px;
    }
    .aku-form-input:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .aku-form-input::placeholder { color: var(--text-4); }
    .aku-form-textarea {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-1); font-size: 14px;
        padding: 10px 14px; outline: none; resize: none;
        min-height: 100px; line-height: 1.7; font-family: 'DM Sans', sans-serif;
        transition: 0.2s;
    }
    .aku-form-textarea:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .aku-form-textarea::placeholder { color: var(--text-4); }
    .aku-form-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 10px; flex-wrap: wrap; gap: 8px;
    }
    .aku-form-tools { display: flex; gap: 8px; }
    .aku-tool-btn {
        display: flex; align-items: center; gap: 5px;
        padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 500;
        border: 1px solid var(--border); background: var(--surface);
        color: var(--text-3); cursor: pointer; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .aku-tool-btn:hover { background: var(--sky-lt); color: var(--sky-dk); border-color: var(--sky-mid); }
    .aku-mood-input {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 20px; color: var(--text-2); font-size: 11px;
        padding: 6px 14px; outline: none; width: 120px;
        font-family: 'DM Sans', sans-serif; transition: 0.2s;
    }
    .aku-mood-input:focus { border-color: var(--sky); }
    .aku-mood-input::placeholder { color: var(--text-4); }
    .btn-post-aku {
        padding: 8px 24px; border-radius: 20px; font-size: 12px; font-weight: 600;
        background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; border: none; cursor: pointer; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 4px 12px var(--sky-glow);
        position: relative; overflow: hidden;
    }
    .btn-post-aku::after {
        content: ''; position: absolute; top: 0; left: -120%; width: 55%; height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255,255,255,0.4), transparent);
        transform: skewX(-20deg); transition: left 0.6s ease; pointer-events: none;
    }
    .btn-post-aku:hover::after { left: 150%; }
    .btn-post-aku:hover { transform: translateY(-1px); box-shadow: var(--shadow); }

    /* POST CARD */
    .aku-post {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; margin-bottom: 1rem; overflow: hidden;
        box-shadow: var(--shadow-sm); transition: 0.2s;
        animation: akuIn 0.55s ease backwards;
    }
    .aku-post:hover { border-color: var(--sky-mid); box-shadow: var(--shadow); transform: translateY(-3px); }
    .aku-post.pinned {
        border-color: var(--orange);
        box-shadow: 0 4px 16px var(--orange-glow);
    }
    .aku-post.pinned:hover { border-color: var(--orange); box-shadow: 0 14px 34px -14px rgba(240,112,64,0.5); }

    @keyframes akuIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }

    .aku-post-header {
        display: flex; align-items: center; gap: 10px;
        padding: 1rem 1rem 0.5rem;
    }
    .aku-post-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--sky-lt);
        box-shadow: var(--shadow-sm); flex-shrink: 0;
    }
    .aku-post-meta { flex: 1; min-width: 0; }
    .aku-post-name {
        font-family: 'Sora', sans-serif;
        font-size: 13px; font-weight: 600; color: var(--text-1);
        display: flex; align-items: center; gap: 6px;
    }
    .aku-admin-tag {
        font-size: 10px; color: var(--sky-dk); background: var(--sky-lt);
        border: 1px solid var(--border); border-radius: 10px;
        padding: 1px 8px; font-weight: 600;
    }
    .aku-post-date { font-size: 11px; color: var(--text-4); margin-top: 2px; }

    .aku-post-top-actions { display: flex; align-items: center; gap: 6px; }
    .aku-pin-badge {
        font-size: 10px; color: var(--orange-dk); background: var(--orange-lt);
        border: 1px solid rgba(240,112,64,0.2); border-radius: 10px;
        padding: 2px 8px; font-weight: 600;
    }
    .aku-top-btn {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--surface); border: 1px solid var(--border);
        color: var(--text-4); font-size: 11px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .aku-top-btn:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .aku-edit-btn:hover { background: var(--sky-lt); color: var(--sky-dk); border-color: var(--sky-mid); }

    .aku-post-title {
        font-family: 'Sora', sans-serif;
        font-size: 15px; font-weight: 600; color: var(--text-1);
        padding: 0 1rem; margin-bottom: 6px; line-height: 1.4;
    }
    .aku-post-body {
        font-size: 14px; color: var(--text-2); line-height: 1.8;
        padding: 0 1rem 0.875rem; word-break: break-word; white-space: pre-wrap;
    }
    .aku-post-body-edit {
        width: calc(100% - 2rem); background: var(--cream);
        border: 1px solid var(--sky); border-radius: 10px;
        color: var(--text-1); font-size: 14px; padding: 10px 14px;
        outline: none; resize: vertical; min-height: 80px;
        line-height: 1.7; font-family: 'DM Sans', sans-serif;
        margin: 0 1rem 0.875rem; display: none;
        box-shadow: 0 0 0 3px var(--sky-glow);
    }
    .aku-post-edit-actions {
        display: none; gap: 8px; padding: 0 1rem 0.75rem;
    }
    .aku-save-btn {
        padding: 6px 18px; border-radius: 16px; font-size: 12px; font-weight: 500;
        background: var(--sky); color: #fff; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .aku-cancel-btn {
        padding: 6px 16px; border-radius: 16px; font-size: 12px;
        background: transparent; border: 1px solid var(--border);
        color: var(--text-3); cursor: pointer; font-family: 'DM Sans', sans-serif;
    }

    .aku-post-image {
        width: 100%; max-height: 360px; object-fit: cover; display: block;
        border-top: 1px solid var(--border-lt); border-bottom: 1px solid var(--border-lt);
    }
    .aku-post-mood {
        display: inline-flex; align-items: center; gap: 4px;
        margin: 0 1rem 0.75rem; font-size: 11px; font-weight: 500;
        color: var(--sky-dk); background: var(--sky-lt);
        border: 1px solid var(--border); border-radius: 20px;
        padding: 3px 12px;
    }

    /* POST FOOTER */
    .aku-post-footer {
        display: flex; align-items: center; gap: 16px;
        padding: 0.75rem 1rem; border-top: 1px solid var(--border-lt);
    }
    .aku-action-btn {
        display: flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500; color: var(--text-4);
        background: transparent; border: none; cursor: pointer;
        transition: 0.2s; padding: 4px 8px; border-radius: 20px;
        font-family: 'DM Sans', sans-serif;
    }
    .aku-action-btn:hover { background: var(--sky-lt); color: var(--sky-dk); }
    .aku-action-btn.liked { color: #ef4444; }
    .aku-action-btn.liked:hover { background: #fef2f2; }
    .like-icon { font-size: 14px; }
    .aku-like-wrap { position: relative; display: inline-flex; align-items: center; gap: 2px; }
    .like-count-btn {
        font-size: 12px; font-weight: 500; color: var(--text-3);
        cursor: pointer; padding: 4px 6px 4px 2px; border-radius: 6px; transition: 0.15s;
    }
    .like-count-btn:hover { color: var(--sky-dk); background: var(--sky-lt); }
    .likers-tooltip {
        display: none; position: absolute; bottom: calc(100% + 8px); left: 0;
        background: var(--card); border: 1px solid var(--border);
        border-radius: 12px; padding: 10px 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        z-index: 20; min-width: 140px; max-width: 220px;
    }
    .likers-tooltip.open { display: block; }
    .likers-tooltip::after {
        content: ''; position: absolute; top: 100%; left: 14px;
        border: 6px solid transparent; border-top-color: var(--card);
    }
    .likers-tooltip-title { font-size: 10px; color: var(--text-4); margin-bottom: 6px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; }
    .likers-tooltip-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--text-2); padding: 3px 0; }
    .likers-tooltip-item img { width: 20px; height: 20px; border-radius: 50%; object-fit: cover; background: var(--sky-lt); }
    .likers-tooltip-empty { font-size: 12px; color: var(--text-4); }

    /* COMMENTS */
    .aku-comments {
        padding: 0.75rem 1rem; border-top: 1px solid var(--border-lt);
        display: none; background: var(--cream);
    }
    .aku-comments.open { display: block; }
    .aku-comment-item {
        display: flex; gap: 8px; margin-bottom: 10px;
    }
    .aku-comment-avatar {
        width: 28px; height: 28px; border-radius: 50%;
        object-fit: cover; background: var(--surface); flex-shrink: 0;
        border: 1.5px solid var(--border);
    }
    .aku-comment-bubble {
        background: var(--card); border-radius: 12px;
        padding: 8px 12px; flex: 1; border: 1px solid var(--border-lt);
        box-shadow: var(--shadow-sm);
    }
    .aku-comment-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 3px; }
    .aku-comment-name { font-size: 11px; font-weight: 600; color: var(--text-2); }
    .aku-comment-time { font-size: 10px; color: var(--text-4); }
    .aku-comment-body { font-size: 13px; color: var(--text-2); line-height: 1.5; }
    .aku-comment-delete {
        background: transparent; border: none; color: var(--text-4);
        font-size: 10px; cursor: pointer; padding: 2px 4px; transition: 0.15s;
        border-radius: 4px;
    }
    .aku-comment-delete:hover { color: #ef4444; background: #fef2f2; }

    .aku-replies {
        margin-left: 36px; margin-top: 6px;
    }

    .aku-comment-input-wrap {
        display: flex; gap: 8px; margin-top: 10px; align-items: center;
    }
    .aku-comment-input {
        flex: 1; background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; color: var(--text-1); font-size: 12px;
        padding: 7px 16px; outline: none; font-family: 'DM Sans', sans-serif;
        transition: 0.2s;
    }
    .aku-comment-input:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .aku-comment-input::placeholder { color: var(--text-4); }
    .aku-comment-submit {
        padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 600;
        background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif; white-space: nowrap;
        box-shadow: 0 2px 8px var(--sky-glow);
    }
    .aku-comment-reply-btn {
        font-size: 10px; color: var(--sky); background: transparent;
        border: none; cursor: pointer; margin-left: 6px; font-weight: 500;
        transition: 0.15s;
    }
    .aku-comment-reply-btn:hover { color: var(--sky-dk); }

    .empty-aku {
        text-align: center; padding: 4rem 1rem;
        background: var(--card); border-radius: 20px; border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }
    .empty-aku p { font-size: 13px; color: var(--text-3); margin-top: 0.75rem; }

    /* WELCOME BANNER */
    .welcome-banner {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, var(--sky-lt) 0%, #fff 60%, var(--orange-lt) 100%);
        border: 1px solid var(--sky-mid); border-radius: 20px;
        padding: 1.25rem 1.25rem 1.25rem 1.5rem;
        margin-bottom: 1.5rem; box-shadow: var(--shadow);
        display: flex; align-items: flex-start; gap: 14px;
    }
    .welcome-banner::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--sky), var(--orange));
    }
    .welcome-banner-icon { font-size: 32px; flex-shrink: 0; line-height: 1; margin-top: 2px; }
    .welcome-banner-body { flex: 1; min-width: 0; }
    .welcome-banner-title {
        font-family: 'Sora', sans-serif;
        font-size: 14px; font-weight: 700; color: var(--text-1);
        margin-bottom: 4px;
    }
    .welcome-banner-sub {
        font-size: 12px; color: var(--text-3); line-height: 1.6;
    }
    .welcome-banner-close {
        position: absolute; top: 12px; right: 12px;
        width: 24px; height: 24px; border-radius: 50%;
        background: var(--surface); border: 1px solid var(--border);
        color: var(--text-4); font-size: 11px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s; flex-shrink: 0;
    }
    .welcome-banner-close:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }

    /* Dark theme adjustments */
    [data-theme="dark"] .admin-badge { background: var(--surface); color: var(--sky-mid); }
    [data-theme="dark"] .welcome-banner { background: linear-gradient(135deg, rgba(56,168,204,0.14) 0%, var(--surface) 60%, rgba(240,112,64,0.12) 100%); border-color: var(--sky-dk); }

    @media (prefers-reduced-motion: reduce) {
        .aku-post { animation: none !important; }
        .btn-post-aku::after { display: none; }
    }

    /* Guest interaction blocker overlay */
    .guest-blocker {
        position: relative;
    }
    .guest-blocker::after {
        content: '';
        position: absolute; inset: 0;
        background: rgba(6,8,15,0.03);
        cursor: default;
        border-radius: inherit;
        pointer-events: auto;
    }

    /* ══════════════════════════════════════════
       EMINOR INTRO OVERLAY — dark backdrop
       z-index stack: backdrop(9998) < intro(9999) < disc-badge(10000)
    ══════════════════════════════════════════ */
    #eminor-backdrop {
        position: fixed; inset: 0; z-index: 9998;
        background: rgba(2, 3, 7, 0.88);
        transition: opacity .9s ease;
    }
    #eminor-backdrop.hide { opacity: 0; pointer-events: none; }

    /* ── Intro animation container ── */
    #eminor-intro {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        pointer-events: none;
    }

    /* Vinyl record */
    .ei-vinyl {
        width: 76px; height: 76px; border-radius: 50%;
        background: conic-gradient(#0f0f0f 0deg,#1c1c1c 22deg,#090909 44deg,#151515 66deg,
            #0f0f0f 88deg,#090909 110deg,#1c1c1c 132deg,#0f0f0f 154deg,#111 176deg,
            #090909 198deg,#151515 220deg,#1c1c1c 242deg,#0f0f0f 264deg,#111 286deg,
            #090909 308deg,#1c1c1c 330deg,#0f0f0f 352deg,#111 360deg);
        animation: eiSpin 4s linear infinite;
        margin-bottom: 1.4rem;
        box-shadow: 0 0 28px rgba(56,168,204,.25), 0 0 60px rgba(56,168,204,.08);
        flex-shrink: 0;
    }
    .ei-vinyl::before {
        content: ''; position: absolute; inset: 36%;
        border-radius: 50%;
        background: radial-gradient(circle, #38A8CC 30%, #2186a8);
        box-shadow: 0 0 14px rgba(56,168,204,.7);
    }
    .ei-vinyl { position: relative; }
    @keyframes eiSpin { to { transform: rotate(360deg); } }

    /* Dot metro */
    .ei-metro {
        display: flex; gap: 8px; margin-bottom: 2rem;
        align-items: center; height: 24px; flex-shrink: 0;
    }
    .ei-dot {
        width: 5px; height: 5px; background: #38A8CC;
        border-radius: 50%; opacity: 0; transform: scale(0);
    }
    @keyframes eiDotPop {
        0%,100%{ opacity:0; transform:scale(0); }
        30%,70%{ opacity:1; transform:scale(1); }
    }

    /* Text area */
    .ei-text {
        text-align: center; height: 80px;
        display: flex; align-items: center; justify-content: center;
        padding: 0 2rem; flex-shrink: 0;
    }
    .ei-line {
        font-family: 'Sora', sans-serif;
        font-size: clamp(.9rem, 2.5vw, 1.15rem);
        font-weight: 300; color: rgba(255,255,255,.88);
        letter-spacing: .03em; opacity: 0;
        position: absolute; transition: opacity .65s ease;
        max-width: 520px; line-height: 1.5; text-align: center;
    }
    .ei-line.dim { font-size: clamp(.8rem, 1.8vw, .95rem); color: rgba(255,255,255,.4); letter-spacing: .12em; text-transform: uppercase; }
    .ei-line.big { font-size: clamp(1.5rem,3.5vw,2.2rem); font-weight: 800; color: #fff; letter-spacing: .12em; }
    .ei-line.big span { color: #38A8CC; }
    .ei-line.cyan { color: #38A8CC; }
    .ei-line.show { opacity: 1; }

    /* EQ bars */
    .ei-eq {
        display: flex; align-items: flex-end; gap: 3px;
        height: 18px; opacity: .55; margin-top: 1rem; flex-shrink: 0;
    }
    .ei-eqb {
        width: 3px; border-radius: 2px 2px 0 0;
        background: linear-gradient(to top, #38A8CC, #5B6EF5);
        animation: eiEqBar 1s ease-in-out infinite alternate;
    }
    .ei-eqb:nth-child(1){animation-duration:.78s}
    .ei-eqb:nth-child(2){animation-duration:1.12s;animation-delay:.1s}
    .ei-eqb:nth-child(3){animation-duration:.68s;animation-delay:.22s}
    .ei-eqb:nth-child(4){animation-duration:1.25s;animation-delay:.08s}
    .ei-eqb:nth-child(5){animation-duration:.9s;animation-delay:.35s}
    .ei-eqb:nth-child(6){animation-duration:1.05s;animation-delay:.15s}
    @keyframes eiEqBar { from{height:3px} to{height:16px} }

    /* ── Disclaimer badge ── */
    #eminor-disc {
        position: fixed; bottom: 2rem; left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        max-width: 330px; width: calc(100vw - 3rem);
        background: rgba(8, 14, 30, 0.82);
        border: 1px solid rgba(56,168,204,.28);
        border-radius: 16px; padding: 1.1rem 1.25rem;
        backdrop-filter: blur(16px);
        animation: discSlideUp .7s ease .3s backwards;
        transition: opacity .9s ease;
        pointer-events: none;
    }
    #eminor-disc.hide { opacity: 0; }
    @keyframes discSlideUp {
        from { opacity:0; transform: translateX(-50%) translateY(16px); }
        to   { opacity:1; transform: translateX(-50%) translateY(0); }
    }
    .disc-tag-pill {
        display: inline-block; font-size: 8px; font-weight: 800;
        letter-spacing: .15em; text-transform: uppercase;
        color: #38A8CC; background: rgba(56,168,204,.12);
        border: 1px solid rgba(56,168,204,.25);
        padding: 3px 10px; border-radius: 20px; margin-bottom: .65rem;
    }
    .disc-main-title {
        font-family: 'Sora', sans-serif; font-size: 1.1rem;
        font-weight: 800; letter-spacing: .07em; color: #fff;
        margin-bottom: .2rem; line-height: 1;
    }
    .disc-main-title span { color: #38A8CC; }
    .disc-sub { font-size: 10.5px; color: #94a3b8; line-height: 1.55; margin-bottom: .2rem; }
    .disc-sub strong { color: #38A8CC; font-weight: 700; }
    .disc-pwa { font-size: 10px; color: #4a5568; margin-bottom: .75rem; }
    .disc-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(56,168,204,.18), transparent); margin: .6rem 0; }
    .disc-support { font-size: 9.5px; color: #94a3b8; line-height: 1.85; }
    .disc-support strong { color: #38A8CC; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════
     EMINOR INTRO OVERLAY
     Muncul saat pertama kali buka halaman (sessionStorage check).
     Layer: backdrop(9998) < intro-anim(9999) < disc-badge(10000)
     Total durasi: 12 detik → semua fade out → fanbase visible.
══════════════════════════════════════════════════════ --}}
{{-- 1. Dark backdrop --}}
<div id="eminor-backdrop"></div>

{{-- 2. Intro animation (vinyl + teks loop 12s) --}}
<div id="eminor-intro">
    <div class="ei-metro">
        <div class="ei-dot" id="eid1"></div>
        <div class="ei-dot" id="eid2"></div>
        <div class="ei-dot" id="eid3"></div>
    </div>
    <div class="ei-vinyl"></div>
    <div class="ei-text">
        <div class="ei-line dim"  id="eil0">Dulu...</div>
        <div class="ei-line"      id="eil1">musisi membutuhkan label untuk didengar.</div>
        <div class="ei-line dim"  id="eil2">Sekarang...</div>
        <div class="ei-line"      id="eil3">yang dibutuhkan hanya tempat yang tepat.</div>
        <div class="ei-line big"  id="eil4">E<span>MINOR</span></div>
        <div class="ei-line"      id="eil5">Tidak semua musisi lahir di kota besar.</div>
        <div class="ei-line"      id="eil6">Tidak semua musisi punya studio.</div>
        <div class="ei-line"      id="eil7">Tidak semua musisi punya koneksi.</div>
        <div class="ei-line big"  id="eil8">Tetapi semua musisi<br>pantas didengar.</div>
        <div class="ei-line cyan" id="eil9">Ekosistem Musik Indie Indonesia.</div>
    </div>
    <div class="ei-eq" aria-hidden="true">
        <div class="ei-eqb"></div><div class="ei-eqb"></div><div class="ei-eqb"></div>
        <div class="ei-eqb"></div><div class="ei-eqb"></div><div class="ei-eqb"></div>
    </div>
</div>

{{-- 3. Disclaimer badge (foreground, selalu terlihat selama overlay aktif) --}}
<div id="eminor-disc">
    <div class="disc-tag-pill">Beta Edition</div>
    <p class="disc-main-title">E<span>MINOR</span></p>
    <p class="disc-sub">Menumpang di Project <strong>Margonoandi Fanbase</strong></p>
    <p class="disc-pwa">Sudah bisa di-download sebagai aplikasi (PWA)</p>
    <div class="disc-divider"></div>
    <p class="disc-support">
        Jika dukunganmu besar,<br>
        kami siap membuat <strong>rumah baru yang lebih layak</strong>
    </p>
</div>

{{-- ============================================================= --}}
{{-- LOGIN MODAL — muncul saat guest tekan action button --}}
{{-- ============================================================= --}}
@guest
<div id="mbg" style="display:none; position:fixed; inset:0; z-index:99990; background:rgba(4,6,14,0.85); backdrop-filter:blur(12px); align-items:center; justify-content:center; padding:1.5rem;">
    <div style="max-width:400px; width:100%; background:var(--card,#0c1120); border:1px solid rgba(56,168,204,0.2); border-radius:24px; padding:2.5rem 2rem; text-align:center; box-shadow:0 24px 64px rgba(0,0,0,0.7); position:relative;">
        <button onclick="document.getElementById('mbg').style.display='none'; document.body.style.overflow='';" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; color:var(--text-3,#7A9DB0); font-size:18px; cursor:pointer; line-height:1; padding:4px 8px;">&times;</button>
        <div style="font-size:2.5rem; margin-bottom:1rem;">🎵</div>
        <h3 style="font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:800; color:#fff; margin-bottom:.5rem;">Login untuk berinteraksi</h3>
        <p style="font-size:12.5px; color:var(--text-3,#7A9DB0); line-height:1.7; margin-bottom:1.75rem;">
            Kamu bisa menjelajahi EMINOR secara bebas.<br>
            Login untuk like, komentar, dan bergabung dengan ekosistem musisi indie Indonesia.
        </p>
        <a href="{{ route('google.login') }}" style="display:inline-flex; align-items:center; gap:10px; padding:13px 32px; border-radius:50px; background:linear-gradient(135deg,#38A8CC,#2186a8); color:#fff; font-size:13.5px; font-weight:700; text-decoration:none; box-shadow:0 6px 28px rgba(56,168,204,0.3);">
            <svg width="17" height="17" viewBox="0 0 18 18" fill="none"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
            Lanjutkan dengan Google
        </a>
        <div style="margin-top:.85rem; font-size:10.5px; color:var(--text-4,#5C7488);">Gratis · Tanpa kartu kredit</div>
    </div>
</div>
@endguest

{{-- ============================================================= --}}
{{-- KONTEN UTAMA — TERBUKA UNTUK SEMUA (guest bebas browsing) --}}
{{-- ============================================================= --}}

<div class="aku-page-header">
    <div>
        <div class="aku-page-title">🏠 Aku</div>
        <div class="aku-page-sub">
            @auth
            Catatan &amp; cerita dari Rakhman Andi
            @else
            Ekosistem Musik Indie Indonesia
            @endauth
        </div>
    </div>
    @auth
    @if(in_array(Auth::user()->email, config('admin.emails', [])))
    <div class="admin-badge">⭐ Admin</div>
    @endif
    @endauth
</div>

{{-- WELCOME BANNER (member baru, max 7 hari) --}}
@auth
@if($isNewMember ?? false)
<div class="welcome-banner" id="welcomeBanner">
    <div class="welcome-banner-icon">🎉</div>
    <div class="welcome-banner-body">
        <div class="welcome-banner-title">Selamat datang, {{ Auth::user()->name }}! ✨</div>
        <div class="welcome-banner-sub">
            Halo, member baru! Senang kamu sudah bergabung di fanbase Rakhman Andi.
            Di sini kamu bisa membaca catatan &amp; cerita, memberikan like, dan berkomentar. Selamat menikmati! 💛
        </div>
    </div>
    <button class="welcome-banner-close" onclick="dismissWelcome()" title="Tutup">✕</button>
</div>
@endif
@endauth

{{-- ADMIN FORM — HANYA UNTUK ADMIN --}}
@auth
@if(in_array(Auth::user()->email, config('admin.emails', [])))
<div class="aku-form">
    <span class="aku-form-label">Tulis sesuatu untuk fanbase</span>
    <form method="POST" action="{{ route('aku.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" class="aku-form-input" placeholder="Judul (opsional)...">
        <textarea name="body" class="aku-form-textarea"
            placeholder="Ceritakan sesuatu kepada para fans..." required></textarea>
        <div class="aku-form-footer">
            <div class="aku-form-tools">
                <label class="aku-tool-btn">
                    📷 Foto
                    <input type="file" name="image" accept="image/*" style="display:none;">
                </label>
                <input type="text" name="mood" class="aku-mood-input" placeholder="💛 Mood...">
            </div>
            <button type="submit" class="btn-post-aku">🚀 Posting</button>
        </div>
    </form>
</div>
@endif
@endauth

{{-- POSTS --}}
@if($posts->count() > 0)
    @foreach($posts as $post)
    <div class="aku-post {{ $post->is_pinned ? 'pinned' : '' }}" id="akuPost{{ $post->id }}" style="animation-delay: {{ min($loop->index * 0.05, 0.4) }}s">

        <div class="aku-post-header">
            <img src="{{ $post->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                 class="aku-post-avatar" alt="">
            <div class="aku-post-meta">
                <div class="aku-post-name">
                    {{ $post->user->name }}
                    @if($post->user->email && in_array($post->user->email, config('admin.emails', [])))
                    <span class="aku-admin-tag">⭐ Admin</span>
                    @endif
                </div>
                <div class="aku-post-date">
                    {{ $post->created_at->format('d M Y') }} · {{ $post->created_at->format('H:i') }}
                </div>
            </div>
            @auth
            <div class="aku-post-top-actions">
                @if($post->is_pinned)
                <span class="aku-pin-badge">📌</span>
                @endif
                @if(in_array(Auth::user()->email, config('admin.emails', [])))
                <button class="aku-top-btn aku-edit-btn" title="Edit"
                        onclick="akuEditPost({{ $post->id }})">✏️</button>
                <form method="POST" action="{{ route('aku.destroy', $post->id) }}"
                      onsubmit="return confirm('Hapus postingan ini?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="aku-top-btn" title="Hapus">✕</button>
                </form>
                @endif
            </div>
            @endauth
        </div>

        @if($post->title)
        <div class="aku-post-title">{{ $post->title }}</div>
        @endif

        <div class="aku-post-body" id="akuPostBody{{ $post->id }}">{{ $post->body }}</div>
        <textarea class="aku-post-body-edit" id="akuPostEdit{{ $post->id }}">{{ $post->body }}</textarea>
        <div class="aku-post-edit-actions" id="akuEditActions{{ $post->id }}">
            <button class="aku-save-btn" onclick="akuSavePost({{ $post->id }})">Simpan</button>
            <button class="aku-cancel-btn" onclick="akuCancelEdit({{ $post->id }})">Batal</button>
        </div>

        @if($post->image)
        <img src="{{ asset($post->image) }}" class="aku-post-image" alt="">
        @endif

        @if($post->mood)
        <span class="aku-post-mood">💛 {{ $post->mood }}</span>
        @endif

        <div class="aku-post-footer">
            <div class="aku-like-wrap">
                <button class="aku-action-btn {{ in_array($post->id, $likedIds) ? 'liked' : '' }}"
                        id="akuLike{{ $post->id }}"
                        data-liked="{{ in_array($post->id, $likedIds) ? '1' : '0' }}"
                        onclick="akuToggleLike({{ $post->id }})">
                    <span class="like-icon">{{ in_array($post->id, $likedIds) ? '♥' : '♡' }}</span>
                </button>
                <span id="akuLikeCount{{ $post->id }}"
                      class="like-count-btn"
                      onclick="akuToggleLikers({{ $post->id }}, event)">{{ $post->likes_count }}</span>
                <div class="likers-tooltip" id="akuLikers{{ $post->id }}"
                     data-likers="{{ json_encode(($likersByPost[$post->id] ?? collect())->values()->toArray()) }}"></div>
            </div>
            <button class="aku-action-btn" onclick="akuToggleComments({{ $post->id }})">
                <span>💬</span>
                <span id="akuCommentCount{{ $post->id }}">{{ $post->comments_count }}</span>
            </button>
        </div>

        {{-- COMMENTS --}}
        <div class="aku-comments" id="akuComments{{ $post->id }}">
            <div id="akuCommentsList{{ $post->id }}">
                @foreach($post->comments as $comment)
                <div class="aku-comment-item" id="akuComment{{ $comment->id }}">
                    <img src="{{ $comment->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                         class="aku-comment-avatar" alt="">
                    <div style="flex:1;">
                        <div class="aku-comment-bubble">
                            <div class="aku-comment-header">
                                <span class="aku-comment-name">{{ $comment->user->name }}</span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span class="aku-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    @auth
                                    @if(Auth::id() === $comment->user_id || in_array(Auth::user()->email, config('admin.emails', [])))
                                    <button class="aku-comment-delete"
                                            onclick="akuDeleteComment({{ $post->id }}, {{ $comment->id }})">✕</button>
                                    @endif
                                    @endauth
                                </div>
                            </div>
                            <div class="aku-comment-body">{{ $comment->body }}</div>
                        </div>
                        @auth
                        <button class="aku-comment-reply-btn"
                                onclick="akuSetReply({{ $post->id }}, {{ $comment->id }}, '{{ addslashes($comment->user->name) }}')">
                            Balas
                        </button>
                        @endauth
                        @if($comment->replies->count() > 0)
                        <div class="aku-replies">
                            @foreach($comment->replies as $reply)
                            <div class="aku-comment-item" id="akuComment{{ $reply->id }}" style="margin-bottom:6px;">
                                <img src="{{ $reply->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                                     class="aku-comment-avatar" style="width:24px;height:24px;" alt="">
                                <div class="aku-comment-bubble">
                                    <div class="aku-comment-header">
                                        <span class="aku-comment-name">{{ $reply->user->name }}</span>
                                        @auth
                                        @if(Auth::id() === $reply->user_id || in_array(Auth::user()->email, config('admin.emails', [])))
                                        <button class="aku-comment-delete"
                                                onclick="akuDeleteComment({{ $post->id }}, {{ $reply->id }})">✕</button>
                                        @endif
                                        @endauth
                                    </div>
                                    <div class="aku-comment-body">{{ $reply->body }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="aku-comment-input-wrap">
                @auth
                <img src="{{ Auth::user()->avatar }}" class="aku-comment-avatar" alt="">
                <input type="text" class="aku-comment-input"
                       id="akuInput{{ $post->id }}"
                       placeholder="Tulis komentar..."
                       onkeydown="if(event.key==='Enter'){akuSubmitComment({{ $post->id }});return false;}">
                <input type="hidden" id="akuParent{{ $post->id }}" value="">
                <button class="aku-comment-submit" onclick="akuSubmitComment({{ $post->id }})">Kirim</button>
                @else
                <div style="flex:1; padding:7px 16px; background:var(--surface); border-radius:20px; color:var(--text-4); font-size:12px; border:1px dashed var(--border); text-align:center;">
                    <a href="{{ route('google.login') }}" style="color:var(--sky); font-weight:600; text-decoration:none;">Login</a> untuk berkomentar
                </div>
                @endauth
            </div>
        </div>

    </div>
    @endforeach

    @if($posts->hasPages())
    <div style="display:flex;justify-content:center;gap:8px;margin-top:1.5rem;">
        @if(!$posts->onFirstPage())
        <a href="{{ $posts->previousPageUrl() }}"
           style="padding:8px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:12px;text-decoration:none;background:var(--card);font-weight:500;">
            ← Sebelumnya
        </a>
        @endif
        @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}"
           style="padding:8px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:12px;text-decoration:none;background:var(--card);font-weight:500;">
            Berikutnya →
        </a>
        @endif
    </div>
    @endif

@else
<div class="empty-aku">
    <div style="font-size:42px;">✏️</div>
    <p>Belum ada postingan dari Rakhman Andi.</p>
</div>
@endif


@endsection

@push('scripts')
<script>
var BASE_URL   = '{{ url("") }}';
var csrfToken  = '{{ csrf_token() }}';
var isGuest    = {{ Auth::guest() ? 'true' : 'false' }};

// ══════════════════════════════════════════════════════
// EMINOR INTRO OVERLAY — 12 detik
// Struktur layer: backdrop(9998) < intro(9999) < disc-badge(10000)
// ══════════════════════════════════════════════════════
(function () {
    var TOTAL_MS   = 12000;  // total durasi overlay (ubah 10000–15000)
    var FADE_MS    = 900;    // durasi fade-out

    var SESS_KEY = 'eminor_seen_intro';

    // Returning visitor — skip semua overlay
    if (sessionStorage.getItem(SESS_KEY)) {
        ['eminor-backdrop','eminor-intro','eminor-disc'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
        return;
    }

    // ── Helper: fade out semua overlay
    function hideAll() {
        ['eminor-backdrop','eminor-intro','eminor-disc'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) {
                el.classList.add('hide');
                setTimeout(function(){ el.style.display = 'none'; }, FADE_MS);
            }
        });
        sessionStorage.setItem(SESS_KEY, '1');
    }

    // Jadwalkan hide setelah TOTAL_MS
    setTimeout(hideAll, TOTAL_MS - FADE_MS);

    // ── Dot metro animasi
    var di = 0;
    var dots = [
        document.getElementById('eid1'),
        document.getElementById('eid2'),
        document.getElementById('eid3')
    ];
    function popDot() {
        var d = dots[di % 3]; di++;
        if (!d) return;
        d.style.animation = 'none';
        d.offsetHeight; // reflow
        d.style.animation = 'eiDotPop .4s ease forwards';
    }

    // ── Audio tick
    var AC = window.AudioContext || window.webkitAudioContext;
    function tick() {
        if (!AC) return;
        try {
            var c = new AC(), o = c.createOscillator(), g = c.createGain();
            o.connect(g); g.connect(c.destination);
            o.frequency.value = 900; o.type = 'sine';
            g.gain.setValueAtTime(.1, c.currentTime);
            g.gain.exponentialRampToValueAtTime(.001, c.currentTime + .09);
            o.start(); o.stop(c.currentTime + .11);
            setTimeout(function(){ c.close(); }, 200);
        } catch(e){}
    }

    // ── Tampilkan / sembunyikan line
    var activeLineId = null;
    function showLine(id) {
        if (activeLineId) {
            var prev = document.getElementById(activeLineId);
            if (prev) prev.classList.remove('show');
        }
        activeLineId = id;
        var el = document.getElementById(id);
        if (el) el.classList.add('show');
    }
    function hideLine(id) {
        var el = document.getElementById(id);
        if (el) el.classList.remove('show');
        if (activeLineId === id) activeLineId = null;
    }

    // ── Sequence timeline (total ≈ 12 detik)
    // Format: [delay_ms, fn]
    var seq = [
        [0,    function(){ tick(); popDot(); }],
        [400,  function(){ tick(); popDot(); }],
        [800,  function(){ tick(); popDot(); }],

        // Phase 1 — intro klasik (0–5.5 detik)
        [1100, function(){ showLine('eil0'); }],                         // "Dulu..."
        [1700, function(){ showLine('eil1'); }],                         // "musisi butuh label"
        [2900, function(){ hideLine('eil0'); hideLine('eil1'); showLine('eil2'); }], // "Sekarang..."
        [3500, function(){ showLine('eil3'); }],                         // "yang dibutuhkan..."
        [4600, function(){ hideLine('eil2'); hideLine('eil3'); showLine('eil4'); }], // EMINOR
        [5500, function(){ hideLine('eil4'); }],

        // Phase 2 — hero text loop (5.5–11 detik)
        [5700, function(){ showLine('eil5'); }],                         // "Tidak semua... kota besar"
        [7100, function(){ hideLine('eil5'); showLine('eil6'); }],       // "...punya studio"
        [8300, function(){ hideLine('eil6'); showLine('eil7'); }],       // "...punya koneksi"
        [9500, function(){ hideLine('eil7'); showLine('eil8'); }],       // "Tetapi semua musisi pantas didengar"
        [11000,function(){ hideLine('eil8'); showLine('eil9'); }],       // "Ekosistem Musik Indie Indonesia"
        [11800,function(){ hideLine('eil9'); }],
    ];

    seq.forEach(function(s){ setTimeout(s[1], s[0]); });

})();

// ══════════════════════════════════════════════════════
// AUTH GATE — requireLogin()
// ══════════════════════════════════════════════════════
window.APP_LOGGED_IN = {{ auth()->check() ? 'true' : 'false' }};

function requireLogin(redirectTo) {
    if (window.APP_LOGGED_IN) {
        if (redirectTo) window.location.href = redirectTo;
    } else {
        var mbg = document.getElementById('mbg');
        if (mbg) {
            mbg.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            window.location.href = '{{ route("google.login") }}';
        }
    }
}

/* ================================================================
   GUEST BLOCKER — intercept semua interaksi, arahkan ke login modal
   ================================================================ */
@guest
function guestBlock() {
    requireLogin();
    return false;
}

// Override semua fungsi interaksi untuk guest
window.akuToggleLike = function(postId) { guestBlock(); };
window.akuSubmitComment = function(postId) { guestBlock(); };
window.akuSetReply = function(postId, commentId, name) { guestBlock(); };
window.akuDeleteComment = function(postId, commentId) { guestBlock(); };
window.akuToggleLikers = function(postId, evt) { guestBlock(); };

// Cegah klik di tombol comment submit
document.addEventListener('click', function(e) {
    var target = e.target.closest('.aku-comment-submit, .aku-comment-input, .aku-action-btn, .aku-comment-reply-btn');
    if (target) {
        e.preventDefault();
        e.stopPropagation();
        guestBlock();
        return false;
    }
}, true);
@endguest

/* ================================================================
   WELCOME BANNER
   ================================================================ */
(function(){
    var uid = '{{ Auth::id() }}';
    if(!uid) return;
    var key = 'welcome_dismissed_' + uid;
    var banner = document.getElementById('welcomeBanner');
    if(banner && localStorage.getItem(key)) banner.style.display = 'none';
})();

function dismissWelcome(){
    var uid = '{{ Auth::id() }}';
    if(!uid) return;
    var key = 'welcome_dismissed_' + uid;
    localStorage.setItem(key, '1');
    var banner = document.getElementById('welcomeBanner');
    if(banner){ banner.style.opacity='0'; banner.style.transition='opacity 0.3s'; setTimeout(function(){ banner.style.display='none'; }, 300); }
}

/* ================================================================
   LIKE
   ================================================================ */
function akuToggleLike(postId) {
    if (isGuest) return guestBlock();

    fetch(BASE_URL + '/aku/' + postId + '/like', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        var btn     = document.getElementById('akuLike'+postId);
        var count   = document.getElementById('akuLikeCount'+postId);
        var tooltip = document.getElementById('akuLikers'+postId);
        if(!btn||!count)return;
        count.textContent = d.likes_count;
        btn.classList.toggle('liked', d.liked);
        btn.dataset.liked = d.liked?'1':'0';
        var icon = btn.querySelector('.like-icon');
        if(icon) icon.textContent = d.liked ? '♥' : '♡';
        if(tooltip && d.likers) tooltip.dataset.likers = JSON.stringify(d.likers);
    });
}

function akuToggleLikers(postId, evt) {
    if (isGuest) return guestBlock();

    evt.stopPropagation();
    var tooltip = document.getElementById('akuLikers'+postId);
    if(!tooltip) return;
    var isOpen = tooltip.classList.contains('open');
    document.querySelectorAll('.likers-tooltip.open').forEach(function(t){t.classList.remove('open');});
    if(isOpen) return;
    var likers = [];
    try { likers = JSON.parse(tooltip.dataset.likers||'[]'); } catch(e){}
    if(likers.length === 0) {
        tooltip.innerHTML = '<div class="likers-tooltip-empty">Belum ada yang suka</div>';
    } else {
        tooltip.innerHTML = '<div class="likers-tooltip-title">Disukai oleh</div>' +
            likers.map(function(l){
                return '<div class="likers-tooltip-item">' +
                    (l.avatar ? '<img src="'+escHtml(l.avatar)+'" alt="">' : '<div style="width:20px;height:20px;border-radius:50%;background:var(--sky-lt);flex-shrink:0"></div>') +
                    '<span>'+escHtml(l.name)+'</span></div>';
            }).join('');
    }
    tooltip.classList.add('open');
}

document.addEventListener('click', function(){
    document.querySelectorAll('.likers-tooltip.open').forEach(function(t){t.classList.remove('open');});
});

/* ================================================================
   COMMENTS
   ================================================================ */
function akuToggleComments(postId) {
    if (isGuest) return guestBlock();

    var el = document.getElementById('akuComments'+postId);
    if(el) el.classList.toggle('open');
}

function akuSetReply(postId, commentId, name) {
    if (isGuest) return guestBlock();

    var input  = document.getElementById('akuInput'+postId);
    var parent = document.getElementById('akuParent'+postId);
    if(input)  { input.placeholder='Membalas '+name+'...'; input.focus(); }
    if(parent) parent.value = commentId;
}

function akuSubmitComment(postId) {
    if (isGuest) return guestBlock();

    var input  = document.getElementById('akuInput'+postId);
    var parent = document.getElementById('akuParent'+postId);
    var body   = input ? input.value.trim() : '';
    if(!body) return;

    fetch(BASE_URL + '/aku/' + postId + '/comment', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body, parent_id: parent?parent.value:null})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(!d.success)return;
        var list = document.getElementById('akuCommentsList'+postId);
        if(list){
            var html='<div class="aku-comment-item" id="akuComment'+d.comment.id+'">'+
                '<img src="'+d.comment.avatar+'" class="aku-comment-avatar" alt="">'+
                '<div style="flex:1;"><div class="aku-comment-bubble">'+
                '<div class="aku-comment-header">'+
                '<span class="aku-comment-name">'+escHtml(d.comment.user)+'</span>'+
                '<span class="aku-comment-time">Baru saja</span></div>'+
                '<div class="aku-comment-body">'+escHtml(d.comment.body)+'</div>'+
                '</div></div></div>';
            list.insertAdjacentHTML('beforeend', html);
        }
        var cnt = document.getElementById('akuCommentCount'+postId);
        if(cnt) cnt.textContent = parseInt(cnt.textContent||0)+1;
        if(input)  {input.value=''; input.placeholder='Tulis komentar...';}
        if(parent) parent.value='';
    });
}

function akuDeleteComment(postId, commentId) {
    if (isGuest) return guestBlock();

    if(!confirm('Hapus komentar ini?'))return;
    fetch(BASE_URL + '/aku/' + postId + '/comment/' + commentId, {
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'}
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d.success){
            var el=document.getElementById('akuComment'+commentId);
            if(el) el.remove();
            var cnt=document.getElementById('akuCommentCount'+postId);
            if(cnt) cnt.textContent=Math.max(0,parseInt(cnt.textContent||0)-1);
        }
    });
}

/* ================================================================
   EDIT POST (Admin)
   ================================================================ */
function akuEditPost(id) {
    var body    = document.getElementById('akuPostBody'+id);
    var edit    = document.getElementById('akuPostEdit'+id);
    var actions = document.getElementById('akuEditActions'+id);
    if(body)    body.style.display    = 'none';
    if(edit)    edit.style.display    = 'block';
    if(actions) actions.style.display = 'flex';
}

function akuCancelEdit(id) {
    var body    = document.getElementById('akuPostBody'+id);
    var edit    = document.getElementById('akuPostEdit'+id);
    var actions = document.getElementById('akuEditActions'+id);
    if(body)    body.style.display    = 'block';
    if(edit)    edit.style.display    = 'none';
    if(actions) actions.style.display = 'none';
}

function akuSavePost(id) {
    var edit = document.getElementById('akuPostEdit'+id);
    var body = edit ? edit.value.trim() : '';
    if(!body) return;

    fetch(BASE_URL + '/aku/' + id, {
        method:'PUT',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(!d.success)return;
        var bodyEl = document.getElementById('akuPostBody'+id);
        if(bodyEl) bodyEl.textContent = body;
        akuCancelEdit(id);
    });
}

/* ================================================================
   UTILITY
   ================================================================ */
function escHtml(t){
    var d=document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}
</script>
@endpush