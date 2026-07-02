@extends('layouts.fanbase')
@section('title', 'Tools')

@push('styles')
<style>
    /* PROFILE HERO */
    .kamu-hero {
        background: linear-gradient(145deg, var(--sky-dk) 0%, var(--sky) 60%, var(--sky-mid) 100%);
        border-radius: 24px; padding: 2rem 1.5rem;
        margin-bottom: 1.5rem; text-align: center;
        position: relative; overflow: hidden;
        box-shadow: var(--shadow-lg);
    }
    .kamu-hero::before {
        content: '';
        position: absolute; top: -60px; right: -60px;
        width: 200px; height: 200px; border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .kamu-hero::after {
        content: '';
        position: absolute; bottom: -40px; left: -40px;
        width: 150px; height: 150px; border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .kamu-avatar-wrap {
        position: relative; display: inline-block; margin-bottom: 1rem;
    }
    .kamu-avatar {
        width: 84px; height: 84px; border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255,255,255,0.5);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .kamu-avatar-badge {
        position: absolute; bottom: 2px; right: 2px;
        width: 22px; height: 22px; border-radius: 50%;
        background: var(--orange); border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: #fff;
    }
    .kamu-name {
        font-family: 'Sora', sans-serif;
        font-size: 1.2rem; font-weight: 700; color: #fff;
        margin-bottom: 4px; text-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .kamu-email { font-size: 12px; color: rgba(255,255,255,0.65); margin-bottom: 1.5rem; }

    .kamu-stats {
        display: flex; justify-content: center; gap: 0;
        background: rgba(255,255,255,0.12);
        border-radius: 16px; padding: 0; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .kamu-stat {
        flex: 1; text-align: center; padding: 0.875rem 0.5rem;
        border-right: 1px solid rgba(255,255,255,0.15);
    }
    .kamu-stat:last-child { border-right: none; }
    .kamu-stat-num {
        font-family: 'Sora', sans-serif;
        font-size: 1.3rem; font-weight: 700; color: #fff;
        line-height: 1;
    }
    .kamu-stat-label {
        font-size: 10px; color: rgba(255,255,255,0.6);
        margin-top: 4px; letter-spacing: 0.05em;
    }

    /* HERO RAMPING + PROFIL MUSISI */
    .kamu-hero-compact { padding: 1.1rem 1.25rem; }
    .kamu-hero-row { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
    .kamu-avatar-sm { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.5); flex-shrink: 0; }
    .kamu-hero-info { min-width: 0; }
    .kamu-name-sm { font-family: 'Sora',sans-serif; font-size: 1.05rem; font-weight: 700; color: #fff; }
    .kamu-follow-row { display: flex; gap: 12px; flex-wrap: wrap; font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 3px; }
    .kamu-follow-row b { color: #fff; }
    .kamu-private { color: rgba(255,255,255,0.6); }
    .kamu-mus-card { position: relative; z-index: 1; margin-top: 12px; background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; padding: 10px 12px; }
    .kamu-mus-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .kamu-mus-title { font-size: 12px; font-weight: 700; color: #fff; letter-spacing: 0.02em; }
    .kamu-mus-link { color: #fff; text-decoration: none; font-size: 14px; padding: 2px 6px; border-radius: 6px; opacity: 0.85; }
    .kamu-mus-link:hover { background: rgba(255,255,255,0.18); opacity: 1; }
    .kamu-mus-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
    .kamu-mus-badge { font-size: 11px; padding: 2px 9px; border-radius: 20px; background: rgba(255,255,255,0.22); color: #fff; }
    .kamu-mus-badge.lv-pemula { background: #facc15; color: #5a3e00; font-weight: 700; }
    .kamu-mus-badge.lv-menengah { background: #38bdf8; color: #062b3a; font-weight: 700; }
    .kamu-mus-badge.lv-mahir { background: #4ade80; color: #053a1a; font-weight: 700; }
    .kamu-mus-badge.lv-profesional { background: #c084fc; color: #2e0a4a; font-weight: 700; }
    .kamu-mus-look { font-size: 12px; color: rgba(255,255,255,0.92); margin-top: 8px; }
    .kamu-mus-activate { position: relative; z-index: 1; display: block; margin-top: 12px; text-align: center; background: rgba(255,255,255,0.16); border: 1px dashed rgba(255,255,255,0.4); border-radius: 12px; padding: 10px; color: #fff; text-decoration: none; font-size: 13px; font-weight: 600; }
    .kamu-mus-activate:hover { background: rgba(255,255,255,0.24); }
    .kamu-stats-compact { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: 14px; margin-top: 12px; font-size: 12px; color: rgba(255,255,255,0.78); }
    .kamu-stats-compact b { color: #fff; }

    /* TABS — scrollable horizontal */
    .kamu-tabs {
        display: flex; gap: 4px; margin-bottom: 1.25rem;
        background: var(--card); border-radius: 14px;
        padding: 4px; border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow-x: auto; scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .kamu-tabs::-webkit-scrollbar { display: none; }
    .kamu-tab {
        flex: 0 0 auto; white-space: nowrap;
        padding: 8px 14px; border-radius: 10px;
        font-size: 12px; font-weight: 500; color: var(--text-3);
        background: transparent; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif; transition: 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .kamu-tab:hover { color: var(--text-2); background: var(--surface); }
    .kamu-tab.active {
        background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; box-shadow: 0 3px 10px var(--sky-glow);
    }

    /* TAB CONTENT */
    .kamu-tab-content { display: none; }
    .kamu-tab-content.active { display: block; }

    /* ===== CHORD ===== */
    .chord-inst { display:flex; gap:8px; margin-bottom:14px; flex-wrap:wrap; }
    .strum-dir { display:flex; align-items:center; gap:8px; margin:-4px 0 14px; font-size:12px; color:var(--text-3); }
    .strum-dir button { font-size:12px; padding:5px 12px; border-radius:20px; background:var(--surface); border:1px solid var(--border); color:var(--text-2); cursor:pointer; font-weight:600; }
    .strum-dir button:hover { border-color:var(--sky); color:var(--sky-dk); }
    .chord-instbtn { font-size:13px; padding:8px 18px; border-radius:20px; background:var(--surface); border:1px solid var(--border); color:var(--text-2); cursor:pointer; font-weight:600; }
    .chord-instbtn.active { background:var(--sky); color:#fff; border-color:var(--sky); }
    .chord-head { margin-bottom: 1rem; }
    .chord-title { font-family:'Sora',sans-serif; font-size:1rem; font-weight:600; color:var(--text-1); }
    .chord-sub { font-size:12px; color:var(--text-3); margin-top:4px; line-height:1.5; }
    .chord-legend { display:flex; flex-wrap:wrap; gap:12px; margin-top:8px; font-size:11px; color:var(--text-2); }
    .chord-legend b { color:var(--sky-dk); }
    .chord-filter { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }
    .chord-chip { font-size:12px; padding:5px 12px; border-radius:20px; background:var(--surface); border:1px solid var(--border); color:var(--text-2); cursor:pointer; transition:0.15s; }
    .chord-chip.active { background:var(--sky); color:#fff; border-color:var(--sky); }
    .chord-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(94px,1fr)); gap:10px; }
    .chord-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:10px 6px 9px; text-align:center; box-shadow:var(--shadow-sm); }
    .chord-card .cc-name { font-family:'Sora',sans-serif; font-weight:700; font-size:14px; color:var(--text-1); margin-bottom:4px; }
    .chord-card .cc-tip { font-size:10px; color:var(--text-3); margin-top:6px; line-height:1.4; }
    .chord-card svg { width:78px; height:auto; }
    .chord-tip { margin-top:1rem; font-size:12px; color:var(--text-2); background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:11px 14px; line-height:1.6; }
    .barre-player { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:14px; margin-top:12px; box-shadow:var(--shadow-sm); }
    .barre-diagram { overflow-x:auto; }
    .barre-diagram svg { width:100%; min-width:330px; height:auto; }
    .barre-row { display:flex; gap:16px; align-items:center; flex-wrap:wrap; margin-top:12px; }
    .barre-base { text-align:center; flex-shrink:0; }
    .barre-base svg { width:62px; height:auto; }
    .barre-base-lbl { font-size:11px; color:var(--text-3); margin-top:2px; }
    .barre-ctrl { flex:1; min-width:170px; }
    .barre-name { font-family:'Sora',sans-serif; font-size:2rem; font-weight:700; color:var(--sky-dk); line-height:1; }
    .barre-fret { font-size:12px; color:var(--text-3); margin:2px 0 10px; }
    .barre-btns { display:flex; align-items:center; gap:8px; }
    .barre-btns button { background:var(--surface); border:1px solid var(--border); color:var(--text-2); border-radius:8px; padding:6px 10px; font-size:12px; cursor:pointer; }
    .barre-btns button:hover { border-color:var(--sky); color:var(--sky-dk); }
    .barre-btns input[type=range] { flex:1; accent-color:var(--sky); }
    .barre-sound { margin-top:10px; background:var(--sky); color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:600; cursor:pointer; }
    .barre-sound:hover { filter:brightness(0.92); }

    /* ===== GUITAR TUNER ===== */
    .tuner-card {
        background: #080f1a;
        border: 1px solid rgba(56,168,204,0.12);
        border-radius: 24px; padding: 1.5rem 1rem 1.25rem;
        text-align: center; color: #e8f4fa;
        box-shadow: 0 12px 40px rgba(0,0,0,0.5);
    }
    .tuner-label {
        font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase;
        color: #5b8298; font-weight: 700; margin-bottom: 0.5rem;
    }
    .tuner-steps {
        font-size: 12px; color: #9fc0d0; margin-bottom: 1rem; line-height: 1.5;
    }
    .tuner-steps b { color: #7EC8E3; font-weight: 700; }
    /* ===== Meter ===== */
    .tuner-meter-wrap {
        position: relative; width: calc(100% - 24px); max-width: 320px;
        margin: 0 auto 0.4rem;
    }
    /* angka cent besar di atas meter */
    .tuner-cents {
        font-family: 'Sora', sans-serif;
        font-size: 1.35rem; font-weight: 800; min-height: 30px; line-height: 30px;
        color: #8fb3c4; margin: 0 0 0.6rem; letter-spacing: 0.02em;
        font-variant-numeric: tabular-nums; transition: color 0.15s;
    }
    .tuner-cents.in-tune  { color: #22c55e; }
    .tuner-cents.too-low  { color: #fb923c; }
    .tuner-cents.too-high { color: #ef4444; }
    .tuner-meter {
        position: relative; width: 100%; height: 64px;
        border-radius: 14px; overflow: hidden;
        background:
            radial-gradient(120% 140% at 50% 120%, rgba(56,168,204,0.08), transparent 60%),
            #0a1422;
        border: 1px solid rgba(56,168,204,0.14);
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
        opacity: 0.45; transition: opacity 0.3s;
    }
    .tuner-meter.active { opacity: 1; }
    /* garis-garis skala (tick) */
    .tuner-meter-ticks {
        position: absolute; left: 0; right: 0; top: 14px; bottom: 14px;
        background-image: repeating-linear-gradient(90deg,
            rgba(255,255,255,0.13) 0 1px, transparent 1px, transparent 10%);
    }
    /* zona "pas" hijau di tengah */
    .tuner-meter-zone {
        position: absolute; top: 0; bottom: 0; left: 50%; width: 12%;
        transform: translateX(-50%);
        background: linear-gradient(180deg, rgba(34,197,94,0), rgba(34,197,94,0.22), rgba(34,197,94,0));
        border-left: 1px solid rgba(34,197,94,0.35);
        border-right: 1px solid rgba(34,197,94,0.35);
    }
    /* garis tengah target */
    .tuner-meter-axis {
        position: absolute; top: 6px; bottom: 6px; left: 50%; width: 2px;
        background: rgba(34,197,94,0.55); transform: translateX(-50%);
    }
    /* jarum penunjuk yang meluncur */
    .tuner-meter-needle {
        position: absolute; top: 8px; bottom: 8px; left: 50%;
        width: 4px; border-radius: 3px; transform: translateX(-50%);
        background: #cfeaf5;
        transition: left 0.09s cubic-bezier(.22,.61,.36,1), background 0.15s, box-shadow 0.15s;
        box-shadow: 0 0 10px rgba(207,234,245,0.6);
    }
    .tuner-meter-needle::before {
        content: ''; position: absolute; top: -7px; left: 50%; transform: translateX(-50%);
        border-left: 6px solid transparent; border-right: 6px solid transparent;
        border-top: 8px solid currentColor;
    }
    .tuner-meter-needle { color: #cfeaf5; }
    .tuner-meter-needle.in-tune  { background: #22c55e; color: #22c55e; box-shadow: 0 0 18px rgba(34,197,94,0.95); width: 5px; }
    .tuner-meter-needle.too-low  { background: #fb923c; color: #fb923c; box-shadow: 0 0 12px rgba(251,146,60,0.8); }
    .tuner-meter-needle.too-high { background: #ef4444; color: #ef4444; box-shadow: 0 0 12px rgba(239,68,68,0.8); }
    .tuner-meter-labels {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.4);
        margin-top: 7px; padding: 0 2px;
    }
    .tuner-meter-labels .flat  { color: #fb923c; }
    .tuner-meter-labels .sharp { color: #ef4444; }
    .tuner-meter-labels .mid   { color: #22c55e; }
    /* Note */
    .tuner-note-big {
        font-family: 'Sora', sans-serif;
        font-size: 4.5rem; font-weight: 800; line-height: 1;
        color: #2a4a5a; letter-spacing: -3px;
        transition: color 0.12s, text-shadow 0.2s; margin: 0.4rem 0 0.1rem;
    }
    .tuner-note-big.active   { color: #7EC8E3; }
    .tuner-note-big.in-tune  { color: #22c55e; text-shadow: 0 0 28px rgba(34,197,94,0.6); }
    .tuner-note-big.too-low  { color: #fb923c; }
    .tuner-note-big.too-high { color: #ef4444; }
    /* ===== Headstock (gambar + area klik pasak) ===== */
    .tuner-headstock-wrap { margin: 0.25rem auto 0.75rem; width: 100%; max-width: 230px; }
    .tuner-hs { position: relative; width: 100%; margin: 0 auto; line-height: 0; }
    .tuner-hs-img { width: 100%; display: block; user-select: none; -webkit-user-drag: none; }
    /* tombol transparan di atas tiap pasak */
    .tuner-peg {
        position: absolute; transform: translate(-50%,-50%);
        width: 15%; aspect-ratio: 1/1;
        border-radius: 50%; border: 2.5px solid transparent;
        background: transparent; cursor: pointer; padding: 0;
        display: flex; align-items: center; justify-content: center;
        transition: 0.15s; -webkit-tap-highlight-color: transparent;
    }
    .tuner-peg .pg-txt {
        font-family: 'Sora',sans-serif; font-weight: 800; font-size: 13px;
        color: #fff; opacity: 0; transition: 0.15s;
        text-shadow: 0 1px 4px rgba(0,0,0,0.85); pointer-events: none;
    }
    .tuner-peg:hover { border-color: rgba(126,200,227,0.7); background: rgba(56,168,204,0.18); }
    .tuner-peg:hover .pg-txt { opacity: 1; }
    .tuner-peg.active  { border-color: #38A8CC; background: rgba(56,168,204,0.28); box-shadow: 0 0 14px rgba(56,168,204,0.6); }
    .tuner-peg.active .pg-txt  { opacity: 1; color: #cdeefb; }
    .tuner-peg.in-tune { border-color: #22c55e; background: rgba(34,197,94,0.30); box-shadow: 0 0 16px rgba(34,197,94,0.75); }
    .tuner-peg.in-tune .pg-txt { opacity: 1; color: #dcfce7; }
    /* Button */
    .tuner-btn {
        padding: 12px 36px; border-radius: 50px; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 700;
        background: linear-gradient(135deg, #38A8CC 0%, #2186A8 100%);
        color: #fff; box-shadow: 0 4px 16px rgba(56,168,204,0.4); transition: 0.2s;
        letter-spacing: 0.03em;
    }
    .tuner-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(56,168,204,0.5); }
    .tuner-btn.stop {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 4px 16px rgba(239,68,68,0.4);
    }
    .tuner-msg { font-size: 12px; color: #7a9db0; margin-top: 10px; min-height: 18px; }

    /* ===== NOTES ===== */
    .notes-form {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; padding: 1.25rem; margin-bottom: 1.25rem;
        box-shadow: var(--shadow);
    }
    .notes-form-header {
        display: flex; align-items: center; gap: 8px; margin-bottom: 10px;
    }
    .notes-form-header span { font-size: 18px; }
    .notes-form-title-label {
        font-family: 'Sora', sans-serif;
        font-size: 12px; font-weight: 600; color: var(--text-2);
    }
    .notes-input {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-1); font-size: 13px;
        padding: 9px 14px; outline: none; font-family: 'DM Sans', sans-serif;
        transition: 0.2s; margin-bottom: 8px;
    }
    .notes-input:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .notes-input::placeholder { color: var(--text-4); }
    .notes-textarea {
        width: 100%; background: var(--cream); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-1); font-size: 14px;
        padding: 10px 14px; outline: none; resize: none;
        min-height: 100px; line-height: 1.7; font-family: 'DM Sans', sans-serif;
        transition: 0.2s;
    }
    .notes-textarea:focus { border-color: var(--sky); box-shadow: 0 0 0 3px var(--sky-glow); }
    .notes-textarea::placeholder { color: var(--text-4); }
    .notes-form-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 10px; flex-wrap: wrap; gap: 8px;
    }
    .notes-colors {
        display: flex; gap: 6px; align-items: center;
    }
    .notes-color-dot {
        width: 20px; height: 20px; border-radius: 50%; cursor: pointer;
        border: 2px solid transparent; transition: 0.2s;
    }
    .notes-color-dot:hover { transform: scale(1.2); }
    .notes-color-dot.selected { border-color: var(--text-2); transform: scale(1.15); }
    .btn-save-note {
        padding: 8px 22px; border-radius: 20px; font-size: 12px;
        font-weight: 600; background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; border: none; cursor: pointer; transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 4px 12px var(--sky-glow);
    }
    .btn-save-note:hover { transform: translateY(-1px); box-shadow: var(--shadow); }

    /* NOTES GRID */
    .notes-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px; margin-top: 0.5rem;
    }
    .note-card {
        border-radius: 16px; padding: 1rem;
        border: 1px solid rgba(216,234,242,0.6);
        box-shadow: var(--shadow-sm);
        transition: 0.2s; position: relative;
        min-height: 120px;
    }
    .note-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
    .note-card-title {
        font-family: 'Sora', sans-serif;
        font-size: 12px; font-weight: 600; color: var(--text-1);
        margin-bottom: 6px;
    }
    .note-card-body {
        font-size: 13px; color: var(--text-2); line-height: 1.6;
        white-space: pre-wrap; word-break: break-word;
    }
    .note-card-date {
        font-size: 10px; color: var(--text-4);
        margin-top: 10px; letter-spacing: 0.05em;
    }
    .note-card-actions {
        position: absolute; top: 8px; right: 8px;
        display: flex; gap: 4px; opacity: 0; transition: 0.2s;
    }
    .note-card:hover .note-card-actions { opacity: 1; }
    .note-action-btn {
        width: 24px; height: 24px; border-radius: 50%;
        background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.08);
        font-size: 11px; cursor: pointer; display: flex;
        align-items: center; justify-content: center; transition: 0.15s;
        color: var(--text-3);
    }
    .note-action-btn:hover { background: #fff; color: var(--text-1); }
    .note-action-btn.delete:hover { color: #ef4444; }
    [data-theme="dark"] .note-action-btn { background: rgba(255,255,255,0.10); border-color: rgba(255,255,255,0.14); color: var(--text-2); }
    [data-theme="dark"] .note-action-btn:hover { background: rgba(255,255,255,0.18); color: var(--text-1); }

    .empty-notes {
        text-align: center; padding: 3rem 1rem; color: var(--text-4);
        background: var(--card); border-radius: 16px; border: 1px dashed var(--border);
    }
    .empty-notes p { font-size: 13px; margin-top: 0.75rem; }

    /* ===== POSTS ===== */
    .kamu-section-title {
        font-size: 10px; color: var(--text-3); letter-spacing: 0.2em;
        text-transform: uppercase; margin-bottom: 1rem;
        padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-lt);
        font-weight: 700;
    }
    .kamu-post {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 14px; padding: 1rem; margin-bottom: 0.75rem;
        box-shadow: var(--shadow-sm); transition: 0.2s; position: relative;
    }
    .kamu-post:hover { border-color: var(--sky-mid); box-shadow: var(--shadow); }
    .kamu-post-body { font-size: 13px; color: var(--text-2); line-height: 1.7; margin-bottom: 0.75rem; }
    .kamu-post-body-edit {
        width: 100%; background: var(--cream); border: 1px solid var(--sky);
        border-radius: 8px; color: var(--text-1); font-size: 13px;
        padding: 8px 12px; outline: none; resize: vertical;
        min-height: 60px; line-height: 1.7; font-family: 'DM Sans', sans-serif;
        margin-bottom: 8px; display: none;
        box-shadow: 0 0 0 3px var(--sky-glow);
    }
    .kamu-post-meta {
        display: flex; align-items: center; gap: 14px;
        font-size: 11px; color: var(--text-4); flex-wrap: wrap;
    }
    .kamu-post-meta span { display: flex; align-items: center; gap: 4px; }
    .kamu-post-location { color: var(--sky); }
    .kamu-post-actions {
        position: absolute; top: 10px; right: 10px;
        display: flex; gap: 4px; opacity: 0; transition: 0.2s;
    }
    .kamu-post:hover .kamu-post-actions { opacity: 1; }
    .kamu-post-btn {
        padding: 3px 10px; border-radius: 12px; font-size: 10px;
        font-weight: 500; cursor: pointer; border: 1px solid var(--border);
        background: var(--surface); color: var(--text-3);
        font-family: 'DM Sans', sans-serif; transition: 0.15s;
    }
    .kamu-post-btn:hover { background: var(--sky-lt); color: var(--sky-dk); border-color: var(--sky-mid); }
    .kamu-post-btn.save { background: var(--sky); color: #fff; border-color: var(--sky); display: none; }
    .kamu-post-btn.save:hover { background: var(--sky-dk); }
    .kamu-post-btn.delete:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }

    .empty-posts {
        text-align: center; padding: 3rem 1rem;
        background: var(--card); border-radius: 16px; border: 1px solid var(--border);
    }
    .empty-posts p { font-size: 13px; color: var(--text-3); margin-bottom: 1rem; }
    .btn-go-kita {
        display: inline-block; padding: 10px 24px; border-radius: 30px;
        background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; text-decoration: none; font-size: 13px; font-weight: 500;
        box-shadow: 0 4px 12px var(--sky-glow); transition: 0.2s;
    }
    .btn-go-kita:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

    /* EDIT MODAL */
    .note-modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(22,32,48,0.5); backdrop-filter: blur(6px);
        z-index: 1000; align-items: center; justify-content: center; padding: 1rem;
    }
    .note-modal-overlay.open { display: flex; }
    .note-modal {
        background: var(--card); border-radius: 24px;
        width: 100%; max-width: 460px; padding: 1.5rem;
        box-shadow: var(--shadow-xl); border: 1px solid var(--border);
    }
    .note-modal-title {
        font-family: 'Sora', sans-serif;
        font-size: 14px; font-weight: 600; color: var(--text-1);
        margin-bottom: 1rem;
    }
    .note-modal-actions { display: flex; gap: 8px; margin-top: 1rem; justify-content: flex-end; }
    .btn-modal-save {
        padding: 8px 20px; border-radius: 20px; font-size: 12px;
        font-weight: 600; background: var(--sky); color: #fff;
        border: none; cursor: pointer;
    }
    .btn-modal-cancel {
        padding: 8px 16px; border-radius: 20px; font-size: 12px;
        background: transparent; border: 1px solid var(--border);
        color: var(--text-3); cursor: pointer;
    }

    @media (max-width: 480px) {
        .notes-grid { grid-template-columns: 1fr 1fr; }
        .kamu-stats { gap: 0; }
    }

    /* ===== MATERI TAB ===== */
    .mat-tab-header { margin-bottom: 1rem; }
    .mat-tab-header h2 { font-family:'Sora',sans-serif;font-size:1rem;font-weight:700;color:var(--text-1); }
    .mat-tab-header p { font-size:12px;color:var(--text-3);margin-top:3px;line-height:1.5; }
    .mat-tab-stats { display:flex;gap:1rem;flex-wrap:wrap;font-size:11px;color:var(--text-3);margin-top:.5rem; }
    .mat-tab-stats b { color:var(--sky-dk); }
    .mat-filter-row { display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1rem; }
    .mat-fchip { padding:5px 12px;border-radius:20px;border:1px solid var(--border);background:var(--surface);color:var(--text-3);font-size:11px;font-weight:500;cursor:pointer;transition:.15s;font-family:inherit; }
    .mat-fchip:hover { border-color:var(--sky);color:var(--sky-dk); }
    .mat-fchip.active { background:var(--sky);color:#fff;border-color:var(--sky); }
    .mat-article-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px; }
    .mat-article-card { background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1rem;transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:7px; }
    .mat-article-card:hover { transform:translateY(-2px);box-shadow:var(--shadow);border-color:var(--sky-mid); }
    .mat-ac-top { display:flex;justify-content:space-between;align-items:center; }
    .mat-ac-pill { font-size:9px;font-weight:700;padding:2px 8px;border-radius:10px;color:#fff; }
    .mat-ac-time { font-size:10px;color:var(--text-4); }
    .mat-ac-title { font-family:'Sora',sans-serif;font-size:13px;font-weight:600;color:var(--text-1);line-height:1.35; }
    .mat-ac-excerpt { font-size:11.5px;color:var(--text-3);line-height:1.55;flex:1; }
    .mat-ac-footer { display:flex;align-items:center;justify-content:space-between;gap:6px;margin-top:3px; }
    .mat-ac-read { font-size:11px;color:var(--sky-dk);font-weight:600;text-decoration:none; }
    .mat-ac-dl { font-size:11px;padding:3px 10px;border-radius:10px;background:rgba(56,168,204,.1);border:1px solid rgba(56,168,204,.25);color:var(--sky-dk);font-weight:600;text-decoration:none;transition:.15s; }
    .mat-ac-dl:hover { background:var(--sky);color:#fff; }
    .mat-sec-label { font-size:9.5px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);font-weight:700;margin:1.1rem 0 .6rem;padding-bottom:.3rem;border-bottom:1px solid var(--border-lt); }

    /* Spotlight animation untuk tab Materi */
    @keyframes materiBlink {
        0%,100% { box-shadow: 0 0 0 0 rgba(56,168,204,0); }
        50%      { box-shadow: 0 0 0 6px rgba(56,168,204,0.35); }
    }
    .materi-highlight {
        animation: materiBlink 1.2s ease-in-out 4;
        background: linear-gradient(135deg, rgba(56,168,204,.25), rgba(56,168,204,.1)) !important;
        color: var(--sky-dk) !important;
        border: 1px solid var(--sky-mid) !important;
        position: relative;
    }
    .materi-highlight::after {
        content: 'Baru!';
        position: absolute;
        top: -7px;
        right: -3px;
        background: #f59e0b;
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 8px;
        letter-spacing: .04em;
        pointer-events: none;
    }
</style>
@endpush

@section('content')

{{-- PROFILE HERO: hanya untuk user yang login --}}
@auth
<div class="kamu-hero kamu-hero-compact">
    <div class="kamu-hero-row">
        <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" class="kamu-avatar-sm" alt="{{ $user->name }}">
        <div class="kamu-hero-info">
            <div class="kamu-name-sm">{{ $user->name }}</div>
            <div class="kamu-follow-row">
                <span><b>{{ $followers }}</b> pengikut</span>
                <span><b>{{ $following }}</b> diikuti</span>
                <span class="kamu-private">&#128274; privat</span>
            </div>
        </div>
    </div>

    {{-- Profil Musisi: display + pengaturan --}}
    @if($musician)
    <div class="kamu-mus-card">
        <div class="kamu-mus-head">
            <span class="kamu-mus-title">&#127928; Profil Musisi</span>
            <div>
                <a href="{{ route('musisi.show', $musician->id) }}" class="kamu-mus-link" title="Lihat di direktori">&#128065;</a>
                <a href="{{ route('musisi.edit') }}" class="kamu-mus-link" title="Edit profil musisi">&#9998;</a>
            </div>
        </div>
        <div class="kamu-mus-tags">
            @if($musician->skill_level)<span class="kamu-mus-badge lv-{{ $musician->skill_level }}">{{ ucfirst($musician->skill_level) }}</span>@endif
            @foreach($musician->rolesArray() as $r)<span class="kamu-mus-badge">{{ $r }}</span>@endforeach
            @foreach($musician->genresArray() as $g)<span class="kamu-mus-badge">{{ $g }}</span>@endforeach
        </div>
        @if($musician->looking_for)<div class="kamu-mus-look">&#128270; {{ $musician->looking_for }}</div>@endif
    </div>
    @else
    <a href="{{ route('musisi.edit') }}" class="kamu-mus-activate">&#127928; Aktifkan profil musisimu &rarr;</a>
    @endif

    <div class="kamu-stats-compact">
        <span><b>{{ $totalPosts }}</b> postingan</span>
        <span><b>{{ $totalLikes }}</b> like</span>
        <span><b>{{ $notes->count() }}</b> catatan</span>
        <span>sejak <b>{{ $user->created_at?->format('Y') ?? date('Y') }}</b></span>
    </div>
</div>

@endauth

{{-- Banner login mini untuk guest --}}
@guest
<div style="text-align:center;padding:.75rem 1rem;background:var(--card);border-radius:14px;margin-bottom:1rem;border:1px solid var(--border);">
    <p style="font-size:13px;color:var(--text-3);margin:0;">
        <a href="{{ route('google.login') }}" style="color:var(--sky-dk);font-weight:600;">Login</a> untuk lihat profil musisi, catatan, dan statistik kamu.
    </p>
</div>
@endguest

{{-- TABS --}}
<div class="kamu-tabs">
    @auth
    <button class="kamu-tab active" onclick="kamuTab('Notes', this)">
        &#128196; Catatan
    </button>
    <button class="kamu-tab" onclick="kamuTab('Posts', this)">
        &#128172; Postingan
    </button>
    @endauth
    <button class="kamu-tab @guest active @endguest" onclick="kamuTab('Tuner', this)">
        &#127928; Tuner
    </button>
    <button class="kamu-tab" onclick="kamuTab('Chord', this)">
        &#127932; Chord
    </button>
    <button class="kamu-tab" onclick="kamuTab('PotongLagu', this)">
        ✂️ Potong Lagu
    </button>
    <button class="kamu-tab" onclick="kamuTab('Konversi', this)">
        🔄 Konversi
    </button>
    <button class="kamu-tab" onclick="kamuTab('SplitInstrumen', this)">
        🎤 Karaoke
    </button>
    <button class="kamu-tab" id="kamuTabMateriBtn" onclick="kamuTab('Materi', this)">
        📚 Materi
    </button>
</div>

{{-- TAB: NOTES (hanya auth) --}}
@auth
<div class="kamu-tab-content active" id="kamuTabNotes" style="display:block">

    {{-- NOTE FORM --}}
    <div class="notes-form">
        <div class="notes-form-header">
            <span>&#128196;</span>
            <span class="notes-form-title-label">Tulis catatan baru</span>
        </div>
        <form method="POST" action="{{ route('kamu.note.store') }}" id="noteForm">
            @csrf
            <input type="hidden" name="color" id="noteColor" value="#FFF8F0">
            <input type="text" name="title" class="notes-input" placeholder="Judul catatan (opsional)...">
            <textarea name="body" class="notes-textarea" placeholder="Tulis apapun yang ada di pikiranmu — hanya kamu yang bisa membacanya." required></textarea>
            <div class="notes-form-footer">
                <div class="notes-colors">
                    @php
                    $noteColors = ['#FFF8F0','#E6F4FA','#F0FAF0','#FFF0F8','#FFFBE6','#F0F0FA'];
                    @endphp
                    @foreach($noteColors as $color)
                    <div class="notes-color-dot {{ $loop->first ? 'selected' : '' }}"
                         style="background:{{ $color }}; border-color: {{ $loop->first ? '#38A8CC' : 'transparent' }};"
                         onclick="selectColor('{{ $color }}', this)"></div>
                    @endforeach
                </div>
                <button type="submit" class="btn-save-note">Simpan</button>
            </div>
        </form>
    </div>

    {{-- NOTES GRID --}}
    @if($notes->count() > 0)
    <div class="notes-grid">
        @foreach($notes as $note)
        <div class="note-card" id="noteCard{{ $note->id }}"
             style="background:{{ $note->color }};">
            <div class="note-card-actions">
                <button class="note-action-btn"
                        onclick="editNote({{ $note->id }}, '{{ addslashes($note->title) }}', '{{ addslashes($note->body) }}', '{{ $note->color }}')"
                        title="Edit">&#9998;</button>
                <button class="note-action-btn delete"
                        onclick="deleteNote({{ $note->id }})"
                        title="Hapus">&#10005;</button>
            </div>
            @if($note->title)
            <div class="note-card-title">{{ $note->title }}</div>
            @endif
            <div class="note-card-body">{{ Str::limit($note->body, 150) }}</div>
            <div class="note-card-date">{{ $note->created_at?->format('d M Y · H:i') ?? '-' }}</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-notes">
        <div style="font-size:36px;">&#128196;</div>
        <p>Belum ada catatan.<br>Tulis sesuatu yang ingin kamu ingat.</p>
    </div>
    @endif

</div>

{{-- TAB: POSTS (hanya auth) --}}
<div class="kamu-tab-content" id="kamuTabPosts">
    <p class="kamu-section-title">&#128172; Semua postinganmu di Kita</p>

    @if($posts->count() > 0)
        @foreach($posts as $post)
        <div class="kamu-post" id="kamuPost{{ $post->id }}">
            <div class="kamu-post-actions">
                <button class="kamu-post-btn" id="editBtn{{ $post->id }}"
                        onclick="kamuEditPost({{ $post->id }})">Edit</button>
                <button class="kamu-post-btn save" id="saveBtn{{ $post->id }}"
                        onclick="kamuSavePost({{ $post->id }})">Simpan</button>
                <button class="kamu-post-btn delete"
                        onclick="kamuDeletePost({{ $post->id }})">Hapus</button>
            </div>
            <div class="kamu-post-body" id="kamuPostBody{{ $post->id }}">{{ $post->body }}</div>
            <textarea class="kamu-post-body-edit" id="kamuPostEdit{{ $post->id }}">{{ $post->body }}</textarea>
            <div class="kamu-post-meta">
                <span>&#128197; {{ $post->created_at?->format('d M Y H:i') ?? '-' }}</span>
                <span>&#9825; {{ $post->likes_count }}</span>
                <span>&#128172; {{ $post->comments_count }}</span>
                @if($post->location)
                <span class="kamu-post-location">&#128205; {{ $post->location }}</span>
                @endif
            </div>
        </div>
        @endforeach
    @else
    <div class="empty-posts">
        <div style="font-size:32px;">&#128172;</div>
        <p>Kamu belum pernah posting di Kita.</p>
        <a href="{{ route('kita') }}" class="btn-go-kita">Mulai posting</a>
    </div>
    @endif
</div>
@endauth

{{-- TAB: TUNER --}}
<div class="kamu-tab-content @guest active @endguest" id="kamuTabTuner">
<div class="tuner-card">

    <div class="tuner-label">Tuner Gitar &mdash; Standar EADGBE</div>
    
    {{-- Note besar --}}
    <div class="tuner-note-big" id="tunerNote">—</div>
    {{-- Status: arah putar + seberapa meleset --}}
    <div class="tuner-cents" id="tunerCents">Petik senar gitarmu</div>

    {{-- Meter jarum --}}
    <div class="tuner-meter-wrap">
        <div class="tuner-meter" id="tunerBarTrack">
            <div class="tuner-meter-ticks"></div>
            <div class="tuner-meter-zone"></div>
            <div class="tuner-meter-axis"></div>
            <div class="tuner-meter-needle" id="tunerBarCursor"></div>
        </div>
        <div class="tuner-meter-labels">
            <span class="flat">terlalu rendah</span><span class="mid">pas</span><span class="sharp">terlalu tinggi</span>
        </div>
    </div>

    {{-- Headstock (gambar + area klik pasak) --}}
    <div class="tuner-headstock-wrap">
        <div class="tuner-hs">
            <img class="tuner-hs-img" src="{{ asset('images/tuner/headstock.png') }}" alt="Headstock gitar EMINOR">
            {{-- Area klik transparan di tiap pasak (digeser keluar agar tak menimpa tuner). KIRI: D·A·E  KANAN: G·B·e --}}
            <button class="tuner-peg" style="left:-5%;top:24.5%"  data-freq="146.83" data-label="D" id="pegD"  onclick="tunerPickPeg(this)"><span class="pg-txt">D</span></button>
            <button class="tuner-peg" style="left:-5%;top:42%"  data-freq="110.00" data-label="A" id="pegA"  onclick="tunerPickPeg(this)"><span class="pg-txt">A</span></button>
            <button class="tuner-peg" style="left:-5%;top:62%"    data-freq="82.41"  data-label="E" id="pegE2" onclick="tunerPickPeg(this)"><span class="pg-txt">E</span></button>
            <button class="tuner-peg" style="left:105%;top:24.5%" data-freq="196.00" data-label="G" id="pegG"  onclick="tunerPickPeg(this)"><span class="pg-txt">G</span></button>
            <button class="tuner-peg" style="left:105%;top:42%" data-freq="246.94" data-label="B" id="pegB"  onclick="tunerPickPeg(this)"><span class="pg-txt">B</span></button>
            <button class="tuner-peg" style="left:105%;top:62%"   data-freq="329.63" data-label="e" id="pegE4" onclick="tunerPickPeg(this)"><span class="pg-txt">e</span></button>
        </div>
    </div>

    <button class="tuner-btn" id="tunerBtn" onclick="tunerToggle()">&#9654; Mulai Tuning</button>
    <div class="tuner-msg" id="tunerMsg">Tip: ketuk pasak di gambar untuk kunci 1 senar</div>

</div>
</div>

{{-- TAB: CHORD --}}
<div class="kamu-tab-content" id="kamuTabChord">
    <div class="chord-inst" id="chordInst">
        <span class="chord-instbtn active" data-i="gitar" onclick="chordInst('gitar',this)">&#127928; Gitar</span>
        <span class="chord-instbtn" data-i="piano" onclick="chordInst('piano',this)">&#127929; Piano</span>
        <span class="chord-instbtn" data-i="ukulele" onclick="chordInst('ukulele',this)">&#127926; Ukulele</span>
        <span class="chord-instbtn" data-i="bass" onclick="chordInst('bass',this)">&#127925; Bass</span>
    </div>

    <div class="strum-dir">
        <span>Arah genjreng:</span>
        <button type="button" id="strumDirBtn" onclick="toggleStrumDir()">&#11015;&#65039; Bawah</button>
    </div>

    <div id="chordGitar">
    <div class="chord-head">
        <div class="chord-title">&#127928; Chord Gitar untuk Pemula</div>
        <p class="chord-sub">Kunci dasar yang paling sering dipakai di lagu pop/indie — termasuk lagu EMINOR. Senar tebal di kiri = E rendah (senar 6).</p>
        <div class="chord-legend">
            <span><b>&times;</b> jangan dibunyikan</span>
            <span><b>o</b> senar terbuka</span>
            <span><b>1&ndash;4</b> jari (telunjuk&ndash;kelingking)</span>
        </div>
        <div class="chord-filter" id="chordFilter">
            <span class="chord-chip active" data-c="all" onclick="chordFilter('all',this)">Semua</span>
            <span class="chord-chip" data-c="mayor" onclick="chordFilter('mayor',this)">Mayor</span>
            <span class="chord-chip" data-c="minor" onclick="chordFilter('minor',this)">Minor</span>
            <span class="chord-chip" data-c="seven" onclick="chordFilter('seven',this)">Septim (7)</span>
        </div>
    </div>
    <div class="chord-grid" id="chordGrid"></div>

    <div class="chord-head" style="margin-top:1.5rem;">
        <div class="chord-title">&#127899; Chord Geser (Barre)</div>
        <p class="chord-sub">Satu bentuk, geser naik = nada naik. Bisa untuk semua kunci (termasuk #/&#9837;). Pakai jari telunjuk menekan semua senar di garis barre.</p>
    </div>
    <div class="chord-filter" id="barreShapes">
        <span class="chord-chip active" data-s="Emaj" onclick="barreShape('Emaj',this)">Bentuk E (F)</span>
        <span class="chord-chip" data-s="Emin" onclick="barreShape('Emin',this)">Bentuk Em (Fm)</span>
        <span class="chord-chip" data-s="Amaj" onclick="barreShape('Amaj',this)">Bentuk A</span>
        <span class="chord-chip" data-s="Amin" onclick="barreShape('Amin',this)">Bentuk Am</span>
        <span class="chord-chip" data-s="Dmaj" onclick="barreShape('Dmaj',this)">Bentuk D</span>
        <span class="chord-chip" data-s="Dmin" onclick="barreShape('Dmin',this)">Bentuk Dm</span>
        <span class="chord-chip" data-s="E7" onclick="barreShape('E7',this)">Bentuk E7</span>
        <span class="chord-chip" data-s="A7" onclick="barreShape('A7',this)">Bentuk A7</span>
    </div>
    <div class="barre-player">
        <div class="barre-diagram" id="barreDiagram"></div>
        <div class="barre-row">
            <div class="barre-base" onclick="barreBasePlay()" title="Ketuk untuk dengar bentuk dasar" style="cursor:pointer;">
                <div id="barreBaseSvg"></div>
                <div class="barre-base-lbl">Bentuk dasar: <b id="barreBaseName">E</b> &#128266;</div>
            </div>
            <div class="barre-ctrl">
                <div class="barre-name" id="barreName">F</div>
                <div class="barre-fret" id="barreFretLbl">Posisi fret 1</div>
                <div class="barre-btns">
                    <button type="button" onclick="barreMove(-1)">&#9664; turun</button>
                    <input type="range" id="barreFret" min="1" max="12" value="1" oninput="barreSlide()">
                    <button type="button" onclick="barreMove(1)">naik &#9654;</button>
                </div>
                <button type="button" class="barre-sound" onclick="barrePlay()">&#128266; Bunyikan chord</button>
            </div>
        </div>
    </div>

    <div class="chord-tip">&#128161; Tekan senar tepat di belakang garis fret (bukan di atasnya). Petik satu-satu dulu untuk cek tiap senar bunyi bersih, baru strum. Latih perpindahan <b>C &harr; G &harr; D &harr; Em</b> pelan-pelan tiap hari.</div>
    </div>{{-- /chordGitar --}}

    {{-- ===== PIANO ===== --}}
    <div id="chordPiano" style="display:none;">
        <div class="chord-head">
            <div class="chord-title">&#127929; Chord Piano untuk Pemula</div>
            <p class="chord-sub">Tiap chord = beberapa tuts ditekan bersamaan. Tuts <b style="color:#2186A8;">biru</b> = nada chord, <b style="color:#0f5a78;">biru tua</b> = nada dasar (root). Ketuk kartu untuk dengar.</p>
            <div class="chord-filter" id="pianoFilter">
                <span class="chord-chip active" data-c="all" onclick="pianoFilter('all',this)">Semua</span>
                <span class="chord-chip" data-c="mayor" onclick="pianoFilter('mayor',this)">Mayor</span>
                <span class="chord-chip" data-c="minor" onclick="pianoFilter('minor',this)">Minor</span>
                <span class="chord-chip" data-c="seven" onclick="pianoFilter('seven',this)">Septim (7)</span>
            </div>
        </div>
        <div class="chord-grid" id="pianoGrid"></div>

        <div class="chord-head" style="margin-top:1.5rem;">
            <div class="chord-title">&#127899; Chord Geser (Transpose)</div>
            <p class="chord-sub">Satu bentuk, geser nada dasar naik/turun = chord baru. Konsep sama dengan gitar.</p>
        </div>
        <div class="chord-filter" id="pianoQual">
            <span class="chord-chip active" data-q="maj" onclick="pianoQual('maj',this)">Mayor</span>
            <span class="chord-chip" data-q="min" onclick="pianoQual('min',this)">Minor</span>
            <span class="chord-chip" data-q="dom7" onclick="pianoQual('dom7',this)">7</span>
            <span class="chord-chip" data-q="m7" onclick="pianoQual('m7',this)">m7</span>
            <span class="chord-chip" data-q="maj7" onclick="pianoQual('maj7',this)">maj7</span>
        </div>
        <div class="barre-player">
            <div class="barre-diagram" id="pianoDiagram"></div>
            <div class="barre-row">
                <div class="barre-ctrl">
                    <div class="barre-name" id="pianoName">C</div>
                    <div class="barre-fret" id="pianoRootLbl">Nada dasar C</div>
                    <div class="barre-btns">
                        <button type="button" onclick="pianoMove(-1)">&#9664; turun</button>
                        <input type="range" id="pianoRoot" min="0" max="11" value="0" oninput="pianoSlide()">
                        <button type="button" onclick="pianoMove(1)">naik &#9654;</button>
                    </div>
                    <button type="button" class="barre-sound" onclick="pianoPlayCur()">&#128266; Bunyikan chord</button>
                </div>
            </div>
        </div>
        <div class="chord-tip">&#128161; Mayor &amp; minor cuma beda satu tuts (nada tengah). Coba dengar bedanya pakai tombol Bunyikan.</div>
    </div>

    {{-- ===== UKULELE ===== --}}
    <div id="chordUkulele" style="display:none;">
        <div class="chord-head">
            <div class="chord-title">&#127926; Chord Ukulele untuk Pemula</div>
            <p class="chord-sub">Senar G&ndash;C&ndash;E&ndash;A (dari atas). Ketuk kartu untuk dengar. <b>&times;</b> tidak dibunyikan, <b>o</b> senar terbuka.</p>
            <div class="chord-filter" id="ukeFilter">
                <span class="chord-chip active" data-c="all" onclick="ukeFilter('all',this)">Semua</span>
                <span class="chord-chip" data-c="mayor" onclick="ukeFilter('mayor',this)">Mayor</span>
                <span class="chord-chip" data-c="minor" onclick="ukeFilter('minor',this)">Minor</span>
                <span class="chord-chip" data-c="seven" onclick="ukeFilter('seven',this)">Septim (7)</span>
            </div>
        </div>
        <div class="chord-grid" id="ukeGrid"></div>

        <div class="chord-head" style="margin-top:1.5rem;">
            <div class="chord-title">&#127899; Chord Geser (Barre)</div>
            <p class="chord-sub">Satu bentuk, geser naik = nada naik. Pakai bentuk A (telunjuk nge-barre semua senar).</p>
        </div>
        <div class="chord-filter" id="ukeShapes">
            <span class="chord-chip active" data-s="Amaj" onclick="ukeShape('Amaj',this)">Bentuk A</span>
            <span class="chord-chip" data-s="Amin" onclick="ukeShape('Amin',this)">Bentuk Am</span>
            <span class="chord-chip" data-s="A7" onclick="ukeShape('A7',this)">Bentuk A7</span>
        </div>
        <div class="barre-player">
            <div class="barre-diagram" id="ukeDiagram"></div>
            <div class="barre-row">
                <div class="barre-ctrl">
                    <div class="barre-name" id="ukeName">A&#9839;</div>
                    <div class="barre-fret" id="ukeFretLbl">Posisi fret 1</div>
                    <div class="barre-btns">
                        <button type="button" onclick="ukeMove(-1)">&#9664; turun</button>
                        <input type="range" id="ukeFret" min="1" max="10" value="1" oninput="ukeSlide()">
                        <button type="button" onclick="ukeMove(1)">naik &#9654;</button>
                    </div>
                    <button type="button" class="barre-sound" onclick="ukePlayCur()">&#128266; Bunyikan chord</button>
                </div>
            </div>
        </div>
        <div class="chord-tip">&#128161; Ukulele cuma 4 senar &mdash; paling cepat buat ngiringi nyanyi. Coba <b>C &harr; Am &harr; F &harr; G</b>.</div>
    </div>

    {{-- ===== BASS ===== --}}
    <div id="chordBass" style="display:none;">
        <div class="chord-head">
            <div class="chord-title">&#127925; Bass untuk Pemula</div>
            <p class="chord-sub">Senar E&ndash;A&ndash;D&ndash;G. Pola dasar bass = <b>root + kuint + oktaf</b> (tanpa nada ke-3, jadi cocok untuk mayor maupun minor). Ketuk kartu untuk dengar polanya.</p>
        </div>
        <div class="chord-grid" id="bassGrid"></div>

        <div class="chord-head" style="margin-top:1.5rem;">
            <div class="chord-title">&#127899; Geser Nada Dasar</div>
            <p class="chord-sub">Geser posisi root di senar E &mdash; pola oktaf ikut pindah. Pola ini bisa dipakai untuk chord apa pun.</p>
        </div>
        <div class="barre-player">
            <div class="barre-diagram" id="bassDiagram"></div>
            <div class="barre-row">
                <div class="barre-ctrl">
                    <div class="barre-name" id="bassName">C</div>
                    <div class="barre-fret" id="bassFretLbl">Root fret 8 (senar E)</div>
                    <div class="barre-btns">
                        <button type="button" onclick="bassMove(-1)">&#9664; turun</button>
                        <input type="range" id="bassFret" min="1" max="12" value="8" oninput="bassSlide()">
                        <button type="button" onclick="bassMove(1)">naik &#9654;</button>
                    </div>
                    <button type="button" class="barre-sound" onclick="bassPlayCur()">&#128266; Bunyikan pola</button>
                </div>
            </div>
        </div>
        <div class="chord-tip">&#128161; Di bass, kunci utamanya <b>root</b> tiap chord, lalu hias pakai kuint &amp; oktaf. Dengar polanya, lalu tiru.</div>
    </div>
</div>

{{-- TAB: POTONG LAGU (flat, no popup) --}}
<div class="kamu-tab-content" id="kamuTabPotongLagu">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;">
        <div style="padding:.7rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
            <span style="font-size:13px;font-weight:600;color:var(--text);">✂️ Pemotong Lagu Online</span>
            <span style="font-size:11px;color:var(--text-3);">Diproses di browser · tidak diunggah ke server</span>
        </div>
        <div style="padding:1rem;">
            {{-- Drop zone --}}
            <div id="kacDrop" onclick="document.getElementById('kacFile').click()" style="border:2px dashed var(--border);border-radius:12px;padding:1.5rem;text-align:center;cursor:pointer;transition:.2s;background:var(--bg-2);" onmouseover="this.style.borderColor='#38bdf8'" onmouseout="this.style.borderColor=''">
                <div style="font-size:1.6rem;margin-bottom:.35rem;">🎵</div>
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:.2rem;">Ketuk atau seret file audio</div>
                <div style="font-size:11px;color:var(--text-3);">MP3 · WAV · OGG · FLAC · M4A · Maks 100 MB</div>
            </div>
            <input type="file" id="kacFile" accept="audio/*" style="display:none">

            {{-- Editor --}}
            <div id="kacEditor" style="display:none;margin-top:.75rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;margin-bottom:.4rem;">
                    <span id="kacName" style="font-weight:600;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:65%;"></span>
                    <span id="kacMeta" style="color:var(--text-3);flex-shrink:0;"></span>
                </div>

                {{-- Waveform canvas --}}
                <div id="kacWaveWrap" style="position:relative;border-radius:10px;overflow:hidden;background:#070d18;touch-action:none;">
                    <canvas id="kacWave" style="display:block;width:100%;height:130px;"></canvas>
                </div>
                <div style="font-size:10px;color:var(--text-3);text-align:center;margin:.2rem 0 .45rem;">Seret △ cyan(Mulai) / △ kuning(Akhir) · pinch/scroll untuk zoom</div>

                {{-- Zoom bar --}}
                <div style="display:flex;align-items:center;gap:5px;margin-bottom:.5rem;">
                    <button onclick="kacZoomIn()"    style="padding:4px 9px;border-radius:6px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-3);font-size:12px;cursor:pointer;line-height:1;">🔍+</button>
                    <button onclick="kacZoomOut()"   style="padding:4px 9px;border-radius:6px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-3);font-size:12px;cursor:pointer;line-height:1;">🔍−</button>
                    <button onclick="kacZoomReset()" style="padding:4px 9px;border-radius:6px;border:1px solid var(--border);background:var(--bg-2);color:var(--text-3);font-size:12px;cursor:pointer;line-height:1;">↺</button>
                    <div id="kacScrollWrap" style="flex:1;height:5px;background:var(--border);border-radius:3px;position:relative;cursor:grab;">
                        <div id="kacScrollBar" style="position:absolute;top:0;height:100%;background:rgba(56,189,248,.35);border:1px solid #38bdf8;border-radius:3px;left:0;width:100%;"></div>
                    </div>
                    <span id="kacZoomLbl" style="font-size:10px;color:var(--text-3);white-space:nowrap;min-width:46px;text-align:right;">Semua</span>
                </div>

                {{-- Selection info --}}
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:.5rem;flex-wrap:wrap;">
                    <span style="font-size:11px;color:#22d3ee;font-weight:700;font-variant-numeric:tabular-nums;">▶ <span id="kacSelS">0:00.0</span></span>
                    <span style="color:var(--text-3);">→</span>
                    <span style="font-size:11px;color:#f59e0b;font-weight:700;font-variant-numeric:tabular-nums;"><span id="kacSelE">0:00.0</span> ◀</span>
                    <span style="color:var(--text-3);">|</span>
                    <span style="font-size:11px;color:#22c55e;font-weight:700;font-variant-numeric:tabular-nums;"><span id="kacSelD">0:00.0</span></span>
                </div>

                {{-- Controls --}}
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    <button id="kacBtnPlay"  onclick="kacPlay()"    style="padding:9px 12px;background:var(--surface,#1e293b);border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;min-height:40px;">▶ Play</button>
                    <button id="kacBtnPause" onclick="kacPause()"   style="display:none;padding:9px 12px;background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.3);color:#facc15;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;min-height:40px;">⏸ Pause</button>
                    <button                  onclick="kacPreview()" style="padding:9px 12px;background:rgba(14,165,233,.1);border:1px solid rgba(14,165,233,.3);color:#38bdf8;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;min-height:40px;">▶ Preview</button>
                    <button                  onclick="kacStop()"    style="padding:9px 10px;background:var(--surface,#1e293b);border:1px solid var(--border);color:var(--text-3);border-radius:8px;font-size:12px;cursor:pointer;min-height:40px;">⏹</button>
                    <select id="kacFmt" style="padding:6px 8px;border-radius:7px;border:1px solid var(--border);background:var(--surface,#1e293b);color:var(--text-3);font-size:11px;cursor:pointer;min-height:40px;">
                        <option value="mp3-128">MP3 128k</option>
                        <option value="mp3-192">MP3 192k</option>
                        <option value="mp3-320">MP3 320k</option>
                        <option value="wav">WAV</option>
                    </select>
                    <button id="kacBtnCut"   onclick="kacCut()"    style="flex:1;padding:9px;background:linear-gradient(135deg,#0ea5e9,#0369a1);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;min-height:40px;">✂️ Potong &amp; Unduh</button>
                </div>
                <div id="kacStatus" style="font-size:11px;color:var(--text-3);margin-top:5px;min-height:14px;"></div>

                {{-- Result --}}
                <div id="kacResult" style="display:none;margin-top:.65rem;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:.7rem;">
                    <div style="font-size:12px;font-weight:700;color:#22c55e;margin-bottom:.4rem;">✅ Siap diunduh</div>
                    <audio id="kacPlayer" controls style="width:100%;height:36px;margin-bottom:.4rem;"></audio>
                    <a id="kacDl" download style="display:inline-flex;align-items:center;gap:5px;background:#22c55e;color:#fff;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">⬇️ Unduh</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TAB: KONVERSI AUDIO --}}
<div class="kamu-tab-content" id="kamuTabKonversi" style="display:none;padding:1rem 0;">
    <div style="margin-bottom:1rem;">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">🔄 Konversi Audio</div>
        <div style="font-size:12px;color:var(--text-3);">Ubah format audio langsung di browser — tanpa upload ke server. Input: MP3 · WAV · OGG · FLAC · AAC · M4A</div>
    </div>

    {{-- Drop zone --}}
    <div id="kcvDrop" style="border:2px dashed var(--border);border-radius:12px;padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s;">
        <div style="font-size:2rem;margin-bottom:.5rem;">🎵</div>
        <div style="font-size:13px;color:var(--text-3);margin-bottom:.6rem;">Seret file audio ke sini atau</div>
        <label style="display:inline-block;padding:7px 18px;background:var(--accent,#6366f1);color:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
            Pilih File
            <input type="file" id="kcvFile" accept="audio/*" style="display:none;">
        </label>
    </div>

    {{-- Editor (hidden until file loaded) --}}
    <div id="kcvEditor" style="display:none;margin-top:1rem;">
        <div id="kcvInfo" style="font-size:12px;color:var(--text-3);margin-bottom:.75rem;font-variant-numeric:tabular-nums;"></div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:.75rem;">
            <span style="font-size:12px;color:var(--text-3);">Konversi ke:</span>
            <select id="kcvFmt" style="padding:7px 10px;border-radius:7px;border:1px solid var(--border);background:var(--surface,#1e293b);color:var(--text);font-size:12px;cursor:pointer;">
                <option value="mp3-128">🎵 MP3 128 kbps</option>
                <option value="mp3-192">🎵 MP3 192 kbps</option>
                <option value="mp3-320">🎵 MP3 320 kbps (HQ)</option>
                <option value="wav">📦 WAV (lossless PCM)</option>
            </select>
            <button id="kcvBtn" onclick="kcvConvert()" style="padding:7px 18px;background:var(--accent,#6366f1);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">🔄 Konversi</button>
            <button onclick="kcvReset()" style="padding:7px 12px;background:var(--surface,#1e293b);border:1px solid var(--border);color:var(--text-3);border-radius:8px;font-size:12px;cursor:pointer;">Ganti file</button>
        </div>
        <div id="kcvStatus" style="font-size:11px;color:var(--text-3);min-height:14px;margin-bottom:.5rem;"></div>

        {{-- Result --}}
        <div id="kcvResult" style="display:none;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:.75rem;">
            <div style="font-size:12px;font-weight:700;color:#22c55e;margin-bottom:.4rem;">✅ Konversi selesai</div>
            <audio id="kcvPlayer" controls style="width:100%;height:36px;margin-bottom:.5rem;"></audio>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a id="kcvDl" download style="display:inline-flex;align-items:center;gap:5px;background:#22c55e;color:#fff;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">⬇️ Unduh</a>
            </div>
        </div>
    </div>
</div>

{{-- TAB: SPLIT INSTRUMEN --}}
<div class="kamu-tab-content" id="kamuTabSplitInstrumen" style="display:none;padding:1rem 0;">
    <div style="margin-bottom:1rem;">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">🎤 Hapus Vokal (Karaoke)</div>
        <div style="font-size:12px;color:var(--text-3);">Pisah jadi <b>Instrumen (karaoke)</b> + <b>Vokal</b> — instan di browser, tanpa upload. Hasil terbaik untuk lagu <b>stereo</b>.</div>
    </div>
    <div id="ksiDrop" style="border:2px dashed var(--border);border-radius:12px;padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s;">
        <div style="font-size:2rem;margin-bottom:.5rem;">🎛️</div>
        <div style="font-size:13px;color:var(--text-3);margin-bottom:.6rem;">Seret file audio ke sini atau</div>
        <label style="display:inline-block;padding:7px 18px;background:var(--accent,#6366f1);color:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
            Pilih File<input type="file" id="ksiFile" accept="audio/*" style="display:none;">
        </label>
        <div style="font-size:10px;color:var(--text-3);margin-top:.4rem;">MP3 · WAV · OGG · FLAC · AAC — maks 150 MB</div>
    </div>
    <div id="ksiEditor" style="display:none;margin-top:1rem;">
        <div id="ksiInfo" style="font-size:12px;color:var(--text-3);margin-bottom:.75rem;padding:.5rem .75rem;background:rgba(0,0,0,.2);border-radius:8px;"></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:.75rem;">
            <select id="ksiFmt" style="padding:7px 10px;border-radius:7px;border:1px solid var(--border);background:var(--surface,#1e293b);color:var(--text);font-size:12px;cursor:pointer;">
                <option value="mp3-128">MP3 128 kbps</option>
                <option value="mp3-192">MP3 192 kbps</option>
                <option value="mp3-320">MP3 320 kbps (HQ)</option>
                <option value="wav">WAV (lossless)</option>
            </select>
            <button id="ksiBtn" onclick="ksiProcess()" style="flex:1;padding:8px 14px;background:linear-gradient(135deg,#6366f1,#4338ca);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">🎤 Hapus Vokal</button>
            <button onclick="ksiReset()" style="padding:7px 12px;background:var(--surface,#1e293b);border:1px solid var(--border);color:var(--text-3);border-radius:8px;font-size:12px;cursor:pointer;">🔄 Ganti</button>
        </div>
        <div id="ksiProgress" style="display:none;margin-bottom:.75rem;">
            <div style="background:var(--border);border-radius:6px;height:8px;overflow:hidden;margin-bottom:5px;">
                <div id="ksiPBar" style="height:100%;width:0%;background:linear-gradient(90deg,#6366f1,#818cf8);border-radius:6px;transition:width .4s ease;"></div>
            </div>
            <div id="ksiPLbl" style="font-size:11px;color:var(--text-3);text-align:center;"></div>
        </div>
        <div id="ksiResult" style="display:none;">
            <div style="font-size:12px;font-weight:700;color:#818cf8;margin-bottom:.65rem;">✅ Hasil siap:</div>
            <div id="ksiStemWrap"></div>
            <div style="display:flex;gap:8px;margin-top:.75rem;flex-wrap:wrap;">
                <button onclick="ksiDownloadAll()" style="flex:1;padding:9px;background:linear-gradient(135deg,#6366f1,#4338ca);color:#fff;border:none;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer;">📦 Download Semua (ZIP)</button>
                <button onclick="ksiReset()" style="padding:9px 14px;background:var(--surface,#1e293b);border:1px solid var(--border);color:var(--text-3);border-radius:9px;font-size:12px;cursor:pointer;">🔄 Split Lagi</button>
            </div>
        </div>
        <div id="ksiStatus" style="font-size:11px;color:var(--text-3);min-height:14px;margin-top:.5rem;"></div>
    </div>
</div>

{{-- TAB: MATERI --}}
<div class="kamu-tab-content" id="kamuTabMateri" style="display:none;">
    @php
    $catColors = ['teori' => '#38A8CC', 'produksi' => '#a855f7', 'kolaborasi' => '#f59e0b', 'rilis' => '#22c55e'];
    $catIcons  = ['teori' => '🎵', 'produksi' => '🎛️', 'kolaborasi' => '🤝', 'rilis' => '🚀'];
    $catNames  = ['teori' => 'Teori Musik', 'produksi' => 'Produksi', 'kolaborasi' => 'Kolaborasi', 'rilis' => 'Rilis & Branding'];
    @endphp

    <div class="mat-tab-header">
        <h2>📚 Materi Musik</h2>
        <p>Panduan lengkap dari teori sampai rilis — semua bisa diunduh sebagai file.</p>
        @if($articles->count() > 0)
        <div class="mat-tab-stats">
            <span><b>{{ $articles->count() }}</b> artikel</span>
            <span><b>4</b> kategori</span>
            <span>100% gratis · bisa diunduh</span>
        </div>
        @endif
    </div>

    @if($articles->count() > 0)
    <div class="mat-filter-row">
        <button class="mat-fchip active" onclick="matTabFilter('all', this)">Semua</button>
        @foreach($catNames as $cat => $label)
        @if($articles->where('category', $cat)->count() > 0)
        <button class="mat-fchip" onclick="matTabFilter('{{ $cat }}', this)">{{ $catIcons[$cat] }} {{ $label }}</button>
        @endif
        @endforeach
    </div>

    @foreach($catNames as $cat => $label)
    @php $catArticles = $articles->where('category', $cat); @endphp
    @if($catArticles->count() > 0)
    <div class="mat-sec-block" data-cat="{{ $cat }}">
        <div class="mat-sec-label">{{ $catIcons[$cat] ?? '' }} {{ $label }}</div>
        <div class="mat-article-grid">
            @foreach($catArticles as $a)
            <a href="{{ route('library.materi.show', $a->slug) }}" class="mat-article-card" target="_blank">
                <div class="mat-ac-top">
                    <span class="mat-ac-pill" style="background:{{ $catColors[$cat] ?? '#38A8CC' }}">{{ $label }}</span>
                    <span class="mat-ac-time">🕐 {{ $a->reading_time }} mnt</span>
                </div>
                <div class="mat-ac-title">{{ $a->title }}</div>
                <div class="mat-ac-excerpt">{{ $a->excerpt }}</div>
                <div class="mat-ac-footer" onclick="event.stopPropagation()">
                    <span class="mat-ac-read">Baca →</span>
                    <a href="{{ route('library.materi.download', $a->slug) }}" class="mat-ac-dl" onclick="event.stopPropagation()" target="_blank">⬇ Unduh</a>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
    @else
    <div style="text-align:center;padding:3rem 1rem;color:var(--text-4);background:var(--card);border-radius:16px;border:1px dashed var(--border);">
        <div style="font-size:2.5rem;margin-bottom:.5rem;">📚</div>
        <p style="font-size:13px;">Materi belum tersedia. Cek lagi nanti!</p>
        <a href="{{ route('library.materi') }}" style="font-size:12px;color:var(--sky-dk);text-decoration:none;margin-top:.5rem;display:inline-block;">Lihat di halaman materi →</a>
    </div>
    @endif
</div>

{{-- EDIT NOTE MODAL --}}
<div class="note-modal-overlay" id="noteModal" onclick="closeNoteModal()">
    <div class="note-modal" onclick="event.stopPropagation()">
        <div class="note-modal-title">&#9998; Edit Catatan</div>
        <input type="text" class="notes-input" id="editNoteTitle" placeholder="Judul...">
        <textarea class="notes-textarea" id="editNoteBody" style="margin-top:8px;" rows="5"></textarea>
        <div class="note-modal-actions">
            <button class="btn-modal-cancel" onclick="closeNoteModal()">Batal</button>
            <button class="btn-modal-save" onclick="saveNoteEdit()">Simpan</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/vocal-remover.js') }}?v={{ @filemtime(public_path('js/vocal-remover.js')) ?: 1 }}"></script>
<script>
var BASE_URL = '{{ url("") }}';
var csrfToken = '{{ csrf_token() }}';
var editingNoteId = null;

/* ===== TABS ===== */
function kamuTab(name, btn) {
    document.querySelectorAll('.kamu-tab-content').forEach(function(el){ el.classList.remove('active'); el.style.display='none'; });
    document.querySelectorAll('.kamu-tab').forEach(function(el){ el.classList.remove('active'); });
    var target = document.getElementById('kamuTab' + name);
    if (target) { target.classList.add('active'); target.style.display='block'; }
    btn.classList.add('active');
    // Stop tuner ketika pindah tab
    if (name !== 'Tuner' && tunerRunning) tunerStop();
    // Kalau user klik Materi tab — hapus highlight dan catat sudah dilihat
    if (name === 'Materi') {
        var mb = document.getElementById('kamuTabMateriBtn');
        if (mb) mb.classList.remove('materi-highlight');
        try { localStorage.setItem('materiTabSeen', '1'); } catch(e) {}
    }
}

/* ===== MATERI FILTER ===== */
function matTabFilter(cat, btn) {
    document.querySelectorAll('.mat-fchip').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.mat-sec-block').forEach(function(s){
        s.style.display = (cat === 'all' || s.dataset.cat === cat) ? '' : 'none';
    });
}

/* ===== MATERI TAB HIGHLIGHT (new feature guide) ===== */
(function() {
    try {
        var seen = localStorage.getItem('materiTabSeen');
        if (!seen) {
            var btn = document.getElementById('kamuTabMateriBtn');
            if (btn) {
                // Tunda sebentar supaya halaman sudah render penuh
                setTimeout(function() {
                    btn.classList.add('materi-highlight');
                    // Scroll ke tab bar kalau perlu
                    btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }, 800);
            }
        }
    } catch(e) {}
})();

/* ===== CHORD (kamus pemula) ===== */
var CHORDS = [
    {n:'C',  cat:'mayor', f:[-1,3,2,0,1,0], fg:[0,3,2,0,1,0], tip:'Jari 3-2-1, senar bawah dibiarkan terbuka.'},
    {n:'G',  cat:'mayor', f:[3,2,0,0,0,3], fg:[2,1,0,0,0,3], tip:'Jari 2 & 1 di senar 6-5, jari 3 di senar 1.'},
    {n:'D',  cat:'mayor', f:[-1,-1,0,2,3,2], fg:[0,0,0,1,3,2], tip:'Bentuk segitiga di 3 senar tipis.'},
    {n:'A',  cat:'mayor', f:[-1,0,2,2,2,0], fg:[0,0,1,2,3,0], tip:'Tiga jari sejajar di fret 2.'},
    {n:'E',  cat:'mayor', f:[0,2,2,1,0,0], fg:[0,2,3,1,0,0], tip:'Chord favorit EMINOR.'},
    {n:'F',  cat:'mayor', f:[-1,-1,3,2,1,1], fg:[0,0,3,2,1,1], tip:'Versi mudah 4 senar, tanpa barre.'},
    {n:'Am', cat:'minor', f:[-1,0,2,2,1,0], fg:[0,0,2,3,1,0], tip:'Mirip E digeser — nuansa galau.'},
    {n:'Em', cat:'minor', f:[0,2,2,0,0,0], fg:[0,2,3,0,0,0], tip:'Paling gampang, cuma 2 jari.'},
    {n:'Dm', cat:'minor', f:[-1,-1,0,2,3,1], fg:[0,0,0,2,3,1], tip:'Segitiga kecil, senar 1 ditekan.'},
    {n:'A7', cat:'seven', f:[-1,0,2,0,2,0], fg:[0,0,1,0,2,0], tip:''},
    {n:'D7', cat:'seven', f:[-1,-1,0,2,1,2], fg:[0,0,0,2,1,3], tip:''},
    {n:'E7', cat:'seven', f:[0,2,0,1,0,0], fg:[0,2,0,1,0,0], tip:''},
    {n:'G7', cat:'seven', f:[3,2,0,0,0,1], fg:[3,2,0,0,0,1], tip:''},
    {n:'C7', cat:'seven', f:[-1,3,2,3,1,0], fg:[0,3,2,4,1,0], tip:''},
    {n:'B7', cat:'seven', f:[-1,2,1,2,0,2], fg:[0,2,1,3,0,4], tip:''}
];
/* ===== BUNYI STRUM (Web Audio) ===== */
var chordAudioCtx=null;
var OPEN_HZ=[82.41,110.00,146.83,196.00,246.94,329.63]; // E A D G B e
var GTR_LABELS=['E','A','D','G','B','e'];
var STRUM_DIR=true; // true=genjreng bawah, false=atas
function toggleStrumDir(){ STRUM_DIR=!STRUM_DIR; var b=document.getElementById('strumDirBtn'); if(b) b.innerHTML = STRUM_DIR ? '&#11015;&#65039; Bawah' : '&#11014;&#65039; Atas'; }
function strumChord(fr, down){
    try { chordAudioCtx = chordAudioCtx || new (window.AudioContext||window.webkitAudioContext)(); } catch(e){ return; }
    if (chordAudioCtx.state==='suspended') chordAudioCtx.resume();
    var ctx=chordAudioCtx, t0=ctx.currentTime+0.02;
    var notes=[];
    for (var i=0;i<6;i++){ if(fr[i]>=0) notes.push({i:i, hz:OPEN_HZ[i]*Math.pow(2, fr[i]/12)}); }
    if (down===false) notes.reverse();   // up-strum
    notes.forEach(function(n, k){
        var t=t0+k*0.038;
        var osc=ctx.createOscillator(), g=ctx.createGain(), lp=ctx.createBiquadFilter();
        osc.type='triangle'; osc.frequency.value=n.hz;
        lp.type='lowpass'; lp.frequency.value=3200;
        g.gain.setValueAtTime(0, t);
        g.gain.linearRampToValueAtTime(0.20, t+0.006);
        g.gain.exponentialRampToValueAtTime(0.0008, t+1.6);
        osc.connect(lp); lp.connect(g); g.connect(ctx.destination);
        osc.start(t); osc.stop(t+1.7);
    });
}
function chordSvg(c, labels){
    var W=88, H=110, padX=12, top=24, bot=14, nf=4, ns=(c.f&&c.f.length)||6;
    var gw=W-padX*2, gh=H-top-bot, sx=gw/(ns-1), fy=gh/nf, fr=c.f, fg=c.fg||[];
    var fretted=fr.filter(function(v){return v>0;});
    var maxF=fretted.length?Math.max.apply(null,fretted):0, minF=fretted.length?Math.min.apply(null,fretted):0;
    var base=(maxF<=nf)?1:minF;
    var s='<svg viewBox="0 0 '+W+' '+H+'" xmlns="http://www.w3.org/2000/svg">';
    for(var f=0; f<=nf; f++){ var y=top+f*fy; s+='<line x1="'+padX+'" y1="'+y+'" x2="'+(padX+gw)+'" y2="'+y+'" stroke="#cfe1ec" stroke-width="1"/>'; }
    if(base===1){ s+='<rect x="'+padX+'" y="'+(top-3)+'" width="'+gw+'" height="3" rx="1" fill="#5a7282"/>'; }
    else { s+='<text x="'+(padX-3)+'" y="'+(top+fy*0.72)+'" font-size="9" text-anchor="end" fill="#7a9db0">'+base+'fr</text>'; }
    for(var i=0;i<ns;i++){ var x=padX+i*sx; s+='<line x1="'+x+'" y1="'+top+'" x2="'+x+'" y2="'+(top+gh)+'" stroke="#cfe1ec" stroke-width="1"/>'; }
    // garis barre: >=2 senar ditekan di fret terendah (base)
    var barreCols=[]; for(var i=0;i<ns;i++){ if(fr[i]>0 && fr[i]===base) barreCols.push(i); }
    var hasBarre = barreCols.length>=2, c1=barreCols[0], c2=barreCols[barreCols.length-1];
    if(hasBarre){ var by=top+0.5*fy; s+='<rect x="'+(padX+c1*sx-6.6)+'" y="'+(by-6.6)+'" width="'+((c2-c1)*sx+13.2)+'" height="13.2" rx="6.6" fill="#2186A8"/>'; }
    for(var i=0;i<ns;i++){
        var x=padX+i*sx, v=fr[i];
        if(v<0){ s+='<text x="'+x+'" y="'+(top-6)+'" font-size="11" text-anchor="middle" fill="#e0567a">&#215;</text>'; }
        else if(v===0){ s+='<circle cx="'+x+'" cy="'+(top-10)+'" r="3.6" fill="none" stroke="#7a9db0" stroke-width="1.3"/>'; }
        else {
            if(hasBarre && v===base){ if(i===c1){ var fn0=fg[i]; if(fn0>0) s+='<text x="'+x+'" y="'+(top+0.5*fy+3.3)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn0+'</text>'; } continue; }
            var pos=v-base+1, cy=top+(pos-0.5)*fy;
            s+='<circle cx="'+x+'" cy="'+cy+'" r="6.6" fill="#2186A8"/>';
            var fn=fg[i]; if(fn>0) s+='<text x="'+x+'" y="'+(cy+3.3)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn+'</text>';
        }
    }
    if(labels){ for(var li=0; li<ns; li++){ s+='<text x="'+(padX+li*sx)+'" y="'+(top+gh+11)+'" font-size="8" text-anchor="middle" fill="#9bb6c4">'+labels[li]+'</text>'; } }
    return s+'</svg>';
}
// Diagram LEHER PENUH (fret 1-15) untuk chord geser — senar atas→bawah (E..e)
function neckSvg(fr, fg){
    fg = fg || [];
    var ns=6, nFr=15, padL=28, padT=18, padR=10, padB=18, fw=20, sy=15;
    var gh=(ns-1)*sy, W=padL+nFr*fw+padR, H=padT+gh+padB, labels=['E','A','D','G','B','e'];
    var fx=function(f){ return padL+(f-0.5)*fw; }, ry=function(i){ return padT+i*sy; };
    var s='<svg viewBox="0 0 '+W+' '+H+'" xmlns="http://www.w3.org/2000/svg">';
    // titik penanda fret (inlay)
    [3,5,7,9,15].forEach(function(f){ s+='<circle cx="'+fx(f)+'" cy="'+(padT+2.5*sy)+'" r="2.7" fill="#d4e6f0"/>'; });
    s+='<circle cx="'+fx(12)+'" cy="'+(padT+1.5*sy)+'" r="2.7" fill="#d4e6f0"/><circle cx="'+fx(12)+'" cy="'+(padT+3.5*sy)+'" r="2.7" fill="#d4e6f0"/>';
    for(var i=0;i<ns;i++){ var y=ry(i); s+='<line x1="'+padL+'" y1="'+y+'" x2="'+(padL+nFr*fw)+'" y2="'+y+'" stroke="#cfe1ec" stroke-width="1"/>'; s+='<text x="4" y="'+(y+3.2)+'" font-size="9" fill="#7a9db0">'+labels[i]+'</text>'; }
    for(var f=1; f<=nFr; f++){ var x=padL+f*fw; s+='<line x1="'+x+'" y1="'+padT+'" x2="'+x+'" y2="'+(padT+gh)+'" stroke="#cfe1ec" stroke-width="1"/>'; }
    s+='<rect x="'+(padL-2)+'" y="'+padT+'" width="2.5" height="'+gh+'" fill="#5a7282"/>';
    [3,5,7,9,12,15].forEach(function(f){ s+='<text x="'+fx(f)+'" y="'+(padT+gh+10)+'" font-size="8" text-anchor="middle" fill="#9bb6c4">'+f+'</text>'; });
    for(var i=0;i<ns;i++){ if(fr[i]<0) s+='<text x="'+(padL-9)+'" y="'+(ry(i)+3.5)+'" font-size="10" text-anchor="middle" fill="#e0567a">&#215;</text>'; }
    var fretted=fr.filter(function(v){return v>0;}), minF=fretted.length?Math.min.apply(null,fretted):0;
    var bRows=[]; for(var i=0;i<ns;i++){ if(fr[i]>0 && fr[i]===minF) bRows.push(i); }
    if(bRows.length>=2){ var r1=bRows[0], r2=bRows[bRows.length-1], bx=fx(minF); s+='<rect x="'+(bx-6.2)+'" y="'+(ry(r1)-6.2)+'" width="12.4" height="'+((r2-r1)*sy+12.4)+'" rx="6.2" fill="#2186A8"/>'; }
    for(var i=0;i<ns;i++){ var v=fr[i]; if(v<=0) continue;
        if(bRows.length>=2 && v===minF){ if(i===bRows[0]){ var fn0=fg[i]; if(fn0>0) s+='<text x="'+fx(minF)+'" y="'+(ry(i)+3.2)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn0+'</text>'; } continue; }
        var x=fx(v), y=ry(i); s+='<circle cx="'+x+'" cy="'+y+'" r="6.2" fill="#2186A8"/>';
        var fn=fg[i]; if(fn>0) s+='<text x="'+x+'" y="'+(y+3.2)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn+'</text>';
    }
    return s+'</svg>';
}
function renderChords(cat){
    var grid=document.getElementById('chordGrid'); if(!grid) return;
    grid.innerHTML='';
    CHORDS.filter(function(c){ return !cat||cat==='all'||c.cat===cat; }).forEach(function(c){
        var card=document.createElement('div'); card.className='chord-card'; card.style.cursor='pointer'; card.title='Ketuk untuk dengar';
        card.innerHTML='<div class="cc-name">'+c.n+' <span style="font-size:10px;opacity:0.6;">&#128266;</span></div>'+chordSvg(c, GTR_LABELS)+(c.tip?'<div class="cc-tip">'+c.tip+'</div>':'');
        card.addEventListener('click', function(){ strumChord(c.f, STRUM_DIR); });
        grid.appendChild(card);
    });
}
function chordFilter(cat, el){
    document.querySelectorAll('#chordFilter .chord-chip').forEach(function(x){ x.classList.remove('active'); });
    el.classList.add('active'); renderChords(cat);
}
renderChords('all');

/* ===== CHORD GESER (BARRE) ===== */
var BARRE = {
    Emaj:{root:6, suf:'',  off:[0,2,2,1,0,0],     fg:[1,3,4,2,1,1], bfg:[0,2,3,1,0,0]},
    Emin:{root:6, suf:'m', off:[0,2,2,0,0,0],     fg:[1,3,4,1,1,1], bfg:[0,2,3,0,0,0]},
    E7:  {root:6, suf:'7', off:[0,2,0,1,0,0],     fg:[1,3,1,2,1,1], bfg:[0,2,0,1,0,0]},
    Amaj:{root:5, suf:'',  off:[-99,0,2,2,2,0],   fg:[0,1,3,3,3,1], bfg:[0,0,1,2,3,0]},
    Amin:{root:5, suf:'m', off:[-99,0,2,2,1,0],   fg:[0,1,3,4,2,1], bfg:[0,0,2,3,1,0]},
    A7:  {root:5, suf:'7', off:[-99,0,2,0,2,0],   fg:[0,1,3,1,3,1], bfg:[0,0,1,0,2,0]},
    Dmaj:{root:4, suf:'',  off:[-99,-99,0,2,3,2], fg:[0,0,1,2,4,3], bfg:[0,0,0,1,3,2]},
    Dmin:{root:4, suf:'m', off:[-99,-99,0,2,3,1], fg:[0,0,1,3,4,2], bfg:[0,0,0,2,3,1]}
};
var NOTES_E=['E','F','F#','G','G#','A','A#','B','C','C#','D','D#'];
var NOTES_A=['A','A#','B','C','C#','D','D#','E','F','F#','G','G#'];
var NOTES_D=['D','D#','E','F','F#','G','G#','A','A#','B','C','C#'];
var NOTES_BY_ROOT = {6:NOTES_E, 5:NOTES_A, 4:NOTES_D};
var barreState = {shape:'Emaj', fret:1};
function barreRender(){
    var sh=BARRE[barreState.shape], p=barreState.fret;
    var f=sh.off.map(function(o){ return o===-99 ? -1 : p+o; });
    var root=NOTES_BY_ROOT[sh.root][p%12];
    var dia=document.getElementById('barreDiagram'); if(dia) dia.innerHTML = neckSvg(f, sh.fg);
    var nm=document.getElementById('barreName'); if(nm) nm.textContent = root + sh.suf;
    var fl=document.getElementById('barreFretLbl'); if(fl) fl.textContent = 'Posisi fret ' + p;
    // bentuk dasar (posisi terbuka)
    var baseF=sh.off.map(function(o){ return o===-99 ? -1 : o; });
    var bs=document.getElementById('barreBaseSvg'); if(bs) bs.innerHTML = chordSvg({f:baseF, fg:sh.bfg||sh.fg}, GTR_LABELS);
    var bn=document.getElementById('barreBaseName'); if(bn) bn.textContent = NOTES_BY_ROOT[sh.root][0] + sh.suf;
}
function barreShape(s, el){
    barreState.shape=s;
    document.querySelectorAll('#barreShapes .chord-chip').forEach(function(x){ x.classList.remove('active'); });
    el.classList.add('active'); barreRender();
}
function barreMove(d){
    barreState.fret = Math.min(11, Math.max(1, barreState.fret + d));
    document.getElementById('barreFret').value = barreState.fret; barreRender();
}
function barreSlide(){ barreState.fret = parseInt(document.getElementById('barreFret').value,10)||1; barreRender(); }
function barrePlay(){ var sh=BARRE[barreState.shape], p=barreState.fret; strumChord(sh.off.map(function(o){return o===-99?-1:p+o;}), STRUM_DIR); }
function barreBasePlay(){ var sh=BARRE[barreState.shape]; strumChord(sh.off.map(function(o){return o===-99?-1:o;}), STRUM_DIR); }
barreRender();

/* ===== PIANO ===== */
var NOTE_NAMES=['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
var PIANO_TYPES={ maj:[0,4,7], min:[0,3,7], dom7:[0,4,7,10], m7:[0,3,7,10], maj7:[0,4,7,11] };
var QUAL_SUF={ maj:'', min:'m', dom7:'7', m7:'m7', maj7:'maj7' };
var PIANO_DICT=[
    {n:'C',cat:'mayor',root:0,type:'maj'},{n:'D',cat:'mayor',root:2,type:'maj'},{n:'E',cat:'mayor',root:4,type:'maj'},
    {n:'F',cat:'mayor',root:5,type:'maj'},{n:'G',cat:'mayor',root:7,type:'maj'},{n:'A',cat:'mayor',root:9,type:'maj'},
    {n:'Am',cat:'minor',root:9,type:'min'},{n:'Em',cat:'minor',root:4,type:'min'},{n:'Dm',cat:'minor',root:2,type:'min'},
    {n:'C7',cat:'seven',root:0,type:'dom7'},{n:'G7',cat:'seven',root:7,type:'dom7'},{n:'D7',cat:'seven',root:2,type:'dom7'},
    {n:'A7',cat:'seven',root:9,type:'dom7'},{n:'E7',cat:'seven',root:4,type:'dom7'}
];
function pianoSvg(semis, rootSemi){
    var whites=[0,2,4,5,7,9,11,12,14,16,17,19,21,23], blacks=[1,3,6,8,10,13,15,18,20,22];
    var ww=20, wh=78, bw=12, bh=48, padT=4, W=whites.length*ww, H=wh+padT+13;
    var set={}; (semis||[]).forEach(function(s){ set[(s%24+24)%24]=1; });
    var xW={}, s='<svg viewBox="0 0 '+W+' '+H+'" xmlns="http://www.w3.org/2000/svg">';
    whites.forEach(function(sem, i){ var x=i*ww; xW[sem]=x; var hl=set[sem], fill=hl?(sem===rootSemi?'#1E7FA8':'#7EC8E3'):'#fff';
        s+='<rect x="'+x+'" y="'+padT+'" width="'+(ww-1)+'" height="'+wh+'" rx="2.5" fill="'+fill+'" stroke="#9bb6c4" stroke-width="0.8"/>'; });
    s+='<text x="0" y="'+(padT+wh+10)+'" font-size="8" fill="#9bb6c4">C</text><text x="'+(7*ww)+'" y="'+(padT+wh+10)+'" font-size="8" fill="#9bb6c4">C</text>';
    blacks.forEach(function(sem){ var x=xW[sem-1]+ww-bw/2; var hl=set[sem], fill=hl?(sem===rootSemi?'#0b4a64':'#2186A8'):'#2a3a45';
        s+='<rect x="'+x+'" y="'+padT+'" width="'+bw+'" height="'+bh+'" rx="2" fill="'+fill+'"/>'; });
    return s+'</svg>';
}
function playPiano(semis){
    try { chordAudioCtx = chordAudioCtx || new (window.AudioContext||window.webkitAudioContext)(); } catch(e){ return; }
    if (chordAudioCtx.state==='suspended') chordAudioCtx.resume();
    var ctx=chordAudioCtx, t0=ctx.currentTime+0.02;
    semis.forEach(function(sm, k){ var t=t0+k*0.012, hz=130.81*Math.pow(2, sm/12);
        var osc=ctx.createOscillator(), g=ctx.createGain(), lp=ctx.createBiquadFilter();
        osc.type='triangle'; osc.frequency.value=hz; lp.type='lowpass'; lp.frequency.value=2800;
        g.gain.setValueAtTime(0,t); g.gain.linearRampToValueAtTime(0.17,t+0.005); g.gain.exponentialRampToValueAtTime(0.0008,t+1.8);
        osc.connect(lp); lp.connect(g); g.connect(ctx.destination); osc.start(t); osc.stop(t+1.9);
    });
}
function renderPiano(cat){
    var grid=document.getElementById('pianoGrid'); if(!grid) return; grid.innerHTML='';
    PIANO_DICT.filter(function(c){ return !cat||cat==='all'||c.cat===cat; }).forEach(function(c){
        var semis=PIANO_TYPES[c.type].map(function(iv){ return c.root+iv; });
        var card=document.createElement('div'); card.className='chord-card'; card.style.cursor='pointer'; card.title='Ketuk untuk dengar';
        card.innerHTML='<div class="cc-name">'+c.n+' <span style="font-size:10px;opacity:0.6;">&#128266;</span></div>'+pianoSvg(semis, c.root);
        card.addEventListener('click', function(){ playPiano(semis); });
        grid.appendChild(card);
    });
}
function pianoFilter(cat, el){ document.querySelectorAll('#pianoFilter .chord-chip').forEach(function(x){x.classList.remove('active');}); el.classList.add('active'); renderPiano(cat); }
var pianoState={qual:'maj', root:0};
function pianoRender(){
    var iv=PIANO_TYPES[pianoState.qual], semis=iv.map(function(x){ return pianoState.root+x; });
    var d=document.getElementById('pianoDiagram'); if(d) d.innerHTML=pianoSvg(semis, pianoState.root);
    var nm=document.getElementById('pianoName'); if(nm) nm.textContent=NOTE_NAMES[pianoState.root]+QUAL_SUF[pianoState.qual];
    var rl=document.getElementById('pianoRootLbl'); if(rl) rl.textContent='Nada dasar '+NOTE_NAMES[pianoState.root];
}
function pianoQual(q, el){ pianoState.qual=q; document.querySelectorAll('#pianoQual .chord-chip').forEach(function(x){x.classList.remove('active');}); el.classList.add('active'); pianoRender(); }
function pianoMove(d){ pianoState.root=(pianoState.root+d+12)%12; document.getElementById('pianoRoot').value=pianoState.root; pianoRender(); }
function pianoSlide(){ pianoState.root=parseInt(document.getElementById('pianoRoot').value,10)||0; pianoRender(); }
function pianoPlayCur(){ var iv=PIANO_TYPES[pianoState.qual]; playPiano(iv.map(function(x){ return pianoState.root+x; })); }
/* ===== ENGINE GENERIK (uke & bass) ===== */
function strumGen(fr, openHz, down){
    try { chordAudioCtx = chordAudioCtx || new (window.AudioContext||window.webkitAudioContext)(); } catch(e){ return; }
    if (chordAudioCtx.state==='suspended') chordAudioCtx.resume();
    var ctx=chordAudioCtx, t0=ctx.currentTime+0.02, notes=[];
    for (var i=0;i<fr.length;i++){ if(fr[i]>=0) notes.push(openHz[i]*Math.pow(2, fr[i]/12)); }
    if (down===false) notes.reverse();
    notes.forEach(function(hz, k){
        var t=t0+k*0.04, osc=ctx.createOscillator(), g=ctx.createGain(), lp=ctx.createBiquadFilter();
        osc.type='triangle'; osc.frequency.value=hz; lp.type='lowpass'; lp.frequency.value=3000;
        g.gain.setValueAtTime(0,t); g.gain.linearRampToValueAtTime(0.20,t+0.006); g.gain.exponentialRampToValueAtTime(0.0008,t+1.6);
        osc.connect(lp); lp.connect(g); g.connect(ctx.destination); osc.start(t); osc.stop(t+1.7);
    });
}
/* leher penuh generik (N senar) */
function neckSvgG(fr, fg, labels){
    fg=fg||[]; var ns=labels.length, nFr=15, padL=28, padT=18, padB=18, fw=20, sy=15;
    var gh=(ns-1)*sy, W=padL+nFr*fw+10, H=padT+gh+padB, mid=(ns-1)/2;
    var fx=function(f){return padL+(f-0.5)*fw;}, ry=function(i){return padT+i*sy;};
    var s='<svg viewBox="0 0 '+W+' '+H+'" xmlns="http://www.w3.org/2000/svg">';
    [3,5,7,9,15].forEach(function(f){ s+='<circle cx="'+fx(f)+'" cy="'+(padT+mid*sy)+'" r="2.7" fill="#d4e6f0"/>'; });
    s+='<circle cx="'+fx(12)+'" cy="'+(padT+(mid-1)*sy)+'" r="2.7" fill="#d4e6f0"/><circle cx="'+fx(12)+'" cy="'+(padT+(mid+1)*sy)+'" r="2.7" fill="#d4e6f0"/>';
    for(var i=0;i<ns;i++){ var y=ry(i); s+='<line x1="'+padL+'" y1="'+y+'" x2="'+(padL+nFr*fw)+'" y2="'+y+'" stroke="#cfe1ec" stroke-width="1"/>'; s+='<text x="4" y="'+(y+3.2)+'" font-size="9" fill="#7a9db0">'+labels[i]+'</text>'; }
    for(var f=1;f<=nFr;f++){ var x=padL+f*fw; s+='<line x1="'+x+'" y1="'+padT+'" x2="'+x+'" y2="'+(padT+gh)+'" stroke="#cfe1ec" stroke-width="1"/>'; }
    s+='<rect x="'+(padL-2)+'" y="'+padT+'" width="2.5" height="'+gh+'" fill="#5a7282"/>';
    [3,5,7,9,12,15].forEach(function(f){ s+='<text x="'+fx(f)+'" y="'+(padT+gh+10)+'" font-size="8" text-anchor="middle" fill="#9bb6c4">'+f+'</text>'; });
    for(var i=0;i<ns;i++){ if(fr[i]<0) s+='<text x="'+(padL-9)+'" y="'+(ry(i)+3.5)+'" font-size="10" text-anchor="middle" fill="#e0567a">&#215;</text>'; }
    var fretted=fr.filter(function(v){return v>0;}), minF=fretted.length?Math.min.apply(null,fretted):0;
    var bRows=[]; for(var i=0;i<ns;i++){ if(fr[i]>0&&fr[i]===minF) bRows.push(i); }
    if(bRows.length>=2){ var r1=bRows[0], r2=bRows[bRows.length-1], bx=fx(minF); s+='<rect x="'+(bx-6.2)+'" y="'+(ry(r1)-6.2)+'" width="12.4" height="'+((r2-r1)*sy+12.4)+'" rx="6.2" fill="#2186A8"/>'; }
    for(var i=0;i<ns;i++){ var v=fr[i]; if(v<=0) continue;
        if(bRows.length>=2 && v===minF){ if(i===bRows[0]){ var fn0=fg[i]; if(fn0>0) s+='<text x="'+fx(minF)+'" y="'+(ry(i)+3.2)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn0+'</text>'; } continue; }
        var x=fx(v), y=ry(i); s+='<circle cx="'+x+'" cy="'+y+'" r="6.2" fill="#2186A8"/>';
        var fn=fg[i]; if(fn>0) s+='<text x="'+x+'" y="'+(y+3.2)+'" font-size="9" text-anchor="middle" fill="#fff" font-weight="700">'+fn+'</text>';
    }
    return s+'</svg>';
}

/* ===== UKULELE (G C E A) ===== */
var UKE_OPEN=[392.00,261.63,329.63,440.00], UKE_LABELS=['G','C','E','A'];
var UKE_CHORDS=[
    {n:'C', cat:'mayor', f:[0,0,0,3], fg:[0,0,0,3], tip:'Cuma 1 jari!'},
    {n:'F', cat:'mayor', f:[2,0,1,0], fg:[2,0,1,0], tip:''},
    {n:'G', cat:'mayor', f:[0,2,3,2], fg:[0,1,3,2], tip:''},
    {n:'D', cat:'mayor', f:[2,2,2,0], fg:[1,2,3,0], tip:'Tiga jari rapat.'},
    {n:'A', cat:'mayor', f:[2,1,0,0], fg:[2,1,0,0], tip:''},
    {n:'Am', cat:'minor', f:[2,0,0,0], fg:[2,0,0,0], tip:'Cuma 1 jari.'},
    {n:'Em', cat:'minor', f:[0,4,3,2], fg:[0,3,2,1], tip:''},
    {n:'Dm', cat:'minor', f:[2,2,1,0], fg:[2,3,1,0], tip:''},
    {n:'C7', cat:'seven', f:[0,0,0,1], fg:[0,0,0,1], tip:''},
    {n:'G7', cat:'seven', f:[0,2,1,2], fg:[0,2,1,3], tip:''},
    {n:'A7', cat:'seven', f:[0,1,0,0], fg:[0,1,0,0], tip:''},
    {n:'D7', cat:'seven', f:[2,2,2,3], fg:[1,1,1,2], tip:''}
];
function ukeStrum(fr){ strumGen(fr, UKE_OPEN, STRUM_DIR); }
function renderUke(cat){
    var grid=document.getElementById('ukeGrid'); if(!grid) return; grid.innerHTML='';
    UKE_CHORDS.filter(function(c){return !cat||cat==='all'||c.cat===cat;}).forEach(function(c){
        var card=document.createElement('div'); card.className='chord-card'; card.style.cursor='pointer'; card.title='Ketuk untuk dengar';
        card.innerHTML='<div class="cc-name">'+c.n+' <span style="font-size:10px;opacity:0.6;">&#128266;</span></div>'+chordSvg(c, UKE_LABELS)+(c.tip?'<div class="cc-tip">'+c.tip+'</div>':'');
        card.addEventListener('click', function(){ ukeStrum(c.f); });
        grid.appendChild(card);
    });
}
function ukeFilter(cat, el){ document.querySelectorAll('#ukeFilter .chord-chip').forEach(function(x){x.classList.remove('active');}); el.classList.add('active'); renderUke(cat); }
var UKE_BARRE={ Amaj:{suf:'', off:[2,1,0,0], fg:[3,2,1,1]}, Amin:{suf:'m', off:[2,0,0,0], fg:[2,1,1,1]}, A7:{suf:'7', off:[0,1,0,0], fg:[0,2,1,1]} };
var ukeState={shape:'Amaj', fret:1};
function ukeRender(){
    var sh=UKE_BARRE[ukeState.shape], p=ukeState.fret, f=sh.off.map(function(o){return p+o;});
    var dia=document.getElementById('ukeDiagram'); if(dia) dia.innerHTML=neckSvgG(f, sh.fg, UKE_LABELS);
    var nm=document.getElementById('ukeName'); if(nm) nm.textContent=NOTES_A[p%12]+sh.suf;
    var fl=document.getElementById('ukeFretLbl'); if(fl) fl.textContent='Posisi fret '+p;
}
function ukeShape(s, el){ ukeState.shape=s; document.querySelectorAll('#ukeShapes .chord-chip').forEach(function(x){x.classList.remove('active');}); el.classList.add('active'); ukeRender(); }
function ukeMove(d){ ukeState.fret=Math.min(10,Math.max(1,ukeState.fret+d)); document.getElementById('ukeFret').value=ukeState.fret; ukeRender(); }
function ukeSlide(){ ukeState.fret=parseInt(document.getElementById('ukeFret').value,10)||1; ukeRender(); }
function ukePlayCur(){ var sh=UKE_BARRE[ukeState.shape], p=ukeState.fret; ukeStrum(sh.off.map(function(o){return p+o;})); }

/* ===== BASS (E A D G) — pola root+kuint+oktaf ===== */
var BASS_OPEN=[41.20,55.00,73.42,98.00], BASS_LABELS=['E','A','D','G'];
function bassBoxFr(r){ return [r, r+2, r+2, -1]; }   // root(E), kuint(A), oktaf(D), G mute
var BASS_BOX_FG=[1,3,4,0];
var BASS_ROOTS=[ {n:'C',r:8},{n:'D',r:10},{n:'E',r:12},{n:'F',r:1},{n:'G',r:3},{n:'A',r:5},{n:'B',r:7} ];
function bassPlay(r){
    try { chordAudioCtx = chordAudioCtx || new (window.AudioContext||window.webkitAudioContext)(); } catch(e){ return; }
    if (chordAudioCtx.state==='suspended') chordAudioCtx.resume();
    var ctx=chordAudioCtx, t0=ctx.currentTime+0.02;
    var hz=[ BASS_OPEN[0]*Math.pow(2,r/12), BASS_OPEN[1]*Math.pow(2,(r+2)/12), BASS_OPEN[2]*Math.pow(2,(r+2)/12) ];
    hz.forEach(function(f,k){ var t=t0+k*0.22, osc=ctx.createOscillator(), g=ctx.createGain(), lp=ctx.createBiquadFilter();
        osc.type='triangle'; osc.frequency.value=f; lp.type='lowpass'; lp.frequency.value=900;
        g.gain.setValueAtTime(0,t); g.gain.linearRampToValueAtTime(0.32,t+0.01); g.gain.exponentialRampToValueAtTime(0.0008,t+0.5);
        osc.connect(lp); lp.connect(g); g.connect(ctx.destination); osc.start(t); osc.stop(t+0.6);
    });
}
function renderBass(){
    var grid=document.getElementById('bassGrid'); if(!grid) return; grid.innerHTML='';
    BASS_ROOTS.forEach(function(c){
        var card=document.createElement('div'); card.className='chord-card'; card.style.cursor='pointer'; card.title='Ketuk untuk dengar';
        card.innerHTML='<div class="cc-name">'+c.n+' <span style="font-size:10px;opacity:0.6;">&#128266;</span></div>'+chordSvg({f:bassBoxFr(c.r), fg:BASS_BOX_FG}, BASS_LABELS);
        card.addEventListener('click', function(){ bassPlay(c.r); });
        grid.appendChild(card);
    });
}
var bassState={root:8};
function bassRender(){
    var dia=document.getElementById('bassDiagram'); if(dia) dia.innerHTML=neckSvgG(bassBoxFr(bassState.root), BASS_BOX_FG, BASS_LABELS);
    var nm=document.getElementById('bassName'); if(nm) nm.textContent=NOTES_E[bassState.root%12];
    var fl=document.getElementById('bassFretLbl'); if(fl) fl.textContent='Root fret '+bassState.root+' (senar E)';
}
function bassMove(d){ bassState.root=Math.min(12,Math.max(1,bassState.root+d)); document.getElementById('bassFret').value=bassState.root; bassRender(); }
function bassSlide(){ bassState.root=parseInt(document.getElementById('bassFret').value,10)||1; bassRender(); }
function bassPlayCur(){ bassPlay(bassState.root); }

function chordInst(inst, el){
    document.querySelectorAll('#chordInst .chord-instbtn').forEach(function(x){ x.classList.remove('active'); }); el.classList.add('active');
    var map={gitar:'chordGitar', piano:'chordPiano', ukulele:'chordUkulele', bass:'chordBass'};
    for (var k in map){ var d=document.getElementById(map[k]); if(d) d.style.display = (k===inst)?'block':'none'; }
}
renderPiano('all'); pianoRender();
renderUke('all'); ukeRender();
renderBass(); bassRender();

/* ===== NOTE COLOR ===== */
function selectColor(color, el) {
    document.getElementById('noteColor').value = color;
    document.querySelectorAll('.notes-color-dot').forEach(function(d){
        d.classList.remove('selected');
        d.style.borderColor = 'transparent';
    });
    el.classList.add('selected');
    el.style.borderColor = '#38A8CC';
}

/* ===== EDIT NOTE ===== */
function editNote(id, title, body, color) {
    editingNoteId = id;
    document.getElementById('editNoteTitle').value = title;
    document.getElementById('editNoteBody').value  = body;
    document.getElementById('noteModal').classList.add('open');
}
function closeNoteModal() {
    document.getElementById('noteModal').classList.remove('open');
    editingNoteId = null;
}
function saveNoteEdit() {
    if (!editingNoteId) return;
    var title = document.getElementById('editNoteTitle').value;
    var body  = document.getElementById('editNoteBody').value;
    if (!body.trim()) return;

    fetch(BASE_URL + '/kamu/note/' + editingNoteId, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: title, body: body })
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (!d.success) return;
        var card = document.getElementById('noteCard' + editingNoteId);
        if (card) {
            var titleEl = card.querySelector('.note-card-title');
            var bodyEl  = card.querySelector('.note-card-body');
            if (titleEl) titleEl.textContent = d.note.title || '';
            if (bodyEl)  bodyEl.textContent  = d.note.body;
        }
        closeNoteModal();
    });
}

/* ===== DELETE NOTE ===== */
function deleteNote(id) {
    if (!confirm('Hapus catatan ini?')) return;
    fetch(BASE_URL + '/kamu/note/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.success) {
            var el = document.getElementById('noteCard' + id);
            if (el) el.remove();
        }
    });
}

/* ===== EDIT POST ===== */
function kamuEditPost(id) {
    var body = document.getElementById('kamuPostBody' + id);
    var edit = document.getElementById('kamuPostEdit' + id);
    var editBtn = document.getElementById('editBtn' + id);
    var saveBtn = document.getElementById('saveBtn' + id);
    if (!body || !edit) return;
    body.style.display = 'none';
    edit.style.display = 'block';
    edit.focus();
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
}

function kamuSavePost(id) {
    var edit = document.getElementById('kamuPostEdit' + id);
    var body = edit ? edit.value.trim() : '';
    if (!body) return;

    fetch(BASE_URL + '/kamu/' + id, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ body: body })
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (!d.success) return;
        var bodyEl  = document.getElementById('kamuPostBody' + id);
        var editEl  = document.getElementById('kamuPostEdit' + id);
        var editBtn = document.getElementById('editBtn' + id);
        var saveBtn = document.getElementById('saveBtn' + id);
        if (bodyEl)  { bodyEl.textContent = body; bodyEl.style.display = 'block'; }
        if (editEl)  editEl.style.display = 'none';
        if (editBtn) editBtn.style.display = 'inline-block';
        if (saveBtn) saveBtn.style.display = 'none';
    });
}

function kamuDeletePost(id) {
    if (!confirm('Hapus postingan ini?')) return;
    fetch(BASE_URL + '/kamu/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.success) {
            var el = document.getElementById('kamuPost' + id);
            if (el) el.remove();
        }
    });
}

/* ===== GUITAR TUNER ===== */
var tunerCtx = null, tunerAnalyser = null, tunerBuf = null;
var tunerStream = null, tunerRunning = false, tunerRaf = null;
var tunerSmooth = 0, tunerSelectedFreq = 0;
var tunerWasInTune = false;
var tunerFreqHistory = [];
var tunerA4 = 440; // referensi A4
var tunerHannWin = null; // Hann window cache

// minFreq/maxFreq untuk adaptive filter per senar (range ±18%, tolak harmonic)
var TUNER_STRINGS = [
    { freq: 82.41,  label: 'E₂', minFreq: 68,  maxFreq: 97 },
    { freq: 110.00, label: 'A₂', minFreq: 92,  maxFreq: 131 },
    { freq: 146.83, label: 'D₃', minFreq: 122, maxFreq: 172 },
    { freq: 196.00, label: 'G₃', minFreq: 164, maxFreq: 229 },
    { freq: 246.94, label: 'B₃', minFreq: 207, maxFreq: 288 },
    { freq: 329.63, label: 'e⁴', minFreq: 276, maxFreq: 383 },
];

function tunerPickPeg(el) {
    document.querySelectorAll('.tuner-peg').forEach(function(p){ p.classList.remove('active'); });
    el.classList.add('active');
    tunerSelectedFreq = parseFloat(el.getAttribute('data-freq'));
}

function tunerToggle() {
    tunerRunning ? tunerStop() : tunerStart();
}

function tunerStart() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('tunerMsg').textContent = 'Browser tidak mendukung akses mikrofon.';
        return;
    }
    document.getElementById('tunerMsg').textContent = 'Meminta izin mikrofon...';
    navigator.mediaDevices.getUserMedia({ audio: { echoCancellation: false, noiseSuppression: false }, video: false })
    .then(function(stream) {
        tunerStream = stream;
        tunerCtx = new (window.AudioContext || window.webkitAudioContext)();
        tunerAnalyser = tunerCtx.createAnalyser();
        tunerAnalyser.fftSize = 2048;
        tunerBuf = new Float32Array(tunerAnalyser.fftSize);
        tunerCtx.createMediaStreamSource(stream).connect(tunerAnalyser);
        tunerAnalyser.fftSize = 4096;
        tunerBuf = new Float32Array(tunerAnalyser.fftSize);
        // Pre-compute Hann window sekali
        tunerHannWin = new Float32Array(tunerAnalyser.fftSize);
        for (var wi = 0; wi < tunerAnalyser.fftSize; wi++)
            tunerHannWin[wi] = 0.5 - 0.5 * Math.cos(2 * Math.PI * wi / (tunerAnalyser.fftSize - 1));
        tunerRunning = true;
        tunerSmooth = 0;
        tunerFreqHistory = [];
        tunerWasInTune = false;
        tunerLastRender = null;    // diinit ke ts asli pada frame pertama
        tunerLastAnalysis = null;
        var btn = document.getElementById('tunerBtn');
        btn.innerHTML = '&#9646;&#9646; Stop';
        btn.classList.add('stop');
        document.getElementById('tunerMsg').textContent = 'Petik senar gitarmu...';
        tunerRaf = requestAnimationFrame(tunerLoop);   // mulai via RAF agar ts valid
    })
    .catch(function() {
        document.getElementById('tunerMsg').textContent = 'Izin mikrofon ditolak.';
    });
}

function tunerStop() {
    tunerRunning = false;
    tunerFreqHistory = [];
    tunerSmooth = 0;
    if (tunerRaf) cancelAnimationFrame(tunerRaf);
    if (tunerStream) { tunerStream.getTracks().forEach(function(t){ t.stop(); }); tunerStream = null; }
    if (tunerCtx) { tunerCtx.close(); tunerCtx = null; }
    var btn = document.getElementById('tunerBtn');
    btn.innerHTML = '&#9654; Mulai Tuning';
    btn.classList.remove('stop');
    document.getElementById('tunerMsg').textContent = 'Ketuk senar di headstock, lalu mulai';
    tunerRenderUI(null);
}


var tunerLastRender = null;          // ms render UI terakhir (diinit di tunerStart)
var tunerLastAnalysis = null;        // ms analisis pitch terakhir
var TUNER_RENDER_INTERVAL = 120;     // jeda update UI (≈8x/detik) — mata bisa follow
var TUNER_ANALYSIS_INTERVAL = 35;    // jeda deteksi pitch (≈28x/detik) — banyak sampel, CPU aman
function tunerLoop(ts) {
    if (!tunerRunning) return;
    if (tunerLastRender === null)   tunerLastRender = ts;
    if (tunerLastAnalysis === null) tunerLastAnalysis = ts;

    // ===== ANALISIS (lebih sering, isi history + smoothing) =====
    if (ts - tunerLastAnalysis >= TUNER_ANALYSIS_INTERVAL) {
        tunerLastAnalysis = ts;
        tunerAnalyser.getFloatTimeDomainData(tunerBuf);
        var freq = tunerMPM(tunerBuf, tunerCtx.sampleRate);
        // Adaptive range: jika senar dipilih, gunakan minFreq/maxFreq senar itu
        var fMin = 60, fMax = 1400;
        if (tunerSelectedFreq > 0) {
            var selStr = TUNER_STRINGS.filter(function(s){ return s.freq === tunerSelectedFreq; })[0];
            if (selStr) { fMin = selStr.minFreq; fMax = selStr.maxFreq; }
        }
        if (freq >= fMin && freq <= fMax) {
            tunerFreqHistory.push(freq);
            if (tunerFreqHistory.length > 16) tunerFreqHistory.shift();
            // Median buang outlier, lalu low-pass smoothing (alpha 0.35 = lebih halus)
            var sorted = tunerFreqHistory.slice().sort(function(a,b){return a-b;});
            var median = sorted[Math.floor(sorted.length / 2)];
            tunerSmooth = tunerSmooth === 0 ? median : tunerSmooth * 0.65 + median * 0.35;
        } else {
            // Sinyal hilang/redup: TAHAN pembacaan terakhir di layar (lebih mudah dibaca pemula),
            // jangan langsung reset ke "—". Hanya kosongkan history agar petik berikutnya akurat.
            tunerFreqHistory = [];
        }
    }

    // ===== RENDER (lebih jarang, butuh ≥10 sampel = confidence) =====
    if (ts - tunerLastRender >= TUNER_RENDER_INTERVAL) {
        tunerLastRender = ts;
        if (tunerFreqHistory.length >= 10) tunerRenderUI(tunerSmooth);
    }

    tunerRaf = requestAnimationFrame(tunerLoop);
}

// MPM (McLeod Pitch Method) + Hann window + parabolic interpolation
// Lebih akurat dari YIN untuk instrumen string, terutama nada rendah
function tunerMPM(buf, sr) {
    var SIZE = buf.length, HALF = Math.floor(SIZE / 2);

    // 1. Hann window + RMS gate
    var win = tunerHannWin || buf; // fallback jika belum init
    var rms = 0, wbuf = new Float32Array(SIZE);
    for (var i = 0; i < SIZE; i++) {
        wbuf[i] = buf[i] * win[i];
        rms += wbuf[i] * wbuf[i];
    }
    if (Math.sqrt(rms / SIZE) < 0.007) return -1;

    // 2. NSDF: Normalized Square Difference Function
    // nsdf[tau] = 2*acf[tau] / (m'[tau]) — lebih tahan harmonik dari YIN
    var nsdf = new Float32Array(HALF);
    for (var tau = 0; tau < HALF; tau++) {
        var acf = 0, norm = 0;
        for (var i = 0; i < HALF; i++) {
            acf  += wbuf[i] * wbuf[i + tau];
            norm += wbuf[i] * wbuf[i] + wbuf[i + tau] * wbuf[i + tau];
        }
        nsdf[tau] = norm > 1e-10 ? 2 * acf / norm : 0;
    }

    // 3. Cari semua local maximum di region positif
    var candidates = [], tau = 0;
    while (tau < HALF && nsdf[tau] > 0) tau++; // skip region positif pertama (tau~0)
    while (tau < HALF && nsdf[tau] <= 0) tau++;
    while (tau < HALF) {
        var lMax = -Infinity, lTau = tau;
        while (tau < HALF && nsdf[tau] > 0) {
            if (nsdf[tau] > lMax) { lMax = nsdf[tau]; lTau = tau; }
            tau++;
        }
        if (lMax > 0) candidates.push({ tau: lTau, val: lMax });
        while (tau < HALF && nsdf[tau] <= 0) tau++;
    }
    if (!candidates.length) return -1;

    // 4. Global max → pilih kandidat pertama ≥ 0.8 × globalMax (hindari harmonic)
    var gMax = candidates.reduce(function(a,b){ return a.val > b.val ? a : b; }).val;
    var chosen = null;
    for (var c = 0; c < candidates.length; c++) {
        if (candidates[c].val >= 0.8 * gMax) { chosen = candidates[c]; break; }
    }
    if (!chosen || chosen.val < 0.08) return -1;

    // 5. Parabolic interpolation (sub-sample precision)
    var t = chosen.tau;
    if (t > 0 && t < HALF - 1) {
        var s0 = nsdf[t-1], s1 = nsdf[t], s2 = nsdf[t+1];
        var denom = 2*s1 - s2 - s0;
        if (Math.abs(denom) > 1e-9) t = t + (s2 - s0) / (2 * denom);
    }
    return sr / t;
}

function tunerRenderUI(freq) {
    var noteEl  = document.getElementById('tunerNote');
    var centsEl = document.getElementById('tunerCents');
    var cursor  = document.getElementById('tunerBarCursor');
    var track   = document.getElementById('tunerBarTrack');

    if (!freq) {
        noteEl.textContent  = '—'; noteEl.className  = 'tuner-note-big';
        centsEl.textContent = 'Petik senar gitarmu';
        centsEl.className = 'tuner-cents';
        if (cursor) { cursor.style.left = '50%'; cursor.className = 'tuner-meter-needle'; }
        if (track)  track.classList.remove('active');
        return;
    }

    var target = null, minDiff = Infinity;
    TUNER_STRINGS.forEach(function(s) {
        if (tunerSelectedFreq > 0) {
            if (s.freq === tunerSelectedFreq) target = s;
        } else {
            var diff = Math.abs(1200 * Math.log2(freq / s.freq));
            if (diff < minDiff) { minDiff = diff; target = s; }
        }
    });
    if (!target) return;

    var scaledFreq = target.freq * (tunerA4 / 440);
    var centsRaw   = 1200 * Math.log2(freq / scaledFreq);
    var cents      = Math.round(centsRaw * 10) / 10;
    // Map cents [-50..+50] → cursor left [0%..100%]
    var pct = Math.max(0, Math.min(100, 50 + centsRaw));

    noteEl.textContent = target.label;
    if (cursor) { cursor.style.left = pct + '%'; }
    if (track)  track.classList.add('active');

    if (tunerSelectedFreq === 0) {
        document.querySelectorAll('.tuner-peg').forEach(function(p){
            p.classList.toggle('active', parseFloat(p.getAttribute('data-freq')) === target.freq);
        });
    }

    var absC = Math.abs(centsRaw);
    var off  = Math.abs(Math.round(centsRaw)); // angka bulat sederhana, makin kecil makin pas
    // Hysteresis: masuk in-tune saat ≤5 cent, bertahan sampai >8 cent (cegah flip-flop di tepi)
    var wasInTune = tunerWasInTune;
    var inTune = wasInTune ? (absC < 8) : (absC <= 5);
    if (inTune) {
        noteEl.className    = 'tuner-note-big in-tune';
        centsEl.textContent = '✓ SUDAH PAS';
        centsEl.className   = 'tuner-cents in-tune';
        if (cursor) cursor.className = 'tuner-meter-needle in-tune';
        document.querySelectorAll('.tuner-peg').forEach(function(p){
            if (parseFloat(p.getAttribute('data-freq')) === target.freq) p.classList.add('in-tune');
        });
        tunerWasInTune = true;
        if (!wasInTune) { if (typeof fbSoundTunerInTune === 'function') fbSoundTunerInTune(); }
    } else if (centsRaw < 0) {
        tunerWasInTune = false;
        document.querySelectorAll('.tuner-peg.in-tune').forEach(function(p){ p.classList.remove('in-tune'); });
        noteEl.className    = 'tuner-note-big too-low';
        centsEl.textContent = '− ' + off;   // tanda − = kencangkan
        centsEl.className   = 'tuner-cents too-low';
        if (cursor) cursor.className = 'tuner-meter-needle too-low';
    } else {
        tunerWasInTune = false;
        document.querySelectorAll('.tuner-peg.in-tune').forEach(function(p){ p.classList.remove('in-tune'); });
        noteEl.className    = 'tuner-note-big too-high';
        centsEl.textContent = '+ ' + off;   // tanda + = kendurkan
        centsEl.className   = 'tuner-cents too-high';
        if (cursor) cursor.className = 'tuner-meter-needle too-high';
    }
}


</script>
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
// ══════════════════════════════════════════════════════════════════════════════
//  KAC — Kamu Audio Cutter (tab Potong Lagu, flat, no limit)
// ══════════════════════════════════════════════════════════════════════════════
(function(){
'use strict';
var _ctx=null,_buf=null,_src=null,_dur=0;
var _playing=false,_paused=false,_pausedAt=0,_playOffset=0,_playCtxTime=0;
var _startT=0,_endT=0,_vS=0,_vE=1;
var _drag=null,_pinchD=null,_pinchMid=0,_vS0=0,_vE0=0;
var _sbDrag=false,_sbX0=0,_sbVS0=0,_sbVE0=0;
var _raf=null,_prevStop=null,_resultUrl=null;

function g(id){return document.getElementById(id);}
function fmtP(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=s%60;return m+':'+(x<10?'0':'')+x.toFixed(1);}
function fmtS(s){s=Math.max(0,s||0);var m=Math.floor(s/60),x=Math.floor(s%60);return m+':'+(x<10?'0':'')+x;}
function setSt(t){var e=g('kacStatus');if(e)e.textContent=t||'';}

// ── Draw ──────────────────────────────────────────────────────────────────────
function tToX(t,W){return((t-_vS*_dur)/((_vE-_vS)*_dur))*W;}
function xToT(x,W){return _vS*_dur+(x/W)*(_vE-_vS)*_dur;}
function kacDraw(){
    var c=g('kacWave'),wrap=g('kacWaveWrap');
    if(!c||!wrap)return;
    var rect=wrap.getBoundingClientRect();
    var W=Math.round(rect.width||600),H=130;
    if(W<10)W=600;
    c.width=W;c.height=H;
    var gc=c.getContext('2d');
    gc.fillStyle='#070d18';gc.fillRect(0,0,W,H);
    if(!_buf)return;
    var data=_buf.getChannelData(0),sr=_buf.sampleRate;
    var totS=_vS*_dur,totE=_vE*_dur;
    for(var i=0;i<W;i++){
        var t0=totS+(i/W)*(totE-totS),t1=totS+((i+1)/W)*(totE-totS);
        var i0=Math.floor(t0*sr),i1=Math.ceil(t1*sr),max=0;
        for(var j=i0;j<i1&&j<data.length;j++){var av=Math.abs(data[j]);if(av>max)max=av;}
        var inS=(t0+t1)/2>=_startT&&(t0+t1)/2<=_endT;
        var bH=Math.max(2,max*(H-14)*.9),y=(H-14-bH)/2;
        gc.fillStyle=inS?'rgba(56,189,248,.88)':'rgba(56,189,248,.2)';
        gc.fillRect(i,y,1,bH);
    }
    var sx=tToX(_startT,W),ex=tToX(_endT,W);
    gc.fillStyle='rgba(56,189,248,.07)';gc.fillRect(sx,0,ex-sx,H-14);
    var vD=totE-totS,tk=vD<=2?.2:vD<=5?.5:vD<=20?1:vD<=60?5:vD<=300?30:60;
    gc.fillStyle='rgba(10,14,26,.9)';gc.fillRect(0,H-14,W,14);
    gc.font='8px monospace';
    for(var ts=Math.ceil(totS/tk)*tk;ts<=totE+.001;ts+=tk){
        var tx=tToX(ts,W);if(tx<0||tx>W)continue;
        gc.fillStyle='rgba(255,255,255,.2)';gc.fillRect(tx,H-14,1,14);
        gc.fillStyle='rgba(255,255,255,.45)';gc.fillText(fmtP(ts),tx+2,H-3);
    }
    if(_playing||_paused){
        var pp=_paused?_pausedAt:(_playOffset+(_ctx.currentTime-_playCtxTime));
        if(pp>=0&&pp<=_dur){var px=tToX(pp,W);gc.fillStyle='rgba(255,255,255,.85)';gc.fillRect(px-.75,0,1.5,H-14);}
    }
    function dH(hx,col){
        if(hx<-12||hx>W+12)return;
        gc.beginPath();gc.moveTo(hx-9,0);gc.lineTo(hx+9,0);gc.lineTo(hx,16);gc.closePath();gc.fillStyle=col;gc.fill();
        gc.globalAlpha=0.4;gc.fillRect(hx-.75,16,1.5,H-14-16);gc.globalAlpha=1;
    }
    dH(sx,'#22d3ee');dH(ex,'#f59e0b');
    g('kacSelS')&&(g('kacSelS').textContent=fmtP(_startT));
    g('kacSelE')&&(g('kacSelE').textContent=fmtP(_endT));
    g('kacSelD')&&(g('kacSelD').textContent=fmtP(_endT-_startT));
    var sw=g('kacScrollWrap'),sb=g('kacScrollBar'),zl=g('kacZoomLbl');
    if(sw&&sb){var spW=sw.getBoundingClientRect().width||200;sb.style.left=(_vS*spW)+'px';sb.style.width=Math.max(8,(_vE-_vS)*spW)+'px';}
    if(zl)zl.textContent=_vE-_vS>=.99?'Semua':Math.round(100*(_vE-_vS))+'%';
}

// ── Zoom ──────────────────────────────────────────────────────────────────────
function zoomK(pivot,f){if(!_buf)return;var len=_vE-_vS,nL=Math.min(1,Math.max(0.005,len*f));var s=_vS+pivot*len-pivot*nL,e=s+nL;if(s<0){s=0;e=Math.min(1,nL);}if(e>1){e=1;s=Math.max(0,1-nL);}_vS=s;_vE=e;kacDraw();}
window.kacZoomIn=function(){zoomK(.5,.6);};
window.kacZoomOut=function(){zoomK(.5,1.7);};
window.kacZoomReset=function(){if(!_buf)return;_vS=0;_vE=1;kacDraw();};

// ── File load ─────────────────────────────────────────────────────────────────
var kacDropEl=g('kacDrop');
if(kacDropEl){
    kacDropEl.addEventListener('dragover',function(e){e.preventDefault();kacDropEl.style.borderColor='#38bdf8';});
    kacDropEl.addEventListener('dragleave',function(){kacDropEl.style.borderColor='';});
    kacDropEl.addEventListener('drop',function(e){e.preventDefault();kacDropEl.style.borderColor='';if(e.dataTransfer.files[0])kacLoad(e.dataTransfer.files[0]);});
}
g('kacFile')&&g('kacFile').addEventListener('change',function(){if(this.files[0])kacLoad(this.files[0]);});

function kacLoad(file){
    if(file.size>100*1024*1024){alert('Maks 100 MB');return;}
    setSt('Memuat…');g('kacName').textContent=file.name.replace(/\.[^.]+$/,'');
    if(_ctx){try{_ctx.close();}catch(e){}_ctx=null;}
    _ctx=new(window.AudioContext||window.webkitAudioContext)();
    var r=new FileReader();
    r.onload=function(ev){
        _ctx.decodeAudioData(ev.target.result.slice(0),function(buf){
            _buf=buf;_dur=buf.duration;_startT=0;_endT=_dur;_vS=0;_vE=1;
            g('kacMeta').textContent=fmtS(_dur)+' · '+(file.size/1024/1024).toFixed(1)+' MB';
            g('kacDrop').style.display='none';
            g('kacEditor').style.display='block';
            g('kacResult').style.display='none';
            setSt('');requestAnimationFrame(kacDraw);
        },function(){setSt('Gagal decode. Coba format lain.');});
    };
    r.readAsArrayBuffer(file);
}

// ── Canvas events ─────────────────────────────────────────────────────────────
var kacC=g('kacWave');
if(kacC){
    function cXk(cx){var r=kacC.getBoundingClientRect();return(cx-r.left)*(kacC.width/Math.max(1,r.width));}
    function nearHk(px,tol){if(!_buf)return null;var W=kacC.width||600,sx=tToX(_startT,W),ex=tToX(_endT,W);var ds=Math.abs(px-sx),de=Math.abs(px-ex);if(ds<tol&&ds<=de)return'start';if(de<tol)return'end';return null;}
    kacC.addEventListener('mousedown',function(e){var h=nearHk(cXk(e.clientX),22);if(h){_drag=h;e.preventDefault();}});
    kacC.addEventListener('mousemove',function(e){
        if(_drag&&_drag!=='pinch'&&_buf){var t=xToT(cXk(e.clientX),kacC.width);if(_drag==='start')_startT=Math.max(0,Math.min(t,_endT-.05));else _endT=Math.min(_dur,Math.max(t,_startT+.05));kacDraw();}
        else kacC.style.cursor=nearHk(cXk(e.clientX),22)?'ew-resize':'default';
    });
    document.addEventListener('mouseup',function(){_drag=null;});
    kacC.addEventListener('wheel',function(e){if(!_buf)return;e.preventDefault();var r=kacC.getBoundingClientRect();zoomK((e.clientX-r.left)/r.width,e.deltaY>0?1.4:0.7);},{passive:false});
    kacC.addEventListener('touchstart',function(e){
        if(e.touches.length===2){var t0=e.touches[0],t1=e.touches[1];_drag='pinch';_pinchD=Math.hypot(t1.clientX-t0.clientX,t1.clientY-t0.clientY);var r=kacC.getBoundingClientRect();_pinchMid=((t0.clientX+t1.clientX)/2-r.left)/r.width;_vS0=_vS;_vE0=_vE;e.preventDefault();return;}
        if(e.touches.length===1){var h=nearHk(cXk(e.touches[0].clientX),36);if(h){_drag=h;e.preventDefault();}}
    },{passive:false});
    kacC.addEventListener('touchmove',function(e){
        if(_drag==='pinch'&&e.touches.length===2){var t0=e.touches[0],t1=e.touches[1];var d=Math.hypot(t1.clientX-t0.clientX,t1.clientY-t0.clientY),f=_pinchD/d,len=_vE0-_vS0,nL=Math.min(1,Math.max(0.01,len*f));var s=_vS0+_pinchMid*len-_pinchMid*nL,e2=s+nL;if(s<0){s=0;e2=Math.min(1,nL);}if(e2>1){e2=1;s=Math.max(0,1-nL);}_vS=s;_vE=e2;kacDraw();e.preventDefault();}
        else if(_drag&&_drag!=='pinch'&&e.touches.length===1&&_buf){var t=xToT(cXk(e.touches[0].clientX),kacC.width);if(_drag==='start')_startT=Math.max(0,Math.min(t,_endT-.05));else _endT=Math.min(_dur,Math.max(t,_startT+.05));kacDraw();e.preventDefault();}
    },{passive:false});
    kacC.addEventListener('touchend',function(){_drag=null;_pinchD=null;});
}

// ── Scrollbar pan ─────────────────────────────────────────────────────────────
var kacSWEl=g('kacScrollWrap');
if(kacSWEl){
    function sbSk(cx){var sb=g('kacScrollBar'),r=kacSWEl.getBoundingClientRect();var bL=parseFloat(sb.style.left)||0,bW=parseFloat(sb.style.width)||r.width;if(cx>=bL&&cx<=bL+bW){_sbDrag=true;_sbX0=cx-bL;_sbVS0=_vS;_sbVE0=_vE;return true;}return false;}
    function sbMk(cx){if(!_sbDrag)return;var r=kacSWEl.getBoundingClientRect(),span=_sbVE0-_sbVS0,s=Math.max(0,Math.min((cx-_sbX0)/r.width,1-span));_vS=s;_vE=s+span;kacDraw();}
    kacSWEl.addEventListener('mousedown',function(e){if(sbSk(e.clientX-kacSWEl.getBoundingClientRect().left))e.preventDefault();});
    document.addEventListener('mousemove',function(e){sbMk(e.clientX-kacSWEl.getBoundingClientRect().left);});
    document.addEventListener('mouseup',function(){_sbDrag=false;});
    kacSWEl.addEventListener('touchstart',function(e){if(e.touches.length!==1)return;if(sbSk(e.touches[0].clientX-kacSWEl.getBoundingClientRect().left))e.preventDefault();},{passive:false});
    document.addEventListener('touchmove',function(e){if(_sbDrag&&e.touches.length===1)sbMk(e.touches[0].clientX-kacSWEl.getBoundingClientRect().left);},{passive:true});
    document.addEventListener('touchend',function(){_sbDrag=false;});
}

// ── Playback ──────────────────────────────────────────────────────────────────
function ctxGoK(cb){if(!_ctx)return;(_ctx.state==='suspended'?_ctx.resume():Promise.resolve()).then(cb);}
function stopSrcK(){if(_prevStop){clearTimeout(_prevStop);_prevStop=null;}if(_raf){cancelAnimationFrame(_raf);_raf=null;}if(_src){try{_src.stop();}catch(e){}_src=null;}}
function rafK(){if(!_playing)return;kacDraw();_raf=requestAnimationFrame(rafK);}
function showPK(v){var bp=g('kacBtnPlay'),pp=g('kacBtnPause');if(bp)bp.style.display=v?'none':'';if(pp)pp.style.display=v?'':'none';}
function startK(from,dur){_src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);_playOffset=from;_playCtxTime=_ctx.currentTime;dur!=null?_src.start(0,from,dur):_src.start(0,from);_playing=true;_paused=false;_src.onended=function(){if(_playing&&!_paused)kacStop();};}
window.kacPlay=function(){if(!_buf)return;if(_paused){ctxGoK(function(){_src=_ctx.createBufferSource();_src.buffer=_buf;_src.connect(_ctx.destination);_playOffset=_pausedAt;_playCtxTime=_ctx.currentTime;_src.start(0,_pausedAt);_playing=true;_paused=false;showPK(true);rafK();});return;}kacStop();ctxGoK(function(){startK(0,null);showPK(true);rafK();});};
window.kacPreview=function(){if(!_buf)return;if(_endT-_startT<.05){setSt('Pilihan terlalu pendek.');return;}kacStop();ctxGoK(function(){startK(_startT,_endT-_startT);_prevStop=setTimeout(kacStop,(_endT-_startT)*1000+500);showPK(true);rafK();});};
window.kacPause=function(){if(!_playing||_paused)return;_pausedAt=_playOffset+(_ctx.currentTime-_playCtxTime);stopSrcK();_paused=true;_playing=false;showPK(false);kacDraw();};
function kacStop(){stopSrcK();_playing=false;_paused=false;showPK(false);kacDraw();}
window.kacStop=kacStop;

// ── Encode helpers ────────────────────────────────────────────────────────────
function encWav(buf,s,e){
    var sr=buf.sampleRate,nCh=buf.numberOfChannels;
    var ss=Math.floor(s*sr),es=Math.min(Math.ceil(e*sr),buf.length),n=es-ss;
    var ab=new ArrayBuffer(44+n*nCh*2),v=new DataView(ab);
    function ws(o,st){for(var i=0;i<st.length;i++)v.setUint8(o+i,st.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);
    var off=44;
    for(var i=0;i<n;i++) for(var ch=0;ch<nCh;ch++){var x=Math.max(-1,Math.min(1,buf.getChannelData(ch)[ss+i]));v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;}
    return new Blob([ab],{type:'audio/wav'});
}
function encMp3(buf,s,e,kbps){
    var sr=buf.sampleRate,nCh=buf.numberOfChannels;
    var ss=Math.floor(s*sr),es=Math.min(Math.ceil(e*sr),buf.length),n=es-ss;
    var enc=new lamejs.Mp3Encoder(nCh>1?2:1,sr,kbps||128),mp3=[],BLOCK=1152;
    function f2i(f){var a=new Int16Array(f.length);for(var i=0;i<f.length;i++)a[i]=Math.max(-32768,Math.min(32767,f[i]*32767));return a;}
    var l16=f2i(buf.getChannelData(0).subarray(ss,es));
    var r16=nCh>1?f2i(buf.getChannelData(1).subarray(ss,es)):l16;
    for(var i=0;i<n;i+=BLOCK){var d=enc.encodeBuffer(l16.subarray(i,i+BLOCK),r16.subarray(i,i+BLOCK));if(d.length)mp3.push(new Uint8Array(d));}
    var end=enc.flush();if(end.length)mp3.push(new Uint8Array(end));
    return new Blob(mp3,{type:'audio/mpeg'});
}

// ── Cut ───────────────────────────────────────────────────────────────────────
window.kacCut=function(){
    if(!_buf)return;
    if(_endT-_startT<.1){setSt('Pilihan terlalu pendek (min 0.1 dtk).');return;}
    var fv=g('kacFmt')?g('kacFmt').value:'mp3-128';
    var isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    g('kacBtnCut').disabled=true;setSt('Memotong…');
    setTimeout(function(){
        try{
            var blob=isWav?encWav(_buf,_startT,_endT):encMp3(_buf,_startT,_endT,kbps);
            if(_resultUrl)URL.revokeObjectURL(_resultUrl);
            _resultUrl=URL.createObjectURL(blob);
            g('kacPlayer').src=_resultUrl;
            var dl=g('kacDl');dl.href=_resultUrl;
            dl.download=(g('kacName').textContent||'lagu')+'_'+fmtS(_startT).replace(':','m')+'s-'+fmtS(_endT).replace(':','m')+'s.'+ext;
            g('kacResult').style.display='block';setSt('');
        }catch(e){setSt('Gagal: '+(e.message||e));}
        finally{g('kacBtnCut').disabled=false;}
    },60);
};

window.addEventListener('resize',function(){if(_buf)requestAnimationFrame(kacDraw);});
})(); // end KAC

// ══════════════════════════════════════════════════════════════════════════════
//  KCV — Konversi Audio (tab Konversi)
// ══════════════════════════════════════════════════════════════════════════════
(function(){
'use strict';
var _ctx=null,_buf=null,_resUrl=null,_name='audio';
function gv(id){return document.getElementById(id);}
function setSt(t){var e=gv('kcvStatus');if(e)e.textContent=t||'';}

var dropEl=gv('kcvDrop');
if(dropEl){
    dropEl.addEventListener('dragover',function(e){e.preventDefault();dropEl.style.borderColor='var(--accent,#6366f1)';});
    dropEl.addEventListener('dragleave',function(){dropEl.style.borderColor='';});
    dropEl.addEventListener('drop',function(e){e.preventDefault();dropEl.style.borderColor='';if(e.dataTransfer.files[0])kcvLoad(e.dataTransfer.files[0]);});
}
gv('kcvFile')&&gv('kcvFile').addEventListener('change',function(){if(this.files[0])kcvLoad(this.files[0]);});

function kcvLoad(file){
    if(file.size>200*1024*1024){alert('Maks 200 MB');return;}
    _name=file.name.replace(/\.[^.]+$/,'');
    setSt('Memuat dan mendekode…');
    if(_ctx){try{_ctx.close();}catch(e){}}_ctx=null;
    _ctx=new(window.AudioContext||window.webkitAudioContext)();
    var r=new FileReader();
    r.onload=function(ev){
        _ctx.decodeAudioData(ev.target.result.slice(0),function(buf){
            _buf=buf;_resUrl=null;
            var dur=buf.duration,m=Math.floor(dur/60),x=Math.floor(dur%60);
            gv('kcvInfo').textContent='🎵 '+_name+' · '+(file.size/1024/1024).toFixed(1)+' MB · '+m+':'+(x<10?'0':'')+x;
            gv('kcvDrop').style.display='none';
            gv('kcvEditor').style.display='block';
            gv('kcvResult').style.display='none';
            setSt('');
        },function(){setSt('Gagal mendekode. Coba format lain.');});
    };
    r.readAsArrayBuffer(file);
}

window.kcvConvert=function(){
    if(!_buf){setSt('Pilih file dulu.');return;}
    var fv=gv('kcvFmt')?gv('kcvFmt').value:'mp3-128';
    var isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    var btn=gv('kcvBtn');btn.disabled=true;setSt('Mengonversi…');
    setTimeout(function(){
        try{
            var blob=isWav
                ?encWav(_buf,0,_buf.duration)
                :encMp3(_buf,0,_buf.duration,kbps);
            if(_resUrl)URL.revokeObjectURL(_resUrl);
            _resUrl=URL.createObjectURL(blob);
            gv('kcvPlayer').src=_resUrl;
            var dl=gv('kcvDl');dl.href=_resUrl;
            dl.download=_name+'.'+(isWav?'wav':'mp3');
            dl.textContent='⬇️ Unduh '+(isWav?'WAV':'MP3')+' '+(isWav?'(lossless)':kbps+'kbps');
            gv('kcvResult').style.display='block';setSt('');
        }catch(e){setSt('Gagal: '+(e.message||e));}
        finally{btn.disabled=false;}
    },80);
};

window.kcvReset=function(){
    _buf=null;_resUrl=null;
    gv('kcvDrop').style.display='';
    gv('kcvEditor').style.display='none';
    gv('kcvResult').style.display='none';
    setSt('');
    var fi=gv('kcvFile');if(fi)fi.value='';
};
})(); // end KCV

// ══════════════════════════════════════════════════════════════════════════════
//  KSI — Kamu Split Instrumen
// ══════════════════════════════════════════════════════════════════════════════
(function(){
'use strict';
// Demucs ONNX (289MB) dihapus — diganti phase-cancellation client-side di ksiProcess (instan, jalan di HP)

var _ctx=null,_buf=null,_name='lagu',_stems=null,_worker=null,_urls=[];
function gk(id){return document.getElementById(id);}
function ksiSt(t){var e=gk('ksiStatus');if(e)e.textContent=t||'';}
function ksiProg(v,l){var b=gk('ksiPBar'),lb=gk('ksiPLbl');if(b)b.style.width=v+'%';if(lb)lb.textContent=l||'';}

var kDrop=gk('ksiDrop');
if(kDrop){
    kDrop.addEventListener('dragover',function(e){e.preventDefault();kDrop.style.borderColor='#818cf8';});
    kDrop.addEventListener('dragleave',function(){kDrop.style.borderColor='';});
    kDrop.addEventListener('drop',function(e){e.preventDefault();kDrop.style.borderColor='';if(e.dataTransfer.files[0])ksiLoad(e.dataTransfer.files[0]);});
}
gk('ksiFile')&&gk('ksiFile').addEventListener('change',function(){if(this.files[0])ksiLoad(this.files[0]);});

function ksiLoad(file){
    if(file.size>150*1024*1024){alert('Maks 150 MB');return;}
    _name=file.name.replace(/\.[^.]+$/,'');ksiSt('Memuat…');_stems=null;
    if(_ctx){try{_ctx.close();}catch(e){}}_ctx=new(window.AudioContext||window.webkitAudioContext)();
    var r=new FileReader();
    r.onload=function(ev){
        _ctx.decodeAudioData(ev.target.result.slice(0),function(buf){
            _buf=buf;var d=buf.duration,m=Math.floor(d/60),x=Math.floor(d%60);
            gk('ksiInfo').innerHTML='🎵 <b>'+_name+'</b> · '+(file.size/1024/1024).toFixed(1)+' MB · '+m+':'+(x<10?'0':'')+x
                +(buf.numberOfChannels<2?' <span style="color:#f59e0b">[Mono — hasil terbatas]</span>':'');
            gk('ksiDrop').style.display='none';gk('ksiEditor').style.display='block';
            gk('ksiResult').style.display='none';gk('ksiProgress').style.display='none';ksiSt('');
        },function(){ksiSt('Gagal mendekode.');});
    };
    r.readAsArrayBuffer(file);
}

window.ksiProcess=function(){
    if(!_buf){ksiSt('Pilih file dulu.');return;}
    if(_buf.numberOfChannels<2){ksiSt('⚠️ Butuh lagu STEREO untuk hapus vokal (file ini mono).');return;}
    if(!window.VocalRemover){ksiSt('⚠️ Modul belum termuat, muat ulang halaman.');return;}
    var btn=gk('ksiBtn');btn.disabled=true;
    gk('ksiProgress').style.display='block';gk('ksiResult').style.display='none';
    ksiProg(5,'Memproses…');ksiSt('');
    window.VocalRemover.process(_buf,function(v,l){ksiProg(v,l);},function(out){
        _stems={instL:out.instL,instR:out.instR,vocL:out.vocL,vocR:out.vocR};
        ksiRenderResult(out.sr);btn.disabled=false;
    },function(e){ksiSt('⚠️ '+(e&&e.message||e));btn.disabled=false;gk('ksiProgress').style.display='none';});
};

var _KD=[
    {key:'instrumen',lk:'instL',rk:'instR',icon:'🎸',label:'Instrumen (Karaoke)',color:'#22c55e'},
    {key:'vokal',    lk:'vocL', rk:'vocR', icon:'🎤',label:'Vokal (eksperimen)', color:'#38bdf8'}
];

function ksiRenderResult(sr){
    _urls.forEach(function(u){URL.revokeObjectURL(u);});_urls=[];
    var fv=gk('ksiFmt')?gk('ksiFmt').value:'mp3-128';
    var isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    var wrap=gk('ksiStemWrap');wrap.innerHTML='';
    _KD.forEach(function(st){
        var c0=_stems[st.lk],c1=_stems[st.rk];
        var bl=isWav?ksiEW(c0,c1,sr):ksiEM(c0,c1,sr,kbps);
        var u=URL.createObjectURL(bl);_urls.push(u);
        var row=document.createElement('div');
        row.style.cssText='display:flex;align-items:center;gap:7px;margin-bottom:6px;background:rgba(0,0,0,.25);border-radius:9px;padding:.45rem .6rem;';
        row.innerHTML='<span style="font-size:1.1rem;width:22px;text-align:center;">'+st.icon+'</span>'
            +'<span style="font-size:12px;font-weight:700;color:'+st.color+';min-width:85px;">'+st.label+'</span>'
            +'<audio controls src="'+u+'" style="flex:1;height:28px;min-width:0;"></audio>'
            +'<a href="'+u+'" download="'+_name+'_'+st.key+'.'+ext+'" style="padding:5px 9px;background:'+st.color+';color:'+(st.color==='#f59e0b'||st.color==='#22c55e'?'#000':'#fff')+';border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;white-space:nowrap;">⬇ '+ext.toUpperCase()+'</a>';
        wrap.appendChild(row);
    });
    gk('ksiProgress').style.display='none';gk('ksiResult').style.display='block';
}

function ksiEW(c0,c1,sr){
    var nCh=c1?2:1,n=c0.length,ab=new ArrayBuffer(44+n*nCh*2),v=new DataView(ab);
    function ws(o,st){for(var i=0;i<st.length;i++)v.setUint8(o+i,st.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);var off=44;
    for(var i=0;i<n;i++){var x=Math.max(-1,Math.min(1,c0[i]));v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;if(c1){var y=Math.max(-1,Math.min(1,c1[i]));v.setInt16(off,y<0?y*0x8000:y*0x7FFF,true);off+=2;}}
    return new Blob([ab],{type:'audio/wav'});
}
function ksiEM(c0,c1,sr,kbps){
    var nCh=c1?2:1,n=c0.length,enc=new lamejs.Mp3Encoder(nCh,sr,kbps||128),mp3=[],BLK=1152;
    function f2i(f){var a=new Int16Array(f.length);for(var i=0;i<f.length;i++)a[i]=Math.max(-32768,Math.min(32767,f[i]*32767));return a;}
    var l16=f2i(c0),r16=c1?f2i(c1):l16;
    for(var i=0;i<n;i+=BLK){var d=enc.encodeBuffer(l16.subarray(i,i+BLK),r16.subarray(i,i+BLK));if(d.length)mp3.push(new Uint8Array(d));}
    var end=enc.flush();if(end.length)mp3.push(new Uint8Array(end));
    return new Blob(mp3,{type:'audio/mpeg'});
}

window.ksiDownloadAll=function(){
    if(!_stems){ksiSt('Belum ada stem.');return;}
    ksiSt('Membuat ZIP…');
    var fv=gk('ksiFmt')?gk('ksiFmt').value:'mp3-128';
    var isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    var zip=new JSZip(),sr=(_buf?_buf.sampleRate:44100);
    var pairs=[
        {c0:_stems.instL,c1:_stems.instR,nm:'01_instrumen'},
        {c0:_stems.vocL, c1:_stems.vocR, nm:'02_vokal'}
    ];
    pairs.forEach(function(p){zip.file(_name+'_'+p.nm+'.'+ext,isWav?ksiEW(p.c0,p.c1,sr):ksiEM(p.c0,p.c1,sr,kbps));});
    zip.generateAsync({type:'blob'}).then(function(z){var a=document.createElement('a');a.href=URL.createObjectURL(z);a.download=_name+'_split.zip';a.click();ksiSt('');});
};

window.ksiReset=function(){
    _buf=null;_stems=null;_urls.forEach(function(u){URL.revokeObjectURL(u);});_urls=[];
    var f=gk('ksiFile');if(f)f.value='';
    gk('ksiDrop').style.display='';gk('ksiEditor').style.display='none';
    gk('ksiResult').style.display='none';gk('ksiProgress').style.display='none';ksiSt('');
};
})(); // end KSI
</script>
@endpush
