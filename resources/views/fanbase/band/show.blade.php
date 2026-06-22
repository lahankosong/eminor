@extends('layouts.fanbase')
@section('title', $post->title)

@push('styles')
<style>
    .bs-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; display: inline-block; margin-bottom: 1rem; }
    .bs-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-sm); }
    .bs-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
    .bs-title { font-family: 'Sora',sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--text-1); flex: 1; min-width: 0; }
    .bs-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 700; }
    .bs-badge.urgent { background: #fee2e2; color: #b91c1c; }
    .bs-badge.open { background: #dcfce7; color: #15803d; }
    [data-theme="dark"] .bs-badge.urgent { background: rgba(239,68,68,0.18); color: #fca5a5; }
    [data-theme="dark"] .bs-badge.open { background: rgba(34,197,94,0.18); color: #86efac; }
    .bs-badge.closed { background: var(--surface); color: var(--text-3); }
    .bs-author { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-2); margin-bottom: 1rem; }
    .bs-author img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
    .bs-sec { margin-top: 1rem; }
    .bs-sec-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-3); margin-bottom: 4px; }
    .bs-desc { font-size: 14px; color: var(--text-1); line-height: 1.6; white-space: pre-wrap; }
    .bs-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .bs-tag { font-size: 12px; padding: 3px 10px; border-radius: 20px; background: var(--surface); color: var(--orange); border: 1px solid var(--border-lt); font-weight: 600; }
    .bs-tag.g { color: var(--text-2); font-weight: 400; }
    .bs-actions { margin-top: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .bs-btn { padding: 11px 22px; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }
    .bs-btn-primary { background: var(--orange); color: #fff; }
    .bs-btn-ghost { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
    .bs-btn-danger { background: transparent; color: #ef4444; border: 1px solid var(--border); }
</style>
@endpush

@section('content')

<a href="{{ route('band.index') }}" class="bs-back">← Cari Personil</a>

@if(session('success'))
<div style="background:#0d2e1a;color:#4ade80;border:1px solid #166534;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">{{ session('success') }}</div>
@endif

<div class="bs-card">
    <div class="bs-head">
        <span class="bs-title">{{ $post->title }}</span>
        @if($post->urgent && $post->status === 'open')<span class="bs-badge urgent">URGENT</span>@endif
        <span class="bs-badge {{ $post->status }}">{{ $post->status === 'open' ? 'Dibuka' : 'Ditutup' }}</span>
    </div>
    <div class="bs-author">
        <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
        <span>{{ $post->user->name ?? 'Member' }}</span>
        @if($post->location)<span>· 📍 {{ $post->location }}</span>@endif
        <span>· {{ $post->created_at->diffForHumans() }}</span>
    </div>

    @if($post->rolesArray())
    <div class="bs-sec">
        <div class="bs-sec-label">Dibutuhkan</div>
        <div class="bs-tags">@foreach($post->rolesArray() as $r)<span class="bs-tag">🎵 {{ $r }}</span>@endforeach</div>
    </div>
    @endif

    @if($post->genresArray())
    <div class="bs-sec">
        <div class="bs-sec-label">Genre</div>
        <div class="bs-tags">@foreach($post->genresArray() as $g)<span class="bs-tag g">{{ $g }}</span>@endforeach</div>
    </div>
    @endif

    @if($post->description)
    <div class="bs-sec">
        <div class="bs-sec-label">Deskripsi</div>
        <div class="bs-desc">{{ $post->description }}</div>
    </div>
    @endif

    <div class="bs-actions">
        @if($post->user_id === auth()->id())
            <form method="POST" action="{{ route('band.status', $post->id) }}">
                @csrf @method('PUT')
                <button type="submit" class="bs-btn bs-btn-ghost">{{ $post->status === 'open' ? '🔒 Tutup lowongan' : '🔓 Buka lagi' }}</button>
            </form>
            <form method="POST" action="{{ route('band.destroy', $post->id) }}" onsubmit="return confirm('Hapus lowongan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="bs-btn bs-btn-danger">Hapus</button>
            </form>
        @else
            @if($post->status === 'open')
            <form method="POST" action="{{ route('dia.start', $post->user_id) }}">
                @csrf
                <button type="submit" class="bs-btn bs-btn-primary">💬 Lamar / Hubungi</button>
            </form>
            @else
            <span class="bs-btn bs-btn-ghost" style="cursor:default;">Lowongan ditutup</span>
            @endif
        @endif
    </div>
</div>

@if(($matches ?? collect())->count() > 0)
<div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1.25rem;box-shadow:var(--shadow-sm);margin-top:1.25rem;">
    <div style="font-family:'Sora',sans-serif;font-weight:700;color:var(--text-1);font-size:15px;">🤝 Musisi yang cocok</div>
    <p style="font-size:12px;color:var(--text-3);margin:3px 0 1rem;">Dicocokkan otomatis dari peran yang dicari{{ $post->location ? ' & lokasi' : '' }}.</p>
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($matches as $m)
        <div style="display:flex;align-items:center;gap:12px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:10px 12px;">
            <img src="{{ $m->user->avatar ?? asset('images/default-avatar.png') }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'" style="width:46px;height:46px;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="">
            <div style="flex:1;min-width:0;">
                <div style="font-weight:600;color:var(--text-1);font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $m->user->name ?? 'Musisi' }}</div>
                <div style="font-size:11px;color:var(--text-3);margin-top:1px;">
                    @foreach($m->match_roles as $r)<b style="color:var(--sky-dk);">{{ ucfirst($r) }}</b>@if(!$loop->last) · @endif @endforeach
                    @if($m->match_loc)<span> · 📍 {{ $m->location }}</span>@endif
                </div>
            </div>
            <a href="{{ route('musisi.show', $m->id) }}" style="font-size:12px;padding:6px 11px;border-radius:10px;background:var(--card);border:1px solid var(--border);color:var(--text-2);text-decoration:none;white-space:nowrap;">Lihat</a>
            @if($post->user_id === auth()->id())
            <form method="POST" action="{{ route('dia.start', $m->user_id) }}" style="margin:0;">
                @csrf
                <input type="hidden" name="intro" value="Halo! 👋 Aku lihat profilmu cocok untuk lowongan '{{ $post->title }}' yang aku posting di Margonoandi. Tertarik gabung?">
                <button type="submit" style="font-size:12px;padding:6px 12px;border-radius:10px;background:var(--sky);color:#fff;border:none;cursor:pointer;white-space:nowrap;">💬 Ajak</button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
