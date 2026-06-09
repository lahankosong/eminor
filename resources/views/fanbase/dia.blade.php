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
        border-right:1px solid #0d0d0d; display:flex;
        flex-direction:column; overflow:hidden; background:#060606;
    }
    .dia-sidebar-head {
        padding:0.875rem 1rem; border-bottom:1px solid #0d0d0d;
        display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
    }
    .dia-sidebar-title { font-size:13px; font-weight:500; color:#ccc; }
    .dia-new-group-btn {
        font-size:11px; color:#444; background:transparent;
        border:1px solid #141414; border-radius:6px;
        padding:3px 8px; cursor:pointer; transition:0.15s;
    }
    .dia-new-group-btn:hover { color:#888; border-color:#2a2a2a; }

    .dia-search { padding:8px 10px; border-bottom:1px solid #0a0a0a; flex-shrink:0; }
    .dia-search input {
        width:100%; background:#111; border:1px solid #141414;
        border-radius:8px; color:#ccc; font-size:12px;
        padding:6px 12px; outline:none; font-family:inherit;
    }
    .dia-search input::placeholder { color:#2a2a2a; }

    .dia-list { overflow-y:auto; flex:1; scrollbar-width:none; }
    .dia-list::-webkit-scrollbar { display:none; }
    .dia-section-label {
        font-size:10px; color:#2a2a2a; letter-spacing:0.2em;
        text-transform:uppercase; padding:8px 12px 4px;
    }
    .dia-item {
        display:flex; align-items:center; gap:10px;
        padding:9px 12px; cursor:pointer; transition:0.15s;
        border-bottom:1px solid #090909; text-decoration:none;
        background:transparent; border-left:none; width:100%; text-align:left;
        font-family:inherit;
    }
    .dia-item:hover  { background:#0d0d0d; }
    .dia-item.active { background:#111; border-left:2px solid #fff; padding-left:10px; }
    .dia-item-avatar {
        width:36px; height:36px; border-radius:50%;
        background:#1a1a1a; flex-shrink:0; overflow:hidden;
        display:flex; align-items:center; justify-content:center;
        font-size:14px; color:#333;
    }
    .dia-item-avatar img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
    .dia-item-info { flex:1; min-width:0; }
    .dia-item-name    { font-size:12px; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dia-item-preview { font-size:11px; color:#2a2a2a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .dia-item-time { font-size:10px; color:#1a1a1a; flex-shrink:0; }

    /* MAIN CHAT */
    .dia-main { display:flex; flex-direction:column; overflow:hidden; background:#050505; }
    .dia-empty {
        flex:1; display:flex; flex-direction:column;
        align-items:center; justify-content:center; color:#2a2a2a; text-align:center; padding:2rem;
    }
    .dia-empty p { font-size:13px; margin-top:0.75rem; }

    .dia-header {
        padding:10px 1rem; border-bottom:1px solid #0d0d0d;
        display:flex; align-items:center; gap:10px; flex-shrink:0; background:#080808;
    }
    .dia-header-avatar {
        width:34px; height:34px; border-radius:50%; overflow:hidden;
        background:#1a1a1a; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:14px; color:#333;
    }
    .dia-header-avatar img { width:34px; height:34px; border-radius:50%; object-fit:cover; }
    .dia-header-name { font-size:13px; font-weight:500; color:#fff; }
    .dia-header-sub  { font-size:11px; color:#2a2a2a; }
    .dia-back-btn {
        display:none; background:transparent; border:none;
        color:#444; font-size:20px; cursor:pointer; padding:0 8px 0 0; flex-shrink:0;
    }

    .dia-messages { flex:1; overflow-y:auto; padding:1rem; display:flex; flex-direction:column; gap:6px; scrollbar-width:thin; scrollbar-color:#111 transparent; }

    .dia-msg { display:flex; gap:8px; align-items:flex-end; }
    .dia-msg.mine { flex-direction:row-reverse; }
    .dia-msg-avatar { width:26px; height:26px; border-radius:50%; object-fit:cover; background:#1a1a1a; flex-shrink:0; }
    .dia-msg-wrap { max-width:68%; }
    .dia-msg-name { font-size:10px; color:#2a2a2a; margin-bottom:2px; padding:0 4px; }
    .dia-msg-bubble {
        padding:8px 12px; border-radius:12px; font-size:13px;
        line-height:1.5; word-break:break-word;
    }
    .dia-msg.others .dia-msg-bubble { background:#111; color:#aaa; border-radius:4px 12px 12px 12px; }
    .dia-msg.mine   .dia-msg-bubble { background:#1a1a2e; color:#c0d0f0; border-radius:12px 4px 12px 12px; }
    .dia-msg-time { font-size:10px; color:#1a1a1a; margin-top:3px; padding:0 4px; }

    .dia-input-area {
        padding:10px 1rem; border-top:1px solid #0d0d0d;
        background:#080808; flex-shrink:0;
    }
    .dia-mention-list {
        background:#111; border:1px solid #1a1a1a; border-radius:8px;
        margin-bottom:8px; max-height:120px; overflow-y:auto; display:none;
    }
    .dia-mention-list.show { display:block; }
    .dia-mention-item {
        display:flex; align-items:center; gap:8px;
        padding:7px 12px; cursor:pointer; transition:0.15s; font-size:12px; color:#888;
    }
    .dia-mention-item:hover { background:#1a1a1a; color:#fff; }
    .dia-mention-item img { width:22px; height:22px; border-radius:50%; object-fit:cover; }

    .dia-input-row { display:flex; gap:8px; align-items:flex-end; }
    .dia-input {
        flex:1; background:#111; border:1px solid #141414; border-radius:20px;
        color:#ccc; font-size:13px; padding:8px 14px; outline:none;
        resize:none; font-family:inherit; max-height:100px; min-height:38px;
        line-height:1.5; overflow-y:auto; transition:0.15s;
    }
    .dia-input:focus { border-color:#2a2a2a; }
    .dia-input::placeholder { color:#2a2a2a; }
    .dia-send-btn {
        width:38px; height:38px; border-radius:50%;
        background:#fff; color:#000; border:none; font-size:14px;
        cursor:pointer; transition:0.2s; display:flex;
        align-items:center; justify-content:center; flex-shrink:0;
    }
    .dia-send-btn:hover { background:#ddd; }

    /* MODAL */
    .dia-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:1000; align-items:center; justify-content:center; padding:1rem; }
    .dia-modal.open { display:flex; }
    .dia-modal-box { background:#111; border:1px solid #1a1a1a; border-radius:16px; width:100%; max-width:400px; padding:1.5rem; max-height:85vh; overflow-y:auto; }
    .dia-modal-title { font-size:1rem; font-weight:500; margin-bottom:1.25rem; }
    .dia-modal-field { margin-bottom:1rem; }
    .dia-modal-label { font-size:11px; color:#444; text-transform:uppercase; display:block; margin-bottom:5px; }
    .dia-modal-input { width:100%; background:#0d0d0d; border:1px solid #2a2a2a; border-radius:8px; color:#fff; font-size:13px; padding:9px 12px; outline:none; font-family:inherit; }
    .dia-members-list { max-height:180px; overflow-y:auto; }
    .dia-member-opt { display:flex; align-items:center; gap:10px; padding:7px; border-radius:6px; cursor:pointer; transition:0.15s; }
    .dia-member-opt:hover { background:#0d0d0d; }
    .dia-member-opt img { width:28px; height:28px; border-radius:50%; object-fit:cover; }
    .dia-member-opt span { font-size:13px; color:#888; }
    .dia-modal-actions { display:flex; gap:10px; margin-top:1.25rem; }
    .dia-modal-submit { padding:9px 24px; border-radius:8px; font-size:13px; font-weight:500; background:#fff; color:#000; border:none; cursor:pointer; flex:1; }
    .dia-modal-cancel { padding:9px 20px; border-radius:8px; font-size:13px; border:1px solid #2a2a2a; color:#888; background:transparent; cursor:pointer; }

    @media (max-width:768px) {
        .dia-layout { grid-template-columns:1fr; }
        .dia-sidebar { display:flex; }
        .dia-main    { display:none; }
        .dia-layout.conv-open .dia-sidebar { display:none; }
        .dia-layout.conv-open .dia-main    { display:flex; }
        .dia-back-btn { display:block; }
    }
</style>
@endpush

@section('content')

<div class="dia-layout {{ (isset($conversation)||isset($group)) ? 'conv-open' : '' }}" id="diaLayout">

    {{-- SIDEBAR --}}
    <div class="dia-sidebar">
        <div class="dia-sidebar-head">
            <span class="dia-sidebar-title">&#128172; Dia</span>
            <button class="dia-new-group-btn" onclick="diaOpenGroupModal()">+ Grup</button>
        </div>
        <div class="dia-search">
            <input type="text" placeholder="Cari..." oninput="diaFilter(this.value)">
        </div>
        <div class="dia-list" id="diaList">

            <div class="dia-section-label">Member</div>
            @foreach($users as $user)
            <form method="POST" action="{{ route('dia.start', $user->id) }}" style="display:block;">
                @csrf
                <button type="submit" class="dia-item">
                    <div class="dia-item-avatar">
                        <img src="{{ $user->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
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
                    <img src="{{ $other->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
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
                <div class="dia-item-avatar">&#128101;</div>
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
            <button class="dia-back-btn" onclick="window.location='{{ route('dia') }}'">&#8249;</button>
            <div class="dia-header-avatar">
                <img src="{{ $other->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
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
                <img src="{{ $msg->user->avatar ?? 'https://www.google.com/favicon.ico' }}" class="dia-msg-avatar" alt="">
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
            <div style="text-align:center;color:#2a2a2a;font-size:13px;margin:auto;">Mulai percakapan!</div>
            @endforelse
        </div>

        <div class="dia-input-area">
            <div class="dia-mention-list" id="diaMentionList"></div>
            <div class="dia-input-row">
                <textarea class="dia-input" id="diaInput" placeholder="Ketik pesan... (@nama untuk mention)"
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSend();}"
                    oninput="diaCheckMention(this)"></textarea>
                <button class="dia-send-btn" onclick="diaSend()">&#10148;</button>
            </div>
        </div>

        @elseif(isset($group))
        <div class="dia-header">
            <button class="dia-back-btn" onclick="window.location='{{ route('dia') }}'">&#8249;</button>
            <div class="dia-header-avatar">&#128101;</div>
            <div>
                <div class="dia-header-name">{{ $group->name }}</div>
                <div class="dia-header-sub">{{ $group->members->count() }} anggota</div>
            </div>
        </div>

        <div class="dia-messages" id="diaMessages">
            @forelse($group->messages as $msg)
            <div class="dia-msg {{ $msg->user_id===Auth::id() ? 'mine' : 'others' }}">
                @if($msg->user_id !== Auth::id())
                <img src="{{ $msg->user->avatar ?? 'https://www.google.com/favicon.ico' }}" class="dia-msg-avatar" alt="">
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
            <div style="text-align:center;color:#2a2a2a;font-size:13px;margin:auto;">Belum ada pesan.</div>
            @endforelse
        </div>

        <div class="dia-input-area">
            <div class="dia-input-row">
                <textarea class="dia-input" id="diaInput" placeholder="Ketik ke grup..."
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSendGroup();}"></textarea>
                <button class="dia-send-btn" onclick="diaSendGroup()">&#10148;</button>
            </div>
        </div>

        @else
        <div class="dia-empty">
            <div style="font-size:40px;">&#128172;</div>
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
                        <input type="checkbox" name="members[]" value="{{ $u->id }}">
                        <img src="{{ $u->avatar ?? 'https://www.google.com/favicon.ico' }}" alt="">
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
    input.value = '';

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
        (!isMine ? '<img src="'+msg.avatar+'" class="dia-msg-avatar" alt="">' : '') +
        '<div class="dia-msg-wrap">' +
        (!isMine ? '<div class="dia-msg-name">'+msg.name+'</div>' : '') +
        '<div class="dia-msg-bubble">' + escHtml(msg.body) + '</div>' +
        '<div class="dia-msg-time">' + msg.time + '</div>' +
        '</div>';
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
}

// Mention detection
function diaCheckMention(el) {
    var val  = el.value;
    var match = val.match(/@(\w*)$/);
    var list  = document.getElementById('diaMentionList');
    if (!list) return;

    if (match) {
        var q = match[1].toLowerCase();
        var filtered = diaUsers.filter(function(u){ return u.name.toLowerCase().includes(q); });
        if (filtered.length > 0) {
            list.innerHTML = filtered.map(function(u){
                return '<div class="dia-mention-item" onclick="diaInsertMention(\''+u.name+'\')">' +
                    '<img src="'+(u.avatar||'')+'"> ' + u.name + '</div>';
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