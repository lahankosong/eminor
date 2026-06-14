@extends('layouts.fanbase')
@section('title', 'Kamu')

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

    /* TABS */
    .kamu-tabs {
        display: flex; gap: 4px; margin-bottom: 1.25rem;
        background: var(--card); border-radius: 14px;
        padding: 4px; border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }
    .kamu-tab {
        flex: 1; padding: 8px 12px; border-radius: 10px;
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
        width: 18%; aspect-ratio: 1/1;
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
</style>
@endpush

@section('content')

{{-- PROFILE HERO --}}
<div class="kamu-hero">
    <div class="kamu-avatar-wrap">
        <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
             class="kamu-avatar" alt="{{ $user->name }}">
        <div class="kamu-avatar-badge">&#10022;</div>
    </div>
    <div class="kamu-name">{{ $user->name }}</div>
    <div class="kamu-email">{{ $user->email }}</div>
    <div class="kamu-stats">
        <div class="kamu-stat">
            <div class="kamu-stat-num">{{ $totalPosts }}</div>
            <div class="kamu-stat-label">Postingan</div>
        </div>
        <div class="kamu-stat">
            <div class="kamu-stat-num">{{ $totalLikes }}</div>
            <div class="kamu-stat-label">Like</div>
        </div>
        <div class="kamu-stat">
            <div class="kamu-stat-num">{{ $notes->count() }}</div>
            <div class="kamu-stat-label">Catatan</div>
        </div>
        <div class="kamu-stat">
            <div class="kamu-stat-num">{{ $user->created_at?->format('Y') ?? date('Y') }}</div>
            <div class="kamu-stat-label">Bergabung</div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="kamu-tabs">
    <button class="kamu-tab active" onclick="kamuTab('Notes', this)">
        &#128196; Catatan
    </button>
    <button class="kamu-tab" onclick="kamuTab('Posts', this)">
        &#128172; Postingan
    </button>
    <button class="kamu-tab" onclick="kamuTab('Tuner', this)">
        &#127928; Tuner
    </button>
</div>

{{-- TAB: NOTES --}}
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

{{-- TAB: POSTS --}}
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

{{-- TAB: TUNER --}}
<div class="kamu-tab-content" id="kamuTabTuner">
<div class="tuner-card">

    <div class="tuner-label">Tuner Gitar &mdash; Standar EADGBE</div>
    <div class="tuner-steps">
        <b>1.</b> Tekan Mulai &nbsp;·&nbsp; <b>2.</b> Petik 1 senar &nbsp;·&nbsp; <b>3.</b> Putar pasak sampai jarum di tengah (hijau)
    </div>

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
            <img class="tuner-hs-img" src="{{ asset('images/tuner/headstock.png') }}" alt="Headstock gitar Margonoandi">
            {{-- Area klik transparan di tiap pasak. KIRI: E·A·D  KANAN: G·B·e --}}
            <button class="tuner-peg" style="left:10%;top:24.5%" data-freq="82.41"  data-label="E" id="pegE2" onclick="tunerPickPeg(this)"><span class="pg-txt">E</span></button>
            <button class="tuner-peg" style="left:10%;top:45.5%" data-freq="110.00" data-label="A" id="pegA"  onclick="tunerPickPeg(this)"><span class="pg-txt">A</span></button>
            <button class="tuner-peg" style="left:10%;top:66%"   data-freq="146.83" data-label="D" id="pegD"  onclick="tunerPickPeg(this)"><span class="pg-txt">D</span></button>
            <button class="tuner-peg" style="left:90%;top:24.5%" data-freq="196.00" data-label="G" id="pegG"  onclick="tunerPickPeg(this)"><span class="pg-txt">G</span></button>
            <button class="tuner-peg" style="left:90%;top:45.5%" data-freq="246.94" data-label="B" id="pegB"  onclick="tunerPickPeg(this)"><span class="pg-txt">B</span></button>
            <button class="tuner-peg" style="left:90%;top:66%"   data-freq="329.63" data-label="e" id="pegE4" onclick="tunerPickPeg(this)"><span class="pg-txt">e</span></button>
        </div>
    </div>

    <button class="tuner-btn" id="tunerBtn" onclick="tunerToggle()">&#9654; Mulai Tuning</button>
    <div class="tuner-msg" id="tunerMsg">Tip: ketuk pasak di gambar untuk kunci 1 senar</div>

</div>
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
}

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
        centsEl.textContent = '⬆ KENCANGKAN  ·  meleset ' + off;
        centsEl.className   = 'tuner-cents too-low';
        if (cursor) cursor.className = 'tuner-meter-needle too-low';
    } else {
        tunerWasInTune = false;
        document.querySelectorAll('.tuner-peg.in-tune').forEach(function(p){ p.classList.remove('in-tune'); });
        noteEl.className    = 'tuner-note-big too-high';
        centsEl.textContent = '⬇ KENDURKAN  ·  meleset ' + off;
        centsEl.className   = 'tuner-cents too-high';
        if (cursor) cursor.className = 'tuner-meter-needle too-high';
    }
}
</script>
@endpush
