@extends('layouts.fanbase')
@section('title', 'Dia')

@push('styles')
<style>
    /* Dia: fb-main menjadi flex column, tanpa padding */
    .fb-main {
        padding:0 !important;
        height:calc(100vh - 56px);
        overflow:hidden;
        display:flex;
        flex-direction:column;
    }

    /* ─── HEADER CHAT ──────────────────────────────────────── */
    .dia-header {
        padding:10px 1.25rem;
        border-bottom:1px solid var(--border-lt);
        display:flex; align-items:center; gap:10px; flex-shrink:0;
        background:var(--card);
        box-shadow:0 1px 4px rgba(0,0,0,0.04);
    }
    .dia-back-btn {
        background:transparent; border:none; color:var(--text-3);
        cursor:pointer; padding:0 8px 0 0; flex-shrink:0; line-height:1;
        display:none;
    }
    .dia-back-btn:hover { color:var(--sky-dk); }
    .dia-header-avatar {
        width:36px; height:36px; border-radius:50%; overflow:hidden;
        background:var(--sky-lt); flex-shrink:0; position:relative;
        display:flex; align-items:center; justify-content:center;
        color:var(--sky-dk); border:1.5px solid var(--border);
    }
    .dia-header-avatar img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
    .dia-header-dot {
        position:absolute; bottom:0; right:0;
        width:10px; height:10px; border-radius:50%;
        background:#d1d5db; border:2px solid var(--card);
    }
    .dia-header-dot.online { background:#10b981; }
    .dia-header { position:relative; }
    .dia-header-name { font-size:13px; font-weight:600; color:var(--text-1); }
    .dia-info-btn { margin-left:auto; background:transparent; border:none; color:var(--text-3); cursor:pointer; padding:6px; border-radius:50%; display:flex; align-items:center; }
    .dia-info-btn:hover { background:var(--surface); color:var(--sky-dk); }
    .dia-loc-popup { position:absolute; top:calc(100% + 6px); right:12px; width:260px; background:var(--card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-lg); padding:14px; z-index:50; display:none; }
    .dia-loc-popup.open { display:block; }
    .dia-loc-title { font-size:12px; font-weight:700; color:var(--text-1); margin-bottom:6px; }
    .dia-loc-city { font-size:17px; font-weight:700; color:var(--sky-dk); }
    .dia-loc-note { font-size:12px; color:var(--text-2); margin-top:3px; line-height:1.5; }
    .dia-loc-disc { font-size:10px; color:var(--text-3); margin-top:9px; line-height:1.45; border-top:1px solid var(--border-lt); padding-top:7px; }
    .dia-header-sub  {
        font-size:11px; color:var(--text-3);
        display:flex; align-items:center; gap:4px;
    }
    .dia-header-sub .dot-sm {
        width:7px; height:7px; border-radius:50%; background:#d1d5db;
    }
    .dia-header-sub .dot-sm.online { background:#10b981; }

    /* ─── MESSAGES ─────────────────────────────────────────── */
    .dia-messages {
        flex:1; overflow-y:auto; padding:1rem 1.25rem;
        display:flex; flex-direction:column; gap:6px;
        scrollbar-width:thin; scrollbar-color:var(--border) transparent;
    }
    .dia-msg { display:flex; gap:8px; align-items:flex-end; }
    .dia-msg.mine { flex-direction:row-reverse; }
    .dia-msg-avatar {
        width:26px; height:26px; border-radius:50%; object-fit:cover;
        background:var(--sky-lt); flex-shrink:0; border:1.5px solid var(--border);
    }
    .dia-msg-wrap { max-width:65%; }
    .dia-msg-name { font-size:10px; color:var(--text-3); margin-bottom:2px; padding:0 4px; }
    .dia-msg-bubble {
        padding:8px 12px; border-radius:12px;
        font-size:13px; line-height:1.5; word-break:break-word;
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

    /* ─── INPUT ────────────────────────────────────────────── */
    .dia-input-area {
        padding:10px 1.25rem;
        border-top:1px solid var(--border-lt);
        background:var(--card); flex-shrink:0;
    }
    .dia-mention-list {
        background:var(--card); border:1px solid var(--border); border-radius:10px;
        margin-bottom:8px; max-height:130px; overflow-y:auto; display:none;
        box-shadow:var(--shadow-sm);
    }
    .dia-mention-list.show { display:block; }
    .dia-mention-item {
        display:flex; align-items:center; gap:8px;
        padding:8px 12px; cursor:pointer; transition:0.15s; font-size:12px; color:var(--text-2);
    }
    .dia-mention-item:hover { background:var(--sky-lt); color:var(--sky-dk); }
    .dia-mention-item img { width:22px; height:22px; border-radius:50%; object-fit:cover; }
    .dia-input-row { display:flex; gap:6px; align-items:flex-end; }
    .dia-tool-btn { background:transparent; border:none; cursor:pointer; font-size:18px; padding:6px 7px; border-radius:50%; line-height:1; flex-shrink:0; color:var(--text-2); }
    .dia-tool-btn:hover { background:var(--surface); }
    .dia-tool-btn.recording { background:#fee2e2; animation:diaPulse 1s infinite; }
    [data-theme="dark"] .dia-tool-btn.recording { background:rgba(239,68,68,0.18); }
    /* indikator "mengetik" bot */
    .dia-typing-bubble { display:inline-flex; gap:4px; align-items:center; padding:10px 14px; }
    .dia-typing-bubble span { width:7px; height:7px; border-radius:50%; background:var(--text-3); animation:diaTyping 1.1s infinite ease-in-out; }
    .dia-typing-bubble span:nth-child(2){ animation-delay:.15s; }
    .dia-typing-bubble span:nth-child(3){ animation-delay:.3s; }
    @keyframes diaTyping { 0%,60%,100%{ opacity:.3; transform:translateY(0); } 30%{ opacity:1; transform:translateY(-3px); } }
    @keyframes diaPulse { 0%,100%{opacity:1;} 50%{opacity:0.45;} }
    .dia-media { display:block; margin-bottom:4px; }
    .dia-media-img { max-width:220px; max-height:280px; border-radius:12px; cursor:pointer; display:block; background:var(--surface); }
    .dia-media-audio { width:230px; max-width:100%; height:40px; margin-bottom:2px; }
    .dia-media-video { max-width:260px; max-height:320px; border-radius:12px; display:block; background:#000; }
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
    .dia-send-btn:hover { transform:scale(1.06); }

    /* ─── EMPTY STATE ──────────────────────────────────────── */
    .dia-empty {
        flex:1; display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        color:var(--sky-mid); text-align:center; padding:2rem;
    }
    .dia-empty p { font-size:13px; margin-top:0.75rem; color:var(--text-3); }

    /* ─── SEARCH ──────────────────────────────────────────── */
    .dia-search-wrap {
        padding:10px 1rem 6px; flex-shrink:0;
        background:var(--card); border-bottom:1px solid var(--border-lt);
        position:sticky; top:0; z-index:10;
    }
    .dia-search-box {
        display:flex; align-items:center; gap:6px;
        background:var(--cream); border:1px solid var(--border);
        border-radius:20px; padding:0 12px; transition:0.2s;
    }
    .dia-search-box:focus-within { border-color:var(--sky); box-shadow:0 0 0 3px var(--sky-glow); }
    .dia-search-box svg { flex-shrink:0; color:var(--text-4); }
    .dia-search-input {
        flex:1; border:none; background:transparent; outline:none;
        font-size:13px; color:var(--text-1); padding:9px 0; font-family:inherit;
    }
    .dia-search-input::placeholder { color:var(--text-4); }
    .dia-search-clear {
        background:transparent; border:none; color:var(--text-3);
        cursor:pointer; padding:0; line-height:1;
    }
    .dia-search-results { flex-shrink:0; }
    .dia-search-result-item {
        display:flex; align-items:center; gap:10px;
        padding:10px 1rem; border-bottom:1px solid var(--border-lt);
        background:transparent; border-left:none; border-right:none; border-top:none;
        cursor:pointer; transition:0.15s; width:100%; text-align:left;
    }
    .dia-search-result-item:hover { background:var(--sky-lt); }
    .dia-search-avatar {
        width:36px; height:36px; border-radius:50%; overflow:hidden;
        border:1.5px solid var(--border); flex-shrink:0;
        background:var(--sky-lt); display:flex; align-items:center; justify-content:center;
        color:var(--sky-dk); font-size:15px; font-weight:700;
    }
    .dia-search-avatar img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
    .dia-search-name { font-size:13px; font-weight:500; color:var(--text-1); }
    .dia-search-sub  { font-size:11px; color:var(--text-3); margin-top:1px; }
    .dia-search-empty { text-align:center; padding:1.5rem; font-size:12px; color:var(--text-4); }

    /* ─── MOBILE: daftar obrolan ────────────────────────────── */
    .dia-mobile-list {
        display:none; flex:1; overflow-y:auto; flex-direction:column;
    }
    .dia-mobile-item {
        display:flex; align-items:center; gap:10px;
        padding:12px 1rem; border-bottom:1px solid var(--border-lt);
        text-decoration:none; background:transparent; transition:0.15s;
    }
    button.dia-mobile-item {
        border:none; cursor:pointer; width:100%; text-align:left;
    }
    .dia-mobile-item:hover { background:var(--sky-lt); }
    .dia-mobile-avatar {
        position:relative; width:40px; height:40px; border-radius:50%;
        overflow:hidden; border:1.5px solid var(--border); flex-shrink:0;
        background:var(--sky-lt); display:flex; align-items:center; justify-content:center; color:var(--sky-dk);
    }
    .dia-mobile-avatar img { width:40px; height:40px; border-radius:50%; object-fit:cover; }
    .dia-mobile-dot {
        position:absolute; bottom:1px; right:1px;
        width:10px; height:10px; border-radius:50%;
        background:#d1d5db; border:2px solid var(--card);
    }
    .dia-mobile-dot.online { background:#10b981; }
    .dia-mobile-info { flex:1; min-width:0; }
    .dia-mobile-name { font-size:13px; font-weight:500; color:var(--text-1); }
    .dia-mobile-preview { font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .dia-mobile-meta { display:flex; flex-direction:column; align-items:flex-end; gap:3px; }
    .dia-mobile-time { font-size:10px; color:var(--text-4); }
    .dia-unread-badge {
        background:var(--sky); color:#fff; font-size:9px; font-weight:700;
        min-width:16px; height:16px; border-radius:99px;
        display:inline-flex; align-items:center; justify-content:center; padding:0 3px;
    }
    .dia-mobile-section-label {
        font-size:10px; color:var(--text-4); letter-spacing:0.15em;
        text-transform:uppercase; padding:10px 1rem 4px; font-weight:600;
    }

    @media (max-width:768px) {
        .fb-main { height:calc(100vh - 56px - 84px); }
    }
    @media (max-width:1060px) {
        .dia-chat-wrap { display:none; flex-direction:column; flex:1; overflow:hidden; }
        .dia-chat-wrap.active { display:flex; }
        .dia-mobile-list { display:flex; }
        .dia-mobile-list.hide { display:none; }
        .dia-back-btn { display:block; }
    }
    @media (min-width:1061px) {
        .dia-chat-wrap { display:flex; flex-direction:column; flex:1; overflow:hidden; }
        .dia-mobile-list { display:none !important; }
        .dia-back-btn { display:none !important; }
    }
</style>
@endpush

@section('content')

{{-- MOBILE/TABLET: daftar obrolan/grup --}}
<div class="dia-mobile-list {{ (isset($conversation)||isset($group)) ? 'hide' : '' }}">

    {{-- Search bar --}}
    <div class="dia-search-wrap">
        <div class="dia-search-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="diaSearchInput" class="dia-search-input"
                   placeholder="Cari pengguna untuk ngobrol..."
                   oninput="diaDoSearch(this.value)" autocomplete="off">
            <button class="dia-search-clear" id="diaSearchClear" onclick="diaClearSearch()" style="display:none" aria-label="Hapus">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- Search results (shown while typing) --}}
    <div id="diaSearchResults" class="dia-search-results" style="display:none"></div>

    {{-- Normal content (hidden while searching) --}}
    <div id="diaNormalContent">
        {{-- Online users — tampil di atas obrolan, cek raw atribut last_seen --}}
        @php
        $onlineUsers = $users->filter(function($u) {
            try {
                $seen = $u->getAttributes()['last_seen'] ?? null;
                return $seen && strtotime((string)$seen) > (time() - 120);
            } catch (\Throwable $e) { return false; }
        })->take(8);
        @endphp
        <div class="dia-mobile-section-label" style="display:flex;align-items:center;gap:5px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;"></span>
            Online Sekarang
            @if($onlineUsers->count() > 0)
            <span style="font-size:10px;color:#10b981;font-weight:600;">({{ $onlineUsers->count() }})</span>
            @endif
        </div>
        @forelse($onlineUsers as $u)
        <button class="dia-mobile-item" onclick="diaStartConv({{ $u->id }})">
            <div class="dia-mobile-avatar">
                @if($u->avatar)
                <img src="{{ $u->avatar }}" alt="">
                @else
                <span style="font-size:15px;font-weight:700;">{{ mb_substr($u->name,0,1) }}</span>
                @endif
                <span class="dia-mobile-dot online"></span>
            </div>
            <div class="dia-mobile-info">
                <div class="dia-mobile-name">{{ $u->name }}</div>
                <div class="dia-mobile-preview">Ketuk untuk mulai ngobrol</div>
            </div>
            <div class="dia-mobile-meta">
                <span style="font-size:9px;color:#10b981;font-weight:600;letter-spacing:0.02em;">&#9679; Online</span>
            </div>
        </button>
        @empty
        <div style="font-size:12px;color:var(--text-4);padding:0.5rem 0 0.25rem;text-align:center;">
            Belum ada yang online saat ini
        </div>
        @endforelse

        @if($conversations->count() > 0)
        <div class="dia-mobile-section-label">Obrolan</div>
        @foreach($conversations as $conv)
        @php $other = $conv->getOtherUser(Auth::id()); $convUnread = $unreadCounts[$conv->id] ?? 0; @endphp
        <a href="{{ route('dia.conversation', $conv->id) }}" class="dia-mobile-item">
            <div class="dia-mobile-avatar">
                <img src="{{ $other->avatar ?? asset('images/default-avatar.png') }}" alt="">
                <span class="dia-mobile-dot {{ $other->isOnline() ? 'online' : '' }}"></span>
            </div>
            <div class="dia-mobile-info">
                <div class="dia-mobile-name">{{ $other->name }}</div>
                <div class="dia-mobile-preview">{{ $conv->last_message ?? 'Belum ada pesan' }}</div>
            </div>
            <div class="dia-mobile-meta">
                @if($conv->last_message_at)<span class="dia-mobile-time">{{ $conv->last_message_at->format('H:i') }}</span>@endif
                @if($convUnread > 0)<span class="dia-unread-badge">{{ $convUnread > 99 ? '99+' : $convUnread }}</span>@endif
            </div>
        </a>
        @endforeach
        @endif

        @if($groups->count() > 0)
        <div class="dia-mobile-section-label">Grup</div>
        @foreach($groups as $grp)
        <a href="{{ route('dia.group', $grp->id) }}" class="dia-mobile-item">
            <div class="dia-mobile-avatar" style="background:var(--sky-lt);color:var(--sky-dk);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="dia-mobile-info">
                <div class="dia-mobile-name">{{ $grp->name }}</div>
                <div class="dia-mobile-preview">{{ $grp->last_message ?? 'Belum ada pesan' }}</div>
            </div>
            @if($grp->last_message_at)
            <div class="dia-mobile-meta"><span class="dia-mobile-time">{{ $grp->last_message_at->format('H:i') }}</span></div>
            @endif
        </a>
        @endforeach
        @endif

        @if($conversations->count() === 0 && $groups->count() === 0 && $onlineUsers->count() === 0)
        <div style="text-align:center;padding:3rem 1rem;color:var(--text-4);font-size:12px;">
            <div style="font-size:32px;margin-bottom:8px;">💬</div>
            Belum ada obrolan.<br>Cari pengguna di atas untuk memulai.
        </div>
        @endif
    </div>{{-- /#diaNormalContent --}}

</div>

{{-- Hidden form for POST /dia/start/{userId} --}}
<form id="diaStartForm" method="POST" action="" style="display:none">
    @csrf
</form>

{{-- CHAT WINDOW --}}
<div class="dia-chat-wrap {{ (isset($conversation)||isset($group)) ? 'active' : '' }}">

@if(isset($conversation))
@php $other = $conversation->getOtherUser(Auth::id()); @endphp

<div class="dia-header">
    <button class="dia-back-btn" onclick="window.location='{{ route('dia') }}'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <div class="dia-header-avatar">
        <img src="{{ $other->avatar ?? asset('images/default-avatar.png') }}" alt="">
        <span class="dia-header-dot {{ $other->isOnline() ? 'online' : '' }}"></span>
    </div>
    <div>
        <div class="dia-header-name">{{ $other->name }}</div>
        <div class="dia-header-sub">
            <span class="dot-sm {{ $other->isOnline() ? 'online' : '' }}"></span>
            {{ $other->lastSeenLabel() }}
        </div>
    </div>

    <button class="dia-info-btn" onclick="diaToggleLocInfo(event)" title="Info lokasi lawan bicara">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    </button>
    <div class="dia-loc-popup" id="diaLocPopup" onclick="event.stopPropagation()">
        <div class="dia-loc-title">📍 Lokasi {{ $other->name }}</div>
        @if($other->city)
        <div class="dia-loc-city">{{ $other->city }}</div>
        <div class="dia-loc-note">Lokasi GPS saat terakhir kirim pesan · {{ $other->lastSeenLabel() }}</div>
        @else
        <div class="dia-loc-note">Belum ada data lokasi — lawan bicara belum mengizinkan GPS / belum kirim pesan sejak fitur ini aktif.</div>
        @endif
        <div class="dia-loc-disc">ℹ️ Dari GPS (akurat ~kecamatan), terisi saat lawan bicara mengirim pesan & mengizinkan lokasi. Sinyal anti-penipuan — tetap waspada.</div>
    </div>
</div>

<div class="dia-messages" id="diaMessages">
    @forelse($conversation->messages as $msg)
    <div class="dia-msg {{ $msg->user_id===Auth::id() ? 'mine' : 'others' }}" data-id="{{ $msg->id }}">
        @if($msg->user_id !== Auth::id())
        <img src="{{ $msg->user->avatar ?? asset('images/default-avatar.png') }}" class="dia-msg-avatar" alt="">
        @endif
        <div class="dia-msg-wrap">
            @if($msg->user_id !== Auth::id())
            <div class="dia-msg-name">{{ $msg->user->name }}</div>
            @endif
            @if($msg->media_type)@include('fanbase.partials.dia-media', ['url' => asset($msg->media_url), 'type' => $msg->media_type])@endif
            @if($msg->body)<div class="dia-msg-bubble">{{ $msg->body }}</div>@endif
            <div class="dia-msg-time">{{ $msg->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @empty
    <div style="text-align:center;color:var(--text-4);font-size:13px;margin:auto;">Mulai percakapan!</div>
    @endforelse
</div>

<div class="dia-input-area">
    <div class="dia-mention-list" id="diaMentionList"></div>
    <input type="file" id="diaFileInput" accept="image/*,video/*" style="display:none" onchange="diaFileChosen(this)">
    <div class="dia-input-row">
        <button type="button" class="dia-tool-btn" onclick="diaPickMedia()" title="Kirim foto/video">&#128206;</button>
        <button type="button" class="dia-tool-btn" id="diaVoiceBtn" onclick="diaToggleVoice()" title="Voice note">&#127908;</button>
        <textarea class="dia-input" id="diaInput"
            placeholder="Ketik pesan... (@nama untuk undang)" rows="1"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSend();}"
            oninput="diaCheckMention(this);diaAutoGrow(this);"></textarea>
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
    <div class="dia-header-avatar" style="background:var(--sky-lt);color:var(--sky-dk);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div>
        <div class="dia-header-name">{{ $group->name }}</div>
        <div class="dia-header-sub">{{ $group->members->count() }} anggota</div>
    </div>
</div>

<div class="dia-messages" id="diaMessages">
    @forelse($group->messages as $msg)
    <div class="dia-msg {{ $msg->user_id===Auth::id() ? 'mine' : 'others' }}" data-id="{{ $msg->id }}">
        @if($msg->user_id !== Auth::id())
        <img src="{{ $msg->user->avatar ?? asset('images/default-avatar.png') }}" class="dia-msg-avatar" alt="">
        @endif
        <div class="dia-msg-wrap">
            @if($msg->user_id !== Auth::id())
            <div class="dia-msg-name">{{ $msg->user->name }}</div>
            @endif
            @if($msg->media_type)@include('fanbase.partials.dia-media', ['url' => asset($msg->media_url), 'type' => $msg->media_type])@endif
            @if($msg->body)<div class="dia-msg-bubble">{{ $msg->body }}</div>@endif
            <div class="dia-msg-time">{{ $msg->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @empty
    <div style="text-align:center;color:var(--text-4);font-size:13px;margin:auto;">Belum ada pesan.</div>
    @endforelse
</div>

<div class="dia-input-area">
    <input type="file" id="diaFileInput" accept="image/*,video/*" style="display:none" onchange="diaFileChosen(this)">
    <div class="dia-input-row">
        <button type="button" class="dia-tool-btn" onclick="diaPickMedia()" title="Kirim foto/video">&#128206;</button>
        <button type="button" class="dia-tool-btn" id="diaVoiceBtn" onclick="diaToggleVoice()" title="Voice note">&#127908;</button>
        <textarea class="dia-input" id="diaInput"
            placeholder="Ketik ke grup... (Shift+Enter baris baru)" rows="1"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();diaSendGroup();}"
            oninput="diaAutoGrow(this);"></textarea>
        <button class="dia-send-btn" onclick="diaSendGroup()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
    </div>
</div>

@else

<div class="dia-empty">
    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <p>Pilih obrolan dari sidebar kanan,<br>atau klik nama member yang online.</p>
</div>

@endif
</div>

@endsection

@push('scripts')
<script>
var csrfToken = '{{ csrf_token() }}';

/* Popup info lokasi lawan bicara */
function diaToggleLocInfo(e) {
    if (e) e.stopPropagation();
    var p = document.getElementById('diaLocPopup');
    if (p) p.classList.toggle('open');
}
document.addEventListener('click', function(){
    var p = document.getElementById('diaLocPopup');
    if (p) p.classList.remove('open');
});

/* Tangkap lokasi GPS (akurat) sekali per sesi, dipanggil saat kirim pesan */
var diaLocSent = false;
function diaCaptureGps() {
    if (diaLocSent || !navigator.geolocation) return;
    diaLocSent = true;
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            fetch('{{ route('geocode') }}?lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude,
                  { headers: { 'Accept': 'application/json' } })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    var city = d.city || '';
                    if (city) {
                        fetch('{{ route('dia.locate') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ city: city })
                        }).catch(function(){});
                    }
                })
                .catch(function(){});
        },
        function(){ diaLocSent = false; },   // ditolak/gagal → biar bisa coba lagi nanti
        { enableHighAccuracy: true, timeout: 9000, maximumAge: 300000 }
    );
}

/* ========= MEDIA CHAT (foto / voice / video) =========
   Server hanya transit (auto-hapus >24 jam). Media disimpan di cache device
   (IndexedDB) tiap pengguna — kalau dihapus dari device, tak bisa ditampilkan. */
var DIA_DB = null;
function idbOpen() {
    return new Promise(function(res, rej){
        if (DIA_DB) return res(DIA_DB);
        if (!window.indexedDB) return rej('no-idb');
        var rq = indexedDB.open('diaMedia', 1);
        rq.onupgradeneeded = function(e){ e.target.result.createObjectStore('media'); };
        rq.onsuccess = function(e){ DIA_DB = e.target.result; res(DIA_DB); };
        rq.onerror = function(){ rej(rq.error); };
    });
}
function idbGet(key) {
    return idbOpen().then(function(db){ return new Promise(function(res){
        try { var r = db.transaction('media','readonly').objectStore('media').get(key);
            r.onsuccess = function(){ res(r.result || null); }; r.onerror = function(){ res(null); };
        } catch(e){ res(null); }
    }); });
}
function idbPut(key, blob) {
    return idbOpen().then(function(db){ return new Promise(function(res){
        try { var r = db.transaction('media','readwrite').objectStore('media').put(blob, key);
            r.onsuccess = function(){ res(1); }; r.onerror = function(){ res(0); };
        } catch(e){ res(0); }
    }); }).catch(function(){});
}
function diaMediaKey(url){ return url.substring(url.lastIndexOf('/') + 1); }

function diaMediaHtml(url, type) {
    if (!url || !type) return '';
    var k = escHtml(diaMediaKey(url)), s = escHtml(url);
    if (type === 'image') return '<a class="dia-media" href="javascript:void(0)" onclick="diaOpenImg(this)"><img class="dia-media-img" data-mkey="'+k+'" data-msrc="'+s+'" alt="foto"></a>';
    if (type === 'audio') return '<audio class="dia-media-audio" controls preload="none" data-mkey="'+k+'" data-msrc="'+s+'"></audio>';
    if (type === 'video') return '<video class="dia-media-video" controls preload="metadata" data-mkey="'+k+'" data-msrc="'+s+'"></video>';
    return '';
}
function diaHydrateMedia(root) {
    if (!root) return;
    root.querySelectorAll('[data-msrc]').forEach(function(el){
        if (el.getAttribute('data-hyd')) return;
        el.setAttribute('data-hyd','1');
        var key = el.getAttribute('data-mkey'), src = el.getAttribute('data-msrc');
        idbGet(key).then(function(blob){
            if (blob) { el.src = URL.createObjectURL(blob); return; }
            fetch(src).then(function(r){ return r.ok ? r.blob() : null; }).then(function(b){
                if (b) { idbPut(key, b); el.src = URL.createObjectURL(b); }
                else { el.src = src; }
            }).catch(function(){ el.src = src; });
        }).catch(function(){ el.src = src; });
    });
}
function diaOpenImg(a){ var i = a.querySelector('img'); if (i && i.src) window.open(i.src, '_blank'); }

function diaPickMedia(){ var inp = document.getElementById('diaFileInput'); if (inp) inp.click(); }
function diaFileChosen(input) {
    var f = input.files && input.files[0]; input.value = '';
    if (!f) return;
    var type = f.type.indexOf('video') === 0 ? 'video' : (f.type.indexOf('image') === 0 ? 'image' : (f.type.indexOf('audio') === 0 ? 'audio' : ''));
    if (!type) { alert('Jenis file tidak didukung.'); return; }
    if (f.size > 10 * 1024 * 1024) { alert('File maksimal 10MB.'); return; }
    diaUploadSend(f, type);
}
function diaUploadSend(blob, type) {
    var fd = new FormData();
    var fname = blob.name || (type + '.' + (type === 'audio' ? 'webm' : type === 'video' ? 'mp4' : 'jpg'));
    fd.append('file', blob, fname);
    fd.append('type', type);
    diaSending = true;
    fetch('{{ route('dia.upload') }}', { method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}, body: fd })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (!d || !d.ok) { alert('Upload gagal.'); return; }
            idbPut(diaMediaKey(d.url), blob);        // simpan ke cache device (pengirim)
            diaSendMediaMessage(d.path, type);
        })
        .catch(function(){ alert('Upload gagal.'); })
        .finally(function(){ diaSending = false; });
}
function diaSendMediaMessage(path, type) {
    var url = (typeof convId !== 'undefined') ? ('/dia/conversation/' + convId + '/send')
            : ((typeof groupId !== 'undefined') ? ('/dia/group/' + groupId + '/send') : null);
    if (!url) return;
    fetch(url, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ body:'', media_url: path, media_type: type })
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.success && d.message) {
            diaAppend(d.message, true);
            if (d.message.id && d.message.id > diaLastMsgId) diaLastMsgId = d.message.id;
        }
    }).catch(function(){});
}

/* Voice note (rekam via MediaRecorder) */
var diaRec = null, diaRecChunks = [];
function diaToggleVoice() {
    var btn = document.getElementById('diaVoiceBtn');
    if (diaRec && diaRec.state === 'recording') { diaRec.stop(); return; }
    if (!navigator.mediaDevices || !window.MediaRecorder) { alert('Perekaman suara tidak didukung browser ini.'); return; }
    navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream){
        diaRecChunks = [];
        diaRec = new MediaRecorder(stream);
        diaRec.ondataavailable = function(e){ if (e.data && e.data.size) diaRecChunks.push(e.data); };
        diaRec.onstop = function(){
            stream.getTracks().forEach(function(t){ t.stop(); });
            if (btn) { btn.classList.remove('recording'); btn.textContent = '🎤'; }
            var blob = new Blob(diaRecChunks, { type: 'audio/webm' });
            if (blob.size > 0) diaUploadSend(blob, 'audio');
        };
        diaRec.start();
        if (btn) { btn.classList.add('recording'); btn.textContent = '⏹'; }
    }).catch(function(){ alert('Izin mikrofon ditolak.'); });
}
@php
try {
    $_diaUsersArr = $users->map(function($u) {
        try {
            $seen   = $u->getAttributes()['last_seen'] ?? null;
            $online = $seen && strtotime((string)$seen) > (time() - 120);
        } catch (\Throwable $e) { $online = false; }
        return ['id'=>(int)$u->id,'name'=>(string)$u->name,'avatar'=>(string)($u->avatar??''),'online'=>(bool)$online];
    })->values()->all();
    $_diaUsersJson = json_encode($_diaUsersArr, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) { $_diaUsersJson = '[]'; }
@endphp
var diaUsers = {!! $_diaUsersJson !!};

@if(isset($conversation)) var convId  = {{ $conversation->id }};
@php $diaOtherU = ((int)$conversation->user_one_id === (int)auth()->id()) ? $conversation->userTwo : $conversation->userOne; @endphp
window.diaBot = {{ ($diaOtherU && $diaOtherU->google_id === 'bot-margonoandi') ? 'true' : 'false' }};
@endif
@if(isset($group))        var groupId = {{ $group->id }};        @endif

var diaLastMsgId = 0;
var diaPollTimer  = null;
var diaSending    = false;

document.addEventListener('DOMContentLoaded', function() {
    var msgs = document.getElementById('diaMessages');
    if (msgs) {
        msgs.scrollTop = msgs.scrollHeight;
        msgs.querySelectorAll('[data-id]').forEach(function(el) {
            var id = parseInt(el.getAttribute('data-id'), 10);
            if (id > diaLastMsgId) diaLastMsgId = id;
        });
        diaHydrateMedia(msgs);   // muat media (cache device → server) untuk pesan lama
    }
    startPolling();
    diaPing();
    setInterval(diaPing, 30000);
});

function diaPing() {
    fetch('/dia/ping', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'}
    }).catch(function(){});
}

function startPolling() {
    if (typeof convId === 'undefined' && typeof groupId === 'undefined') return;
    clearInterval(diaPollTimer);
    diaPollTimer = setInterval(diaPoll, 4000);
}

function diaPoll() {
    var url;
    if (typeof convId !== 'undefined') {
        url = '/dia/conversation/' + convId + '/poll?after=' + diaLastMsgId;
    } else if (typeof groupId !== 'undefined') {
        url = '/dia/group/' + groupId + '/poll?after=' + diaLastMsgId;
    } else { return; }

    fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
    .then(function(r){ return r.ok ? r.json() : null; })
    .then(function(data){
        if (!data || !data.messages || data.messages.length === 0) return;
        data.messages.forEach(function(msg){
            if (diaMessageExists(msg.id)) return; // cegah duplikasi
            diaAppend(msg, msg.mine);
            if (msg.id > diaLastMsgId) diaLastMsgId = msg.id;
        });
    }).catch(function(){});
}

function diaMessageExists(id) {
    if (!id) return false;
    return !!document.querySelector('#diaMessages [data-id="'+id+'"]');
}

function diaSend() {
    if (diaSending) return;
    var input = document.getElementById('diaInput');
    var body  = input ? input.value.trim() : '';
    if (!body || typeof convId === 'undefined') return;

    diaCaptureGps();
    diaSending = true;
    input.value = '';
    input.style.height = 'auto';
    var ml = document.getElementById('diaMentionList');
    if (ml) ml.classList.remove('show');

    var typingEl = (typeof window.diaBot !== 'undefined' && window.diaBot) ? diaShowTyping() : null;

    fetch('/dia/conversation/' + convId + '/send', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (typingEl) { diaRemoveTyping(typingEl); typingEl = null; }
        if (d.success && d.message) {
            diaAppend(d.message, true);
            if (d.message.id && d.message.id > diaLastMsgId) diaLastMsgId = d.message.id;
        }
        if (d && d.botReply) {
            diaAppend(d.botReply, false);
            if (d.botReply.id && d.botReply.id > diaLastMsgId) diaLastMsgId = d.botReply.id;
        }
    })
    .catch(function(){})
    .finally(function(){ diaSending = false; if (typingEl) diaRemoveTyping(typingEl); });
}

function diaShowTyping() {
    var area = document.getElementById('diaMessages');
    if (!area) return null;
    var div = document.createElement('div');
    div.className = 'dia-msg others dia-typing';
    div.innerHTML = '<div class="dia-msg-wrap"><div class="dia-msg-bubble dia-typing-bubble"><span></span><span></span><span></span></div></div>';
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
    return div;
}
function diaRemoveTyping(el) { if (el && el.parentNode) el.parentNode.removeChild(el); }

function diaSendGroup() {
    if (diaSending) return;
    var input = document.getElementById('diaInput');
    var body  = input ? input.value.trim() : '';
    if (!body || typeof groupId === 'undefined') return;

    diaCaptureGps();
    diaSending = true;
    input.value = '';
    input.style.height = 'auto';

    fetch('/dia/group/' + groupId + '/send', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({body:body})
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.success && d.message) {
            diaAppend(d.message, true);
            if (d.message.id && d.message.id > diaLastMsgId) diaLastMsgId = d.message.id;
        }
    })
    .catch(function(){})
    .finally(function(){ diaSending = false; });
}

function diaAppend(msg, isMine) {
    var area = document.getElementById('diaMessages');
    if (!area) return;
    if (msg.id && diaMessageExists(msg.id)) return; // cegah duplikasi

    var div = document.createElement('div');
    div.className = 'dia-msg ' + (isMine ? 'mine' : 'others');
    if (msg.id) div.setAttribute('data-id', String(msg.id));

    var avatarHtml = (!isMine)
        ? '<img src="'+escHtml(msg.avatar||'')+'\" class="dia-msg-avatar" onerror="this.style.display=\'none\'" alt="">'
        : '';

    var bodyHtml = (msg.body && msg.body.length) ? '<div class="dia-msg-bubble">'+escHtml(msg.body)+'</div>' : '';
    div.innerHTML = avatarHtml +
        '<div class="dia-msg-wrap">' +
        (!isMine ? '<div class="dia-msg-name">'+escHtml(msg.name||'')+'</div>' : '') +
        diaMediaHtml(msg.media_url, msg.media_type) +
        bodyHtml +
        '<div class="dia-msg-time">'+escHtml(msg.time||'')+'</div>' +
        '</div>';

    area.appendChild(div);
    diaHydrateMedia(div);
    area.scrollTop = area.scrollHeight;
}

function diaCheckMention(el) {
    var match = el.value.match(/@([\w]*)$/);
    var list  = document.getElementById('diaMentionList');
    if (!list) return;
    if (match) {
        var q = match[1].toLowerCase();
        var filtered = diaUsers.filter(function(u){
            var first = u.name.toLowerCase().split(' ')[0];
            var slug  = u.name.toLowerCase().replace(/\s+/g, '_');
            return first.startsWith(q) || slug.includes(q);
        });
        if (filtered.length > 0) {
            list.innerHTML = filtered.slice(0, 5).map(function(u){
                var slug = u.name.replace(/\s+/g, '_');
                return '<div class="dia-mention-item" onclick="diaInsertMention(\''+escHtml(slug)+'\')">'
                    + (u.avatar ? '<img src="'+escHtml(u.avatar)+'">' : '')
                    + ' ' + escHtml(u.name) + '</div>';
            }).join('');
            list.classList.add('show');
            return;
        }
    }
    list.classList.remove('show');
}

function diaInsertMention(slug) {
    var input = document.getElementById('diaInput');
    if (!input) return;
    input.value = input.value.replace(/@[\w]*$/, '@' + slug + ' ');
    input.focus();
    var list = document.getElementById('diaMentionList');
    if (list) list.classList.remove('show');
}

function diaAutoGrow(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

function escHtml(t) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(String(t)));
    return d.innerHTML;
}

function diaStartConv(userId) {
    var form = document.getElementById('diaStartForm');
    if (!form) return;
    form.action = '/dia/start/' + userId;
    form.submit();
}

function diaDoSearch(q) {
    var clear   = document.getElementById('diaSearchClear');
    var results = document.getElementById('diaSearchResults');
    var normal  = document.getElementById('diaNormalContent');
    if (!results || !normal) return;

    q = (q || '').trim().toLowerCase();

    if (clear) clear.style.display = q ? 'flex' : 'none';

    if (!q) {
        results.style.display = 'none';
        results.innerHTML     = '';
        normal.style.display  = 'block';
        return;
    }

    normal.style.display  = 'none';
    results.style.display = 'block';

    var filtered = diaUsers.filter(function(u) {
        return u.name.toLowerCase().includes(q);
    });

    if (filtered.length === 0) {
        results.innerHTML = '<div class="dia-search-empty">Pengguna "<strong>' + escHtml(q) + '</strong>" tidak ditemukan.</div>';
        return;
    }

    results.innerHTML = filtered.slice(0, 10).map(function(u) {
        var initials = escHtml(u.name.charAt(0).toUpperCase());
        var avatarHtml = u.avatar
            ? '<img src="' + escHtml(u.avatar) + '" alt="">'
            : initials;
        var subHtml = u.online
            ? '<span style="color:#10b981;font-weight:600;">&#9679; Online</span>'
            : 'Mulai percakapan';
        return '<button class="dia-search-result-item" onclick="diaStartConv(' + u.id + ')">'
            + '<div class="dia-search-avatar">' + avatarHtml + '</div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div class="dia-search-name">' + escHtml(u.name) + '</div>'
            + '<div class="dia-search-sub">' + subHtml + '</div>'
            + '</div>'
            + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>'
            + '</button>';
    }).join('');
}

function diaClearSearch() {
    var input = document.getElementById('diaSearchInput');
    if (input) { input.value = ''; input.focus(); }
    diaDoSearch('');
}
</script>
@endpush
