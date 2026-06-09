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
        padding: 0 1rem 0.875rem; word-break: break-word;
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

{{-- FEED --}}
@if($posts->count() > 0)
    @foreach($posts as $post)
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
            <button class="kita-action-btn {{ in_array($post->id, $likedIds) ? 'liked' : '' }}"
                    id="kitaLike{{ $post->id }}"
                    onclick="kitaToggleLike({{ $post->id }})">
                <span class="like-icon">{{ in_array($post->id, $likedIds) ? '♥' : '♡' }}</span>
                <span id="kitaLikeCount{{ $post->id }}">{{ $post->likes_count }}</span>
            </button>
            <button class="kita-action-btn" onclick="kitaToggleComments({{ $post->id }})">
                <span>&#128172;</span>
                <span id="kitaCommentCount{{ $post->id }}">{{ $post->comments_count }}</span>
            </button>
        </div>

        {{-- COMMENTS --}}
        <div class="kita-comments" id="kitaComments{{ $post->id }}">
            <div id="kitaCommentsList{{ $post->id }}">
                @foreach($post->comments->take(5) as $comment)
                <div class="kita-comment-item" id="kitaComment{{ $comment->id }}">
                    <img src="{{ $comment->user->avatar ?? 'https://www.google.com/favicon.ico' }}"
                         class="kita-comment-avatar" alt="">
                    <div class="kita-comment-bubble">
                        <div class="kita-comment-header">
                            <span class="kita-comment-name">{{ $comment->user->name }}</span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span class="kita-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                @if(Auth::id() === $comment->user_id)
                                <button class="kita-comment-delete"
                                        onclick="kitaDeleteComment({{ $post->id }}, {{ $comment->id }})">&#10005;</button>
                                @endif
                            </div>
                        </div>
                        <div class="kita-comment-body">{{ $comment->body }}</div>
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
    if(el.classList.contains('show') && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos){
            el.value = pos.coords.latitude.toFixed(4)+', '+pos.coords.longitude.toFixed(4);
        }, function(){ el.placeholder='Masukkan lokasi manual...'; });
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
        var btn   = document.getElementById('kitaLike'+postId);
        var count = document.getElementById('kitaLikeCount'+postId);
        if(!btn||!count) return;
        count.textContent = d.likes_count;
        btn.classList.toggle('liked', d.liked);
        var icon = btn.querySelector('.like-icon');
        if(icon) icon.textContent = d.liked ? '♥' : '♡';
    });
}

/* COMMENTS */
function kitaToggleComments(postId) {
    var el = document.getElementById('kitaComments'+postId);
    if(el) el.classList.toggle('open');
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
</script>
@endpush