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
        font-size: 10px; letter-spacing: 0.25em; text-transform: uppercase;
        color: #2a4a5a; font-weight: 700; margin-bottom: 1rem;
    }
    /* Meter bar */
    .tuner-meter-wrap {
        position: relative; width: calc(100% - 32px); max-width: 280px;
        margin: 0 auto 0.3rem;
    }
    .tuner-meter-track {
        height: 5px; border-radius: 3px; position: relative; overflow: visible;
        background: linear-gradient(to right,
            #ef4444 0%, #fb923c 30%, #22c55e 47%, #22c55e 53%, #fb923c 70%, #ef4444 100%);
        opacity: 0.2; transition: opacity 0.3s;
    }
    .tuner-meter-track.active { opacity: 1; }
    .tuner-meter-center {
        position: absolute; top: -5px; bottom: -5px; left: 50%;
        width: 2px; background: rgba(255,255,255,0.15); transform: translateX(-50%);
    }
    .tuner-meter-cursor {
        position: absolute; top: 50%; left: 50%;
        width: 18px; height: 18px; border-radius: 50%;
        background: #fff; border: 3px solid #38A8CC;
        transform: translate(-50%, -50%);
        transition: left 0.09s ease, background 0.15s, border-color 0.15s;
        box-shadow: 0 0 10px rgba(56,168,204,0.6);
    }
    .tuner-meter-cursor.in-tune  { background: #22c55e; border-color: #22c55e; box-shadow: 0 0 16px rgba(34,197,94,0.8); }
    .tuner-meter-cursor.too-low  { background: #fb923c; border-color: #fb923c; box-shadow: 0 0 10px rgba(251,146,60,0.6); }
    .tuner-meter-cursor.too-high { background: #ef4444; border-color: #ef4444; box-shadow: 0 0 10px rgba(239,68,68,0.6); }
    .tuner-meter-labels {
        display: flex; justify-content: space-between;
        font-size: 9px; color: rgba(255,255,255,0.15);
        margin-top: 4px; padding: 0 2px;
        font-variant-numeric: tabular-nums;
    }
    /* Note */
    .tuner-note-big {
        font-family: 'Sora', sans-serif;
        font-size: 4.5rem; font-weight: 800; line-height: 1;
        color: #2a4a5a; letter-spacing: -3px;
        transition: color 0.12s; margin: 0.5rem 0 0;
    }
    .tuner-note-big.active   { color: #7EC8E3; }
    .tuner-note-big.in-tune  { color: #22c55e; }
    .tuner-note-big.too-low  { color: #fb923c; }
    .tuner-note-big.too-high { color: #ef4444; }
    .tuner-cents {
        font-size: 1rem; font-weight: 700; min-height: 24px; line-height: 24px;
        color: transparent; margin: 0 0 0.6rem;
        font-variant-numeric: tabular-nums; transition: color 0.12s;
        letter-spacing: 0.02em;
    }
    .tuner-cents.in-tune  { color: #22c55e; }
    .tuner-cents.too-low  { color: #fb923c; }
    .tuner-cents.too-high { color: #ef4444; }
    /* Headstock */
    .tuner-headstock-wrap { margin: 0 auto 0.75rem; width: 100%; max-width: 240px; }
    .tuner-peg { cursor: pointer; }
    .tuner-peg .pg-body { fill: #111e2d; stroke: rgba(56,168,204,0.3); stroke-width: 2; transition: all 0.15s; }
    .tuner-peg .pg-btn  { fill: #162030; stroke: rgba(56,168,204,0.25); stroke-width: 1.5; transition: all 0.15s; }
    .tuner-peg .pg-txt  { fill: #2e5a6a; font-size: 13px; font-weight: 800; font-family: 'Sora',sans-serif; transition: fill 0.15s; pointer-events: none; }
    .tuner-peg:hover .pg-body, .tuner-peg:hover .pg-btn { stroke: rgba(56,168,204,0.7); }
    .tuner-peg:hover .pg-txt { fill: #7EC8E3; }
    .tuner-peg.active .pg-body { fill: #1a3a50; stroke: #38A8CC; }
    .tuner-peg.active .pg-btn  { fill: #122840; stroke: #38A8CC; }
    .tuner-peg.active .pg-txt  { fill: #38A8CC; }
    .tuner-peg.in-tune .pg-body { fill: #0f2d1a; stroke: #22c55e; }
    .tuner-peg.in-tune .pg-btn  { fill: #0a2014; stroke: #22c55e; }
    .tuner-peg.in-tune .pg-txt  { fill: #22c55e; }
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
    .tuner-msg { font-size: 11px; color: #2a4a5a; margin-top: 8px; min-height: 16px; }

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

    {{-- Meter bar --}}
    <div class="tuner-meter-wrap">
        <div class="tuner-meter-track" id="tunerBarTrack">
            <div class="tuner-meter-center"></div>
            <div class="tuner-meter-cursor" id="tunerBarCursor"></div>
        </div>
        <div class="tuner-meter-labels">
            <span>♭ −50</span><span>−25</span><span>0</span><span>+25</span><span>+50 ♯</span>
        </div>
    </div>

    {{-- Note & cents --}}
    <div class="tuner-note-big" id="tunerNote">—</div>
    <div class="tuner-cents" id="tunerCents"></div>

    {{-- Headstock SVG --}}
    <div class="tuner-headstock-wrap">
    <svg viewBox="0 0 260 245" style="width:100%;max-width:240px;display:block;margin:0 auto;">
        <defs>
            <linearGradient id="wg" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%"   stop-color="#2a1205"/>
                <stop offset="35%"  stop-color="#5a2a10"/>
                <stop offset="50%"  stop-color="#7a3e1a"/>
                <stop offset="65%"  stop-color="#5a2a10"/>
                <stop offset="100%" stop-color="#2a1205"/>
            </linearGradient>
            <linearGradient id="pegL" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#2a3a4a"/>
                <stop offset="100%" stop-color="#0d1520"/>
            </linearGradient>
        </defs>

        {{-- Headstock body: classical rounded shape --}}
        <path d="M 97 240 L 95 58 Q 94 18 130 12 Q 166 18 165 58 L 163 240 Z"
              fill="url(#wg)" stroke="#4a1e08" stroke-width="1.5"/>
        {{-- Binding --}}
        <path d="M 97 240 L 95 58 Q 94 18 130 12 Q 166 18 165 58 L 163 240"
              fill="none" stroke="rgba(230,210,160,0.3)" stroke-width="2"/>
        {{-- Wood grain --}}
        <line x1="109" y1="12" x2="107" y2="240" stroke="rgba(0,0,0,0.2)"  stroke-width="1.5"/>
        <line x1="119" y1="12" x2="118" y2="240" stroke="rgba(0,0,0,0.12)" stroke-width="1"/>
        <line x1="130" y1="12" x2="130" y2="240" stroke="rgba(0,0,0,0.10)" stroke-width="1"/>
        <line x1="141" y1="12" x2="142" y2="240" stroke="rgba(0,0,0,0.12)" stroke-width="1"/>
        <line x1="151" y1="12" x2="153" y2="240" stroke="rgba(0,0,0,0.2)"  stroke-width="1.5"/>
        {{-- Nut --}}
        <rect x="91" y="232" width="78" height="11" rx="2.5" fill="#F0E4C0" stroke="#B89040" stroke-width="1"/>
        {{-- Strings --}}
        <line x1="105" y1="12" x2="105" y2="232" stroke="rgba(200,175,110,0.5)" stroke-width="1"/>
        <line x1="113" y1="12" x2="113" y2="232" stroke="rgba(200,175,110,0.45)" stroke-width="0.9"/>
        <line x1="121" y1="12" x2="121" y2="232" stroke="rgba(200,175,110,0.38)" stroke-width="0.75"/>
        <line x1="139" y1="12" x2="139" y2="232" stroke="rgba(200,175,110,0.38)" stroke-width="0.75"/>
        <line x1="147" y1="12" x2="147" y2="232" stroke="rgba(200,175,110,0.45)" stroke-width="0.9"/>
        <line x1="155" y1="12" x2="155" y2="232" stroke="rgba(200,175,110,0.5)" stroke-width="1"/>

        {{-- Peg stems LEFT --}}
        <rect x="68" y="52" width="27" height="7" rx="3.5" fill="#3a1a06"/>
        <rect x="68" y="112" width="27" height="7" rx="3.5" fill="#3a1a06"/>
        <rect x="68" y="172" width="27" height="7" rx="3.5" fill="#3a1a06"/>
        {{-- Peg stems RIGHT --}}
        <rect x="165" y="52" width="27" height="7" rx="3.5" fill="#3a1a06"/>
        <rect x="165" y="112" width="27" height="7" rx="3.5" fill="#3a1a06"/>
        <rect x="165" y="172" width="27" height="7" rx="3.5" fill="#3a1a06"/>

        {{-- LEFT pegs: D · A · E --}}
        <g class="tuner-peg" data-freq="146.83" data-label="D" onclick="tunerPickPeg(this)" id="pegD">
            <ellipse class="pg-body" cx="47" cy="55"  rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="28" cy="55"  rx="12" ry="12"/>
            <text    class="pg-txt"  x="55"  y="60"  text-anchor="middle">D</text>
        </g>
        <g class="tuner-peg" data-freq="110.00" data-label="A" onclick="tunerPickPeg(this)" id="pegA">
            <ellipse class="pg-body" cx="47" cy="115" rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="28" cy="115" rx="12" ry="12"/>
            <text    class="pg-txt"  x="55"  y="120" text-anchor="middle">A</text>
        </g>
        <g class="tuner-peg" data-freq="82.41"  data-label="E" onclick="tunerPickPeg(this)" id="pegE2">
            <ellipse class="pg-body" cx="47" cy="175" rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="28" cy="175" rx="12" ry="12"/>
            <text    class="pg-txt"  x="55"  y="180" text-anchor="middle">E</text>
        </g>

        {{-- RIGHT pegs: G · B · e --}}
        <g class="tuner-peg" data-freq="196.00" data-label="G" onclick="tunerPickPeg(this)" id="pegG">
            <ellipse class="pg-body" cx="213" cy="55"  rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="232" cy="55"  rx="12" ry="12"/>
            <text    class="pg-txt"  x="205" y="60"  text-anchor="middle">G</text>
        </g>
        <g class="tuner-peg" data-freq="246.94" data-label="B" onclick="tunerPickPeg(this)" id="pegB">
            <ellipse class="pg-body" cx="213" cy="115" rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="232" cy="115" rx="12" ry="12"/>
            <text    class="pg-txt"  x="205" y="120" text-anchor="middle">B</text>
        </g>
        <g class="tuner-peg" data-freq="329.63" data-label="e" onclick="tunerPickPeg(this)" id="pegE4">
            <ellipse class="pg-body" cx="213" cy="175" rx="21" ry="15"/>
            <ellipse class="pg-btn"  cx="232" cy="175" rx="12" ry="12"/>
            <text    class="pg-txt"  x="205" y="180" text-anchor="middle">e</text>
        </g>
    </svg>
    </div>

    <button class="tuner-btn" id="tunerBtn" onclick="tunerToggle()">&#9654; Mulai Tuning</button>
    <div class="tuner-msg" id="tunerMsg">Ketuk senar di headstock, lalu mulai</div>

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

// minFreq/maxFreq untuk adaptive filter per senar
var TUNER_STRINGS = [
    { freq: 82.41,  label: 'E₂', minFreq: 70,  maxFreq: 100 },
    { freq: 110.00, label: 'A₂', minFreq: 95,  maxFreq: 135 },
    { freq: 146.83, label: 'D₃', minFreq: 130, maxFreq: 175 },
    { freq: 196.00, label: 'G₃', minFreq: 170, maxFreq: 230 },
    { freq: 246.94, label: 'B₃', minFreq: 220, maxFreq: 295 },
    { freq: 329.63, label: 'e⁴', minFreq: 290, maxFreq: 390 },
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
        var btn = document.getElementById('tunerBtn');
        btn.innerHTML = '&#9646;&#9646; Stop';
        btn.classList.add('stop');
        document.getElementById('tunerMsg').textContent = 'Petik senar gitarmu...';
        tunerLoop();
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


var tunerLastRender = 0;
function tunerLoop(ts) {
    if (!tunerRunning) return;
    if (ts - tunerLastRender >= 80) {
        tunerLastRender = ts;
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
            if (tunerFreqHistory.length > 8) tunerFreqHistory.shift();
            // Median buang outlier, lalu low-pass smoothing
            var sorted = tunerFreqHistory.slice().sort(function(a,b){return a-b;});
            var median = sorted[Math.floor(sorted.length / 2)];
            tunerSmooth = tunerSmooth === 0 ? median : tunerSmooth * 0.72 + median * 0.28;
            if (tunerFreqHistory.length >= 4) tunerRenderUI(tunerSmooth);
        } else {
            tunerFreqHistory = [];
            if (tunerSmooth > 0) {
                tunerSmooth *= 0.45;
                if (tunerSmooth < 60) { tunerSmooth = 0; tunerRenderUI(null); }
            }
        }
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
        centsEl.textContent = '';  centsEl.className = 'tuner-cents';
        if (cursor) { cursor.style.left = '50%'; cursor.className = 'tuner-meter-cursor'; }
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
    var sign = centsRaw >= 0 ? '+' : '';
    if (absC <= 5) {
        noteEl.className    = 'tuner-note-big in-tune';
        centsEl.textContent = absC <= 1 ? '✓ Pas' : '✓ ' + sign + cents.toFixed(1) + ' cent';
        centsEl.className   = 'tuner-cents in-tune';
        if (cursor) cursor.className = 'tuner-meter-cursor in-tune';
        document.querySelectorAll('.tuner-peg').forEach(function(p){
            if (parseFloat(p.getAttribute('data-freq')) === target.freq) p.classList.add('in-tune');
        });
        if (!tunerWasInTune) { tunerWasInTune = true; if (typeof fbSoundTunerInTune === 'function') fbSoundTunerInTune(); }
    } else if (centsRaw < 0) {
        tunerWasInTune = false;
        document.querySelectorAll('.tuner-peg.in-tune').forEach(function(p){ p.classList.remove('in-tune'); });
        noteEl.className    = 'tuner-note-big too-low';
        centsEl.textContent = sign + cents.toFixed(1) + ' cent  ▼ naikkan tegangan';
        centsEl.className   = 'tuner-cents too-low';
        if (cursor) cursor.className = 'tuner-meter-cursor too-low';
    } else {
        tunerWasInTune = false;
        document.querySelectorAll('.tuner-peg.in-tune').forEach(function(p){ p.classList.remove('in-tune'); });
        noteEl.className    = 'tuner-note-big too-high';
        centsEl.textContent = sign + cents.toFixed(1) + ' cent  ▲ kendurkan tegangan';
        centsEl.className   = 'tuner-cents too-high';
        if (cursor) cursor.className = 'tuner-meter-cursor too-high';
    }
}
</script>
@endpush
