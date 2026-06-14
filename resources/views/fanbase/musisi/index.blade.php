@extends('layouts.fanbase')
@section('title', 'Direktori Musisi')

@push('styles')
<style>
    .mus-hero { background: linear-gradient(145deg, var(--sky-dk), var(--sky) 60%, var(--sky-mid)); border-radius: 20px; padding: 1.5rem; color: #fff; margin-bottom: 1.25rem; box-shadow: var(--shadow-lg); }
    .mus-hero h2 { font-family: 'Sora',sans-serif; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
    .mus-hero p { font-size: 13px; color: rgba(255,255,255,0.8); }
    .mus-hero-actions { margin-top: 1rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-w { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
    .btn-w-solid { background: #fff; color: var(--sky-dk); }
    .btn-w-ghost { background: rgba(255,255,255,0.18); color: #fff; }

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
</style>
@endpush

@section('content')

<div class="mus-hero">
    <h2>🎸 Direktori Musisi</h2>
    <p>Temukan personil, kolaborator, atau band — sesama musisi Margonoandi.</p>
    <div class="mus-hero-actions">
        @if($myProfile)
        <a href="{{ route('musisi.edit') }}" class="btn-w btn-w-ghost">Edit profilku</a>
        <a href="{{ route('musisi.show', $myProfile->id) }}" class="btn-w btn-w-solid">Lihat profilku</a>
        @else
        <a href="{{ route('musisi.edit') }}" class="btn-w btn-w-solid">+ Lengkapi profil musisimu</a>
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:#0d2e1a;color:#4ade80;border:1px solid #166534;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">{{ session('success') }}</div>
@endif

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
    <div class="mus-empty" id="musEmpty">
        Belum ada musisi terdaftar.<br>Jadilah yang pertama — lengkapi profilmu!
    </div>
    @endforelse
    <div class="mus-empty" id="musNoMatch" style="display:none;">Tidak ada musisi yang cocok.</div>
</div>

<script>
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
</script>

@endsection
