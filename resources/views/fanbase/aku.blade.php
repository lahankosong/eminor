@extends('layouts.fanbase')
@section('title', 'Aku')

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
    }
    .btn-post-aku:hover { transform: translateY(-1px); box-shadow: var(--shadow); }

    /* POST CARD */
    .aku-post {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 20px; margin-bottom: 1rem; overflow: hidden;
        box-shadow: var(--shadow-sm); transition: 0.2s;
    }
    .aku-post:hover { border-color: var(--sky-mid); box-shadow: var(--shadow); }
    .aku-post.pinned {
        border-color: var(--orange);
        box-shadow: 0 4px 16px var(--orange-glow);
    }

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
    .welcome-banner-icon {
        font-size: 32px; flex-shrink: 0; line-height: 1;
        margin-top: 2px;
    }
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
</style>
@endpush

@section('content')

<div class="aku-page-header">
    <div>
        <div class="aku-page-title">&#127968; Aku</div>
        <div class="aku-page-sub">Catatan &amp; cerita dari Rakhman Andi</div>
    </div>
    @if(Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS',''))))
    <div class="admin-badge">&#9733; Admin</div>
    @endif
</div>

{{-- WELCOME BANNER (member baru, max 7 hari) --}}
@if(Auth::check() && ($isNewMember ?? false))
<div class="welcome-banner" id="welcomeBanner">
    <div class="welcome-banner-icon">&#127881;</div>
    <div class="welcome-banner-body">
        <div class="welcome-banner-title">Selamat datang, {{ Auth::user()->name }}! &#10024;</div>
        <div class="welcome-banner-sub">
            Halo, member baru! Senang kamu sudah bergabung di fanbase Rakhman Andi.
            Di sini kamu bisa membaca catatan &amp; cerita, memberikan like, dan berkomentar. Selamat menikmati! &#128149;
        </div>
    </div>
    <button class="welcome-banner-close" onclick="dismissWelcome()" title="Tutup">&#10005;</button>
</div>
@endif

{{-- ADMIN FORM --}}
@if(Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS',''))))
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
                    &#128247; Foto
                    <input type="file" name="image" accept="image/*" style="display:none;">
                </label>
                <input type="text" name="mood" class="aku-mood-input" placeholder="&#128149; Mood...">
            </div>
            <button type="submit" class="btn-post-aku">&#128640; Posting</button>
        </div>
    </form>
</div>
@endif

{{-- POSTS --}}
@if($posts->count() > 0)
    @foreach($posts as $post)
    <div class="aku-post {{ $post->is_pinned ? 'pinned' : '' }}" id="akuPost{{ $post->id }}">

        <div class="aku-post-header">
            <img src="{{ $post->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                 class="aku-post-avatar" alt="">
            <div class="aku-post-meta">
                <div class="aku-post-name">
                    {{ $post->user->name }}
                    <span class="aku-admin-tag">&#9733; Admin</span>
                </div>
                <div class="aku-post-date">
                    {{ $post->created_at->format('d M Y') }} · {{ $post->created_at->format('H:i') }}
                </div>
            </div>
            <div class="aku-post-top-actions">
                @if($post->is_pinned)
                <span class="aku-pin-badge">&#128204;</span>
                @endif
                @if(Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS',''))))
                <button class="aku-top-btn aku-edit-btn" title="Edit"
                        onclick="akuEditPost({{ $post->id }})">&#9998;</button>
                <form method="POST" action="{{ route('aku.destroy', $post->id) }}"
                      onsubmit="return confirm('Hapus postingan ini?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="aku-top-btn" title="Hapus">&#10005;</button>
                </form>
                @endif
            </div>
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
        <span class="aku-post-mood">&#128149; {{ $post->mood }}</span>
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
                <span>&#128172;</span>
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
                                    @if(Auth::id() === $comment->user_id || Auth::check() && in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS',''))))
                                    <button class="aku-comment-delete"
                                            onclick="akuDeleteComment({{ $post->id }}, {{ $comment->id }})">&#10005;</button>
                                    @endif
                                </div>
                            </div>
                            <div class="aku-comment-body">{{ $comment->body }}</div>
                        </div>
                        <button class="aku-comment-reply-btn"
                                onclick="akuSetReply({{ $post->id }}, {{ $comment->id }}, '{{ addslashes($comment->user->name) }}')">
                            Balas
                        </button>
                        @if($comment->replies->count() > 0)
                        <div class="aku-replies">
                            @foreach($comment->replies as $reply)
                            <div class="aku-comment-item" id="akuComment{{ $reply->id }}" style="margin-bottom:6px;">
                                <img src="{{ $reply->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                                     class="aku-comment-avatar" style="width:24px;height:24px;" alt="">
                                <div class="aku-comment-bubble">
                                    <div class="aku-comment-header">
                                        <span class="aku-comment-name">{{ $reply->user->name }}</span>
                                        @if(Auth::id() === $reply->user_id)
                                        <button class="aku-comment-delete"
                                                onclick="akuDeleteComment({{ $post->id }}, {{ $reply->id }})">&#10005;</button>
                                        @endif
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
                <img src="{{ Auth::user()->avatar }}" class="aku-comment-avatar" alt="">
                <input type="text" class="aku-comment-input"
                       id="akuInput{{ $post->id }}"
                       placeholder="Tulis komentar..."
                       onkeydown="if(event.key==='Enter'){akuSubmitComment({{ $post->id }});return false;}">
                <input type="hidden" id="akuParent{{ $post->id }}" value="">
                <button class="aku-comment-submit" onclick="akuSubmitComment({{ $post->id }})">Kirim</button>
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
    <div style="font-size:42px;">&#9997;</div>
    <p>Belum ada postingan dari Rakhman Andi.</p>
</div>
@endif

@endsection

@push('scripts')
<script>
var BASE_URL   = '{{ url("") }}';
var csrfToken  = '{{ csrf_token() }}';

/* WELCOME BANNER */
(function(){
    var uid = '{{ Auth::id() }}';
    var key = 'welcome_dismissed_' + uid;
    var banner = document.getElementById('welcomeBanner');
    if(banner && localStorage.getItem(key)) banner.style.display = 'none';
})();
function dismissWelcome(){
    var uid = '{{ Auth::id() }}';
    var key = 'welcome_dismissed_' + uid;
    localStorage.setItem(key, '1');
    var banner = document.getElementById('welcomeBanner');
    if(banner){ banner.style.opacity='0'; banner.style.transition='opacity 0.3s'; setTimeout(function(){ banner.style.display='none'; }, 300); }
}

/* LIKE */
function akuToggleLike(postId) {
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

/* COMMENTS TOGGLE */
function akuToggleComments(postId) {
    var el = document.getElementById('akuComments'+postId);
    if(el) el.classList.toggle('open');
}

/* SET REPLY */
function akuSetReply(postId, commentId, name) {
    var input  = document.getElementById('akuInput'+postId);
    var parent = document.getElementById('akuParent'+postId);
    if(input)  { input.placeholder='Membalas '+name+'...'; input.focus(); }
    if(parent) parent.value = commentId;
}

/* SUBMIT COMMENT */
function akuSubmitComment(postId) {
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

/* DELETE COMMENT */
function akuDeleteComment(postId, commentId) {
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

/* EDIT POST */
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

function escHtml(t){
    var d=document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}
</script>
@endpush