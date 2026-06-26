@extends('layouts.app')

@push('preload')
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap">
@endpush

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* HERO */
    .hero {
        min-height: 90vh; display: flex; flex-direction: column;
        justify-content: flex-end; padding: 0 2rem 4rem;
        position: relative; overflow: hidden;
        transition: min-height 0.5s ease, padding 0.5s ease;
    }
    .hero.collapsed { min-height: auto; padding-top: 1.25rem; padding-bottom: 1.25rem; justify-content: flex-start; }
    .hero.collapsed .scroll-hint { display: none; }
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
        opacity: 0; transition: opacity 0.9s ease;
        mask-image: linear-gradient(to left, rgba(0,0,0,0.5) 0%, transparent 80%);
        -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.5) 0%, transparent 80%);
    }
    /* foto artis muncul halus saat intro hero dibuka (tidak collapsed) */
    .hero:not(.collapsed) .hero-photo { opacity: 0.18; }
    [data-theme="light"] .hero:not(.collapsed) .hero-photo { opacity: 0.12; }

    .hero-content { position: relative; z-index: 1; }
    .hero-byline {
        font-size: 10px; letter-spacing: 0.35em; color: var(--text-3);
        text-transform: uppercase; margin-bottom: 1.25rem;
    }
    .hero-byline span { color: var(--text-2); }
    .hero-id-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .hero-id-row .hero-byline { margin-bottom: 0; }
    .hero-collapse {
        background: var(--card-bg); border: 1px solid var(--border); color: var(--text-3);
        border-radius: 50px; font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase;
        padding: 5px 13px; cursor: pointer; transition: 0.15s; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
    }
    .hero-collapse:hover { color: var(--text); border-color: var(--text-3); }
    .hero-body { overflow: hidden; transition: max-height 0.55s ease, opacity 0.4s ease; max-height: 1200px; opacity: 1; }
    .hero-body.hidden { max-height: 0; opacity: 0; }
    .hero-title {
        font-size: clamp(1.8rem, 5vw, 3.8rem); font-weight: 300;
        letter-spacing: 0.1em; line-height: 1.2;
        margin-bottom: 1.5rem; color: var(--text);
    }
    .hero-story {
        font-size: 14px; color: var(--text-2); line-height: 1.9;
        max-width: 480px; margin: 0;
    }
    .hero-story em { color: var(--text-2); font-style: normal; }
    .hero-story-wrap {
        max-width: 480px; overflow: hidden;
        max-height: 0; opacity: 0;
        transition: max-height 0.5s ease, opacity 0.4s ease, margin 0.4s ease;
        margin-bottom: 0;
    }
    .hero-story-wrap.open { max-height: 460px; opacity: 1; margin-bottom: 2rem; }
    .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 0.5rem; }

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

    /* BRIDGE */
    .bridge-section { padding: 2rem 2rem; }
    .bridge-inner { max-width: 660px; margin: 0 auto; text-align: center; }
    .bridge-text { font-size: 1rem; color: var(--text-2); line-height: 1.8; margin-bottom: 1.25rem; }
    .bridge-text em { color: var(--text); font-style: normal; }
    .bridge-text strong { color: var(--accent); font-weight: 700; }
    .bridge-cta { display: inline-block; font-size: 13px; font-weight: 600; color: var(--accent); text-decoration: none; border: 1px solid var(--accent-dim); border-radius: 24px; padding: 8px 20px; transition: .2s; }
    .bridge-cta:hover { background: var(--accent-dim); }

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
    .fb-ticker-track span { display: inline-block; margin: 0 1.3rem; font-size: 14px; color: var(--text); font-weight: 600; }
    .fb-ticker-track span b { color: var(--accent); font-weight: 700; }
    .fb-ticker-track span::before { content: '\2726'; color: var(--accent-2); font-size: 9px; vertical-align: middle; margin-right: 1.3rem; opacity: 0.7; }
    @keyframes fbticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* FANBASE PROMO */
    .fb-promo { text-align: center; }
    .fb-promo-intro { font-size: 14px; color: var(--text-2); max-width: 520px; margin: 0 auto 2rem; line-height: 1.7; }
    .fb-promo-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(118px, 1fr));
        gap: 10px; max-width: 640px; margin: 0 auto 2rem;
    }
    .fb-promo-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 0.85rem 0.8rem; text-align: left; }
    .fb-promo-card .ic { font-size: 20px; line-height: 1; }
    .fb-promo-card h4 { font-size: 12.5px; font-weight: 600; color: var(--text); margin: 0.5rem 0 0.2rem; }
    .fb-promo-card p { font-size: 11px; color: var(--text-3); line-height: 1.45; margin: 0; }
    .fb-promo-cta { text-decoration: none; display: inline-block; font-size: 15px; padding: 13px 28px; }
    .fb-promo-note { font-size: 12px; color: var(--text-3); margin-top: 0.85rem; }

    /* FANBASE MOVEMENT */
    .fb-movement {
        position: relative; overflow: hidden;
        background: var(--card-bg); border: 1px solid var(--border);
        border-radius: 20px; padding: 2.75rem 1.75rem; margin-bottom: 2.25rem;
    }
    .fb-movement::before {
        content: ''; position: absolute; top: -90px; left: 50%; transform: translateX(-50%);
        width: 380px; height: 260px;
        background: radial-gradient(ellipse, var(--accent-glow) 0%, transparent 70%);
        pointer-events: none;
    }
    .fb-movement > * { position: relative; z-index: 1; }
    .fb-movement h2 { font-size: clamp(1.6rem, 5.5vw, 2.5rem); font-weight: 300; line-height: 1.18; color: var(--text); margin: 0.5rem 0 1.1rem; }
    .fb-movement h2 b { font-weight: 600; color: var(--accent); }
    .fb-roles-label { font-size: 13px; color: var(--text-3); margin: 1.6rem 0 0.9rem; }
    .fb-roles { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; max-width: 660px; margin: 0 auto; }
    .fb-role { font-size: 12.5px; padding: 7px 14px; border-radius: 20px; background: var(--bg-3); border: 1px solid var(--border); color: var(--text-2); white-space: nowrap; }
    .fb-role.you { background: var(--accent); border-color: var(--accent); color: #fff; font-weight: 600; }
    .fb-beta {
        max-width: 580px; margin: 2rem auto 0;
        background: var(--bg-3); border: 1px dashed var(--border); border-radius: 14px;
        padding: 1rem 1.25rem; font-size: 12.5px; color: var(--text-3); line-height: 1.7;
        text-align: left; display: flex; gap: 11px; align-items: flex-start;
    }
    .fb-beta .bic { font-size: 19px; flex-shrink: 0; line-height: 1.4; }
    .fb-beta b { color: var(--text-2); font-weight: 600; }

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
    /* ============================================================
       AURORA STUDIO — refresh visual (homepage)
       ============================================================ */
    /* layer aurora di belakang konten, di atas bg navy */
    .aurora-bg { position: fixed; inset: -12%; z-index: -1; pointer-events: none; overflow: hidden; }
    .aurora-bg b { position: absolute; display: block; border-radius: 50%; filter: blur(70px); will-change: transform; }
    .aurora-bg .a1 { width: 46vw; height: 46vw; left: -6%; top: -8%;
        background: radial-gradient(circle, var(--accent) 0%, transparent 62%); opacity: 0.28;
        animation: ard1 28s ease-in-out infinite alternate; }
    .aurora-bg .a2 { width: 42vw; height: 42vw; right: -8%; bottom: -12%;
        background: radial-gradient(circle, var(--accent-2) 0%, transparent 62%); opacity: 0.22;
        animation: ard2 34s ease-in-out infinite alternate; }
    .aurora-bg .a3 { width: 36vw; height: 36vw; left: 42%; top: 38%;
        background: radial-gradient(circle, #6f6cff 0%, transparent 62%); opacity: 0.15;
        animation: ard3 40s ease-in-out infinite alternate; }
    @keyframes ard1 { from { transform: translate(0,0) scale(1); } to { transform: translate(7vw,5vh) scale(1.18); } }
    @keyframes ard2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-6vw,-4vh) scale(1.12); } }
    @keyframes ard3 { from { transform: translate(0,0) scale(1); } to { transform: translate(-5vw,4vh) scale(1.20); } }
    [data-theme="light"] .aurora-bg { opacity: 0.65; }
    /* hemat GPU di HP: blur lebih ringan */
    @media (max-width: 640px) { .aurora-bg b { filter: blur(48px); } }

    /* display font + gradient accent text */
    .hero-title, .section-heading, .fb-movement h2 { font-family: 'Space Grotesk', 'Inter', sans-serif; }
    .hero-title { letter-spacing: 0.015em; font-weight: 500; }
    .grad-text, .fb-movement h2 b {
        background: linear-gradient(100deg, var(--accent) 0%, var(--accent-2) 100%);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }

    /* tombol primary: gradient + glow + sheen sweep */
    .btn-primary {
        background: linear-gradient(100deg, var(--accent) 0%, var(--accent-2) 125%);
        color: #fff; position: relative; overflow: hidden;
        box-shadow: 0 6px 22px -8px var(--accent);
        transition: transform 0.2s ease, box-shadow 0.25s ease, opacity 0.2s ease;
    }
    .btn-primary:hover { opacity: 1; transform: translateY(-2px); box-shadow: 0 11px 30px -8px var(--accent); }
    .btn-primary::after {
        content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255,255,255,0.38), transparent);
        transform: skewX(-20deg); transition: left 0.6s ease; pointer-events: none;
    }
    .btn-primary:hover::after { left: 145%; }

    /* ticker: lebih menonjol — tint aksen + tepi bercahaya + tepi memudar */
    .fb-ticker {
        background: linear-gradient(90deg, rgba(56,168,204,0.12), rgba(240,112,64,0.08) 50%, rgba(56,168,204,0.12));
        border-top: 1px solid var(--accent-dim); border-bottom: 1px solid var(--accent-dim);
        padding: 13px 0;
        box-shadow: 0 0 24px -8px var(--accent-glow) inset;
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 7%, #000 93%, transparent);
        mask-image: linear-gradient(90deg, transparent, #000 7%, #000 93%, transparent);
    }

    /* kartu fitur: angkat + border menyala */
    .fb-promo-card { transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.25s ease; }
    .fb-promo-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 12px 28px -14px var(--accent); }
    .fb-role { transition: transform 0.15s ease, border-color 0.15s ease, color 0.15s ease; }
    .fb-role:hover { transform: translateY(-2px); border-color: var(--accent); color: var(--text); }
    a.fb-role.you { text-decoration: none; cursor: pointer; }
    a.fb-role.you:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 8px 20px -8px var(--accent); }

    /* movement banner sedikit glassy */
    .fb-movement { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

    /* motif equalizer */
    .eq { display: inline-flex; align-items: flex-end; gap: 3px; height: 15px; vertical-align: middle; margin-right: 9px; }
    .eq i { width: 3px; background: linear-gradient(var(--accent), var(--accent-2)); border-radius: 2px; transform-origin: bottom; animation: eqb 1s ease-in-out infinite; }
    .eq i:nth-child(1){ height: 40%; animation-delay: -0.2s; }
    .eq i:nth-child(2){ height: 80%; animation-delay: -0.5s; }
    .eq i:nth-child(3){ height: 55%; animation-delay: -0.1s; }
    .eq i:nth-child(4){ height: 95%; animation-delay: -0.7s; }
    .eq i:nth-child(5){ height: 65%; animation-delay: -0.35s; }
    @keyframes eqb { 0%,100% { transform: scaleY(0.4); } 50% { transform: scaleY(1); } }

    /* scroll reveal */
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity 0.7s ease, transform 0.7s ease; }
    .reveal.in { opacity: 1; transform: none; }

    /* polish */
    ::selection { background: var(--accent); color: #fff; }
    .btn-primary:focus-visible, .btn-ghost:focus-visible, .hero-collapse:focus-visible, .fb-ticker:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }

    @media (prefers-reduced-motion: reduce) {
        .aurora-bg b, .eq i, .fb-ticker-track { animation: none !important; }
        .reveal { opacity: 1 !important; transform: none !important; }
        .btn-primary::after { display: none; }
    }

    /* ====== FEATURE SHOWCASE ====== */
    .feat-showcase { max-width: 540px; margin: 0 auto; text-align: center; }
    .feat-tabs {
        display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none;
        justify-content: center; flex-wrap: wrap; margin-bottom: 1.5rem; padding-bottom: 2px;
    }
    .feat-tabs::-webkit-scrollbar { display: none; }
    .feat-tab {
        flex-shrink: 0; padding: 7px 16px; border-radius: 20px;
        border: 1px solid var(--border); background: var(--surface); color: var(--text-3);
        font-size: 12px; font-weight: 500; cursor: pointer; transition: 0.2s; font-family: inherit;
    }
    .feat-tab:hover { border-color: var(--accent); color: var(--accent); }
    .feat-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; }

    .feat-phone {
        position: relative; width: 214px; aspect-ratio: 360 / 740;
        background: #0d0d0f; border-radius: 26px; border: 3px solid #2a2a30;
        box-shadow: 0 0 0 1px rgba(255,255,255,0.06), 0 30px 80px -20px rgba(0,0,0,0.7), inset 0 0 20px rgba(255,255,255,0.02);
        margin: 0 auto 1.25rem; overflow: hidden;
    }
    .feat-phone-inner { position: absolute; inset: 0; overflow: hidden; border-radius: 22px; }
    .feat-notch {
        position: absolute; top: 0; left: 50%; transform: translateX(-50%);
        width: 64px; height: 20px; background: #0d0d0f; border-radius: 0 0 14px 14px; z-index: 10;
    }
    .feat-home-bar {
        position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
        width: 60px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 4px; z-index: 10;
    }

    .feat-screen {
        position: absolute; inset: 0; background: #12121a;
        display: flex; flex-direction: column; overflow: hidden;
        opacity: 0; transform: translateX(30px);
        transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none; color: #e0e0ea; font-size: 11px;
    }
    .feat-screen.active { opacity: 1; transform: translateX(0); pointer-events: auto; }
    .feat-screen.exit  { opacity: 0; transform: translateX(-30px); }
    .fs-screenshot { width: 100%; height: 100%; object-fit: cover; object-position: top; display: block; }

    .fs-header {
        padding: 24px 12px 8px; border-bottom: 1px solid rgba(255,255,255,0.06);
        display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    }
    .fs-app-name { font-size: 13px; font-weight: 600; color: #f0f0fa; }
    .fs-badge.green { font-size: 9px; background: rgba(34,197,94,0.15); color: #22c55e; border-radius: 8px; padding: 2px 7px; }

    /* tuner */
    .fs-tuner { display: flex; flex-direction: column; align-items: center; padding: 8px 12px 12px; flex: 1; }
    .fs-gauge { width: 150px; height: 90px; flex-shrink: 0; }
    .fs-gauge-fill { animation: fsgaugefill 3s ease-in-out infinite alternate; }
    @keyframes fsgaugefill { 0%{stroke-dashoffset:120} 50%{stroke-dashoffset:65} 100%{stroke-dashoffset:40} }
    .fs-needle { transform-origin: 90px 100px; animation: fsneedle 3s ease-in-out infinite alternate; }
    @keyframes fsneedle { 0%{transform:rotate(-45deg)} 50%{transform:rotate(0deg)} 100%{transform:rotate(20deg)} }
    .fs-note-big { font-size: 42px; font-weight: 700; color: #fff; line-height: 1; margin: 4px 0 2px; }
    .fs-in-tune { font-size: 11px; color: #22c55e; font-weight: 600; letter-spacing: 0.05em; }
    .fs-hz { font-size: 10px; color: rgba(255,255,255,0.35); margin: 3px 0 10px; }
    .fs-strings { display: flex; gap: 5px; margin-top: 4px; }
    .fs-str {
        width: 30px; height: 30px; border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05);
        color: rgba(255,255,255,0.6); font-size: 11px; font-weight: 600; cursor: pointer; font-family: inherit;
    }
    .fs-str.active { background: var(--accent); border-color: var(--accent); color: #fff; }

    /* chord */
    .fs-chord-wrap { display: flex; flex-direction: column; align-items: center; padding: 8px 14px 10px; flex: 1; }
    .fs-inst-tabs { display: flex; gap: 4px; margin-bottom: 8px; }
    .fs-inst {
        padding: 4px 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12);
        background: transparent; color: rgba(255,255,255,0.45); font-size: 10px; font-family: inherit; cursor: pointer;
    }
    .fs-inst.active { background: rgba(56,168,204,0.2); border-color: var(--accent); color: var(--accent); }
    .fs-chord-name { font-size: 32px; font-weight: 700; color: #fff; margin: 4px 0; }
    .fs-fretboard {
        position: relative; width: 130px; height: 80px;
        background: #1e1a14; border-radius: 4px; border: 1px solid rgba(255,255,255,0.12);
        margin-bottom: 4px; overflow: hidden;
    }
    .fs-fret-strings { position: absolute; inset: 0; display: flex; justify-content: space-around; }
    .fs-fret-strings span { width: 1px; background: rgba(255,255,255,0.18); height: 100%; }
    .fs-fret-line { position: absolute; left: 0; right: 0; height: 1px; background: rgba(255,255,255,0.12); }
    .fs-dot {
        position: absolute; width: 12px; height: 12px;
        background: var(--accent); border-radius: 50%; transform: translate(-50%,-50%);
    }
    .fs-fret-label { font-size: 9px; color: rgba(255,255,255,0.3); margin-bottom: 6px; }
    .fs-play-btn {
        padding: 7px 18px; border-radius: 14px; border: 1px solid rgba(56,168,204,0.4);
        background: rgba(56,168,204,0.12); color: var(--accent); font-size: 11px; font-family: inherit; cursor: pointer; margin-top: 4px;
    }

    /* porto */
    .fs-porto {
        display: flex; flex-direction: column; align-items: center;
        padding: 6px 12px 10px; flex: 1; overflow-y: auto; scrollbar-width: none;
    }
    .fs-porto::-webkit-scrollbar { display: none; }
    .fs-porto-avatar {
        width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), #6366f1);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; flex-shrink: 0;
    }
    .fs-porto-name { font-size: 14px; font-weight: 600; color: #f0f0fa; }
    .fs-porto-loc { font-size: 10px; color: rgba(255,255,255,0.4); margin: 2px 0 6px; }
    .fs-porto-badges { display: flex; gap: 5px; margin-bottom: 8px; }
    .fs-badge-chip { font-size: 9px; padding: 2px 8px; border-radius: 10px; background: rgba(99,102,241,0.2); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); }
    .fs-porto-rows { width: 100%; margin-bottom: 8px; }
    .fs-porto-row { display: flex; gap: 6px; padding: 4px 0; border-bottom: 1px solid rgba(255,255,255,0.05); align-items: flex-start; }
    .fs-pr-label { font-size: 9px; color: rgba(255,255,255,0.35); min-width: 36px; padding-top: 1px; flex-shrink: 0; }
    .fs-pr-val { font-size: 10px; color: rgba(255,255,255,0.75); line-height: 1.4; }
    .fs-socials { display: flex; gap: 5px; }
    .fs-soc { font-size: 9px; padding: 4px 8px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.55); cursor: pointer; }
    .fs-soc.yt { border-color: rgba(239,68,68,0.3); color: #f87171; }
    .fs-soc.ig { border-color: rgba(236,72,153,0.3); color: #f472b6; }
    .fs-soc.sp { border-color: rgba(34,197,94,0.3); color: #4ade80; }

    /* cari personil */
    .fs-cari { display: flex; flex-direction: column; padding: 8px 12px 10px; flex: 1; overflow-y: auto; scrollbar-width: none; }
    .fs-cari::-webkit-scrollbar { display: none; }
    .fs-search-bar {
        display: flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px; padding: 7px 10px; margin-bottom: 8px; flex-shrink: 0;
    }
    .fs-search-ph { font-size: 11px; color: rgba(255,255,255,0.3); }
    .fs-filter-chips { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 7px; flex-shrink: 0; }
    .fs-chip { font-size: 9px; padding: 3px 9px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.45); }
    .fs-chip.active { background: rgba(56,168,204,0.15); border-color: rgba(56,168,204,0.5); color: var(--accent); }
    .fs-result-count { font-size: 10px; color: rgba(255,255,255,0.4); margin-bottom: 8px; flex-shrink: 0; }
    .fs-musician-card { display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .fs-musician-card:last-child { border-bottom: none; }
    .fs-mus-av {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .fs-mus-av.sm { width: 28px; height: 28px; font-size: 10px; }
    .fs-mus-name { font-size: 11px; font-weight: 600; color: #f0f0fa; }
    .fs-mus-meta { font-size: 9px; color: rgba(255,255,255,0.4); }
    .fs-mus-badge { margin-left: auto; font-size: 9px; padding: 3px 9px; border-radius: 10px; background: rgba(56,168,204,0.15); border: 1px solid rgba(56,168,204,0.3); color: var(--accent); flex-shrink: 0; }

    /* chat */
    .fs-chat-hd { display: flex; align-items: center; gap: 8px; }
    .fs-online { font-size: 9px; color: #22c55e; }
    .fs-chat-body { flex: 1; padding: 8px 12px; display: flex; flex-direction: column; gap: 6px; overflow-y: auto; scrollbar-width: none; }
    .fs-chat-body::-webkit-scrollbar { display: none; }
    .fs-bubble { max-width: 75%; padding: 7px 10px; border-radius: 14px; font-size: 10px; line-height: 1.5; }
    .fs-bubble.them { align-self: flex-start; background: rgba(255,255,255,0.07); border-radius: 14px 14px 14px 4px; color: rgba(255,255,255,0.8); }
    .fs-bubble.me { align-self: flex-end; background: linear-gradient(135deg, var(--accent), #6366f1); border-radius: 14px 14px 4px 14px; color: #fff; }
    .fs-chat-input { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-top: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; margin-bottom: 20px; }
    .fs-ci-ph { font-size: 10px; color: rgba(255,255,255,0.25); }
    .fs-ci-send { font-size: 14px; color: var(--accent); }

    /* posting */
    .fs-feed { flex: 1; padding: 6px 10px; display: flex; flex-direction: column; gap: 6px; overflow-y: auto; scrollbar-width: none; }
    .fs-feed::-webkit-scrollbar { display: none; }
    .fs-post { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; padding: 9px 10px; }
    .fs-post-hd { display: flex; align-items: center; gap: 7px; margin-bottom: 6px; }
    .fs-post-name { font-size: 11px; font-weight: 600; color: #f0f0fa; }
    .fs-post-time { font-size: 9px; color: rgba(255,255,255,0.3); }
    .fs-post-text { font-size: 10px; color: rgba(255,255,255,0.7); line-height: 1.5; margin: 0 0 7px; }
    .fs-post-actions { display: flex; gap: 12px; font-size: 10px; color: rgba(255,255,255,0.4); }

    /* dots */
    .feat-dots { display: flex; justify-content: center; gap: 7px; margin-bottom: 1rem; }
    .feat-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--border); cursor: pointer; transition: 0.25s; display: inline-block; }
    .feat-dot.active { background: var(--accent); transform: scale(1.2); }

    /* desc */
    .feat-desc { min-height: 50px; }
    .feat-desc-item { display: none; font-size: 13px; color: var(--text-2); line-height: 1.6; padding: 0 10px; }
    .feat-desc-item.active { display: block; }
    .feat-desc-item strong { color: var(--text); }

    @media (max-width: 420px) {
        .feat-phone { width: 190px; height: auto; }
        .feat-tabs { flex-wrap: nowrap; justify-content: flex-start; }
    }
</style>
@endpush

@section('content')

<div class="aurora-bg" aria-hidden="true"><b class="a1"></b><b class="a2"></b><b class="a3"></b></div>

{{-- HERO --}}
<div class="hero" id="heroSection">
    <div class="hero-bg"></div>
    @if(file_exists(public_path('images/margonoandi.jpg')))
    <img src="{{ asset('images/margonoandi.jpg') }}" class="hero-photo" alt="Rakhman Andi">
    @endif
    <div class="hero-content">
        <div class="hero-id-row">
            <p class="hero-byline">
                {{ $settings['artist_name'] ?? 'Rakhman Andi' }} ·
                <span>{{ $settings['artist_role'] ?? 'Songwriter' }}</span> ·
                Project <span>{{ $settings['artist_project'] ?? 'Margonoandi' }}</span>
            </p>
            <button class="hero-collapse" id="heroCollapse" onclick="toggleHero()" aria-expanded="true">Sembunyikan &#9652;</button>
        </div>
        <div class="hero-body" id="heroBody">
        <h1 class="hero-title">
            {{ $settings['tagline_1'] ?? 'Tiga chord.' }}<br>
            {{ $settings['tagline_2'] ?? 'Satu rindu.' }}<br>
            {{ $settings['tagline_3'] ?? 'Dua puluh tahun.' }}
        </h1>
        <div class="hero-story-wrap" id="heroStoryWrap">
            <p class="hero-story">{!! nl2br(e($settings['hero_story'] ?? '')) !!}</p>
        </div>
        <div class="hero-actions">
            <button class="btn-primary" onclick="document.getElementById('featuredSection').scrollIntoView({behavior:'smooth'})">
                &#9654; Dengarkan
            </button>
            <button class="btn-ghost" id="heroStoryToggle" aria-expanded="false" onclick="toggleHeroStory()">
                Baca ceritanya &#9662;
            </button>
        </div>
        </div>{{-- /hero-body --}}
    </div>
    <div class="scroll-hint">
        <div class="scroll-line"></div>
        <span>scroll</span>
    </div>
</div>

<script>
function toggleHeroStory(){
    var w=document.getElementById('heroStoryWrap'), b=document.getElementById('heroStoryToggle');
    var open=w.classList.toggle('open');
    b.innerHTML = open ? 'Tutup cerita &#9652;' : 'Baca ceritanya &#9662;';
    b.setAttribute('aria-expanded', open ? 'true' : 'false');
}
function setHeroCollapsed(collapsed, animate){
    var hero=document.getElementById('heroSection'), body=document.getElementById('heroBody'), btn=document.getElementById('heroCollapse');
    if(!hero||!body||!btn) return;
    if(!animate){ var ht=hero.style.transition, bt=body.style.transition; hero.style.transition='none'; body.style.transition='none'; }
    hero.classList.toggle('collapsed', collapsed);
    body.classList.toggle('hidden', collapsed);
    btn.innerHTML = collapsed ? 'Tampilkan intro &#9662;' : 'Sembunyikan &#9652;';
    btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    if(!animate){ requestAnimationFrame(function(){ hero.style.transition=''; body.style.transition=''; }); }
}
function toggleHero(){
    var collapsed = !document.getElementById('heroSection').classList.contains('collapsed');
    setHeroCollapsed(collapsed, true);
    try { localStorage.setItem('heroCollapsed', collapsed ? '1' : '0'); } catch(e){}
}
try { if(localStorage.getItem('heroCollapsed')==='1') setHeroCollapsed(true, false); } catch(e){}
</script>

@php $fbEntry = auth()->check() ? route('aku') : route('google.login'); @endphp

{{-- BRIDGE: Personal → Ecosystem --}}
<div class="section bridge-section">
    <div class="bridge-inner">
        <p class="bridge-text">
            Saya Rakhman Andi — musisi indie yang selama dua puluh tahun menulis lagu di kamar sendiri,
            bingung bagaimana caranya <em>tumbuh</em> tanpa label, tanpa manajer, tanpa modal besar.
            Dari kebingungan itu, saya bangun ini: sebuah ekosistem tempat musisi seperti kita
            bisa saling menemukan, berbagi alat, dan membuktikan bahwa
            <strong>kamar rekaman pun bisa melahirkan karir yang nyata.</strong>
        </p>
        <a href="{{ $fbEntry }}" class="bridge-cta">Bergabung sekarang — gratis selamanya</a>
    </div>
</div>

{{-- FANBASE MOVEMENT / PROMO CTA --}}
<div class="section fb-promo" id="fbPromoSection">

    {{-- Gerakan / fb-movement --}}
    <div class="fb-movement">
        <p class="section-eyebrow"><span class="eq"><i></i><i></i><i></i><i></i><i></i></span>Sebuah gerakan, bukan sekadar aplikasi</p>
        <h2>Ekosistem Musik Indonesia,<br><b>Ayo kita mulai dari kamarmu.</b></h2>
        <p class="fb-promo-intro">Bukan soal kamu sudah terkenal atau belum. Budaya baru ini lahir dari siapa saja yang cinta musik. Bahkan dari kamarmu, hari ini. Tempat para musisi rumahan membangun interaksi, saling menginspirasi, dan mari tumbuh bersama.</p>
    {{-- CTA 2: Mulai dari kamarmu --}}
    <a href="{{ $fbEntry }}" class="btn-primary fb-promo-cta"
       @guest onclick="gtag && gtag('event', 'cta_click', {event_category:'engagement', button:'promo_gabung'})" @endguest
    >Ayoo Masuk</a>
    </div>
    
    <p class="section-eyebrow">FITUR APLIKASI LENGKAP</p>
    <p class="section-heading">Tanpa diganggu iklan - Wajib kamu coba</p>

    <div class="feat-showcase">
        <div class="feat-tabs" id="featTabs">
            <button class="feat-tab active" onclick="switchFeat(0)">Maftuner</button>
            <button class="feat-tab" onclick="switchFeat(1)">Chord</button>
            <button class="feat-tab" onclick="switchFeat(2)">Portofolio</button>
            <button class="feat-tab" onclick="switchFeat(3)">Cari Personil</button>
            <button class="feat-tab" onclick="switchFeat(4)">Chat</button>
            <button class="feat-tab" onclick="switchFeat(5)">Posting</button>
        </div>

        @php
        $featShots = [
            $settings['feat_screenshot_0'] ?? '',
            $settings['feat_screenshot_1'] ?? '',
            $settings['feat_screenshot_2'] ?? '',
            $settings['feat_screenshot_3'] ?? '',
            $settings['feat_screenshot_4'] ?? '',
            $settings['feat_screenshot_5'] ?? '',
        ];
        @endphp

        <div class="feat-phone">
            <div class="feat-phone-inner">

                {{-- Screen 0: Maftuner --}}
                <div class="feat-screen active" data-screen="0">
                    @if($featShots[0])
                    <img src="{{ asset($featShots[0]) }}" class="fs-screenshot" alt="Maftuner">
                    @else
                    <div class="fs-header">
                        <span class="fs-app-name">Maftuner</span>
                        <span class="fs-badge green">Gratis &amp; bebas iklan</span>
                    </div>
                    <div class="fs-tuner">
                        <svg class="fs-gauge" viewBox="0 0 180 110">
                            <path d="M10 100 A 80 80 0 0 1 170 100" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="12" stroke-linecap="round"/>
                            <path d="M10 100 A 80 80 0 0 1 170 100" fill="none" stroke="url(#gaug)" stroke-width="12" stroke-linecap="round" stroke-dasharray="251" stroke-dashoffset="65" class="fs-gauge-fill"/>
                            <defs>
                                <linearGradient id="gaug" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#f59e0b"/>
                                    <stop offset="50%" stop-color="#22c55e"/>
                                    <stop offset="100%" stop-color="#f59e0b"/>
                                </linearGradient>
                            </defs>
                            <line class="fs-needle" x1="90" y1="100" x2="90" y2="28" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="90" cy="100" r="5" fill="#fff"/>
                        </svg>
                        <div class="fs-note-big">E</div>
                        <div class="fs-in-tune">&#10003; IN TUNE</div>
                        <div class="fs-hz">440.0 Hz</div>
                        <div class="fs-strings">
                            <button class="fs-str active">E</button>
                            <button class="fs-str">A</button>
                            <button class="fs-str">D</button>
                            <button class="fs-str">G</button>
                            <button class="fs-str">B</button>
                            <button class="fs-str">e</button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Screen 1: Belajar Chord --}}
                <div class="feat-screen" data-screen="1">
                    @if($featShots[1])
                    <img src="{{ asset($featShots[1]) }}" class="fs-screenshot" alt="Belajar Chord">
                    @else
                    <div class="fs-header">
                        <span class="fs-app-name">Belajar Chord</span>
                    </div>
                    <div class="fs-chord-wrap">
                        <div class="fs-inst-tabs">
                            <button class="fs-inst active">Gitar</button>
                            <button class="fs-inst">Piano</button>
                            <button class="fs-inst">Ukulele</button>
                        </div>
                        <div class="fs-chord-name">Am</div>
                        <div class="fs-fretboard">
                            <div class="fs-fret-strings">
                                <span></span><span></span><span></span><span></span><span></span><span></span>
                            </div>
                            <div class="fs-fret-line" style="top:20%"></div>
                            <div class="fs-fret-line" style="top:40%"></div>
                            <div class="fs-fret-line" style="top:60%"></div>
                            <div class="fs-fret-line" style="top:80%"></div>
                            <div class="fs-dot" style="top:18%;left:33%;"></div>
                            <div class="fs-dot" style="top:18%;left:50%;"></div>
                            <div class="fs-dot" style="top:18%;left:66%;"></div>
                        </div>
                        <div class="fs-fret-label">Fret 1 &mdash; Am</div>
                        <button class="fs-play-btn">&#9654; Dengar Suara</button>
                    </div>
                    @endif
                </div>

                {{-- Screen 2: Portofolio Musisi --}}
                <div class="feat-screen" data-screen="2">
                    @if($featShots[2])
                    <img src="{{ asset($featShots[2]) }}" class="fs-screenshot" alt="Portofolio Musisi">
                    @else
                    <div class="fs-header">
                        <span class="fs-app-name">Portofolio Musisi</span>
                    </div>
                    <div class="fs-porto">
                        <div class="fs-porto-avatar">MG</div>
                        <div class="fs-porto-name">Margonoandi</div>
                        <div class="fs-porto-loc">&#128205; Yogyakarta</div>
                        <div class="fs-porto-badges">
                            <span class="fs-badge-chip">Musisi</span>
                            <span class="fs-badge-chip">Gitaris</span>
                        </div>
                        <div class="fs-porto-rows">
                            <div class="fs-porto-row"><span class="fs-pr-label">Genre</span><span class="fs-pr-val">Indie Pop &middot; Folk</span></div>
                            <div class="fs-porto-row"><span class="fs-pr-label">Posisi</span><span class="fs-pr-val">Gitaris, Vokalis</span></div>
                            <div class="fs-porto-row"><span class="fs-pr-label">Bio</span><span class="fs-pr-val">Musisi rumahan yang percaya bahwa musik kecil bisa menyentuh hati besar.</span></div>
                        </div>
                        <div class="fs-socials">
                            <span class="fs-soc yt">&#9654; YouTube</span>
                            <span class="fs-soc ig">&#128247; IG</span>
                            <span class="fs-soc sp">&#127925; Spotify</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Screen 3: Cari Personil --}}
                <div class="feat-screen" data-screen="3">
                    @if($featShots[3])
                    <img src="{{ asset($featShots[3]) }}" class="fs-screenshot" alt="Cari Personil">
                    @else
                    <div class="fs-header">
                        <span class="fs-app-name">Cari Personil</span>
                    </div>
                    <div class="fs-cari">
                        <div class="fs-search-bar">
                            <span>&#128269;</span>
                            <span class="fs-search-ph">Cari musisi...</span>
                        </div>
                        <div class="fs-filter-chips">
                            <span class="fs-chip active">Yogyakarta</span>
                            <span class="fs-chip active">Gitaris</span>
                            <span class="fs-chip">Bassist</span>
                            <span class="fs-chip">Vokalis</span>
                        </div>
                        <div class="fs-result-count">23 musisi ditemukan</div>
                        <div class="fs-musician-card">
                            <div class="fs-mus-av" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">RN</div>
                            <div><div class="fs-mus-name">Rizky N.</div><div class="fs-mus-meta">Gitaris &middot; Bandung</div></div>
                            <span class="fs-mus-badge">Follow</span>
                        </div>
                        <div class="fs-musician-card">
                            <div class="fs-mus-av" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">SR</div>
                            <div><div class="fs-mus-name">Sari R.</div><div class="fs-mus-meta">Bassist &middot; Jogja</div></div>
                            <span class="fs-mus-badge">Follow</span>
                        </div>
                        <div class="fs-musician-card">
                            <div class="fs-mus-av" style="background:linear-gradient(135deg,#06b6d4,#3b82f6)">DP</div>
                            <div><div class="fs-mus-name">Dani P.</div><div class="fs-mus-meta">Drummer &middot; Solo</div></div>
                            <span class="fs-mus-badge">Follow</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Screen 4: Chat --}}
                <div class="feat-screen" data-screen="4">
                    @if($featShots[4])
                    <img src="{{ asset($featShots[4]) }}" class="fs-screenshot" alt="Chat">
                    @else
                    <div class="fs-header">
                        <div class="fs-chat-hd">
                            <div class="fs-mus-av sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">RN</div>
                            <div>
                                <div class="fs-app-name">Rizky N.</div>
                                <div class="fs-online">&#9679; Online</div>
                            </div>
                        </div>
                    </div>
                    <div class="fs-chat-body">
                        <div class="fs-bubble them">Hei, kamu pakai chord Am atau Am7 di lagu itu?</div>
                        <div class="fs-bubble me">Am7 bro, lebih enak di bagian reff-nya &#9996;</div>
                        <div class="fs-bubble them">Kapan ada sesi latihan bareng?</div>
                        <div class="fs-bubble me">Sabtu sore bisa! Kamu bawa gitar akustik aja &#127928;</div>
                    </div>
                    <div class="fs-chat-input">
                        <span class="fs-ci-ph">Tulis pesan...</span>
                        <span class="fs-ci-send">&#10148;</span>
                    </div>
                    @endif
                </div>

                {{-- Screen 5: Posting --}}
                <div class="feat-screen" data-screen="5">
                    @if($featShots[5])
                    <img src="{{ asset($featShots[5]) }}" class="fs-screenshot" alt="Posting">
                    @else
                    <div class="fs-header">
                        <span class="fs-app-name">Posting</span>
                    </div>
                    <div class="fs-feed">
                        <div class="fs-post">
                            <div class="fs-post-hd">
                                <div class="fs-mus-av sm" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">SR</div>
                                <div><div class="fs-post-name">Sari R.</div><div class="fs-post-time">2 jam lalu</div></div>
                            </div>
                            <p class="fs-post-text">Akhirnya berhasil ngerekam versi akustik lagu pertama! Nunggu feedback dari kalian &#127925;&#10024;</p>
                            <div class="fs-post-actions"><span>&#10084;&#65039; 24</span><span>&#128172; 8 komentar</span></div>
                        </div>
                        <div class="fs-post">
                            <div class="fs-post-hd">
                                <div class="fs-mus-av sm" style="background:linear-gradient(135deg,#06b6d4,#3b82f6)">DP</div>
                                <div><div class="fs-post-name">Dani P.</div><div class="fs-post-time">5 jam lalu</div></div>
                            </div>
                            <p class="fs-post-text">Lagi nyari gitaris buat project lagu folk-indie. DM kalau tertarik! &#128588;</p>
                            <div class="fs-post-actions"><span>&#10084;&#65039; 17</span><span>&#128172; 12 komentar</span></div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
            <div class="feat-home-bar"></div>
        </div>

        <div class="feat-dots" id="featDots">
            <span class="feat-dot active" onclick="switchFeat(0)"></span>
            <span class="feat-dot" onclick="switchFeat(1)"></span>
            <span class="feat-dot" onclick="switchFeat(2)"></span>
            <span class="feat-dot" onclick="switchFeat(3)"></span>
            <span class="feat-dot" onclick="switchFeat(4)"></span>
            <span class="feat-dot" onclick="switchFeat(5)"></span>
        </div>

        <div class="feat-desc" id="featDesc">
            <div class="feat-desc-item active" data-feat="0"><strong>Maftuner</strong> &mdash; Stem gitar langsung dari HP, akurat, gratis, tanpa iklan yang meresahkan.</div>
            <div class="feat-desc-item" data-feat="1"><strong>Belajar Chord</strong> &mdash; Kamus chord gitar, piano, dan ukulele. Visual fretboard + bisa didengarkan suaranya.</div>
            <div class="feat-desc-item" data-feat="2"><strong>Portofolio Musisi</strong> &mdash; Tampilkan dirimu: nama, lokasi, genre, posisi, bio, link YouTube, Instagram, dan Spotify.</div>
            <div class="feat-desc-item" data-feat="3"><strong>Cari Personil</strong> &mdash; Filter musisi berdasarkan kota, instrumen, dan genre. Temukan partner musik yang tepat.</div>
            <div class="feat-desc-item" data-feat="4"><strong>Chat</strong> &mdash; Obrolan privat antar musisi. Lebih personal, lebih fokus, tanpa noise media sosial.</div>
            <div class="feat-desc-item" data-feat="5"><strong>Posting</strong> &mdash; Bagikan cerita, progress, dan ide musikmu. Dapatkan sambutan dari komunitas musisi.</div>
        </div>
    </div>
    {{-- CTA 1: Coba Sekarang --}}
    <a href="{{ $fbEntry }}" class="btn-primary fb-promo-cta"
       @guest onclick="gtag && gtag('event', 'cta_click', {event_category:'engagement', button:'coba_sekarang'})" @endguest
    >Coba Sekarang</a>
    <p class="fb-promo-note">@auth Kamu sudah di dalam &mdash; ayo lanjut berkarya. @else Cukup login pakai Google &mdash; Gratis &amp; Aman. @endauth</p>

    {{-- STUDIO GRATIS — kartu menuju HALAMAN tool (internal-link untuk SEO) --}}
    <style>
        .fb-studio { margin-top:1.75rem; }
        .fb-studio-label { font-size:11px;font-weight:700;letter-spacing:.05em;color:var(--text-3,#94a3b8);text-transform:uppercase;margin-bottom:.85rem;text-align:center; }
        .fb-studio-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px; }
        .fb-studio-card { display:block;background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border);border-radius:14px;padding:1.1rem .85rem;text-decoration:none;text-align:center;transition:.18s; }
        .fb-studio-card:hover { border-color:var(--accent,#38bdf8);transform:translateY(-3px);box-shadow:0 14px 30px -18px var(--accent,#38bdf8); }
        .fb-studio-ic { font-size:1.9rem;margin-bottom:.45rem; }
        .fb-studio-t { font-weight:700;font-size:13.5px;color:var(--text,#f0f0f0); }
        .fb-studio-d { font-size:11px;color:var(--text-3,#94a3b8);margin-top:3px;line-height:1.45; }
    </style>
    <div class="fb-studio">
        <a href="{{ route('tools.index') }}" class="fb-studio-label" style="display:block;text-decoration:none;">🎛️ Studio Gratis — alat untuk musisi · lihat semua →</a>
        <div class="fb-studio-grid">
            <a href="{{ route('tools.potong-lagu') }}" class="fb-studio-card">
                <div class="fb-studio-ic">✂️</div>
                <div class="fb-studio-t">Potong Lagu</div>
                <div class="fb-studio-d">Potong MP3/WAV di browser</div>
            </a>
            <a href="{{ route('tools.hapus-vokal') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🎤</div>
                <div class="fb-studio-t">Hapus Vokal</div>
                <div class="fb-studio-d">Bikin karaoke / minus one</div>
            </a>
            <a href="{{ route('tools.cover-art') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🎨</div>
                <div class="fb-studio-t">Buat Cover</div>
                <div class="fb-studio-d">Cover art 1:1 hingga 3000px</div>
            </a>
            <a href="{{ route('tools.kartu-rilis') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🚀</div>
                <div class="fb-studio-t">Kartu Promo Rilis</div>
                <div class="fb-studio-d">Kartu promo + QR untuk story</div>
            </a>
            <a href="{{ route('tools.countdown') }}" class="fb-studio-card">
                <div class="fb-studio-ic">⏳</div>
                <div class="fb-studio-t">Countdown Rilis</div>
                <div class="fb-studio-d">Link hitung mundur real-time</div>
            </a>
            <a href="{{ route('tools.edit-metadata') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🏷️</div>
                <div class="fb-studio-t">Edit Metadata &amp; WAV</div>
                <div class="fb-studio-d">Tag MP3 + cover / konversi WAV</div>
            </a>
            <a href="{{ route('tools.chord-builder') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🎸</div>
                <div class="fb-studio-t">Chord Builder</div>
                <div class="fb-studio-d">Generate progresi chord siap pakai</div>
            </a>
            <a href="{{ route('tools.kalkulator-royalti') }}" class="fb-studio-card">
                <div class="fb-studio-ic">💰</div>
                <div class="fb-studio-t">Kalkulator Royalti</div>
                <div class="fb-studio-d">Estimasi pendapatan streaming</div>
            </a>
            <a href="{{ route('tools.rate-card') }}" class="fb-studio-card">
                <div class="fb-studio-ic">💼</div>
                <div class="fb-studio-t">Rate Card Generator</div>
                <div class="fb-studio-d">Buat daftar harga jasa musik</div>
            </a>
            <a href="{{ route('tools.transpose-kunci') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🔀</div>
                <div class="fb-studio-t">Transpose Kunci</div>
                <div class="fb-studio-d">Pindah kunci chord otomatis</div>
            </a>
            <a href="{{ route('tools.epk') }}" class="fb-studio-card">
                <div class="fb-studio-ic">📄</div>
                <div class="fb-studio-t">EPK Generator</div>
                <div class="fb-studio-d">Buat press kit musisi profesional</div>
            </a>
            <a href="{{ route('tools.setlist') }}" class="fb-studio-card">
                <div class="fb-studio-ic">🎵</div>
                <div class="fb-studio-t">Setlist Builder</div>
                <div class="fb-studio-d">Susun setlist manggung, cetak PDF</div>
            </a>
            <a href="{{ route('tools.release-planner') }}" class="fb-studio-card">
                <div class="fb-studio-ic">📅</div>
                <div class="fb-studio-t">Release Planner</div>
                <div class="fb-studio-d">Jadwal promo rilis T-42 sampai T+28</div>
            </a>
        </div>
    </div>

    <hr style="border:none;border-top:1px solid var(--border);margin:2.5rem 0;">

    {{-- Role cards --}}
    <p class="fb-roles-label" style="margin-top:2.5rem;">Apa pun latar musikmu, ada tempat di sini untuk berkembang :</p>
    <div class="fb-roles">
        <span class="fb-role">&#127928; Gitaris</span>
        <span class="fb-role">&#127925; Basis</span>
        <span class="fb-role">&#129345; Drummer</span>
        <span class="fb-role">&#127908; Vokalis</span>
        <span class="fb-role">&#127929; Keyboardis</span>
        <span class="fb-role">&#9997;&#65039; Songwriter</span>
        <span class="fb-role">&#127898;&#65039; Arranger</span>
        <span class="fb-role">&#127915; Event Organizer</span>
        <span class="fb-role">&#128141; Wedding Organizer</span>
        <span class="fb-role">&#128227; Promotor</span>
        <span class="fb-role">&#127911; Penikmat Musik</span>
        <a href="{{ $fbEntry }}" class="fb-role you"
           @guest onclick="gtag && gtag('event', 'cta_click', {event_category:'engagement', button:'dan_kamu'})" @endguest
        >&hellip;dan kamu? &#128075;</a>
    </div>

    {{-- Jujur ya — beta + CTA 3: Berani --}}
    <div class="fb-beta" style="margin-top:2.5rem;">
        <span class="bic">&#128679;</span>
        <span>Jujur ya &mdash; ini <b>masih tahap beta</b>, dan untuk sekarang masih <b>menumpang di web pribadi</b> saya. Tapi kalau dukungan kalian besar, kita serius bangun <b>rumah baru</b> yang layak buat ekosistem ini. Langkah besar ini dimulai dari kamu yang berani gabung lebih dulu. &#128293;</span>
    </div>
    <a href="{{ $fbEntry }}" class="btn-primary fb-promo-cta" style="margin-top:1rem;background:linear-gradient(135deg,#f59e0b,#ef4444);border:none;"
       @guest onclick="gtag && gtag('event', 'cta_click', {event_category:'engagement', button:'berani'})" @endguest
    >&#128293; Berani</a>
</div>

<hr class="divider">

{{-- COMMUNITY PREVIEW (ghost mode — blur gate, konversi) --}}
@php $previewPosts = $previewPosts ?? collect(); @endphp
<style>
    .cp-section { text-align:center; position:relative; }
    .cp-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; max-width:860px; margin:1.5rem auto 0; position:relative; }
    .cp-card { background:var(--card-bg); border:1px solid var(--border); border-radius:16px; padding:1.1rem 1.2rem; text-align:left; transition:border-color .2s; }
    .cp-card-header { display:flex; align-items:center; gap:10px; margin-bottom:.75rem; }
    .cp-av { width:38px; height:38px; border-radius:50%; object-fit:cover; border:1.5px solid var(--border); background:var(--accent-dim); }
    .cp-av-placeholder { width:38px; height:38px; border-radius:50%; background:var(--accent-dim); display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; color:var(--accent); }
    .cp-meta { flex:1; min-width:0; }
    .cp-name { font-weight:600; font-size:13.5px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .cp-time { font-size:11px; color:var(--text-3); margin-top:1px; }
    .cp-body { font-size:13.5px; color:var(--text-2); line-height:1.6; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    .cp-actions { display:flex; gap:14px; margin-top:.85rem; }
    .cp-act { font-size:12px; color:var(--text-3); display:flex; align-items:center; gap:4px; }
    /* blur gate overlay */
    .cp-blur-overlay { position:absolute; bottom:0; left:0; right:0; height:220px; background:linear-gradient(to bottom, transparent, var(--bg) 75%); pointer-events:none; z-index:2; }
    .cp-gate { position:relative; z-index:3; margin-top:-60px; padding-bottom:1.5rem; }
    .cp-gate-cta { display:inline-flex; flex-direction:column; align-items:center; gap:.5rem; }
    .cp-gate-label { font-size:13px; color:var(--text-3); }
    .cp-ghost-card { filter:blur(4px); pointer-events:none; user-select:none; opacity:.6; }
</style>
<div class="section cp-section">
    <p class="section-eyebrow">&#128172; Komunitas Aktif</p>
    <p class="section-heading">Apa yang sedang dibahas</p>
    <div class="cp-grid" id="cpGrid">
        @forelse($previewPosts as $i => $post)
        <div class="cp-card {{ $i >= 2 ? 'cp-ghost-card' : '' }}">
            <div class="cp-card-header">
                @if($post->user?->profile_photo_url ?? false)
                    <img src="{{ $post->user->profile_photo_url }}" class="cp-av" alt="">
                @else
                    <div class="cp-av-placeholder">{{ strtoupper(substr($post->user?->name ?? '?', 0, 1)) }}</div>
                @endif
                <div class="cp-meta">
                    <div class="cp-name">{{ $post->user?->name ?? 'Anggota' }}</div>
                    <div class="cp-time">{{ $post->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <p class="cp-body">{{ $post->body }}</p>
            <div class="cp-actions">
                <span class="cp-act">&#10084;&#65039; {{ $post->likes_count ?? 0 }}</span>
                <span class="cp-act">&#128172; {{ $post->comments_count ?? 0 }}</span>
            </div>
        </div>
        @empty
        {{-- Ghost placeholder cards if no posts yet --}}
        @foreach([['Hei, ada yang udah coba chord builder-nya? Bagus banget buat nulis lagu di kamar', 4, 2], ['Lagi rekaman EP pertama nih, ada yang mau kolaborasi vokal? Genre indie pop.', 7, 5], ['Share dong tips mixing untuk home recording dengan budget minim!', 12, 8]] as $j => $ghost)
        <div class="cp-card {{ $j >= 2 ? 'cp-ghost-card' : '' }}">
            <div class="cp-card-header">
                <div class="cp-av-placeholder">{{ ['M','R','A'][$j] }}</div>
                <div class="cp-meta">
                    <div class="cp-name">{{ ['Musisi Indie','Rocker Kamar','Andi R.'][$j] }}</div>
                    <div class="cp-time">{{ [2,5,1][$j] }} jam lalu</div>
                </div>
            </div>
            <p class="cp-body">{{ $ghost[0] }}</p>
            <div class="cp-actions">
                <span class="cp-act">&#10084;&#65039; {{ $ghost[1] }}</span>
                <span class="cp-act">&#128172; {{ $ghost[2] }}</span>
            </div>
        </div>
        @endforeach
        @endforelse
    </div>
    @guest
    <div class="cp-blur-overlay"></div>
    <div class="cp-gate">
        <div class="cp-gate-cta">
            <span class="cp-gate-label">Masuk untuk ikut berdiskusi</span>
            <a href="{{ route('google.login') }}" class="btn-primary" onclick="gtag && gtag('event','cta_click',{event_category:'engagement',button:'community_preview'})">Gabung — baca semua post</a>
        </div>
    </div>
    @endguest
</div>

<hr class="divider">

{{-- MUSISI SHOWCASE (teaser publik; detail wajib login) — selalu tampil sbg mesin konversi --}}
@php $musicians = $musicians ?? collect(); @endphp
<style>
    .ms-land { text-align:center; }
    .ms-land-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px; max-width:760px; margin:1.5rem auto 0; }
    .ms-land-card { background:var(--card-bg); border:1px solid var(--border); border-radius:16px; padding:1rem .75rem; cursor:pointer; text-align:center; transition:transform .15s ease, border-color .2s, box-shadow .25s; font-family:inherit; color:inherit; transform-style:preserve-3d; will-change:transform; }
    .ms-land-card:hover { border-color:var(--accent); box-shadow:0 18px 36px -16px var(--accent); }
    .ms-land-av { width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--border); }
    .ms-land-name { font-weight:600; font-size:14px; color:var(--text); margin-top:.6rem; }
    .ms-land-role { font-size:12px; color:var(--accent); margin-top:2px; }
    .ms-land-loc { font-size:11px; color:var(--text-3); margin-top:2px; }
    .ms-land-ov { display:none; position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,.7); align-items:center; justify-content:center; padding:1rem; }
    .ms-land-ov.open { display:flex; }
    .ms-land-modal { background:var(--bg-2); border:1px solid var(--border); border-radius:20px; padding:1.6rem 1.4rem; max-width:380px; width:100%; text-align:center; position:relative; }
    .ms-land-x { position:absolute; top:12px; right:14px; background:var(--card-bg); border:1px solid var(--border); border-radius:8px; width:28px; height:28px; color:var(--text-3); cursor:pointer; }
    .ms-land-modal-av { width:84px; height:84px; border-radius:50%; object-fit:cover; border:3px solid var(--border); }
    .ms-land-modal-name { font-family:'Space Grotesk','Inter',sans-serif; font-size:1.2rem; font-weight:600; color:var(--text); margin-top:.6rem; }
    .ms-land-modal-tags { display:flex; flex-wrap:wrap; gap:6px; justify-content:center; margin:.75rem 0; }
    .ms-land-modal-tags span { font-size:11px; padding:3px 10px; border-radius:20px; background:var(--accent-dim); color:var(--accent); }
    .ms-land-modal-bio { font-size:13px; color:var(--text-2); line-height:1.6; }
    .ms-land-join { display:flex; flex-direction:column; align-items:center; justify-content:center; background:linear-gradient(150deg,var(--accent),#F07040); border:none; color:#fff; text-decoration:none; min-height:140px; }
    .ms-land-join:hover { box-shadow:0 18px 36px -16px var(--accent); border-color:transparent; }
    .ms-join-plus { font-size:28px; line-height:1; font-weight:300; width:46px; height:46px; border-radius:50%; border:2px dashed rgba(255,255,255,.75); display:flex; align-items:center; justify-content:center; }
    .ms-join-t { font-weight:700; font-size:13.5px; margin-top:.55rem; }
    .ms-join-s { font-size:11px; opacity:.92; margin-top:2px; }
    .ms-land-pitch { max-width:520px; margin:.6rem auto 0; color:var(--text-2); font-size:14px; line-height:1.6; }
    .ms-land { position:relative; }
    .ms-land::before { content:''; position:absolute; top:4%; left:50%; transform:translateX(-50%); width:min(640px,92%); height:280px; background:radial-gradient(ellipse at center, rgba(56,168,204,.20), transparent 70%); filter:blur(38px); z-index:0; pointer-events:none; }
    .ms-land > * { position:relative; z-index:1; }
    .ms-land-perks { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; margin:1.1rem auto 0; max-width:580px; }
    .ms-land-perk { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:var(--text-2); background:var(--card-bg); border:1px solid var(--border); border-radius:20px; padding:6px 13px; }
    .ms-land-perk b { color:var(--accent); font-weight:700; }
    @keyframes msJoinPulse { 0%{ box-shadow:0 0 0 0 rgba(56,168,204,.5); } 70%{ box-shadow:0 0 0 12px rgba(56,168,204,0); } 100%{ box-shadow:0 0 0 0 rgba(56,168,204,0); } }
    .ms-land-join { animation:msJoinPulse 2.6s ease-out infinite; }
    .ms-join-plus { transition:transform .3s ease; }
    .ms-land-join:hover .ms-join-plus { transform:rotate(90deg) scale(1.12); }
    @media (prefers-reduced-motion: reduce) { .ms-land-join { animation:none; } }
</style>
<div class="section ms-land">
    <p class="section-eyebrow">Dari kamarmu ke panggung</p>
    <p class="section-heading">Komunitas musisi yang sedang tumbuh</p>
    <p class="ms-land-pitch">Masih tahap awal &mdash; jadilah salah satu musisi pertama. Buat <b>profil portofolio</b> (kartu + QR), lalu <b>temukan personil &amp; gig</b> lewat matchmaking otomatis.</p>
    <div class="ms-land-perks">
        <span class="ms-land-perk">🎴 Kartu portofolio <b>+ QR</b></span>
        <span class="ms-land-perk">🤝 Matchmaking <b>personil &amp; gig</b></span>
        <span class="ms-land-perk">☕ <b>Tip Jar</b> dukungan</span>
    </div>
    <div class="ms-land-grid">
        @foreach($musicians as $m)
        <button type="button" class="ms-land-card" data-m='@json($m, JSON_HEX_APOS|JSON_HEX_QUOT)' onclick="openMsLand(this)">
            <img src="{{ $m['avatar'] }}" class="ms-land-av" loading="lazy" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'">
            <div class="ms-land-name">{{ $m['name'] }}</div>
            <div class="ms-land-role">{{ implode(' · ', array_slice($m['roles'], 0, 2)) ?: 'Musisi' }}</div>
            @if($m['location'])<div class="ms-land-loc">📍 {{ $m['location'] }}</div>@endif
        </button>
        @endforeach
        <a href="{{ route('google.login') }}" class="ms-land-card ms-land-join"
           @guest onclick="gtag && gtag('event','cta_click',{event_category:'engagement',button:'jadi_musisi_card'})" @endguest>
            <div class="ms-join-plus">+</div>
            <div class="ms-join-t">Jadi musisi di sini</div>
            <div class="ms-join-s">Gratis &middot; 2 menit</div>
        </a>
    </div>
    <a href="{{ route('google.login') }}" class="btn-primary" style="text-decoration:none;display:inline-block;margin-top:1.4rem;"
       @guest onclick="gtag && gtag('event','cta_click',{event_category:'engagement',button:'mulai_jadi_musisi'})" @endguest
    >🎸 Mulai &mdash; buat profil musisimu</a>
</div>

<div class="ms-land-ov" id="msLandOv" onclick="if(event.target===this)closeMsLand()">
    <div class="ms-land-modal">
        <button class="ms-land-x" onclick="closeMsLand()">&#10005;</button>
        <img id="msLandAv" src="" class="ms-land-modal-av" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'">
        <div id="msLandName" class="ms-land-modal-name"></div>
        <div id="msLandTags" class="ms-land-modal-tags"></div>
        <div id="msLandBio" class="ms-land-modal-bio"></div>
        <a href="{{ route('google.login') }}" class="btn-primary" style="text-decoration:none;display:block;text-align:center;margin-top:1.1rem;">&#128274; Masuk untuk lihat profil lengkap &amp; hubungi</a>
        <p style="font-size:11px;color:var(--text-3);margin-top:8px;">Portofolio, kontak &amp; dukungan hanya untuk member.</p>
    </div>
</div>
<script>
function openMsLand(btn){
    var m;
    try { m = JSON.parse(btn.getAttribute('data-m')); } catch(e){ return; }
    if(!m) return;
    document.getElementById('msLandAv').src = m.avatar || '';
    document.getElementById('msLandName').textContent = m.name || 'Musisi';
    var tags = [];
    if (m.skill) tags.push(m.skill);
    (m.roles || []).slice(0,4).forEach(function(r){ tags.push(r); });
    (m.genres || []).slice(0,3).forEach(function(g){ tags.push(g); });
    document.getElementById('msLandTags').innerHTML = tags.map(function(t){
        var d=document.createElement('div'); d.textContent=String(t); return '<span>'+d.innerHTML+'</span>';
    }).join('');
    document.getElementById('msLandBio').textContent = m.bio || '';
    document.getElementById('msLandOv').classList.add('open');
}
function closeMsLand(){ document.getElementById('msLandOv').classList.remove('open'); }

/* Efek 3D tilt: kartu "menghadap" kursor (mouse) + ikut gerak HP (giroskop) */
(function(){
    var cards = document.querySelectorAll('.ms-land-card');
    if (!cards.length) return;

    function tilt(card, rx, ry, lift){
        card.style.transform = 'perspective(620px) rotateX(' + rx.toFixed(2) + 'deg) rotateY(' + ry.toFixed(2) + 'deg)' + (lift ? ' translateY(-5px) scale(1.04)' : '');
    }

    // Desktop: ikut kursor · HP: ikut jari (touch-drag)
    cards.forEach(function(card){
        card.addEventListener('mousemove', function(e){
            var r = card.getBoundingClientRect();
            tilt(card, (0.5 - (e.clientY - r.top) / r.height) * 18, ((e.clientX - r.left) / r.width - 0.5) * 18, true);
        });
        card.addEventListener('mouseleave', function(){ card.style.transform = ''; });
        card.addEventListener('touchstart', function(e){
            var t = e.touches[0]; if(!t) return;
            var r = card.getBoundingClientRect();
            tilt(card, (0.5 - (t.clientY - r.top) / r.height) * 16, ((t.clientX - r.left) / r.width - 0.5) * 16, true);
        }, {passive:true});
        card.addEventListener('touchmove', function(e){
            var t = e.touches[0]; if(!t) return;
            var r = card.getBoundingClientRect();
            tilt(card, (0.5 - (t.clientY - r.top) / r.height) * 16, ((t.clientX - r.left) / r.width - 0.5) * 16, true);
        }, {passive:true});
        card.addEventListener('touchend', function(){ card.style.transform = ''; });
        card.addEventListener('touchcancel', function(){ card.style.transform = ''; });
    });

    // HP: ikut kemiringan perangkat (giroskop)
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', function(e){
            if (e.gamma == null && e.beta == null) return;
            var ry = Math.max(-14, Math.min(14, (e.gamma || 0) * 0.5));  // kiri-kanan
            var rx = Math.max(-14, Math.min(14, ((e.beta || 0) - 45) * 0.4)); // depan-belakang
            cards.forEach(function(card){ tilt(card, rx, ry, false); });
        }, true);
    }
})();
</script>

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

    // catat pemutaran (fire-and-forget)
    try {
        var _m = document.querySelector('meta[name="csrf-token"]');
        if (song.id) fetch('/lagu/' + song.id + '/play', {
            method: 'POST', keepalive: true,
            headers: { 'X-CSRF-TOKEN': _m ? _m.content : '' }
        });
    } catch (e) {}

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

/* ===== AURORA STUDIO: scroll-reveal ===== */
(function(){
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce || !('IntersectionObserver' in window)) return;
    var els = document.querySelectorAll('main .section, .fb-ticker');
    if (!els.length) return;
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    els.forEach(function(el){ el.classList.add('reveal'); io.observe(el); });
})();
</script>

{{-- GA4 Events: scroll depth + song interactions --}}
@if(config('services.google_analytics_id'))
<script>
(function(){
    if (typeof gtag !== 'function') return;

    // Scroll depth (25 / 50 / 75 / 90%)
    var reached = {};
    window.addEventListener('scroll', function(){
        var pct = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
        [25, 50, 75, 90].forEach(function(t){
            if (pct >= t && !reached[t]) {
                reached[t] = true;
                gtag('event', 'scroll', {event_category:'engagement', event_label:'scroll_' + t, value: t});
            }
        });
    }, { passive: true });

    // Section visibility via IntersectionObserver
    var sections = {
        'featuredSection': 'section_lagu',
        'fbPromoSection':  'section_fanbase_cta',
    };
    if ('IntersectionObserver' in window) {
        Object.keys(sections).forEach(function(id){
            var el = document.getElementById(id);
            if (!el) return;
            new IntersectionObserver(function(entries){
                if (entries[0].isIntersecting) {
                    gtag('event', 'section_view', {event_category:'engagement', event_label: sections[id]});
                    this.disconnect();
                }
            }, { threshold: 0.3 }).observe(el);
        });
    }
})();
</script>
@endif

<script>
/* ====== Feature Showcase ====== */
(function(){
    var featTotal = 6, featCurrent = 0, featTimer = null;

    window.switchFeat = function(idx) {
        var screens = document.querySelectorAll('.feat-screen');
        var tabs    = document.querySelectorAll('.feat-tab');
        var dots    = document.querySelectorAll('.feat-dot');
        var descs   = document.querySelectorAll('.feat-desc-item');
        if (!screens.length) return;

        var prev = featCurrent;
        featCurrent = ((idx % featTotal) + featTotal) % featTotal;

        screens[prev].classList.remove('active');
        screens[prev].classList.add('exit');
        var exitEl = screens[prev];
        setTimeout(function(){ if(exitEl) exitEl.classList.remove('exit'); }, 420);

        screens[featCurrent].classList.add('active');
        tabs.forEach(function(t,i){ t.classList.toggle('active', i === featCurrent); });
        dots.forEach(function(d,i){ d.classList.toggle('active', i === featCurrent); });
        descs.forEach(function(d,i){ d.classList.toggle('active', i === featCurrent); });

        var activeTab = tabs[featCurrent];
        if (activeTab) {
            var tc = document.getElementById('featTabs');
            if (tc) tc.scrollTo({ left: activeTab.offsetLeft - tc.offsetWidth / 2 + activeTab.offsetWidth / 2, behavior: 'smooth' });
        }

        featResetTimer();
    };

    function featResetTimer() {
        clearInterval(featTimer);
        featTimer = setInterval(function(){ switchFeat(featCurrent + 1); }, 4000);
    }

    document.addEventListener('DOMContentLoaded', function(){
        var showcase = document.querySelector('.feat-showcase');
        if (!showcase) return;
        showcase.addEventListener('mouseenter', function(){ clearInterval(featTimer); });
        showcase.addEventListener('mouseleave', featResetTimer);
        showcase.addEventListener('touchstart', function(){ clearInterval(featTimer); }, { passive:true });
        showcase.addEventListener('touchend', function(){ setTimeout(featResetTimer, 2000); }, { passive:true });
        featResetTimer();
    });
})();
</script>
@endpush

