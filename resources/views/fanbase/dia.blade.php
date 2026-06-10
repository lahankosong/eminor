@extends('layouts.fanbase')
@section('title', 'Dia')

@push('styles')
<style>
    /* Override main padding for chat layout */
    .fb-main { padding:0 !important; }

    .dia-layout {
        display:grid; grid-template-columns:260px 1fr;
        height:calc(100vh - 52px); overflow:hidden;
    }

    /* SIDEBAR */
    .dia-sidebar {
        border-right:1px solid var(--border-lt); display:flex;
        flex-direction:column; overflow:hidden; background:var(--card);
    }
    .dia-sidebar-head {
        padding:0.875rem 1rem; border-bottom:1px solid var(--border-lt);
        display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
    }
    .dia-sidebar-title {
        font-size:13px; font-weight:600; color:var(--text-1);
        display:flex; align-items:center; gap:6px;
    }
    .dia-new-group-btn {
        font-size:11px; color:var(--text-3); background:transparent;
        border:1px solid var(--border); border-radius:6px;
        padding:4px 10px; cursor:pointer; transition:0.15s;
        font-family:'DM Sans', sans-serif; font-weight:500;
    }
    .dia-new-group-btn:hover { color:var(--sky-dk); border-color:var(--sky-mid); background:var(--sky-lt); }

    .dia-search { padding:8px 10px; border-bottom:1px solid var(--border-lt); flex-shrink:0; }
    .dia-search input {
        width:100%; background:var(--cream); border:1px solid var(--border);
        border-radius:8px; color:var(--text-1); font-size:12px;
        padding:7px 12px; outline:none; font-family:inherit; transition:0.2s;
    }
    .dia-search input:focus { border-color:var(--sky); box-shadow:0 0 0 3px var(--sky-glow); }
    .dia-search input::placeholder { color:var(--text-4); }

    .dia-list { overflow-y:auto; flex:1; scrollbar-width:thin; scrollbar-color:var(--border) transparent; }
    .dia-section-label {
        font-size:10px; color:var(--text-4); letter-spacing:0.15em;
        text-transform:uppercase; padding:10px 14px 4px; font-weight:600;
    }
    .dia-item {
        display:flex; align-items:center; gap:10px;
        padding:9px 12px; cursor:pointer; transition:0.15s;
        border-bottom:1px solid var(--border-lt); text-decoration:none;
        background:transparent; border-left:3px solid transparent;
        width:100%; text-align:left; font-family:inherit;
    }
    .dia-item:hover  { background:var(--sky-lt); border-left-color:var(--sky-mid); }
    .dia-item.active { background:var(--sky-lt); border-left-color:var(--sky); }
    .dia-item-avatar {
        width:36px; height:36px; border-radius:50%;
        background:var(--sky-lt); flex-shrink:0; overflow:hidden;
        display:flex; align-items:center; justify-content:center;
        color:var(--sky-dk); border:1.5px solid var(--border);
    }
    .dia-item-avatar img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
    .dia-item-info { flex:1; min-width:0; }
    .dia-item-name    { font-size:12px; font-weight:500; color:var(--text-1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dia-item-preview { font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .dia-item-time { font-size:10px; color:var(--text-4); flex-shrink:0; }

    /* MAIN CHAT */
    .dia-main { display:flex; flex-direction:column; overflow:hidden; background:var(--cream); }
    .dia-empty {
        flex:1; display:flex; flex-direction:column;
        align-items:center; justify-content:center; color:var(--sky-mid); text-align:center; padding:2rem;
    }
    .dia-empty p { font-size:13px; margin-top:0.75rem; color:var(--text-3); }

    .dia-header {
        padding:10px 1rem; border-bottom:1px solid var(--border-lt);
        display:flex; align-items:center; gap:10px; flex-shrink:0;
        background:var(--card); box-shadow:0 1px 4px rgba(0,0,0,0.04);
    }
    .dia-header-avatar {
        width:34px; height:34px; border-radius:50%; overflow:hidden;
        background:var(--sky-lt); flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        color:var(--sky-dk); border:1.5px solid var(--border);
    }
    .dia-header-avatar img { width:34px; height:34px; border-radius:50%; object-fit:cover; }
    .dia-header-name { font-size:13px; font-weight:600; color:var(--text-1); }
    .dia-header-sub  { font-size:11px; color:var(--text-3); }
    .dia-back-btn {
        display:none; background:transparent; border:none;
        color:var(--text-3); cursor:pointer; padding:0 8px 0 0; flex-shrink:0; line-height:1;
    }
    .dia-back-btn:hover { color:var(--sky-dk); }

    .dia-messages {
        flex:1; overflow-y:auto; padding:1rem;
        display:flex; flex-direction:column; gap:6px;
        scrollbar-width:thin; scrollbar-color:var(--border) transparent;
    }

    .dia-msg { display:flex; gap:8px; align-items:flex-end; }
    .dia-msg.mine { flex-direction:row-reverse; }
    .dia-msg-avatar {
        width:26px; height:26px; border-radius:50%; object-fit:cover;
        background:var(--sky-lt); flex-shrink:0; border:1.5px solid var(--border);
    }
    .dia-msg-wrap { max-width:68%; }
    .dia-msg-name { font-size:10px; color:var(--text-3); margin-bottom:2px; padding:0 4px; }
    .dia-msg-bubble {
        padding:8px 12px; border-radius:12px; font-size:13px;
        line-height:1.5; word-break:break-word;
    }
    .dia-msg.others .dia-msg-bubble {
        background:var(--card); color:var(--text-2);
        border-radius:4px 12px 12px 12px;
        border:1px solid var(--border-lt); box-shadow:var(--shadow-sm);
    }
    .dia-msg.mine .dia-msg-bubble {
        background:linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color:#fff; border-radius:12px 4px 12px 12px;
        box-shadow:0 2px 8px var(--sky-glow);
    }
    .dia-msg-time { font-size:10px; color:var(--text-4); margin-top:3px; padding:0 4px; }

    .dia-input-area {
        padding:10px 1rem; border-top:1px solid var(--border-lt);
        background:var(--card); flex-shrink:0;
    }
    .dia-mention-list {
        background:var(--card); border:1px solid var(--border); border-radius:10px;
        margin-bottom:8px; max-height:140px; overflow-y:auto; display:none;
        box-shadow:var(--shadow-sm);
    }
    .dia-mention-list.show { display:block; }
    .dia-mention-item {
        display:flex; align-items:center; gap:8px;
        padding:8px 12px; cursor:pointer; transition:0.15s;
        font-size:12px; color:var(--text-2);
    }
    .dia-mention-item:hover { background:var(--sky-lt); color:var(--sky-dk); }
    .dia-mention-item img { width:22px; height:22px; border-radius:50%; object-fit:cover; }

    .dia-input-row { display:flex; gap:8px; align-items:flex-end; }
    .dia-input {
        flex:1; background:var(--cream); border:1px solid var(--border); border-radius:20px;
        color:var(--text-1); font-size:13px; padding:9px 16px; outline:none;
        resize:none; font-family:inherit; max-height:100px; min-height:38px;
        line-height:1.5; overflow-y:auto; transition:0.2s;
    }
    .dia-input:focus { border-color:var(--sky); box-shadow:0 0 0 3px var(--sky-glow); }
    .dia-input::placeholder { color:var(--text-4); }
    .dia-send-btn {
        width:38px; height:38px; border-radius:50%;
        background:linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color:#fff; border:none; cursor:pointer; transition:0.2s;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
        box-shadow:0 2px 8px var(--sky-glow);
    }
    .dia-send-btn:hover { transform:scale(1.06); box-shadow:0 4px 14px var(--sky-glow); }

    /* MODAL */
    .dia-modal {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,0.45); z-index:1000;
        align-items:center; justify-content:center; padding:1rem;
    }
    .dia-modal.open { display:flex; }
    .dia-modal-box {
        background:var(--card); border:1px solid var(--border); border-radius:20px;
        width:100%; max-width:400px; padding:1.5rem;
        max-height:85vh; overflow-y:auto; box-shadow:var(--shadow);
    }
    .dia-modal-title { font-size:1rem; font-weight:600; margin-bottom:1.25rem; color:var(--text-1); }
    .dia-modal-field { margin-bottom:1rem; }
    .dia-modal-label {
        font-size:11px; color:var(--text-4); text-transform:uppercase;
        letter-spacing:0.05em; font-weight:600; display:block; margin-bottom:5px;
    }
    .dia-modal-input {
        width:100%; background:var(--cream); border:1px solid var(--border);
        border-radius:8px; color:var(--text-1); font-size:13px;
        padding:9px 12px; outline:none; font-family:inherit; transition:0.2s;
    }
    .dia-modal-input:focus { border-color:var(--sky); box-shadow:0 0 0 3px var(--sky-glow); }
    .dia-members-list { max-height:180px; overflow-y:auto; }
    .dia-member-opt {
        display:flex; align-items:center; gap:10px;
        padding:7px; border-radius:8px; cursor:pointer; transition:0.15s;
    }
    .dia-member-opt:hover { background:var(--sky-lt); }
    .dia-member-opt img { width:28px; height:28px; border-radius:50%; object-fit:cover; }
    .dia-member-opt span { font-size:13px; color:var(--text-2); }
    .dia-modal-actions { display:flex; gap:10px; margin-top:1.25rem; }
    .dia-modal-submit {
        padding:9px 24px; border-radius:10px; font-size:13px; font-weight:600;
        background:linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
        color:#fff; border:none; cursor:pointer; flex:1;
        box-shadow:0 2px 8px var(--sky-glow); font-family:'DM Sans',sans-serif;
    }
    .dia-modal-cancel {
        padding:9px 20px; border-radius:10px; font-size:13px;
        border:1px solid var(--border); color:var(--text-3);
        background:transparent; cursor:pointer; font-family:'DM Sans',sans-serif;
    }
    .dia-modal-cancel:hover { background:var(--sky-lt); color:var(--sky-dk); }

    @media (max-width:768px) {
        .dia-layout {
            grid-template-columns:1fr;
            height: calc(100vh - 56px - 84px);
        }
        .dia-sidebar { display:flex; }
        .dia-main    { display:none; }
        .dia-layout.conv-open .dia-sidebar { display:none; }
        .dia-layout.conv-open .dia-main    { display:flex; }
        .dia-back-btn { display:block; }
        .dia-input-area {
            position: sticky;
            bottom: 0;
        }
    }
</style>
@endpush

@section('content')

<div class="dia-layout {{ (isset($conversation)||isset($group)) ? 'conv-open' : '' }}" id="diaLayout">

    {{-- SIDEBAR --}}
    <div class="dia-sidebar">
        <div class="dia-sidebar-head">
            <span class="dia-sidebar-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Dia
            </span>
            <button class="dia-new-group-btn" onclick="diaOpenGroupModal()">+ Grup</button>
        </div>
        <div class="dia-search">
            <input type="text" placeholder="Cari obrolan..." oninput="diaFilter(this.value)">
        </div>
        <div class="dia-list" id="diaList">

            <div class="dia-section-label">Member</div>
            @foreach($users as $user)
            <form method="POST" action="{{ route('dia.start', $user->id) }}" style="display:block;">
                @csrf
                <button type="submit" class="dia-item">
                    <div class="dia-item-avatar">
                        <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" alt="">
                    </div>
                    <div class="dia-item-info">
                        <div class="dia-item-name">{{ $user->name }}</div>
                        <div class="dia-item-preview">Mulai obrolan</div>
                    </div>
                </button>
            </form>
            @endforeach

            @if($conversations->count() > 0)
            <div class="dia-section-label">Obrolan</div>
            @foreach($conversations as $conv)
            @php $other = $conv->getOtherUser(Auth::id()); @endphp
            <a href="{{ route('dia.conversation', $conv->id) }}"
               class="dia-item {{ isset($conversation) && $conversation->id===$conv->id ? 'active' : '' }}">
                <div class="dia-item-avatar">
                    <img src="{{ $other->avatar ?? asset('images/default-avatar.png') }}" alt="">
                </div>
                <div class="dia-item-info">
                    <div class="dia-item-name">{{ $other->name }}</div>
                    <div class="dia-item-preview">{{ $conv->last_message ?? 'Belum ada pesan' }}</div>
                </div>
                @if($conv->last_message_at)
                <span class="dia-item-time">{{ $conv->last_message_at->format('H:i') }}</span>
                @endif
            </a>
            @endforeach
            @endif

            @if($groups->count() > 0)
            <div class="dia-section-label">Grup</div>
            @foreach($groups as $grp)
            <a href="{{ route('dia.group', $grp->id) }}"
               class="dia-item {{ isset($group) && $group->id===$grp->id ? 'active' : '' }}">
                <div class="dia-item-avatar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="dia-item-info">
                    <div class="dia-item-name">{{ $grp->name }}</div>
                    <div class="dia-item-preview">{{ $grp->last_message ?? 'Belum ada pesan' }}</div>
                </div>
                @if($grp->last_message_at)
                <span class="dia-item-time">{{ $grp->last_message_at->format('H:i') }}</span>
                @endif
            </a>
            @endforeach
            @endif

        </div>
    </div>

    {{-- MAIN --}}
    <div class="dia-main">

        @if(isset($conversation))
        @php $other = $conversation->getOtherUser(Auth::id()); @endphp
        <div class="dia-header">
            <button class="dia-back-btn" onclick="window.location='{{ route('dia') }}'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="dia-header-avatar">
                <img src="{{ $other->avatar ?? asset('images/default-avatar.png') }}" alt="">
            </div>
            <div>
                <div class="dia-header-name">{{ $other->name }}</div>
                <div class="dia-header-sub">Member Margonoandi</div>
            </div>
        </div>

        <div class="dia-messages" id="diaMessages">
            @forelse($conversation->messages as $msg)
            <div class="dia-msg {{ $msg->user_id===Auth::id() ? 'mine' : 'others' }}">
                @if($msg->user_id !== Auth::id())
                <img src="{{ $msg->user->avatar ?? asset('images/default-avatar.png') }}" class="dia-msg-avatar" alt="">
                @endif
                <div class="dia-msg-wrap">
                    @if($msg->user_id !== Auth::id())
                    <div class="dia-msg-name">{{ $msg->user->name }}</div>
                    @endif
                    <div class="dia-msg-bubble">{{ $msg->body }}</div>
                    <div class="dia-msg-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:var(--text-4);font-size:13px;margin:auto;">Mulai percakapan!</div>
            @endforelse
        </div>

        <div class="dia-input-area">
            <div class="dia-mention-list" id="diaMentionList"></div>
            <div class="dia-input-row">
                <textarea class="dia-input" id="diaInput" placeholder="Ketik pesan... (@nama untuk mention)"
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSend();}"
                    oninput="diaCheckMention(this)"></textarea>
                <button class="dia-send-btn" onclick="diaSend()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        </div>

        @elseif(isset($group))
        <div class="dia-header">
            <button class="dia-back-btn" onclick="window.location='{{ route('dia') }}'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="dia-header-avatar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="dia-header-name">{{ $group->name }}</div>
                <div class="dia-header-sub">{{ $group->members->count() }} anggota</div>
            </div>
        </div>

        <div class="dia-messages" id="diaMessages">
            @forelse($group->messages as $msg)
            <div class="dia-msg {{ $msg->user_id===Auth::id() ? 'mine' : 'others' }}">
                @if($msg->user_id !== Auth::id())
                <img src="{{ $msg->user->avatar ?? asset('images/default-avatar.png') }}" class="dia-msg-avatar" alt="">
                @endif
                <div class="dia-msg-wrap">
                    @if($msg->user_id !== Auth::id())
                    <div class="dia-msg-name">{{ $msg->user->name }}</div>
                    @endif
                    <div class="dia-msg-bubble">{{ $msg->body }}</div>
                    <div class="dia-msg-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:var(--text-4);font-size:13px;margin:auto;">Belum ada pesan.</div>
            @endforelse
        </div>

        <div class="dia-input-area">
            <div class="dia-input-row">
                <textarea class="dia-input" id="diaInput" placeholder="Ketik ke grup... (Shift+Enter untuk baris baru)"
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSendGroup();}"></textarea>
                <button class="dia-send-btn" onclick="diaSendGroup()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        </div>

        @else
        <div class="dia-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p>Pilih obrolan atau mulai yang baru</p>
        </div>
        @endif

    </div>
</div>

{{-- GROUP MODAL --}}
<div class="dia-modal" id="diaGroupModal" onclick="diaCloseGroupModal()">
    <div class="dia-modal-box" onclick="event.stopPropagation()">
        <h3 class="dia-modal-title">Buat Grup</h3>
        <form method="POST" action="{{ route('dia.group.create') }}">
            @csrf
            <div class="dia-modal-field">
                <label class="dia-modal-label">Nama Grup</label>
                <input type="text" name="name" class="dia-modal-input" required maxlength="100">
            </div>
            <div class="dia-modal-field">
                <label class="dia-modal-label">Pilih Anggota</label>
                <div class="dia-members-list">
                    @foreach($users as $u)
                    <label class="dia-member-opt">
                        <input type="checkbox" name="members[]" value="{{ $u->id }}" style="accent-color:var(--sky);">
                        <img src="{{ $u->avatar ?? asset('images/default-avatar.png') }}" alt="">
                        <span>{{ $u->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="dia-modal-actions">
                <button type="submit" class="dia-modal-submit">Buat Grup</button>
                <button type="button" class="dia-modal-cancel" onclick="diaCloseGroupModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
var csrfToken = '{{ csrf_token() }}';
var diaUsers  = @json($users->map(fn($u) => ['id'=>$u->id,'name'=>$u->name,'avatar'=>$u->avatar]));

@if(isset($conversation)) var convId = {{ $conversation->id }}; @endif
@if(isset($group))        var groupId = {{ $group->id }}; @endif

document.addEventListener('DOMContentLoaded', function() {
    var msgs = document.getElementById('diaMessages');
    if (msgs) msgs.scrollTop = msgs.scrollHeight;
});

function diaSend() {
    var input = document.getElementById('diaInput');
    var body  = input ? input.value.trim() : '';
    if (!body || typeof convId === 'undefined') return;
    input.value = ''; input.style.height = 'auto';

    fetch('/dia/conversation/' + convId + '/send', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){return r.json();})
    .then(function(d){ if(d.success) diaAppend(d.message, true); });
}

function diaSendGroup() {
    var input = document.getElementById('diaInput');
    var body  = input ? input.value.trim() : '';
    if (!body || typeof groupId === 'undefined') return;
    input.value = ''; input.style.height = 'auto';

    fetch('/dia/group/' + groupId + '/send', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){return r.json();})
    .then(function(d){ if(d.success) diaAppend(d.message, true); });
}

function diaAppend(msg, isMine) {
    var area = document.getElementById('diaMessages');
    if (!area) return;
    var div  = document.createElement('div');
    div.className = 'dia-msg ' + (isMine ? 'mine' : 'others');
    div.innerHTML =
        (!isMine ? '<img src="'+(msg.avatar||'')+'\" class="dia-msg-avatar" alt="">' : '') +
        '<div class="dia-msg-wrap">' +
        (!isMine ? '<div class="dia-msg-name">'+escHtml(msg.name)+'</div>' : '') +
        '<div class="dia-msg-bubble">' + escHtml(msg.body) + '</div>' +
        '<div class="dia-msg-time">' + msg.time + '</div>' +
        '</div>';
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
}

function diaCheckMention(el) {
    var val   = el.value;
    var match = val.match(/@(\w*)$/);
    var list  = document.getElementById('diaMentionList');
    if (!list) return;

    if (match) {
        var q = match[1].toLowerCase();
        var filtered = diaUsers.filter(function(u){ return u.name.toLowerCase().includes(q); });
        if (filtered.length > 0) {
            list.innerHTML = filtered.map(function(u){
                return '<div class="dia-mention-item" onclick="diaInsertMention(\''+escHtml(u.name)+'\')">' +
                    (u.avatar ? '<img src="'+escHtml(u.avatar)+'">' : '') +
                    ' ' + escHtml(u.name) + '</div>';
            }).join('');
            list.classList.add('show');
            return;
        }
    }
    list.classList.remove('show');
}

function diaInsertMention(name) {
    var input = document.getElementById('diaInput');
    if (!input) return;
    input.value = input.value.replace(/@\w*$/, '@' + name + ' ');
    input.focus();
    var list = document.getElementById('diaMentionList');
    if (list) list.classList.remove('show');
}

function diaFilter(q) {
    document.querySelectorAll('.dia-item').forEach(function(item){
        var name = item.querySelector('.dia-item-name');
        if (name) item.style.display = name.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}

function diaOpenGroupModal()  { document.getElementById('diaGroupModal').classList.add('open'); }
function diaCloseGroupModal() { document.getElementById('diaGroupModal').classList.remove('open'); }

var diaInput = document.getElementById('diaInput');
if (diaInput) {
    diaInput.addEventListener('input', function(){
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
}

function escHtml(t) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}
</script>
@endpush
