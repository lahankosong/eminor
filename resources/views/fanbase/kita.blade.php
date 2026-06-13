@extends('layouts.fanbase')
@section('title', 'Kita')

@push('styles')
<style>
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
    }
    .kita-post-date { font-size: 11px; color: var(--text-4); margin-top: 2px; }
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
</style>
@endpush

@section('content')

<div class="kita-page-header">
    <div class="kita-page-title">&#128101; Kita</div>
    <div class="kita-page-sub">Ceritakan apapun — semuanya mendengar</div>
</div>

{{-- POST FORM --}}
<div class="kita-form">
    <div class="kita-form-header">
        <img src="{{ Auth::user()->avatar }}" class="kita-form-avatar" alt="">
        <span class="kita-form-name">{{ Auth::user()->name }}</span>
    </div>
    <form method="POST" action="{{ route('kita.store') }}" id="kitaForm">
        @csrf
        <textarea name="body" class="kita-textarea" id="kitaBody"
            placeholder="Apa yang ada di pikiranmu sekarang?"
            maxlength="500" oninput="kitaCharCount(this)" required></textarea>
        <input type="text" name="location" class="kita-location-input"
               id="kitaLocation" placeholder="&#128205; Lokasi kamu...">
        <div class="kita-form-footer">
            <div class="kita-form-left">
                <button type="button" class="kita-loc-btn" onclick="kitaToggleLocation()">
                    &#128205; Lokasi
                </button>
                <span class="kita-char-count" id="kitaCharCount">0 / 500</span>
            </div>
            <button type="submit" class="btn-post-kita">&#128640; Posting</button>
        </div>
    </form>
</div>

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
                <strong>{{ $log->user->name ?? 'Member' }}</strong> baru saja bergabung di fanbase Rakhman Andi &#127881;
            </div>
            <div class="member-log-date">{{ $log->created_at->diffForHumans() }}</div>
        </div>
        <span class="member-log-badge">&#127381; Member Baru</span>
    </div>
    @endforeach

    <div class="kita-post" id="kitaPost{{ $post->id }}">

        <div class="kita-post-header">
            <img src="{{ $post->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                 class="kita-post-avatar" alt="">
            <div class="kita-post-meta">
                <div class="kita-post-name">{{ $post->user->name }}</div>
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
                            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}"
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
                <img src="{{ Auth::user()->avatar }}" class="kita-comment-avatar" alt="">
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
                <strong>{{ $log->user->name ?? 'Member' }}</strong> baru saja bergabung di fanbase Rakhman Andi &#127881;
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

@endsection

@push('scripts')
<script>
var BASE_URL  = '{{ url("") }}';
var csrfToken = '{{ csrf_token() }}';

/* CHAR COUNT */
function kitaCharCount(el) {
    var c = el.value.length;
    var cnt = document.getElementById('kitaCharCount');
    if(cnt){ cnt.textContent=c+' / 500'; cnt.classList.toggle('warn',c>400); }
}

/* LOCATION */
function kitaToggleLocation() {
    var el = document.getElementById('kitaLocation');
    if(!el) return;
    el.classList.toggle('show');
    if (el.classList.contains('show') && !el.value) {
        if (navigator.geolocation) {
            el.placeholder = 'Mendeteksi lokasi...';
            navigator.geolocation.getCurrentPosition(function(pos) {
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='
                    + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=10', {
                    headers: { 'Accept-Language': 'id' }
                })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    var a = d.address || {};
                    var city = a.city || a.town || a.village || a.county || a.municipality || '';
                    el.value = city;
                    el.placeholder = '📍 Lokasi kamu...';
                })
                .catch(function(){ el.placeholder = 'Ketik lokasi manual...'; });
            }, function(){
                el.placeholder = 'Ketik lokasi manual...';
            }, { timeout: 8000, maximumAge: 300000 });
        } else {
            el.placeholder = 'Ketik lokasi manual...';
        }
    }
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
            + '<img src="'+escHtml(d.comment.avatar)+'" class="kita-comment-avatar" style="width:22px;height:22px;" onerror="this.src=\''+d.comment.avatar+'\'" alt="">'
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
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'}
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d.success){
            var el=document.getElementById('kitaPost'+id);
            if(el) el.remove();
        }
    });
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