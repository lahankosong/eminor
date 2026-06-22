@extends('layouts.fanbase')
@section('title', 'Direktori Musisi')

@push('styles')
<style>
    /* ── Hero ── */
    .mus-hero { background: linear-gradient(145deg, var(--sky-dk), var(--sky) 60%, var(--sky-mid)); border-radius: 20px; padding: 1.5rem; color: #fff; margin-bottom: 1.25rem; box-shadow: var(--shadow-lg); }
    .mus-hero h2 { font-family: 'Sora',sans-serif; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
    .mus-hero p { font-size: 13px; color: rgba(255,255,255,0.8); }
    .mus-hero-actions { margin-top: 1rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-w { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 5px; }
    .btn-w-solid { background: #fff; color: var(--sky-dk); }
    .btn-w-ghost { background: rgba(255,255,255,0.18); color: #fff; }

    /* ── Tabs ── */
    .mus-tabs { display: flex; gap: 4px; margin-bottom: 1.25rem; background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 4px; }
    .mus-tab { flex: 1; text-align: center; padding: 9px 6px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-3); transition: 0.15s; border: none; background: none; font-family: inherit; }
    .mus-tab.active { background: var(--sky); color: #fff; }
    .mus-panel { display: none; }
    .mus-panel.active { display: block; }

    /* ── Direktori ── */
    .mus-toolbar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1rem; }
    .mus-search { flex: 1; min-width: 200px; background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 10px 14px; font-size: 13px; color: var(--text-1); outline: none; font-family: inherit; }
    .mus-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 1rem; }
    .mus-chip { padding: 6px 12px; border-radius: 20px; font-size: 12px; background: var(--card); border: 1px solid var(--border); color: var(--text-2); cursor: pointer; transition: 0.15s; }
    .mus-chip:hover { border-color: var(--sky); }
    .mus-chip.active { background: var(--sky); color: #fff; border-color: var(--sky); }
    .mus-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px,1fr)); gap: 12px; }
    .mus-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 1rem; text-decoration: none; display: block; transition: 0.15s; box-shadow: var(--shadow-sm); }
    .mus-card:hover { border-color: var(--sky); transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .mus-card-top { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .mus-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); flex-shrink: 0; }
    .mus-name { font-weight: 700; font-size: 14px; color: var(--text-1); line-height: 1.2; }
    .mus-loc { font-size: 11px; color: var(--text-3); margin-top: 1px; }
    .mus-tags { display: flex; flex-wrap: wrap; gap: 4px; margin: 6px 0; }
    .mus-tag { font-size: 11px; padding: 2px 8px; border-radius: 20px; background: var(--surface); color: var(--sky-dk); border: 1px solid var(--border-lt); }
    .mus-tag.g { color: var(--text-3); }
    .mus-look { font-size: 12px; color: var(--text-2); margin-top: 4px; }
    .mus-empty { text-align: center; color: var(--text-3); padding: 2.5rem 1rem; font-size: 13px; }

    /* ── Band / Gig post cards ── */
    .bp-list { display: flex; flex-direction: column; gap: 12px; }
    .bp-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 1rem 1.1rem; box-shadow: var(--shadow-sm); }
    .bp-card-head { display: flex; align-items: flex-start; gap: 10px; }
    .bp-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); flex-shrink: 0; }
    .bp-user { font-size: 12px; color: var(--text-3); }
    .bp-title { font-weight: 700; font-size: 14px; color: var(--text-1); line-height: 1.3; }
    .bp-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; padding: 2px 9px; border-radius: 20px; font-weight: 600; margin-bottom: 6px; }
    .bp-badge.open  { background: #d1fae5; color: #065f46; }
    .bp-badge.closed{ background: #fee2e2; color: #991b1b; }
    .bp-badge.urgent{ background: #fef9c3; color: #854d0e; }
    .bp-type-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; padding: 2px 9px; border-radius: 20px; font-weight: 600; margin-bottom: 6px; background: #ede9fe; color: #5b21b6; }
    .bp-meta { font-size: 12px; color: var(--text-3); margin-top: 5px; display: flex; flex-wrap: wrap; gap: 8px; }
    .bp-desc { font-size: 13px; color: var(--text-2); margin-top: 7px; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }
    .bp-actions { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
    .bp-btn { font-size: 12px; padding: 6px 13px; border-radius: 9px; border: 1px solid var(--border); background: var(--surface); color: var(--text-2); cursor: pointer; font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; }
    .bp-btn:hover { border-color: var(--sky); color: var(--sky-dk); }
    .bp-btn.danger:hover { border-color: #ef4444; color: #ef4444; }
    .bp-btn.primary { background: var(--sky); color: #fff; border-color: var(--sky); font-weight: 600; }
    .bp-new-btn { display: flex; align-items: center; gap: 6px; background: var(--orange); color: #fff; border: none; border-radius: 12px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; margin-bottom: 1.1rem; }
    .bp-new-btn:hover { opacity: .9; }
</style>
@endpush

@section('content')

<div class="mus-hero">
    <h2>🎸 Direktori Musisi</h2>
    <p>Temukan personil, kolaborator, band, dan peluang gig — sesama musisi Margonoandi.</p>
    <div class="mus-hero-actions">
        @if($myProfile)
        <a href="{{ route('musisi.edit') }}" class="btn-w btn-w-ghost">Edit profilku</a>
        <a href="{{ route('musisi.show', $myProfile->id) }}" class="btn-w btn-w-ghost">Lihat profil</a>
        @else
        <a href="{{ route('musisi.edit') }}" class="btn-w btn-w-solid">+ Lengkapi profil musisimu</a>
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:#0d2e1a;color:#4ade80;border:1px solid #166534;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">{{ session('success') }}</div>
@endif

{{-- Tab buttons --}}
@php $activeTab = request('tab', 'direktori'); @endphp
<div class="mus-tabs">
    <button class="mus-tab {{ $activeTab === 'direktori' ? 'active' : '' }}" onclick="switchTab('direktori')">🎸 Direktori</button>
    <button class="mus-tab {{ $activeTab === 'band' ? 'active' : '' }}" onclick="switchTab('band')">🎯 Cari Personil</button>
    <button class="mus-tab {{ $activeTab === 'gig' ? 'active' : '' }}" onclick="switchTab('gig')">📢 Papan Gig</button>
</div>

{{-- ════ TAB: DIREKTORI ════ --}}
<div class="mus-panel {{ $activeTab === 'direktori' ? 'active' : '' }}" id="panel-direktori">
    <div class="mus-toolbar">
        <input type="text" id="musSearch" class="mus-search" placeholder="Cari nama, role, genre, lokasi..." oninput="musFilter()">
    </div>
    <div class="mus-chips" id="musChips">
        <span class="mus-chip active" data-r="all" onclick="musRole('all',this)">Semua</span>
        @foreach(['Vokalis','Gitaris','Bassis','Drummer','Keyboardis','Produser'] as $r)
        <span class="mus-chip" data-r="{{ strtolower($r) }}" onclick="musRole('{{ strtolower($r) }}',this)">{{ $r }}</span>
        @endforeach
    </div>
    <div class="mus-grid" id="musGrid">
        @forelse($profiles as $p)
        <a href="{{ route('musisi.show', $p->id) }}" class="mus-card mus-row"
           data-name="{{ strtolower($p->user->name ?? '') }}"
           data-roles="{{ strtolower($p->roles) }}"
           data-genres="{{ strtolower($p->genres) }}"
           data-location="{{ strtolower($p->location) }}">
            <div class="mus-card-top">
                <img class="mus-avatar" src="{{ $p->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
                <div style="min-width:0;">
                    <div class="mus-name">{{ $p->user->name ?? 'Musisi' }}</div>
                    @if($p->location)<div class="mus-loc">📍 {{ $p->location }}</div>@endif
                </div>
            </div>
            <div class="mus-tags">
                @foreach($p->rolesArray() as $role)<span class="mus-tag">{{ $role }}</span>@endforeach
                @foreach($p->genresArray() as $g)<span class="mus-tag g">{{ $g }}</span>@endforeach
            </div>
            @if($p->looking_for)<div class="mus-look">🔎 {{ $p->looking_for }}</div>@endif
        </a>
        @empty
        <div class="mus-empty">Belum ada musisi terdaftar.<br>Jadilah yang pertama — lengkapi profilmu!</div>
        @endforelse
        <div class="mus-empty" id="musNoMatch" style="display:none;">Tidak ada musisi yang cocok.</div>
    </div>
</div>

{{-- ════ TAB: CARI PERSONIL ════ --}}
<div class="mus-panel {{ $activeTab === 'band' ? 'active' : '' }}" id="panel-band">
    <a href="{{ route('band.create') }}" class="bp-new-btn">🎯 Pasang Lowongan Personil</a>
    <div class="bp-list">
        @forelse($bandPosts as $bp)
        <div class="bp-card">
            <div class="bp-card-head">
                <img class="bp-avatar" src="{{ $bp->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
                <div style="flex:1;min-width:0;">
                    @if($bp->urgent)<span class="bp-badge urgent">⚡ URGENT</span> @endif
                    <span class="bp-badge {{ $bp->status }}">{{ $bp->status === 'open' ? '● Buka' : '✕ Tutup' }}</span>
                    <div class="bp-title">{{ $bp->title }}</div>
                    <div class="bp-user">oleh {{ $bp->user->name ?? 'Musisi' }}</div>
                </div>
            </div>
            <div class="bp-meta">
                @if($bp->roles_needed)<span>🎵 {{ $bp->roles_needed }}</span>@endif
                @if($bp->genres)<span>🎶 {{ $bp->genres }}</span>@endif
                @if($bp->location)<span>📍 {{ $bp->location }}</span>@endif
            </div>
            @if($bp->description)<div class="bp-desc">{{ \Illuminate\Support\Str::limit($bp->description, 200) }}</div>@endif
            <div class="bp-actions">
                <a href="{{ route('band.show', $bp->id) }}" class="bp-btn">Lihat Detail</a>
                @auth
                    @if(Auth::id() !== $bp->user_id)
                    <form method="POST" action="{{ route('dia.start', $bp->user_id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="bp-btn primary">💬 Hubungi</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('band.status', $bp->id) }}" style="display:inline;">
                        @csrf @method('PUT')
                        <button class="bp-btn">{{ $bp->status === 'open' ? 'Tutup' : 'Buka Lagi' }}</button>
                    </form>
                    <form method="POST" action="{{ route('band.destroy', $bp->id) }}" style="display:inline;" onsubmit="return confirm('Hapus lowongan ini?')">
                        @csrf @method('DELETE')
                        <button class="bp-btn danger">Hapus</button>
                    </form>
                    @endif
                @endauth
            </div>
        </div>
        @empty
        <div class="mus-empty">Belum ada lowongan personil.<br>Pasang lowonganmu sekarang!</div>
        @endforelse
    </div>
</div>

{{-- ════ TAB: PAPAN GIG ════ --}}
<div class="mus-panel {{ $activeTab === 'gig' ? 'active' : '' }}" id="panel-gig">
    <a href="{{ route('gig.create') }}" class="bp-new-btn" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">📢 Pasang Pengumuman Gig</a>

    @php
        $typeFilter = request('type_filter', 'all');
    @endphp
    <div class="mus-chips" style="margin-bottom:1rem;">
        <span class="mus-chip {{ $typeFilter === 'all' ? 'active' : '' }}" onclick="gigTypeFilter('all',this)">Semua</span>
        @foreach($gigTypes as $key => $label)
        <span class="mus-chip {{ $typeFilter === $key ? 'active' : '' }}" onclick="gigTypeFilter('{{ $key }}',this)">{{ $label }}</span>
        @endforeach
    </div>

    <div class="bp-list" id="gigList">
        @forelse($gigPosts as $gp)
        <div class="bp-card gig-row" data-type="{{ $gp->type }}">
            <div class="bp-card-head">
                <img class="bp-avatar" src="{{ $gp->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
                <div style="flex:1;min-width:0;">
                    <span class="bp-type-badge">{{ \App\Models\GigPost::typeLabel($gp->type) }}</span>
                    <span class="bp-badge {{ $gp->status }}">{{ $gp->status === 'open' ? '● Buka' : '✕ Tutup' }}</span>
                    <div class="bp-title">{{ $gp->title }}</div>
                    <div class="bp-user">oleh {{ $gp->user->name ?? 'Musisi' }}</div>
                </div>
            </div>
            <div class="bp-meta">
                @if($gp->location)<span>📍 {{ $gp->location }}</span>@endif
                @if($gp->date_event)<span>🗓 {{ $gp->date_event->format('d M Y') }}</span>@endif
            </div>
            @if($gp->description)
            <div class="bp-desc" style="white-space:pre-wrap;max-height:7.5em;overflow:hidden;">{{ $gp->description }}</div>
            @if(mb_strlen($gp->description) > 180)
            <button type="button" style="background:none;border:none;color:var(--sky-dk);font-size:12px;font-weight:600;cursor:pointer;padding:3px 0;font-family:inherit;"
                onclick="var d=this.previousElementSibling;var open=!d.style.maxHeight;d.style.maxHeight=open?'':'7.5em';this.textContent=open?'Tutup ▲':'Selengkapnya ▼';">Selengkapnya ▼</button>
            @endif
            @endif
            <div class="bp-actions">
                @auth
                    @if(Auth::id() !== $gp->user_id)
                    <form method="POST" action="{{ route('dia.start', $gp->user_id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="intro" value="Halo! 👋 Saya tertarik dengan {{ \App\Models\GigPost::typeLabel($gp->type) }} '{{ $gp->title }}' yang kamu posting. Boleh info lebih lanjut?">
                        <button type="submit" class="bp-btn primary">💬 Saya Minat — Hubungi</button>
                    </form>
                    @else
                    <a href="{{ route('gig.edit', $gp->id) }}" class="bp-btn" style="text-decoration:none;">✏️ Edit</a>
                    <form method="POST" action="{{ route('gig.status', $gp->id) }}" style="display:inline;">
                        @csrf @method('PUT')
                        <button class="bp-btn">{{ $gp->status === 'open' ? 'Tutup' : 'Buka Lagi' }}</button>
                    </form>
                    <form method="POST" action="{{ route('gig.destroy', $gp->id) }}" style="display:inline;" onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf @method('DELETE')
                        <button class="bp-btn danger">Hapus</button>
                    </form>
                    @endif
                @endauth
            </div>
        </div>
        @empty
        <div class="mus-empty">Belum ada pengumuman gig.<br>Pasang pengumumanmu sekarang!</div>
        @endforelse
        <div class="mus-empty" id="gigNoMatch" style="display:none;">Tidak ada pengumuman untuk kategori ini.</div>
    </div>
</div>

<script>
// ── Tab switching ──
var _tabs = ['direktori','band','gig'];
function switchTab(name) {
    _tabs.forEach(function(t){
        document.getElementById('panel-' + t).classList.toggle('active', t === name);
        document.querySelectorAll('.mus-tab').forEach(function(btn, i){ btn.classList.toggle('active', i === _tabs.indexOf(name)); });
    });
    history.replaceState(null, '', '?tab=' + name);
}

// ── Direktori filter ──
var musRoleFilter = 'all';
function musRole(r, el) {
    musRoleFilter = r;
    document.querySelectorAll('#musChips .mus-chip').forEach(function(c){ c.classList.toggle('active', c.getAttribute('data-r') === r); });
    musFilter();
}
function musFilter() {
    var q = (document.getElementById('musSearch').value || '').toLowerCase().trim();
    var rows = document.querySelectorAll('.mus-row');
    var shown = 0;
    rows.forEach(function(row){
        var hay = row.getAttribute('data-name') + ' ' + row.getAttribute('data-roles') + ' ' + row.getAttribute('data-genres') + ' ' + row.getAttribute('data-location');
        var okQ = !q || hay.indexOf(q) !== -1;
        var okR = (musRoleFilter === 'all') || (row.getAttribute('data-roles') || '').indexOf(musRoleFilter) !== -1;
        var vis = okQ && okR;
        row.style.display = vis ? '' : 'none';
        if (vis) shown++;
    });
    var nm = document.getElementById('musNoMatch');
    if (nm) nm.style.display = (shown === 0 && rows.length > 0) ? '' : 'none';
}

// ── Gig type filter ──
var _gigTypeFilter = 'all';
function gigTypeFilter(type, el) {
    _gigTypeFilter = type;
    document.querySelectorAll('#panel-gig .mus-chip').forEach(function(c){ c.classList.remove('active'); });
    el.classList.add('active');
    var rows = document.querySelectorAll('.gig-row');
    var shown = 0;
    rows.forEach(function(row){
        var vis = type === 'all' || row.getAttribute('data-type') === type;
        row.style.display = vis ? '' : 'none';
        if (vis) shown++;
    });
    var nm = document.getElementById('gigNoMatch');
    if (nm) nm.style.display = (shown === 0 && rows.length > 0) ? '' : 'none';
}
</script>

@endsection
