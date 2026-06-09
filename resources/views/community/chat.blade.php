@extends('layouts.app')

@push('styles')
<style>
    /* Override main padding for chat */
    main { padding: 0 !important; max-width: 100% !important; }

    .chat-wrap {
        max-width: 900px; margin: 0 auto;
    }

    .chat-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        height: calc(100vh - 64px - 60px); /* subtract top nav + bottom nav */
        border-top: 1px solid #111;
        overflow: hidden;
    }

    /* SIDEBAR */
    .chat-sidebar {
        border-right: 1px solid #111;
        display: flex; flex-direction: column;
        overflow: hidden; background: #080808;
    }
    .chat-sidebar-header {
        padding: 0.875rem 1rem; border-bottom: 1px solid #111;
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    .chat-sidebar-title { font-size: 13px; font-weight: 500; color: #ccc; }
    .btn-new-group {
        font-size: 11px; color: #555; background: transparent;
        border: 1px solid #1a1a1a; border-radius: 6px; padding: 4px 10px;
        cursor: pointer; transition: 0.15s;
    }
    .btn-new-group:hover { color: #fff; border-color: #333; }

    .chat-search {
        padding: 8px 10px; border-bottom: 1px solid #0d0d0d; flex-shrink: 0;
    }
    .chat-search input {
        width: 100%; background: #111; border: 1px solid #1a1a1a;
        border-radius: 8px; color: #ccc; font-size: 12px;
        padding: 6px 12px; outline: none; font-family: inherit;
    }
    .chat-search input::placeholder { color: #2a2a2a; }

    .chat-list { overflow-y: auto; flex: 1; }
    .chat-section-label {
        font-size: 10px; color: #2a2a2a; letter-spacing: 0.2em;
        text-transform: uppercase; padding: 8px 12px 4px;
    }
    .chat-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 12px; cursor: pointer; transition: 0.15s;
        border-bottom: 1px solid #0a0a0a; text-decoration: none;
        width: 100%; text-align: left; border-left: none;
        background: transparent;
    }
    .chat-item:hover { background: #0d0d0d; }
    .chat-item.active { background: #111; border-left: 2px solid #fff; padding-left: 10px; }

    .chat-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: #1a1a1a; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; color: #444; overflow: hidden;
    }
    .chat-avatar img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
    .chat-info { flex: 1; min-width: 0; }
    .chat-name { font-size: 12px; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .chat-preview { font-size: 11px; color: #2a2a2a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
    .chat-time { font-size: 10px; color: #222; flex-shrink: 0; }

    /* MAIN AREA */
    .chat-main {
        display: flex; flex-direction: column; overflow: hidden;
        background: #050505;
    }
    .chat-empty {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: #2a2a2a; text-align: center; padding: 2rem;
    }
    .chat-empty p { font-size: 13px; margin-top: 0.75rem; }

    .chat-header {
        padding: 10px 1rem; border-bottom: 1px solid #111;
        display: flex; align-items: center; gap: 10px; flex-shrink: 0;
        background: #080808;
    }
    .chat-header-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: #1a1a1a; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; color: #444; overflow: hidden;
    }
    .chat-header-avatar img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; }
    .chat-header-name { font-size: 13px; font-weight: 500; color: #fff; }
    .chat-header-sub { font-size: 11px; color: #333; margin-top: 1px; }

    /* Back button mobile */
    .chat-back-btn {
        display: none; background: transparent; border: none;
        color: #555; font-size: 18px; cursor: pointer; padding: 4px 8px 4px 0;
        line-height: 1; flex-shrink: 0;
    }

    .messages-area {
        flex: 1; overflow-y: auto; padding: 1rem;
        display: flex; flex-direction: column; gap: 4px;
    }
    .msg-item { display: flex; gap: 8px; align-items: flex-end; }
    .msg-item.mine { flex-direction: row-reverse; }
    .msg-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        object-fit: cover; background: #1a1a1a; flex-shrink: 0;
    }
    .msg-bubble {
        max-width: 70%; padding: 8px 12px; border-radius: 12px;
        font-size: 13px; line-height: 1.5; word-break: break-word;
    }
    .msg-item.others .msg-bubble {
        background: #111; color: #aaa; border-radius: 4px 12px 12px 12px;
    }
    .msg-item.mine .msg-bubble {
        background: #1a1a2e; color: #c0d0f0; border-radius: 12px 4px 12px 12px;
    }
    .msg-name { font-size: 10px; color: #333; margin-bottom: 2px; padding: 0 4px; }
    .msg-time { font-size: 10px; color: #1a1a1a; margin-top: 2px; padding: 0 4px; }

    .chat-input-area {
        padding: 10px 1rem; border-top: 1px solid #111;
        display: flex; gap: 8px; align-items: flex-end;
        flex-shrink: 0; background: #080808;
    }
    .chat-input {
        flex: 1; background: #111; border: 1px solid #1a1a1a;
        border-radius: 20px; color: #ccc; font-size: 13px;
        padding: 8px 14px; outline: none; resize: none;
        font-family: inherit; transition: 0.15s;
        max-height: 100px; min-height: 38px; line-height: 1.5;
        overflow-y: auto;
    }
    .chat-input:focus { border-color: #2a2a2a; }
    .chat-input::placeholder { color: #2a2a2a; }
    .chat-send-btn {
        width: 38px; height: 38px; border-radius: 50%;
        background: #fff; color: #000; border: none;
        font-size: 15px; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .chat-send-btn:hover { background: #ddd; }

    /* MODAL */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.88); z-index: 1000;
        align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #111; border: 1px solid #1a1a1a; border-radius: 16px;
        width: 100%; max-width: 420px; padding: 1.5rem;
        max-height: 90vh; overflow-y: auto;
    }
    .modal-title { font-size: 1rem; font-weight: 500; margin-bottom: 1.25rem; }
    .modal-field { margin-bottom: 1rem; }
    .modal-label { font-size: 11px; color: #555; text-transform: uppercase; display: block; margin-bottom: 5px; }
    .modal-input {
        width: 100%; background: #0d0d0d; border: 1px solid #2a2a2a;
        border-radius: 8px; color: #fff; font-size: 13px;
        padding: 9px 12px; outline: none; font-family: inherit;
    }
    .modal-input:focus { border-color: #555; }
    .members-list { max-height: 180px; overflow-y: auto; }
    .member-option {
        display: flex; align-items: center; gap: 10px;
        padding: 8px; border-radius: 6px; cursor: pointer; transition: 0.15s;
    }
    .member-option:hover { background: #0d0d0d; }
    .member-option img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }
    .member-option span { font-size: 13px; color: #aaa; }
    .modal-actions { display: flex; gap: 10px; margin-top: 1.25rem; }
    .btn-modal-submit {
        padding: 9px 24px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: #fff; color: #000;
        border: none; cursor: pointer; flex: 1;
    }
    .btn-modal-cancel {
        padding: 9px 20px; border-radius: 8px; font-size: 13px;
        border: 1px solid #2a2a2a; color: #888; background: transparent; cursor: pointer;
    }

    /* MOBILE RESPONSIVE */
    @media (max-width: 768px) {
        .chat-layout {
            grid-template-columns: 1fr;
            height: calc(100vh - 64px - 60px);
        }

        /* On mobile: show sidebar OR main, not both */
        .chat-sidebar {
            display: flex;
        }
        .chat-main {
            display: none;
        }

        /* When a conversation is open, hide sidebar show main */
        .chat-layout.conv-open .chat-sidebar {
            display: none;
        }
        .chat-layout.conv-open .chat-main {
            display: flex;
        }

        .chat-back-btn { display: block; }
        .msg-bubble { max-width: 82%; }
        .chat-input-area { padding: 8px; }
    }
</style>
@endpush

@section('content')
<div class="chat-wrap">
<div class="chat-layout {{ (isset($conversation) || isset($group)) ? 'conv-open' : '' }}" id="chatLayout">

    {{-- SIDEBAR --}}
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <span class="chat-sidebar-title">&#128172; Pesan</span>
            <button class="btn-new-group" onclick="openGroupModal()">+ Grup</button>
        </div>
        <div class="chat-search">
            <input type="text" placeholder="Cari..." id="chatSearch" oninput="filterChats(this.value)">
        </div>
        <div class="chat-list" id="chatList">

            <div class="chat-section-label">Member</div>
            @foreach($users as $user)
            <form method="POST" action="{{ route('chat.start', $user->id) }}" style="display:block;">
                @csrf
                <button type="submit" class="chat-item">
                    <div class="chat-avatar">
                        <img src="{{ $user->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
                    </div>
                    <div class="chat-info">
                        <div class="chat-name">{{ $user->name }}</div>
                        <div class="chat-preview">Mulai percakapan</div>
                    </div>
                </button>
            </form>
            @endforeach

            @if($conversations->count() > 0)
            <div class="chat-section-label">Percakapan</div>
            @foreach($conversations as $conv)
            @php $other = $conv->getOtherUser(Auth::id()); @endphp
            <a href="{{ route('chat.conversation', $conv->id) }}"
               class="chat-item {{ isset($conversation) && $conversation->id === $conv->id ? 'active' : '' }}">
                <div class="chat-avatar">
                    <img src="{{ $other->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
                </div>
                <div class="chat-info">
                    <div class="chat-name">{{ $other->name }}</div>
                    <div class="chat-preview">{{ $conv->last_message ?? 'Belum ada pesan' }}</div>
                </div>
                @if($conv->last_message_at)
                <span class="chat-time">{{ $conv->last_message_at->diffForHumans(null, true) }}</span>
                @endif
            </a>
            @endforeach
            @endif

            @if($groups->count() > 0)
            <div class="chat-section-label">Grup</div>
            @foreach($groups as $grp)
            <a href="{{ route('chat.group', $grp->id) }}"
               class="chat-item {{ isset($group) && $group->id === $grp->id ? 'active' : '' }}">
                <div class="chat-avatar">&#128101;</div>
                <div class="chat-info">
                    <div class="chat-name">{{ $grp->name }}</div>
                    <div class="chat-preview">{{ $grp->last_message ?? 'Belum ada pesan' }}</div>
                </div>
                @if($grp->last_message_at)
                <span class="chat-time">{{ $grp->last_message_at->diffForHumans(null, true) }}</span>
                @endif
            </a>
            @endforeach
            @endif

        </div>
    </div>

    {{-- MAIN --}}
    <div class="chat-main">

        @if(isset($conversation))
        @php $other = $conversation->getOtherUser(Auth::id()); @endphp
        <div class="chat-header">
            <button class="chat-back-btn" onclick="goBack()">&#8249;</button>
            <div class="chat-header-avatar">
                <img src="{{ $other->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
            </div>
            <div>
                <div class="chat-header-name">{{ $other->name }}</div>
                <div class="chat-header-sub">Member Margonoandi</div>
            </div>
        </div>

        <div class="messages-area" id="messagesArea">
            @forelse($conversation->messages as $msg)
            <div class="msg-item {{ $msg->user_id === Auth::id() ? 'mine' : 'others' }}">
                @if($msg->user_id !== Auth::id())
                <img src="{{ $msg->user->avatar ?? 'https://www.google.com/favicon.ico' }}" class="msg-avatar" alt="">
                @endif
                <div>
                    @if($msg->user_id !== Auth::id())
                    <div class="msg-name">{{ $msg->user->name }}</div>
                    @endif
                    <div class="msg-bubble">{{ $msg->body }}</div>
                    <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#2a2a2a;font-size:13px;margin:auto;">Mulai percakapan!</div>
            @endforelse
        </div>

        <div class="chat-input-area">
            <textarea class="chat-input" id="msgInput" placeholder="Ketik pesan..." rows="1"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"></textarea>
            <button class="chat-send-btn" onclick="sendMessage()">&#10148;</button>
        </div>

        @elseif(isset($group))
        <div class="chat-header">
            <button class="chat-back-btn" onclick="goBack()">&#8249;</button>
            <div class="chat-header-avatar">&#128101;</div>
            <div>
                <div class="chat-header-name">{{ $group->name }}</div>
                <div class="chat-header-sub">{{ $group->members->count() }} anggota</div>
            </div>
        </div>

        <div class="messages-area" id="messagesArea">
            @forelse($group->messages as $msg)
            <div class="msg-item {{ $msg->user_id === Auth::id() ? 'mine' : 'others' }}">
                @if($msg->user_id !== Auth::id())
                <img src="{{ $msg->user->avatar ?? 'https://www.google.com/favicon.ico' }}" class="msg-avatar" alt="">
                @endif
                <div>
                    @if($msg->user_id !== Auth::id())
                    <div class="msg-name">{{ $msg->user->name }}</div>
                    @endif
                    <div class="msg-bubble">{{ $msg->body }}</div>
                    <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#2a2a2a;font-size:13px;margin:auto;">Belum ada pesan.</div>
            @endforelse
        </div>

        <div class="chat-input-area">
            <textarea class="chat-input" id="msgInput" placeholder="Ketik ke grup..." rows="1"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendGroupMessage();}"></textarea>
            <button class="chat-send-btn" onclick="sendGroupMessage()">&#10148;</button>
        </div>

        @else
        <div class="chat-empty">
            <div style="font-size:40px;">&#128172;</div>
            <p>Pilih percakapan atau mulai chat baru</p>
        </div>
        @endif

    </div>
</div>
</div>

{{-- CREATE GROUP MODAL --}}
<div class="modal-overlay" id="groupModal" onclick="closeGroupModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <h3 class="modal-title">Buat Grup Baru</h3>
        <form method="POST" action="{{ route('chat.group.create') }}">
            @csrf
            <div class="modal-field">
                <label class="modal-label">Nama Grup</label>
                <input type="text" name="name" class="modal-input" placeholder="Nama grup..." required maxlength="100">
            </div>
            <div class="modal-field">
                <label class="modal-label">Deskripsi</label>
                <input type="text" name="description" class="modal-input" placeholder="Opsional..." maxlength="300">
            </div>
            <div class="modal-field">
                <label class="modal-label">Pilih Anggota</label>
                <div class="members-list">
                    @foreach($users as $user)
                    <label class="member-option">
                        <input type="checkbox" name="members[]" value="{{ $user->id }}">
                        <img src="{{ $user->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
                        <span>{{ $user->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-modal-submit">Buat Grup</button>
                <button type="button" class="btn-modal-cancel" onclick="closeGroupModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
var csrfToken = '{{ csrf_token() }}';
@if(isset($conversation)) var convId = {{ $conversation->id }}; @endif
@if(isset($group)) var groupId = {{ $group->id }}; @endif

document.addEventListener('DOMContentLoaded', function() {
    var area = document.getElementById('messagesArea');
    if (area) area.scrollTop = area.scrollHeight;
});

function goBack() {
    window.location.href = '{{ route("chat.index") }}';
}

function sendMessage() {
    var input = document.getElementById('msgInput');
    var body  = input.value.trim();
    if (!body || typeof convId === 'undefined') return;
    input.value = ''; input.style.height = 'auto';

    fetch('/chat/conversation/' + convId + '/send', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ body: body })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) appendMessage(data.message, true); });
}

function sendGroupMessage() {
    var input = document.getElementById('msgInput');
    var body  = input.value.trim();
    if (!body || typeof groupId === 'undefined') return;
    input.value = '';

    fetch('/chat/group/' + groupId + '/send', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
        body: JSON.stringify({ body: body })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) appendMessage(data.message, true); });
}

function appendMessage(msg, isMine) {
    var area = document.getElementById('messagesArea');
    var div  = document.createElement('div');
    div.className = 'msg-item ' + (isMine ? 'mine' : 'others');
    div.innerHTML =
        (!isMine ? '<img src="' + msg.avatar + '" class="msg-avatar" alt="">' : '') +
        '<div>' +
        (!isMine ? '<div class="msg-name">' + msg.name + '</div>' : '') +
        '<div class="msg-bubble">' + escHtml(msg.body) + '</div>' +
        '<div class="msg-time">' + msg.time + '</div>' +
        '</div>';
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
}

function escHtml(t) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}

var msgInput = document.getElementById('msgInput');
if (msgInput) {
    msgInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
}

function openGroupModal()  { document.getElementById('groupModal').classList.add('open'); }
function closeGroupModal() { document.getElementById('groupModal').classList.remove('open'); }

function filterChats(q) {
    document.querySelectorAll('.chat-item').forEach(function(item) {
        var name = item.querySelector('.chat-name');
        if (name) item.style.display = name.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush
