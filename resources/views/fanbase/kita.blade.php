@extends('layouts.fanbase')
@section('title', 'Stage')

@push('styles')
<style>
    /* ── INTRO OVERLAY ── */
    #eminor-backdrop { position:fixed;inset:0;background:rgba(8,14,30,.92);z-index:9998;opacity:1;transition:opacity .6s ease; }
    #eminor-backdrop.hide { opacity:0;pointer-events:none; }
    #eminor-intro {
        position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;
        align-items:center;justify-content:center;padding:2rem;
    }
    .ei-metro { display:flex;gap:6px;margin-bottom:1.5rem; }
    .ei-dot { width:10px;height:10px;border-radius:50%;background:var(--sky);opacity:0;transition:opacity .3s ease; }
    .ei-dot.on { opacity:1; }
    .ei-vinyl {
        width:140px;height:140px;border-radius:50%;
        background:conic-gradient(#0b1f2e 0deg,#1a3a4a 60deg,#0b1f2e 120deg,#1a3a4a 180deg,#0b1f2e 240deg,#1a3a4a 300deg,#0b1f2e 360deg);
        border:3px solid rgba(56,168,204,.3);display:flex;align-items:center;justify-content:center;
        opacity:0;transition:opacity .7s ease;flex-shrink:0;
    }
    .ei-vinyl::after { content:'';width:36px;height:36px;border-radius:50%;background:#0b1520;border:2px solid rgba(56,168,204,.4); }
    .ei-vinyl.show { opacity:1;animation:eiSpin 3s linear infinite; }
    @keyframes eiSpin { to { transform:rotate(360deg); } }
    .ei-text { position:relative;height:70px;margin-top:1.5rem;text-align:center;width:100%;max-width:360px; }
    .ei-line { position:absolute;left:0;right:0;font-size:1rem;font-weight:600;color:#fff;opacity:0;transform:translateY(8px);transition:opacity .5s ease,transform .5s ease; }
    .ei-line.show { opacity:1;transform:translateY(0); }
    .ei-line.dim { color:rgba(255,255,255,.45);font-size:.85rem;font-weight:400; }
    .ei-line.big { font-size:2rem;font-weight:800;letter-spacing:.08em;color:#fff; }
    .ei-line.big span { color:var(--sky); }
    .ei-eq { display:flex;gap:3px;align-items:flex-end;height:28px;margin-top:1.5rem;opacity:0;transition:opacity .5s ease; }
    .ei-eq.show { opacity:1; }
    .ei-eqb { width:4px;border-radius:3px;background:var(--sky);animation:eiEq 1s ease-in-out infinite alternate; }
    .ei-eqb:nth-child(1){height:8px;animation-delay:0s}
    .ei-eqb:nth-child(2){height:18px;animation-delay:.1s}
    .ei-eqb:nth-child(3){height:12px;animation-delay:.2s}
    .ei-eqb:nth-child(4){height:22px;animation-delay:.05s}
    .ei-eqb:nth-child(5){height:14px;animation-delay:.15s}
    .ei-eqb:nth-child(6){height:20px;animation-delay:.25s}
    @keyframes eiEq { to { height:6px;opacity:.4; } }
    #ei-disc { opacity:0;transition:opacity .7s ease;max-width:360px;width:calc(100vw - 3rem);margin-top:1.75rem;flex-shrink:0; }
    #ei-disc.show { opacity:1; }
    #ei-disc .fb-beta { background:rgba(8,14,30,.78);border:1px solid rgba(56,168,204,.28);border-radius:14px;padding:1rem 1.25rem;backdrop-filter:blur(14px);font-size:13px;color:rgba(255,255,255,.82);line-height:1.7;text-align:left;margin-top:0 !important; }
    #ei-disc .fb-beta b { color:#38A8CC;font-weight:700; }
    #ei-disc .bic { font-size:20px;display:block;margin-bottom:.5rem; }

    /* PAGE HEADER */
    .kita-page-header {
        margin-bottom: 1.25rem;
    }
    .kita-page-title {
        font-family: 'Sora', sans-serif;
        font-size: 1.1rem; font-weight: 700; color: var(--text-1);
    }
    .kita-page-sub { font-size: 12px; color: var(--text-3); margin-top: 3px; }

    /* POST FORM */
    .kita-form {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; padding: 1.25rem; margin-bottom: 1.25rem;
        box-shadow: var(--shadow); position: relative; overflow: hidden;
    }
    .kita-form::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--orange), var(--sky));
    }
    .kita-form-header {
        display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
    }
    .kita-form-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--sky-lt); flex-shrink: 0;
    }
    .kita-form-name { font-size: 13px; font-weight: 500; color: var(--text-2); }
    .kita-textarea {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 12px; color: var(--text-1); font-size: 14px;
        padding: 10px 14px; outline: none; resize: none;
        min-height: 90px; line-height: 1.7; font-family: 'DM Sans', sans-serif;
        transition: 0.2s;
    }
    .kita-textarea:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .kita-textarea::placeholder { color: var(--text-4); }
    .kita-form-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 10px; flex-wrap: wrap; gap: 8px;
    }
    .kita-form-left { display: flex; align-items: center; gap: 10px; }
    .kita-loc-btn {
        display: flex; align-items: center; gap: 5px;
        padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 500;
        border: 1px solid var(--border); background: var(--surface);
        color: var(--text-3); cursor: pointer; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .kita-loc-btn:hover { background: var(--sky-lt); color: var(--sky-dk); border-color: var(--sky-mid); }
    .kita-char-count { font-size: 11px; color: var(--text-4); font-weight: 500; }
    .kita-char-count.warn { color: var(--orange); }
    .btn-post-kita {
        padding: 8px 24px; border-radius: 20px; font-size: 12px; font-weight: 600;
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dk) 100%);
        color: #fff; border: none; cursor: pointer; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 4px 12px var(--orange-glow);
    }
    .btn-post-kita:hover { transform: translateY(-1px); box-shadow: var(--shadow); }
    .kita-location-input {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 10px; color: var(--sky-dk); font-size: 12px;
        padding: 7px 14px; outline: none; font-family: 'DM Sans', sans-serif;
        margin-top: 8px; display: none; transition: 0.2s;
    }
    .kita-location-input.show { display: block; }
    .kita-location-input:focus { border-color: var(--sky); }
    .kita-loc-chip { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: var(--sky-dk); background: var(--sky-lt); border: 1px solid var(--border-lt); border-radius: 20px; padding: 3px 6px 3px 10px; }
    .kita-loc-x { border: none; background: rgba(0,0,0,0.06); color: var(--sky-dk); width: 16px; height: 16px; border-radius: 50%; cursor: pointer; font-size: 10px; line-height: 1; padding: 0; }
    .kita-loc-x:hover { background: rgba(0,0,0,0.14); }
    .kita-loc-status { font-size: 12px; color: var(--text-3); }

    /* POST CARD */
    .kita-post {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; margin-bottom: 1rem; overflow: hidden;
        box-shadow: var(--shadow-sm); transition: 0.2s;
    }
    .kita-post:hover { border-color: var(--sky-mid); box-shadow: var(--shadow); }

    .kita-post-header {
        display: flex; align-items: center; gap: 10px;
        padding: 0.875rem 1rem 0.5rem;
    }
    .kita-post-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--sky-lt); flex-shrink: 0;
        box-shadow: var(--shadow-sm);
    }
    .kita-post-meta { flex: 1; min-width: 0; }
    .kita-post-name {
        font-family: 'Sora', sans-serif;
        font-size: 13px; font-weight: 600; color: var(--text-1);
        display: inline-flex; align-items: center; gap: 5px;
    }
    .mus-badge { border: none; background: var(--sky-lt); color: var(--sky-dk); cursor: pointer; font-size: 11px; line-height: 1; padding: 2px 5px; border-radius: 20px; transition: 0.15s; }
    .mus-badge:hover { transform: scale(1.15); }
    .mus-badge.lv-pemula { background: #facc15; color: #5a3e00; }
    .mus-badge.lv-menengah { background: #38bdf8; color: #062b3a; }
    .mus-badge.lv-mahir { background: #4ade80; color: #053a1a; }
    .mus-badge.lv-profesional { background: #c084fc; color: #2e0a4a; }
    .kita-post-date { font-size: 11px; color: var(--text-4); margin-top: 2px; }

    /* POPUP MUSISI */
    .mc-overlay { position: fixed; inset: 0; background: rgba(10,20,30,0.55); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 1rem; }
    .mc-overlay.open { display: flex; }
    .mc-card { background: var(--card); border-radius: 20px; width: 100%; max-width: 340px; box-shadow: var(--shadow-xl); overflow: hidden; animation: mcIn 0.18s ease; }
    @keyframes mcIn { from { transform: translateY(12px); opacity: 0; } to { transform: none; opacity: 1; } }
    .mc-banner { height: 64px; background: linear-gradient(135deg, var(--sky-dk), var(--sky-mid)); }
    .mc-body { padding: 0 1.25rem 1.25rem; margin-top: -34px; text-align: center; }
    .mc-avatar { width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 3px solid var(--card); margin: 0 auto; display: block; background: var(--surface); }
    .mc-name { font-family: 'Sora',sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--text-1); margin-top: 8px; }
    .mc-sub { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .mc-net { font-size: 11px; color: var(--sky-dk); background: var(--sky-lt); border-radius: 20px; padding: 2px 10px; display: inline-block; margin-top: 5px; }
    .mc-tags { display: flex; flex-wrap: wrap; gap: 5px; justify-content: center; margin: 10px 0; }
    .mc-badge { font-size: 11px; padding: 2px 9px; border-radius: 20px; background: var(--surface); color: var(--sky-dk); border: 1px solid var(--border-lt); }
    .mc-badge.lv-pemula { background: #facc15; color: #5a3e00; border: none; font-weight: 700; }
    .mc-badge.lv-menengah { background: #38bdf8; color: #062b3a; border: none; font-weight: 700; }
    .mc-badge.lv-mahir { background: #4ade80; color: #053a1a; border: none; font-weight: 700; }
    .mc-badge.lv-profesional { background: #c084fc; color: #2e0a4a; border: none; font-weight: 700; }
    .mc-look { font-size: 12px; color: var(--text-2); margin: 6px 0 2px; }
    .mc-followers { font-size: 12px; color: var(--text-3); margin-bottom: 10px; }
    .mc-actions { display: flex; gap: 8px; }
    .mc-btn { flex: 1; padding: 9px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; text-align: center; }
    .mc-btn-follow { background: var(--sky); color: #fff; }
    .mc-btn-follow.following { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
    .mc-btn-ghost { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
    .mc-close { position: absolute; }
    .kita-post-location {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 10px; color: var(--sky-dk); background: var(--sky-lt);
        border-radius: 10px; padding: 1px 8px; margin-top: 2px;
        font-weight: 500;
    }

    .kita-post-actions { display: flex; gap: 5px; }
    .kita-action-top-btn {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--surface); border: 1px solid var(--border);
        color: var(--text-4); font-size: 11px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .kita-action-top-btn:hover { background: var(--sky-lt); color: var(--sky-dk); border-color: var(--sky-mid); }
    .kita-action-top-btn.del:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }

    .kita-post-body {
        font-size: 14px; color: var(--text-2); line-height: 1.8;
        padding: 0 1rem 0.875rem; word-break: break-word; white-space: pre-wrap;
    }
    .kita-post-body-edit {
        width: calc(100% - 2rem); background: var(--cream);
        border: 1px solid var(--sky); border-radius: 10px;
        color: var(--text-1); font-size: 14px; padding: 10px 14px;
        outline: none; resize: vertical; min-height: 70px;
        line-height: 1.7; font-family: 'DM Sans', sans-serif;
        margin: 0 1rem 8px; display: none;
        box-shadow: 0 0 0 3px var(--sky-glow);
    }
    .kita-edit-actions {
        display: none; gap: 8px; padding: 0 1rem 0.75rem;
    }
    .kita-save-btn {
        padding: 6px 18px; border-radius: 16px; font-size: 12px; font-weight: 600;
        background: var(--sky); color: #fff; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .kita-cancel-edit-btn {
        padding: 6px 16px; border-radius: 16px; font-size: 12px;
        background: transparent; border: 1px solid var(--border);
        color: var(--text-3); cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }

    /* POST FOOTER */
    .kita-post-footer {
        display: flex; align-items: center; gap: 14px;
        padding: 0.75rem 1rem; border-top: 1px solid var(--border-lt);
    }
    .kita-action-btn {
        display: flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500; color: var(--text-4);
        background: transparent; border: none; cursor: pointer;
        transition: 0.2s; padding: 4px 10px; border-radius: 20px;
        font-family: 'DM Sans', sans-serif;
    }
    .kita-action-btn:hover { background: var(--sky-lt); color: var(--sky-dk); }
    .kita-action-btn.liked { color: #ef4444; }
    .kita-action-btn.liked:hover { background: #fef2f2; }
    .like-icon { font-size: 14px; }
    .kita-like-wrap { position: relative; display: inline-flex; align-items: center; gap: 2px; }
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
    .kita-comments {
        padding: 0.75rem 1rem; border-top: 1px solid var(--border-lt);
        display: none; background: var(--cream);
    }
    .kita-comments.open { display: block; }
    .kita-comment-item {
        display: flex; gap: 8px; margin-bottom: 10px;
    }
    .kita-comment-avatar {
        width: 28px; height: 28px; border-radius: 50%;
        object-fit: cover; background: var(--surface); flex-shrink: 0;
        border: 1.5px solid var(--border);
    }
    .kita-comment-bubble {
        background: var(--card); border-radius: 12px;
        padding: 8px 12px; flex: 1; border: 1px solid var(--border-lt);
        box-shadow: var(--shadow-sm);
    }
    .kita-comment-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 3px;
    }
    .kita-comment-name { font-size: 11px; font-weight: 600; color: var(--text-2); }
    .kita-comment-time { font-size: 10px; color: var(--text-4); }
    .kita-comment-body { font-size: 13px; color: var(--text-2); line-height: 1.5; }
    .kita-comment-delete {
        background: transparent; border: none; color: var(--text-4);
        font-size: 10px; cursor: pointer; padding: 2px 4px;
        border-radius: 4px; transition: 0.15s;
    }
    .kita-comment-delete:hover { color: #ef4444; background: #fef2f2; }
    .kita-comment-footer {
        display: flex; align-items: center; gap: 10px; margin-top: 5px;
    }
    .kc-action {
        background: transparent; border: none; font-size: 11px; cursor: pointer;
        color: var(--text-3); padding: 2px 6px; border-radius: 6px;
        font-family: 'DM Sans', sans-serif; transition: 0.15s; display: flex; align-items: center; gap: 3px;
    }
    .kc-action:hover { background: var(--sky-lt); color: var(--sky-dk); }
    .kc-action.liked { color: #e11d48; }
    .kc-action.liked:hover { color: #e11d48; background: #fff1f2; }

    /* dark: hover "bahaya" jadi translucent biar tak menyilaukan */
    [data-theme="dark"] .kita-action-top-btn.del:hover,
    [data-theme="dark"] .kita-action-btn.liked:hover,
    [data-theme="dark"] .kita-comment-delete:hover,
    [data-theme="dark"] .kc-action.liked:hover { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.35); }

    .kita-reply-item {
        display: flex; gap: 7px; margin-top: 6px; padding-left: 14px;
        border-left: 2px solid var(--border-lt);
    }
    .kita-reply-item .kita-comment-avatar { width: 22px; height: 22px; }
    .kita-reply-item .kita-comment-bubble {
        padding: 6px 10px; background: var(--cream);
    }
    .kita-reply-wrap {
        display: none; margin-top: 6px; padding-top: 6px;
        border-top: 1px solid var(--border-lt);
    }
    .kita-reply-wrap.open { display: flex; gap: 8px; align-items: center; }

    .kita-comment-input-wrap {
        display: flex; gap: 8px; margin-top: 10px; align-items: center;
    }
    .kita-comment-input {
        flex: 1; background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; color: var(--text-1); font-size: 12px;
        padding: 7px 16px; outline: none; font-family: 'DM Sans', sans-serif;
        transition: 0.2s;
    }
    .kita-comment-input:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .kita-comment-input::placeholder { color: var(--text-4); }
    .kita-comment-submit {
        padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 600;
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dk) 100%);
        color: #fff; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif; white-space: nowrap;
        box-shadow: 0 2px 8px var(--orange-glow);
    }

    .empty-kita {
        text-align: center; padding: 3.5rem 1rem;
        background: var(--card); border-radius: 20px; border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }
    .empty-kita p { color: var(--text-3); font-size: 13px; margin-top: 0.75rem; }

    /* MEMBER LOG CARD */
    .member-log-card {
        display: flex; align-items: center; gap: 12px;
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; padding: 0.875rem 1rem;
        margin-bottom: 1rem; box-shadow: var(--shadow-sm);
        position: relative; overflow: hidden;
    }
    .member-log-card::before {
        content: '';
        position: absolute; top: 0; left: 0; bottom: 0; width: 3px;
        background: linear-gradient(180deg, var(--sky), var(--orange));
        border-radius: 3px 0 0 3px;
    }
    .member-log-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        object-fit: cover; border: 2px solid var(--sky-lt);
        box-shadow: var(--shadow-sm); flex-shrink: 0;
    }
    .member-log-body { flex: 1; min-width: 0; }
    .member-log-text {
        font-size: 13px; color: var(--text-2); line-height: 1.5;
    }
    .member-log-text strong { color: var(--text-1); font-weight: 600; }
    .member-log-date { font-size: 11px; color: var(--text-4); margin-top: 3px; }
    .member-log-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; font-weight: 600; letter-spacing: 0.04em;
        color: var(--sky-dk); background: var(--sky-lt);
        border: 1px solid var(--border); border-radius: 20px;
        padding: 3px 10px; white-space: nowrap; flex-shrink: 0;
    }

    /* ── Linked post (gig/band) detail button ── */
    .kita-action-btn.linked-btn { color: #7c3aed; }
    .kita-action-btn.linked-btn:hover { background: #ede9fe; color: #5b21b6; }

    /* ── Linked popup ── */
    .lp-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 3000; align-items: flex-end; justify-content: center; padding: 0 10px calc(96px + env(safe-area-inset-bottom)); }
    .lp-overlay.open { display: flex; }
    @media(min-width:540px){ .lp-overlay { align-items: center; padding: 1rem; } }
    .lp-modal { background: var(--card); border-radius: 18px; width: 100%; max-width: 540px; max-height: 74vh; overflow-y: auto; padding: 1.25rem; box-shadow: var(--shadow-lg); position: relative; }
    @media(min-width:540px){ .lp-modal { max-height: 80vh; } }
    .lp-close { position: absolute; top: 12px; right: 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; color: var(--text-3); }
    .lp-type-badge { display: inline-flex; align-items: center; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; background: #ede9fe; color: #5b21b6; margin-bottom: 8px; }
    .lp-status-badge { display: inline-flex; align-items: center; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; margin-left: 6px; }
    .lp-status-badge.open   { background: #d1fae5; color: #065f46; }
    .lp-status-badge.closed { background: #fee2e2; color: #991b1b; }
    .lp-title { font-family: 'Sora', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--text-1); margin-bottom: 6px; line-height: 1.3; }
    .lp-creator { font-size: 12px; color: var(--text-3); margin-bottom: 10px; }
    .lp-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 12px; color: var(--text-3); margin-bottom: 10px; }
    .lp-section { margin-top: 10px; }
    .lp-section-label { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .lp-section-body { font-size: 13px; color: var(--text-2); line-height: 1.7; white-space: pre-wrap; word-break: break-word; }
    .lp-cta { display: flex; gap: 10px; margin-top: 1.1rem; flex-wrap: wrap; }
    .lp-daftar { background: var(--sky); color: #fff; border: none; border-radius: 12px; padding: 11px 22px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .lp-daftar:hover { opacity: .9; }
    .lp-cancel { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); border-radius: 12px; padding: 11px 18px; font-size: 13px; cursor: pointer; font-family: inherit; }
</style>
@endpush

@section('content')

{{-- INTRO OVERLAY --}}
<div id="eminor-backdrop"></div>
<div id="eminor-intro">
    <div class="ei-metro">
        <div class="ei-dot" id="eid1"></div>
        <div class="ei-dot" id="eid2"></div>
        <div class="ei-dot" id="eid3"></div>
    </div>
    <div class="ei-vinyl" id="ei-vinyl"></div>
    <div class="ei-text">
        <div class="ei-line dim" id="eil0">Dulu...</div>
        <div class="ei-line" id="eil1">musisi membutuhkan label untuk didengar.</div>
        <div class="ei-line dim" id="eil2">Sekarang...</div>
        <div class="ei-line" id="eil3">yang dibutuhkan hanya tempat yang tepat.</div>
        <div class="ei-line big" id="eil4">E<span>MINOR</span></div>
    </div>
    <div class="ei-eq" id="ei-eq">
        <div class="ei-eqb"></div><div class="ei-eqb"></div><div class="ei-eqb"></div>
        <div class="ei-eqb"></div><div class="ei-eqb"></div><div class="ei-eqb"></div>
    </div>
    <div id="ei-disc">
        <div class="fb-beta">
            <span class="bic">&#128679;</span>
            <span>Jujur ya &mdash; ini <b>masih tahap beta</b>, dan untuk sekarang masih <b>menumpang di web pribadi</b> saya. Tapi kalau dukungan kalian besar, saya akan lebih serius bangun <b>rumah baru</b> yang layak buat ekosistem ini. Langkah besar ini dimulai dari bagikan ke teman temanmu. &#128293;</span>
        </div>
    </div>
</div>

<div class="kita-page-header">
    <div class="kita-page-title">&#127911; Stage</div>
    <div class="kita-page-sub">Ceritakan apapun — semuanya mendengar</div>
</div>

{{-- POST FORM --}}
@auth
<div class="kita-form">
    <div class="kita-form-header">
        <img src="{{ Auth::user()->avatar }}" class="kita-form-avatar" alt="">
        <span class="kita-form-name">{{ Auth::user()->name }}</span>
    </div>
    <form method="POST" action="{{ route('kita.store') }}" id="kitaForm">
        @csrf
        <textarea name="body" class="kita-textarea" id="kitaBody"
            placeholder="Apa yang ada di pikiranmu sekarang?"
            maxlength="500" oninput="kitaCharCount(this)" onfocus="kitaAutoLoc()" required></textarea>
        <input type="hidden" name="location" id="kitaLocation">
        <div class="kita-form-footer">
            <div class="kita-form-left">
                <span class="kita-loc-chip" id="kitaLocChip" style="display:none;">
                    &#128205; <span id="kitaLocText"></span>
                    <button type="button" class="kita-loc-x" onclick="kitaClearLoc()" title="Hapus lokasi">&#10005;</button>
                </span>
                <span class="kita-loc-status" id="kitaLocStatus" style="display:none;"></span>
                <span class="kita-char-count" id="kitaCharCount">0 / 500</span>
            </div>
            <button type="submit" class="btn-post-kita">&#128640; Posting</button>
        </div>
    </form>
</div>
@else
<div style="text-align:center;padding:1rem 1.5rem;background:var(--card);border-radius:16px;margin-bottom:1rem;">
    <p style="font-size:13px;color:var(--text-3);margin:0 0 .6rem;">
        <a href="{{ route('google.login') }}" style="color:var(--sky-dk);font-weight:600;">Login</a> untuk ikut cerita di Stage.
    </p>
</div>
@endauth

{{-- FEED (posts + member logs, urutan kronologis) --}}
@php
    // Sisipkan member logs ke dalam feed posts berdasarkan timestamp
    $shownLogIds = [];
@endphp

@if($posts->count() > 0)
    @foreach($posts as $post)

    {{-- Tampilkan member log yang lebih baru dari post ini dan belum ditampilkan --}}
    @foreach($memberLogs->filter(fn($l) => !in_array($l->id, $shownLogIds) && $l->created_at >= $post->created_at) as $log)
    @php $shownLogIds[] = $log->id; @endphp
    <div class="member-log-card">
        <img src="{{ $log->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
             class="member-log-avatar" alt="">
        <div class="member-log-body">
            <div class="member-log-text">
                <strong>{{ $log->user->name ?? 'Member' }}</strong> baru saja bergabung di Ekosistem Eminor &#127881;
            </div>
            <div class="member-log-date">{{ $log->created_at->diffForHumans() }}</div>
        </div>
        <span class="member-log-badge">&#127381; Member Baru</span>
    </div>
    @endforeach

    @php
    $linkedData = null;
    if (!empty($post->linked_type) && !empty($post->linked_id)) {
        if ($post->linked_type === 'gig' && isset($gigPosts[$post->linked_id])) {
            $gp = $gigPosts[$post->linked_id];
            $linkedData = [
                'kind'         => 'gig',
                'id'           => $gp->id,
                'type_label'   => \App\Models\GigPost::typeLabel($gp->type),
                'title'        => $gp->title,
                'creator_id'   => $gp->user_id,
                'creator_name' => optional($gp->user)->name ?? '',
                'description'  => $gp->description,
                'location'     => $gp->location,
                'date_event'   => $gp->date_event ? $gp->date_event->format('d M Y') : null,
                'requirements' => $gp->requirements,
                'status'       => $gp->status,
            ];
        } elseif ($post->linked_type === 'band' && isset($bandPosts[$post->linked_id])) {
            $bp = $bandPosts[$post->linked_id];
            $linkedData = [
                'kind'         => 'band',
                'type_label'   => '🎯 Cari Personil',
                'title'        => $bp->title,
                'creator_id'   => $bp->user_id,
                'creator_name' => optional($bp->user)->name ?? '',
                'description'  => $bp->description,
                'roles_needed' => $bp->roles_needed,
                'genres'       => $bp->genres,
                'location'     => $bp->location,
                'urgent'       => $bp->urgent,
                'status'       => $bp->status,
            ];
        }
    }
    @endphp

    <div class="kita-post" id="kitaPost{{ $post->id }}">

        <div class="kita-post-header">
            <img src="{{ $post->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                 class="kita-post-avatar" alt="">
            <div class="kita-post-meta">
                @php $mlv = $musicianMap[$post->user_id]['level'] ?? null; @endphp
                <div class="kita-post-name">{{ $post->user->name }}
                    <button type="button" class="mus-badge {{ $mlv ? 'lv-'.$mlv : '' }}" onclick="openMusCard({{ $post->user_id }})" title="{{ $mlv ? 'Musisi · '.ucfirst($mlv) : 'Lihat profil' }}">&#127925;</button>
                </div>
                <div class="kita-post-date">{{ $post->created_at->diffForHumans() }}</div>
                @if(isset($post->location) && $post->location)
                <span class="kita-post-location">&#128205; {{ $post->location }}</span>
                @endif
            </div>
            @if(Auth::id() === $post->user_id)
            <div class="kita-post-actions">
                <button class="kita-action-top-btn"
                        onclick="kitaEditPost({{ $post->id }})" title="Edit">&#9998;</button>
                <button class="kita-action-top-btn del"
                        onclick="kitaDeletePost({{ $post->id }})" title="Hapus">&#10005;</button>
            </div>
            @endif
        </div>

        <div class="kita-post-body" id="kitaPostBody{{ $post->id }}">{{ $post->body }}</div>
        <textarea class="kita-post-body-edit" id="kitaPostEdit{{ $post->id }}">{{ $post->body }}</textarea>
        <div class="kita-edit-actions" id="kitaEditActions{{ $post->id }}">
            <button class="kita-save-btn" onclick="kitaSavePost({{ $post->id }})">Simpan</button>
            <button class="kita-cancel-edit-btn" onclick="kitaCancelEdit({{ $post->id }})">Batal</button>
        </div>

        <div class="kita-post-footer">
            <div class="kita-like-wrap">
                <button class="kita-action-btn {{ in_array($post->id, $likedIds) ? 'liked' : '' }}"
                        id="kitaLike{{ $post->id }}"
                        onclick="kitaToggleLike({{ $post->id }})">
                    <span class="like-icon">{{ in_array($post->id, $likedIds) ? '♥' : '♡' }}</span>
                </button>
                <span id="kitaLikeCount{{ $post->id }}"
                      class="like-count-btn"
                      onclick="kitaToggleLikers({{ $post->id }}, event)">{{ $post->likes_count }}</span>
                <div class="likers-tooltip" id="kitaLikers{{ $post->id }}"
                     data-likers="{{ json_encode(($likersByPost[$post->id] ?? collect())->values()->toArray()) }}"></div>
            </div>
            <button class="kita-action-btn" onclick="kitaToggleComments({{ $post->id }})">
                <span>&#128172;</span>
                <span id="kitaCommentCount{{ $post->id }}">{{ $post->comments_count }}</span>
            </button>
            @if($linkedData)
            <button class="kita-action-btn linked-btn"
                    onclick="openLinkedPost({{ json_encode($linkedData) }})">
                &#128279; Lihat Detail
            </button>
            @endif
        </div>

        {{-- COMMENTS --}}
        <div class="kita-comments" id="kitaComments{{ $post->id }}">
            <div id="kitaCommentsList{{ $post->id }}">
                @foreach($post->comments->take(8) as $comment)
                @php $isLikedCmt = in_array($comment->id, $likedCommentIds); @endphp
                <div class="kita-comment-item" id="kitaComment{{ $comment->id }}">
                    <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.png') }}"
                         class="kita-comment-avatar" alt="">
                    <div class="kita-comment-bubble">
                        <div class="kita-comment-header">
                            <span class="kita-comment-name">{{ $comment->user->name }}</span>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span class="kita-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                @if(Auth::id() === $comment->user_id)
                                <button class="kita-comment-delete"
                                        onclick="kitaDeleteComment({{ $post->id }}, {{ $comment->id }})">&#10005;</button>
                                @endif
                            </div>
                        </div>
                        <div class="kita-comment-body">{{ $comment->body }}</div>
                        <div class="kita-comment-footer">
                            <button class="kc-action {{ $isLikedCmt ? 'liked' : '' }}"
                                    id="kcLike{{ $comment->id }}"
                                    onclick="kitaLikeComment({{ $post->id }}, {{ $comment->id }}, this)">
                                &#9829; <span id="kcLikeCount{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                            </button>
                            <button class="kc-action" onclick="kitaToggleReply({{ $comment->id }}, '{{ addslashes($comment->user->name) }}')">
                                &#8629; Balas
                            </button>
                        </div>
                        {{-- Replies --}}
                        @foreach($comment->replies->take(6) as $reply)
                        @php $isLikedReply = in_array($reply->id, $likedCommentIds); @endphp
                        <div class="kita-reply-item" id="kitaComment{{ $reply->id }}">
                            <img src="{{ $reply->user->avatar ?? asset('images/default-avatar.png') }}"
                                 class="kita-comment-avatar" alt="">
                            <div class="kita-comment-bubble">
                                <div class="kita-comment-header">
                                    <span class="kita-comment-name">{{ $reply->user->name }}</span>
                                    <div style="display:flex;align-items:center;gap:4px;">
                                        <span class="kita-comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                        @if(Auth::id() === $reply->user_id)
                                        <button class="kita-comment-delete"
                                                onclick="kitaDeleteComment({{ $post->id }}, {{ $reply->id }})">&#10005;</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="kita-comment-body">{{ $reply->body }}</div>
                                <div class="kita-comment-footer">
                                    <button class="kc-action {{ $isLikedReply ? 'liked' : '' }}"
                                            id="kcLike{{ $reply->id }}"
                                            onclick="kitaLikeComment({{ $post->id }}, {{ $reply->id }}, this)">
                                        &#9829; <span id="kcLikeCount{{ $reply->id }}">{{ $reply->likes_count ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        {{-- Reply input --}}
                        <div class="kita-reply-wrap" id="kitaReplyWrap{{ $comment->id }}">
                            <img src="{{ Auth::user()?->avatar ?? asset('images/default-avatar.png') }}"
                                 class="kita-comment-avatar" style="width:22px;height:22px;" alt="">
                            <input class="kita-comment-input" id="kitaReplyInput{{ $comment->id }}"
                                   placeholder="Balas..." style="font-size:11px;padding:5px 12px;">
                            <button class="kita-comment-submit" style="padding:5px 12px;font-size:11px;"
                                    onclick="kitaSubmitReply({{ $post->id }}, {{ $comment->id }})">Kirim</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="kita-comment-input-wrap">
                <img src="{{ Auth::user()?->avatar ?? asset('images/default-avatar.png') }}" class="kita-comment-avatar" alt="">
                <input type="text" class="kita-comment-input"
                       id="kitaInput{{ $post->id }}"
                       placeholder="Komentar..."
                       onkeydown="if(event.key==='Enter'){kitaSubmitComment({{ $post->id }});return false;}">
                <button class="kita-comment-submit" onclick="kitaSubmitComment({{ $post->id }})">Kirim</button>
            </div>
        </div>

    </div>

    @endforeach

    {{-- Log yang lebih lama dari semua post di halaman ini --}}
    @foreach($memberLogs->filter(fn($l) => !in_array($l->id, $shownLogIds)) as $log)
    @php $shownLogIds[] = $log->id; @endphp
    <div class="member-log-card">
        <img src="{{ $log->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
             class="member-log-avatar" alt="">
        <div class="member-log-body">
            <div class="member-log-text">
                <strong>{{ $log->user->name ?? 'Member' }}</strong> baru saja bergabung di Ekosistem Eminor &#127881;
            </div>
            <div class="member-log-date">{{ $log->created_at->diffForHumans() }}</div>
        </div>
        <span class="member-log-badge">&#127381; Member Baru</span>
    </div>
    @endforeach

    @if($posts->hasPages())
    <div style="display:flex;justify-content:center;gap:8px;margin-top:1.25rem;">
        @if(!$posts->onFirstPage())
        <a href="{{ $posts->previousPageUrl() }}"
           style="padding:8px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:12px;text-decoration:none;background:var(--card);font-weight:500;box-shadow:var(--shadow-sm);">
            ← Sebelumnya
        </a>
        @endif
        @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}"
           style="padding:8px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:12px;text-decoration:none;background:var(--card);font-weight:500;box-shadow:var(--shadow-sm);">
            Berikutnya →
        </a>
        @endif
    </div>
    @endif

@else
<div class="empty-kita">
    <div style="font-size:38px;">&#128172;</div>
    <p>Belum ada postingan.<br>Jadilah yang pertama berbagi!</p>
</div>
@endif

{{-- POPUP LINKED (GIG / BAND) --}}
<div class="lp-overlay" id="lpOverlay" onclick="closeLpIfBg(event)">
    <div class="lp-modal" onclick="event.stopPropagation()">
        <button class="lp-close" onclick="closeLinkedPost()">&#10005;</button>
        <div id="lpTypeBadge" class="lp-type-badge"></div>
        <span id="lpStatusBadge" class="lp-status-badge"></span>
        <div id="lpTitle" class="lp-title"></div>
        <div id="lpCreator" class="lp-creator"></div>
        <div id="lpMeta" class="lp-meta"></div>
        <div id="lpDescription" class="lp-section" style="display:none;">
            <div class="lp-section-label">Deskripsi</div>
            <div class="lp-section-body" id="lpDescBody"></div>
        </div>
        <div id="lpRoles" class="lp-section" style="display:none;">
            <div class="lp-section-label">Role Dibutuhkan</div>
            <div class="lp-section-body" id="lpRolesBody"></div>
        </div>
        <div id="lpReqs" class="lp-section" style="display:none;">
            <div class="lp-section-label">Persyaratan</div>
            <div class="lp-section-body" id="lpReqsBody"></div>
        </div>
        <div class="lp-cta" id="lpCta"></div>
    </div>
</div>

{{-- POPUP MUSISI / AUDIENS --}}
<div class="mc-overlay" id="musOverlay" onclick="closeMusCard(event)">
    <div class="mc-card" onclick="event.stopPropagation()">
        <div class="mc-banner"></div>
        <div class="mc-body">
            <img id="mcAvatar" class="mc-avatar" src="{{ asset('images/default-avatar.png') }}" alt="">
            <div class="mc-name" id="mcName">—</div>
            <div class="mc-sub" id="mcSub"></div>
            <div class="mc-tags" id="mcTags"></div>
            <div class="mc-look" id="mcLook" style="display:none;"></div>
            <div class="mc-followers" id="mcFollowers"></div>
            <div class="mc-actions" id="mcActions"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ===== INTRO OVERLAY ===== */
(function(){
    var SEEN_KEY = 'eminor_seen_intro';
    if (sessionStorage.getItem(SEEN_KEY)) {
        document.getElementById('eminor-backdrop').style.display = 'none';
        document.getElementById('eminor-intro').style.display    = 'none';
        return;
    }
    sessionStorage.setItem(SEEN_KEY, '1');
    var TOTAL_MS = 14000;
    function hideAll() {
        ['eminor-backdrop','eminor-intro'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) { el.classList.add('hide'); setTimeout(function(){ el.style.display='none'; }, 700); }
        });
    }
    function showLine(id) { var el=document.getElementById(id); if(el) el.classList.add('show'); }
    function hideLine(id) { var el=document.getElementById(id); if(el) el.classList.remove('show'); }
    function popDot(id)   { var el=document.getElementById(id); if(el){ el.classList.add('on'); setTimeout(function(){ el.classList.remove('on'); },300); } }

    setTimeout(function(){ popDot('eid1'); }, 0);
    setTimeout(function(){ popDot('eid2'); }, 400);
    setTimeout(function(){ popDot('eid3'); var v=document.getElementById('ei-vinyl'); if(v) v.classList.add('show'); }, 800);
    setTimeout(function(){ showLine('eil0'); }, 1100);
    setTimeout(function(){ showLine('eil1'); }, 1700);
    setTimeout(function(){ hideLine('eil0'); hideLine('eil1'); showLine('eil2'); }, 2900);
    setTimeout(function(){ showLine('eil3'); }, 3500);
    setTimeout(function(){
        hideLine('eil2'); hideLine('eil3'); showLine('eil4');
        var eq=document.getElementById('ei-eq'); if(eq) eq.classList.add('show');
    }, 4600);
    setTimeout(function(){
        hideLine('eil4');
        ['ei-vinyl','ei-eq','ei-text'].forEach(function(id){
            var el=document.getElementById(id)||document.querySelector('.'+id);
            if(el){ el.style.opacity='0'; setTimeout(function(){ el.style.display='none'; },650); }
        });
        document.querySelectorAll('.ei-vinyl,.ei-eq,.ei-text,.ei-metro').forEach(function(el){
            el.style.transition='opacity .65s ease'; el.style.opacity='0';
            setTimeout(function(){ el.style.display='none'; },650);
        });
        setTimeout(function(){
            var disc=document.getElementById('ei-disc'); if(disc) disc.classList.add('show');
        }, 400);
    }, 5500);
    setTimeout(hideAll, TOTAL_MS);
    document.getElementById('eminor-intro').addEventListener('click', hideAll);
})();
</script>
<script>
var BASE_URL  = '{{ url("") }}';
var csrfToken = '{{ csrf_token() }}';

/* ===== POPUP LINKED (GIG / BAND) ===== */
function openLinkedPost(d) {
    document.getElementById('lpTypeBadge').textContent  = d.type_label || '';
    var sb = document.getElementById('lpStatusBadge');
    sb.textContent  = d.status === 'open' ? '● Buka' : '✕ Tutup';
    sb.className    = 'lp-status-badge ' + (d.status || '');
    document.getElementById('lpTitle').textContent    = d.title || '';
    document.getElementById('lpCreator').textContent  = 'diposting oleh ' + (d.creator_name || '');
    // meta row
    var meta = [];
    if (d.location)   meta.push('📍 ' + d.location);
    if (d.date_event) meta.push('🗓 ' + d.date_event);
    if (d.genres)     meta.push('🎶 ' + d.genres);
    if (d.urgent)     meta.push('⚡ URGENT');
    document.getElementById('lpMeta').textContent = meta.join('   ');
    // description
    var descEl = document.getElementById('lpDescription');
    if (d.description) { document.getElementById('lpDescBody').textContent = d.description; descEl.style.display = ''; }
    else descEl.style.display = 'none';
    // roles (band only)
    var rolesEl = document.getElementById('lpRoles');
    if (d.roles_needed) { document.getElementById('lpRolesBody').textContent = d.roles_needed; rolesEl.style.display = ''; }
    else rolesEl.style.display = 'none';
    // requirements (gig only)
    var reqsEl = document.getElementById('lpReqs');
    if (d.requirements) { document.getElementById('lpReqsBody').textContent = d.requirements; reqsEl.style.display = ''; }
    else reqsEl.style.display = 'none';
    // CTA
    var cta = document.getElementById('lpCta');
    cta.innerHTML = '';
    @auth
    var authId = {{ Auth::id() }};
    if (d.creator_id && d.creator_id != authId) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/dia/start/' + d.creator_id;
        form.style.display = 'inline';
        var csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
        form.appendChild(csrf);
        // Pesan pembuka otomatis: sertakan kategori + judul agar pembuat langsung paham
        var intro = document.createElement('input');
        intro.type = 'hidden'; intro.name = 'intro';
        intro.value = 'Halo! 👋 Saya tertarik dengan ' + (d.type_label || '') + ' "' + (d.title || '') + '" yang kamu posting. Boleh info lebih lanjut?';
        form.appendChild(intro);
        var btn = document.createElement('button');
        btn.type = 'submit';
        btn.className = 'lp-daftar';
        btn.innerHTML = '&#128172; Saya Minat &mdash; Hubungi';
        form.appendChild(btn);
        cta.appendChild(form);
    } else if (d.creator_id == authId && d.kind === 'gig' && d.id) {
        // Pembuat Gig: tombol Edit
        var edit = document.createElement('a');
        edit.href = BASE_URL + '/gig/' + d.id + '/edit';
        edit.className = 'lp-daftar';
        edit.style.textDecoration = 'none';
        edit.innerHTML = '&#9999;&#65039; Edit Pengumuman';
        cta.appendChild(edit);
    }
    @endauth
    document.getElementById('lpOverlay').classList.add('open');
}
function closeLinkedPost() { document.getElementById('lpOverlay').classList.remove('open'); }
function closeLpIfBg(e) { if (e.target === document.getElementById('lpOverlay')) closeLinkedPost(); }

/* ===== POPUP MUSISI ===== */
var mcUser = null;
function openMusCard(userId) {
    document.getElementById('mcName').textContent = 'Memuat…';
    document.getElementById('mcSub').textContent = '';
    document.getElementById('mcTags').innerHTML = '';
    document.getElementById('mcLook').style.display = 'none';
    document.getElementById('mcFollowers').textContent = '';
    document.getElementById('mcActions').innerHTML = '';
    document.getElementById('mcAvatar').src = '{{ asset('images/default-avatar.png') }}';
    document.getElementById('musOverlay').classList.add('open');
    fetch(BASE_URL + '/musisi/card/' + userId, { headers: { 'Accept': 'application/json' } })
        .then(function(r){ return r.json(); })
        .then(function(d){ if (d && d.user_id) renderMusCard(d); else document.getElementById('mcName').textContent = 'Tidak ditemukan'; })
        .catch(function(){ document.getElementById('mcName').textContent = 'Gagal memuat'; });
}
function closeMusCard(e) { document.getElementById('musOverlay').classList.remove('open'); }
function mcCap(s){ return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
function mcEsc(s){ return (s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
function renderMusCard(d) {
    mcUser = d;
    document.getElementById('mcAvatar').src = d.avatar || '{{ asset('images/default-avatar.png') }}';
    document.getElementById('mcName').textContent = d.name || 'Member';
    var sub = d.is_musician ? '🎸 Musisi' : '🎧 Pendengar · calon fans';
    if (d.location) sub += ' · 📍 ' + d.location;   // lokasi manual dari profil musisi
    document.getElementById('mcSub').textContent = sub;

    var tags = '';
    if (d.is_musician) {
        if (d.skill_level) tags += '<span class="mc-badge lv-' + mcEsc(d.skill_level) + '">' + mcCap(d.skill_level) + '</span>';
        (d.roles || []).forEach(function(r){ tags += '<span class="mc-badge">' + mcEsc(r) + '</span>'; });
        (d.genres || []).forEach(function(g){ tags += '<span class="mc-badge">' + mcEsc(g) + '</span>'; });
    }
    document.getElementById('mcTags').innerHTML = tags;

    var look = document.getElementById('mcLook');
    if (d.is_musician && d.looking_for) { look.style.display = ''; look.textContent = '🔎 ' + d.looking_for; }
    else look.style.display = 'none';

    document.getElementById('mcFollowers').textContent = d.followers + ' pengikut';

    var act = '';
    if (!d.is_self) {
        act += '<button class="mc-btn mc-btn-follow' + (d.is_following ? ' following' : '') + '" id="mcFollowBtn" onclick="mcToggleFollow()">' + (d.is_following ? 'Mengikuti' : '+ Ikuti') + '</button>';
        act += '<button class="mc-btn mc-btn-ghost" onclick="mcContact()">💬 Hubungi</button>';
    } else {
        act += '<a class="mc-btn mc-btn-ghost" href="' + BASE_URL + '/musisi/profil">Edit profilku</a>';
    }
    if (d.is_musician && d.profile_id) {
        act += '<a class="mc-btn mc-btn-ghost" href="' + BASE_URL + '/musisi/' + d.profile_id + '">Profil</a>';
    }
    document.getElementById('mcActions').innerHTML = act;
}
function mcToggleFollow() {
    if (!mcUser) return;
    var btn = document.getElementById('mcFollowBtn');
    btn.disabled = true;
    fetch(BASE_URL + '/follow/' + mcUser.user_id, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
        .then(function(r){ return r.json(); })
        .then(function(d){
            btn.disabled = false;
            if (d.error) return;
            mcUser.is_following = d.following;
            btn.textContent = d.following ? 'Mengikuti' : '+ Ikuti';
            btn.classList.toggle('following', d.following);
            document.getElementById('mcFollowers').textContent = d.followers + ' pengikut';
        })
        .catch(function(){ btn.disabled = false; });
}
function mcContact() {
    if (!mcUser) return;
    var f = document.createElement('form');
    f.method = 'POST'; f.action = BASE_URL + '/dia/start/' + mcUser.user_id;
    f.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">';
    document.body.appendChild(f); f.submit();
}

/* CHAR COUNT */
function kitaCharCount(el) {
    var c = el.value.length;
    var cnt = document.getElementById('kitaCharCount');
    if(cnt){ cnt.textContent=c+' / 500'; cnt.classList.toggle('warn',c>400); }
}

/* LOCATION — auto-deteksi GPS akurat saat mulai menulis post, langsung muncul */
var kitaLocStarted = false;
function kitaAutoLoc() {
    if (kitaLocStarted) return;            // sekali saja per buka halaman
    kitaLocStarted = true;
    if (document.getElementById('kitaLocation').value) return; // sudah ada
    if (!navigator.geolocation) return;
    var st = document.getElementById('kitaLocStatus');
    st.style.display = ''; st.textContent = '📍 mendeteksi lokasi…';
    // Pengaman: kalau callback geolocation tak pernah datang (mis. TWA hang),
    // jangan biarkan "mendeteksi" selamanya — sembunyikan setelah 14 detik.
    var settled = false;
    var guard = setTimeout(function(){ if (!settled) { settled = true; st.style.display = 'none'; } }, 14000);
    navigator.geolocation.getCurrentPosition(
        function(pos){ if (settled) return; settled = true; clearTimeout(guard); kitaPostReverse(pos.coords.latitude, pos.coords.longitude); },
        function(err){ if (settled) return; settled = true; clearTimeout(guard); st.style.display = 'none'; },
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 300000 }
    );
}
function kitaPostReverse(lat, lon) {
    fetch('{{ route('geocode') }}?lat=' + lat + '&lon=' + lon,
          { headers: { 'Accept': 'application/json' } })
        .then(function(r){ return r.json(); })
        .then(function(d){
            var city = d.city || '';
            if (city) kitaSetLoc(city);
            else document.getElementById('kitaLocStatus').style.display = 'none';
        })
        .catch(function(){ document.getElementById('kitaLocStatus').style.display = 'none'; });
}
function kitaSetLoc(city) {
    document.getElementById('kitaLocation').value = city;
    document.getElementById('kitaLocText').textContent = city;
    document.getElementById('kitaLocChip').style.display = '';
    document.getElementById('kitaLocStatus').style.display = 'none';
}
function kitaClearLoc() {
    document.getElementById('kitaLocation').value = '';
    document.getElementById('kitaLocChip').style.display = 'none';
}

/* LIKE */
function kitaToggleLike(postId) {
    fetch(BASE_URL+'/kita/'+postId+'/like', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        var btn     = document.getElementById('kitaLike'+postId);
        var count   = document.getElementById('kitaLikeCount'+postId);
        var tooltip = document.getElementById('kitaLikers'+postId);
        if(!btn||!count) return;
        count.textContent = d.likes_count;
        btn.classList.toggle('liked', d.liked);
        var icon = btn.querySelector('.like-icon');
        if(icon) icon.textContent = d.liked ? '♥' : '♡';
        if(tooltip && d.likers) tooltip.dataset.likers = JSON.stringify(d.likers);
    });
}

function kitaToggleLikers(postId, evt) {
    evt.stopPropagation();
    var tooltip = document.getElementById('kitaLikers'+postId);
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

/* COMMENTS */
function kitaToggleComments(postId) {
    var el = document.getElementById('kitaComments'+postId);
    if(el) el.classList.toggle('open');
}

function kitaLikeComment(postId, commentId, btn) {
    fetch(BASE_URL+'/kita/'+postId+'/comment/'+commentId+'/like', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.error) return;
        btn.classList.toggle('liked', d.liked);
        var countEl = document.getElementById('kcLikeCount'+commentId);
        if (countEl) countEl.textContent = d.likes_count;
        fbSoundLike();
    }).catch(function(){});
}

function kitaToggleReply(commentId, replyToName) {
    var wrap  = document.getElementById('kitaReplyWrap'+commentId);
    var input = document.getElementById('kitaReplyInput'+commentId);
    if (!wrap) return;
    var isOpen = wrap.classList.contains('open');
    // Tutup semua reply wrap lain
    document.querySelectorAll('.kita-reply-wrap.open').forEach(function(w){ w.classList.remove('open'); });
    if (!isOpen) {
        wrap.classList.add('open');
        if (input) { input.placeholder = 'Balas @' + replyToName + '...'; input.focus(); }
    }
}

function kitaSubmitReply(postId, parentId) {
    var input = document.getElementById('kitaReplyInput'+parentId);
    var body  = input ? input.value.trim() : '';
    if (!body) return;
    fetch(BASE_URL+'/kita/'+postId+'/comment', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ body: body, parent_id: parentId })
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (!d.success) return;
        input.value = '';
        // Tutup reply wrap
        var wrap = document.getElementById('kitaReplyWrap'+parentId);
        if (wrap) wrap.classList.remove('open');
        // Inject reply HTML sebelum reply-wrap
        var replyHtml = '<div class="kita-reply-item" id="kitaComment'+d.comment.id+'">'
            + '<img src="'+escHtml(d.comment.avatar)+'" class="kita-comment-avatar" style="width:22px;height:22px;" onerror="this.onerror=null;this.src=\''+d.comment.avatar+'\'" alt="">'
            + '<div class="kita-comment-bubble">'
            + '<div class="kita-comment-header">'
            + '<span class="kita-comment-name">'+escHtml(d.comment.user)+'</span>'
            + '<div style="display:flex;align-items:center;gap:4px;">'
            + '<span class="kita-comment-time">Baru saja</span>'
            + '<button class="kita-comment-delete" onclick="kitaDeleteComment('+postId+','+d.comment.id+')">&#10005;</button>'
            + '</div></div>'
            + '<div class="kita-comment-body">'+escHtml(d.comment.body)+'</div>'
            + '<div class="kita-comment-footer"><button class="kc-action" id="kcLike'+d.comment.id+'" onclick="kitaLikeComment('+postId+','+d.comment.id+',this)">&#9829; <span id="kcLikeCount'+d.comment.id+'">0</span></button></div>'
            + '</div></div>';
        if (wrap) wrap.insertAdjacentHTML('beforebegin', replyHtml);
    }).catch(function(){});
}

function kitaSubmitComment(postId) {
    var input = document.getElementById('kitaInput'+postId);
    var body  = input ? input.value.trim() : '';
    if(!body) return;

    fetch(BASE_URL+'/kita/'+postId+'/comment', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(!d.success)return;
        var list = document.getElementById('kitaCommentsList'+postId);
        if(list){
            var html='<div class="kita-comment-item" id="kitaComment'+d.comment.id+'">'+
                '<img src="'+d.comment.avatar+'" class="kita-comment-avatar" alt="">'+
                '<div class="kita-comment-bubble">'+
                '<div class="kita-comment-header">'+
                '<span class="kita-comment-name">'+escHtml(d.comment.user)+'</span>'+
                '<div style="display:flex;align-items:center;gap:6px;">'+
                '<span class="kita-comment-time">Baru saja</span>'+
                '<button class="kita-comment-delete" onclick="kitaDeleteComment('+postId+','+d.comment.id+')">&#10005;</button>'+
                '</div></div>'+
                '<div class="kita-comment-body">'+escHtml(d.comment.body)+'</div>'+
                '</div></div>';
            list.insertAdjacentHTML('beforeend', html);
        }
        var cnt = document.getElementById('kitaCommentCount'+postId);
        if(cnt) cnt.textContent=parseInt(cnt.textContent||0)+1;
        if(input) input.value='';
    });
}

function kitaDeleteComment(postId, commentId) {
    if(!confirm('Hapus komentar ini?'))return;
    fetch(BASE_URL+'/kita/'+postId+'/comment/'+commentId, {
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'}
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d.success){
            var el=document.getElementById('kitaComment'+commentId);
            if(el) el.remove();
            var cnt=document.getElementById('kitaCommentCount'+postId);
            if(cnt) cnt.textContent=Math.max(0,parseInt(cnt.textContent||0)-1);
        }
    });
}

/* EDIT POST */
function kitaEditPost(id) {
    var body    = document.getElementById('kitaPostBody'+id);
    var edit    = document.getElementById('kitaPostEdit'+id);
    var actions = document.getElementById('kitaEditActions'+id);
    if(body)    body.style.display    = 'none';
    if(edit)    {edit.style.display   = 'block'; edit.focus();}
    if(actions) actions.style.display = 'flex';
}

function kitaCancelEdit(id) {
    var body    = document.getElementById('kitaPostBody'+id);
    var edit    = document.getElementById('kitaPostEdit'+id);
    var actions = document.getElementById('kitaEditActions'+id);
    if(body)    body.style.display    = 'block';
    if(edit)    edit.style.display    = 'none';
    if(actions) actions.style.display = 'none';
}

function kitaSavePost(id) {
    var edit = document.getElementById('kitaPostEdit'+id);
    var body = edit ? edit.value.trim() : '';
    if(!body) return;

    fetch(BASE_URL+'/kita/'+id, {
        method:'PUT',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(!d.success)return;
        var bodyEl = document.getElementById('kitaPostBody'+id);
        if(bodyEl) bodyEl.textContent = body;
        kitaCancelEdit(id);
    });
}

function kitaDeletePost(id) {
    if(!confirm('Hapus postingan ini?'))return;
    fetch(BASE_URL+'/kita/'+id, {
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'}
    })
    .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
    .then(function(d){
        if(d.success){
            var el=document.getElementById('kitaPost'+id);
            if(el) el.remove();
        } else { alert('Gagal menghapus postingan.'); }
    })
    .catch(function(e){ alert('Gagal menghapus postingan ('+e.message+'). Coba muat ulang.'); });
}

function escHtml(t){
    var d=document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}

// Auto-buka komentar: deteksi ?openPost= ATAU #kitaPost{id}
document.addEventListener('DOMContentLoaded', function() {
    var params = new URLSearchParams(window.location.search);
    var openId = params.get('openPost');

    // Fallback: deteksi dari hash #kitaPost123
    if (!openId) {
        var hashMatch = window.location.hash.match(/^#kitaPost(\d+)/);
        if (hashMatch) openId = hashMatch[1];
    }
    if (!openId) return;

    var post = document.getElementById('kitaPost' + openId);
    if (!post) return;
    var comments = document.getElementById('kitaComments' + openId);
    if (comments) comments.classList.add('open');
    setTimeout(function() {
        post.scrollIntoView({ behavior: 'smooth', block: 'start' });
        post.style.transition = 'box-shadow 0.5s';
        post.style.boxShadow = '0 0 0 3px rgba(56,168,204,0.45)';
        setTimeout(function() { post.style.boxShadow = ''; }, 2200);
    }, 150);
});
</script>
@endpush