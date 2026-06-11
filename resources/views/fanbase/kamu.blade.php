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
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; padding: 1.5rem 1rem;
        box-shadow: var(--shadow); text-align: center;
    }
    .tuner-gauge-wrap {
        position: relative; width: 220px; height: 125px;
        margin: 0 auto 0.75rem;
    }
    .tuner-note-big {
        font-family: 'Sora', sans-serif;
        font-size: 3rem; font-weight: 700; color: var(--sky-dk);
        line-height: 1; margin-bottom: 2px; letter-spacing: -1px;
        transition: color 0.2s;
    }
    .tuner-note-big.in-tune { color: #16a34a; }
    .tuner-note-big.too-low { color: var(--orange-dk); }
    .tuner-note-big.too-high { color: #dc2626; }
    .tuner-freq-lbl {
        font-size: 11px; color: var(--text-3); margin-bottom: 0.5rem;
        font-variant-numeric: tabular-nums;
    }
    .tuner-status {
        font-size: 13px; font-weight: 600; height: 20px; margin-bottom: 0.875rem;
    }
    .tuner-status.in-tune  { color: #16a34a; }
    .tuner-status.too-low  { color: var(--orange-dk); }
    .tuner-status.too-high { color: #dc2626; }
    .tuner-strings {
        display: flex; gap: 6px; justify-content: center; margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .tuner-str {
        width: 44px; height: 44px; border-radius: 50%;
        border: 2px solid var(--border); background: var(--surface);
        font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 700;
        color: var(--text-2); cursor: pointer; transition: 0.18s;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        line-height: 1.2;
    }
    .tuner-str:hover { border-color: var(--sky-mid); color: var(--sky-dk); }
    .tuner-str.active {
        border-color: var(--sky-dk); background: var(--sky-lt); color: var(--sky-dk);
        box-shadow: 0 0 0 3px var(--sky-glow);
    }
    .tuner-str.in-tune { border-color: #16a34a; background: #f0fdf4; color: #16a34a; }
    .tuner-btn {
        padding: 10px 28px; border-radius: 24px; border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 600;
        background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color: #fff; box-shadow: 0 4px 14px var(--sky-glow); transition: 0.2s;
    }
    .tuner-btn:hover { transform: translateY(-1px); }
    .tuner-btn.stop {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dk) 100%);
        box-shadow: 0 4px 14px var(--orange-glow);
    }
    .tuner-msg { font-size: 11px; color: var(--text-4); margin-top: 7px; min-height: 16px; }
    .tuner-tip {
        margin-top: 1rem; padding: 0.75rem; border-radius: 12px;
        background: var(--sky-lt); border: 1px solid var(--border-lt);
        font-size: 11px; color: var(--text-3); line-height: 1.7; text-align: left;
    }

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
    <p style="font-family:'Sora',sans-serif;font-size:13px;font-weight:600;color:var(--text-1);margin-bottom:1rem;">
        &#127928; Tuning Gitar Standar (EADGBE)
    </p>

    {{-- Gauge SVG --}}
    <div class="tuner-gauge-wrap">
        <svg id="tunerSvg" viewBox="0 0 220 125" style="overflow:visible;">
            {{-- Arc background --}}
            <path d="M 20 112 A 90 90 0 0 1 200 112"
                  stroke="#EBF5F9" stroke-width="16" fill="none" stroke-linecap="round"/>
            {{-- Low (left, red) --}}
            <path d="M 20 112 A 90 90 0 0 1 73 30"
                  stroke="#FEE2E2" stroke-width="16" fill="none" stroke-linecap="round" opacity="0.8"/>
            {{-- In-tune (center, green) --}}
            <path d="M 79 24 A 90 90 0 0 1 141 24"
                  stroke="#DCFCE7" stroke-width="16" fill="none" stroke-linecap="round" opacity="0.9"/>
            {{-- High (right, red) --}}
            <path d="M 147 30 A 90 90 0 0 1 200 112"
                  stroke="#FEE2E2" stroke-width="16" fill="none" stroke-linecap="round" opacity="0.8"/>
            {{-- Center tick --}}
            <line x1="110" y1="20" x2="110" y2="32" stroke="#22c55e" stroke-width="2.5"/>
            {{-- Needle --}}
            <line id="tunerNeedle" x1="110" y1="112" x2="110" y2="28"
                  stroke="#38A8CC" stroke-width="3.5" stroke-linecap="round"
                  style="transform-origin:110px 112px; transition:transform 0.12s ease;"/>
            {{-- Center pivot --}}
            <circle cx="110" cy="112" r="6" fill="var(--sky)"/>
            <circle cx="110" cy="112" r="3" fill="#fff"/>
            {{-- Labels --}}
            <text x="10"  y="122" font-size="9" fill="#94A3B8" text-anchor="middle">-50</text>
            <text x="110" y="13"  font-size="9" fill="#16a34a" text-anchor="middle" font-weight="600">0</text>
            <text x="210" y="122" font-size="9" fill="#94A3B8" text-anchor="middle">+50</text>
        </svg>
    </div>

    <div class="tuner-note-big" id="tunerNote">—</div>
    <div class="tuner-freq-lbl" id="tunerFreq">— Hz</div>
    <div class="tuner-status" id="tunerStatus">&nbsp;</div>

    {{-- String buttons --}}
    <div class="tuner-strings">
        <button class="tuner-str" data-freq="82.41"  data-label="E₂" onclick="tunerPickStr(this)">E<sub>2</sub></button>
        <button class="tuner-str" data-freq="110.00" data-label="A₂" onclick="tunerPickStr(this)">A<sub>2</sub></button>
        <button class="tuner-str" data-freq="146.83" data-label="D₃" onclick="tunerPickStr(this)">D<sub>3</sub></button>
        <button class="tuner-str" data-freq="196.00" data-label="G₃" onclick="tunerPickStr(this)">G<sub>3</sub></button>
        <button class="tuner-str" data-freq="246.94" data-label="B₃" onclick="tunerPickStr(this)">B<sub>3</sub></button>
        <button class="tuner-str" data-freq="329.63" data-label="e⁴" onclick="tunerPickStr(this)">e<sub>4</sub></button>
    </div>

    <button class="tuner-btn" id="tunerBtn" onclick="tunerToggle()">&#9654; Mulai Tuning</button>
    <div class="tuner-msg" id="tunerMsg">Izin mikrofon diperlukan</div>

    <div class="tuner-tip">
        <strong>Cara pakai:</strong><br>
        1. Pilih senar yang ingin di-tune (E₂–e⁴)<br>
        2. Tekan Mulai Tuning → izinkan mikrofon<br>
        3. Petik senarnya — jarum menunjuk <span style="color:#16a34a;font-weight:600;">tengah (0)</span> = tepat<br>
        4. Jarum ke kiri = nada rendah, ke kanan = terlalu tinggi
    </div>
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
var tunerWasInTune = false; // cegah suara ding berulang

var TUNER_STRINGS = [
    { freq: 82.41,  label: 'E₂' },
    { freq: 110.00, label: 'A₂' },
    { freq: 146.83, label: 'D₃' },
    { freq: 196.00, label: 'G₃' },
    { freq: 246.94, label: 'B₃' },
    { freq: 329.63, label: 'e⁴' },
];

function tunerPickStr(btn) {
    document.querySelectorAll('.tuner-str').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    tunerSelectedFreq = parseFloat(btn.getAttribute('data-freq'));
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
        tunerRunning = true;
        tunerSmooth = 0;
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
    if (tunerRaf) cancelAnimationFrame(tunerRaf);
    if (tunerStream) { tunerStream.getTracks().forEach(function(t){ t.stop(); }); tunerStream = null; }
    if (tunerCtx) { tunerCtx.close(); tunerCtx = null; }
    var btn = document.getElementById('tunerBtn');
    btn.innerHTML = '&#9654; Mulai Tuning';
    btn.classList.remove('stop');
    document.getElementById('tunerMsg').textContent = 'Izin mikrofon diperlukan';
    tunerRenderUI(null);
}

var tunerLastRender = 0;
function tunerLoop(ts) {
    if (!tunerRunning) return;
    // Jalankan analisis tiap ~60ms (bukan tiap frame) agar tidak terlalu sering
    if (ts - tunerLastRender >= 60) {
        tunerLastRender = ts;
        tunerAnalyser.getFloatTimeDomainData(tunerBuf);
        var freq = tunerAutoCorr(tunerBuf, tunerCtx.sampleRate);
        if (freq > 55 && freq < 1500) {
            // Smoothing lebih ringan: 55% lama + 45% baru → lebih responsif
            tunerSmooth = tunerSmooth === 0 ? freq : tunerSmooth * 0.55 + freq * 0.45;
            tunerRenderUI(tunerSmooth);
        } else if (tunerSmooth > 0) {
            // Sinyal hilang: fade perlahan
            tunerSmooth *= 0.7;
            if (tunerSmooth < 60) { tunerSmooth = 0; tunerRenderUI(null); }
            else tunerRenderUI(tunerSmooth);
        }
    }
    tunerRaf = requestAnimationFrame(tunerLoop);
}

function tunerAutoCorr(buf, sr) {
    var SIZE = buf.length, HALF = Math.floor(SIZE / 2);
    var rms = 0;
    for (var i = 0; i < SIZE; i++) rms += buf[i] * buf[i];
    if (Math.sqrt(rms / SIZE) < 0.008) return -1;

    var best_off = -1, best_corr = 0, last_corr = 1, found = false;
    for (var off = 1; off < HALF; off++) {
        var corr = 0;
        for (var i = 0; i < HALF; i++) corr += Math.abs(buf[i] - buf[i + off]);
        corr = 1 - corr / HALF;
        if (corr > 0.9 && corr > last_corr) {
            found = true;
            if (corr > best_corr) { best_corr = corr; best_off = off; }
        } else if (found) { break; }
        last_corr = corr;
    }
    return best_off === -1 ? -1 : sr / best_off;
}

function tunerRenderUI(freq) {
    var noteEl   = document.getElementById('tunerNote');
    var freqEl   = document.getElementById('tunerFreq');
    var statusEl = document.getElementById('tunerStatus');
    var needle   = document.getElementById('tunerNeedle');

    if (!freq) {
        noteEl.textContent = '—'; noteEl.className = 'tuner-note-big';
        freqEl.textContent = '— Hz';
        statusEl.textContent = ' '; statusEl.className = 'tuner-status';
        if (needle) needle.style.transform = 'rotate(0deg)';
        return;
    }

    // Cari senar terdekat (atau senar yang dipilih user)
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

    var cents = Math.round(1200 * Math.log2(freq / target.freq));
    var deg   = Math.max(-90, Math.min(90, cents / 50 * 90));

    noteEl.textContent = target.label;
    freqEl.textContent = freq.toFixed(1) + ' Hz';

    if (needle) needle.style.transform = 'rotate(' + deg + 'deg)';

    // Auto-highlight string button
    if (tunerSelectedFreq === 0) {
        document.querySelectorAll('.tuner-str').forEach(function(b){
            b.classList.toggle('active', parseFloat(b.getAttribute('data-freq')) === target.freq);
        });
    }

    var absC = Math.abs(cents);
    if (absC <= 5) {
        noteEl.className   = 'tuner-note-big in-tune';
        statusEl.innerHTML = '&#10003; Tepat!';
        statusEl.className = 'tuner-status in-tune';
        needle.setAttribute('stroke', '#22c55e');
        if (!tunerWasInTune) { tunerWasInTune = true; fbSoundTunerInTune(); }
    } else if (cents < 0) {
        tunerWasInTune = false;
        noteEl.className   = 'tuner-note-big too-low';
        statusEl.textContent = '▼ Rendah ' + cents + ' cent';
        statusEl.className   = 'tuner-status too-low';
        needle.setAttribute('stroke', '#F07040');
    } else {
        tunerWasInTune = false;
        noteEl.className   = 'tuner-note-big too-high';
        statusEl.textContent = '▲ Tinggi +' + cents + ' cent';
        statusEl.className   = 'tuner-status too-high';
        needle.setAttribute('stroke', '#dc2626');
    }
}
</script>
@endpush