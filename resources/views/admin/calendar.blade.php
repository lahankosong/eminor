@extends('layouts.app')

@push('styles')
<style>
    .cal-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
        margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
    }
    .cal-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .cal-header p  { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .btn-back { font-size: 12px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 14px; border-radius: 8px; }
    .btn-back:hover { color: var(--text); border-color: var(--text-3); }

    .alert-success { background: #0d2e1a; color: #4ade80; border: 1px solid #166534; padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 13px; }

    .cal-layout { display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start; }

    /* Form */
    .cal-form { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; position: sticky; top: 1rem; }
    .cal-form h3 { font-size: 12px; color: var(--text-3); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 1rem; }
    .fg { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .fg label { font-size: 11px; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
    .fi {
        background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; padding: 8px 10px; outline: none; font-family: inherit; width: 100%;
    }
    .fi:focus { border-color: var(--text-3); }
    textarea.fi { resize: vertical; min-height: 60px; line-height: 1.6; }
    .platforms-grid { display: flex; flex-wrap: wrap; gap: 6px; }
    .pf-check { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-2);
        background: var(--bg-3); border: 1px solid var(--border); border-radius: 6px; padding: 5px 9px; cursor: pointer; }
    .pf-check input { cursor: pointer; }
    .btn-save { width: 100%; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: 500; background: var(--text); color: var(--bg); border: none; cursor: pointer; transition: 0.2s; margin-top: 4px; }
    .btn-save:hover { filter: brightness(0.88); }

    /* List */
    .cal-list { min-width: 0; }
    .day-group { margin-bottom: 1.5rem; }
    .day-head { font-size: 12px; color: var(--text-2); font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .day-head .dow { color: var(--text-3); font-weight: 400; }
    .day-head.past .dot { background: var(--text-3); }
    .day-head .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); }

    .plan-item {
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 10px;
        padding: 12px 14px; margin-bottom: 8px;
        display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap;
    }
    .plan-main { flex: 1; min-width: 0; }
    .plan-title { font-size: 14px; color: var(--text); font-weight: 500; margin-bottom: 3px; }
    .plan-meta { font-size: 12px; color: var(--text-3); display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .pf-badge { background: var(--bg-3); border: 1px solid var(--border); border-radius: 20px; padding: 1px 8px; font-size: 11px; color: var(--text-2); }
    .plan-notes { font-size: 12px; color: var(--text-3); margin-top: 6px; line-height: 1.5; white-space: pre-wrap; }
    .plan-side { display: flex; align-items: center; gap: 6px; }
    .status-select {
        font-size: 11px; padding: 4px 8px; border-radius: 6px; cursor: pointer;
        border: 1px solid var(--border); background: var(--bg-3); color: var(--text-2); font-family: inherit;
    }
    .status-rencana { color: var(--text-3); }
    .status-proses  { color: #facc15; border-color: #854d0e; }
    .status-selesai { color: #4ade80; border-color: #166534; }
    .btn-del { background: transparent; border: 1px solid var(--border); color: var(--text-3); border-radius: 6px; padding: 4px 9px; font-size: 11px; cursor: pointer; }
    .btn-del:hover { border-color: #ef4444; color: #ef4444; }

    .empty-state { text-align: center; color: var(--text-3); padding: 3rem 1rem; font-size: 13px; line-height: 1.6; }

    .cal-filter { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:1rem; }
    .chip { padding:6px 12px; border-radius:20px; font-size:12px; background:var(--bg-2); border:1px solid var(--border); color:var(--text-2); cursor:pointer; transition:0.15s; }
    .chip:hover { border-color:var(--text-3); color:var(--text); }
    .chip.active { background:var(--text); color:var(--bg); border-color:var(--text); }
    .type-badge { font-size:10px; padding:1px 8px; border-radius:20px; font-weight:600; }
    .type-badge.type-short { background:var(--accent-dim); color:var(--accent); }
    .type-badge.type-long { background:#3b1d0e; color:#fb923c; }
    .type-badge.type-umum { background:var(--bg-3); color:var(--text-3); }

    @media (max-width: 800px) {
        .cal-layout { grid-template-columns: 1fr; }
        .cal-form { position: static; }
    }
</style>
@endpush

@section('content')

<div class="cal-header">
    <div>
        <h2>Content Calendar</h2>
        <p>Rencanakan jadwal posting konten — TikTok, IG, YouTube, dll.</p>
    </div>
    <a href="{{ route('admin.index') }}" class="btn-back">← Panel Admin</a>
</div>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<div class="cal-layout">
    {{-- FORM TAMBAH --}}
    <div class="cal-form">
        <h3>+ Jadwalkan Konten</h3>
        <form method="POST" action="{{ route('admin.calendar.store') }}">
            @csrf
            <div class="fg">
                <label>Tanggal *</label>
                <input type="date" name="plan_date" class="fi" value="{{ now()->toDateString() }}" required>
            </div>
            <div class="fg">
                <label>Ide / Judul konten</label>
                <input type="text" name="title" class="fi" placeholder="Contoh: Hook lirik bait 1">
            </div>
            <div class="fg">
                <label>Lagu terkait</label>
                <select name="song_id" class="fi">
                    <option value="">— Tidak spesifik —</option>
                    @foreach($songs as $song)
                    <option value="{{ $song->id }}">{{ $song->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label>Tipe konten</label>
                <select name="content_type" class="fi">
                    <option value="short">📱 Short Video (9:16)</option>
                    <option value="long">🎬 Video 3–5 menit</option>
                    <option value="umum">Umum / lainnya</option>
                </select>
            </div>
            <div class="fg">
                <label>Platform</label>
                <div class="platforms-grid">
                    @foreach(['TikTok','Instagram','YouTube','Spotify','Discord','Email'] as $pf)
                    <label class="pf-check"><input type="checkbox" name="platforms[]" value="{{ $pf }}"> {{ $pf }}</label>
                    @endforeach
                </div>
            </div>
            <div class="fg">
                <label>Catatan</label>
                <textarea name="notes" class="fi" placeholder="Detail tambahan..."></textarea>
            </div>
            <button type="submit" class="btn-save">Simpan Jadwal</button>
        </form>
    </div>

    {{-- DAFTAR --}}
    <div class="cal-list">
        @php
            $grouped = $plans->groupBy(fn($p) => $p->plan_date->toDateString());
            $today = \Carbon\Carbon::today();
            $dows = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        @endphp

        @if($plans->count())
        <div class="cal-filter">
            <span class="chip active" data-f="all"   onclick="calFilter('all',this)">Semua</span>
            <span class="chip" data-f="short" onclick="calFilter('short',this)">📱 Short</span>
            <span class="chip" data-f="long"  onclick="calFilter('long',this)">🎬 Video panjang</span>
            <span class="chip" data-f="umum"  onclick="calFilter('umum',this)">Umum</span>
        </div>
        @endif

        @forelse($grouped as $date => $items)
            @php $d = \Carbon\Carbon::parse($date); $isPast = $d->lt($today); @endphp
            <div class="day-group">
                <div class="day-head {{ $isPast ? 'past' : '' }}">
                    <span class="dot"></span>
                    {{ $d->format('d M Y') }}
                    <span class="dow">· {{ $dows[$d->dayOfWeek] }}{{ $d->isToday() ? ' (hari ini)' : '' }}</span>
                </div>

                @foreach($items as $plan)
                @php $ct = $plan->content_type ?? 'short'; @endphp
                <div class="plan-item" data-type="{{ $ct }}">
                    <div class="plan-main">
                        <div class="plan-title">{{ $plan->title ?: ($plan->song->title ?? 'Konten') }}</div>
                        <div class="plan-meta">
                            <span class="type-badge type-{{ $ct }}">{{ $ct === 'long' ? '🎬 Panjang' : ($ct === 'short' ? '📱 Short' : 'Umum') }}</span>
                            @if($plan->song)<span class="pf-badge">🎵 {{ $plan->song->title }}</span>@endif
                            @if($plan->platforms)
                                @foreach(explode(',', $plan->platforms) as $pf)
                                <span class="pf-badge">{{ $pf }}</span>
                                @endforeach
                            @endif
                        </div>
                        @if($plan->notes)<div class="plan-notes">{{ $plan->notes }}</div>@endif
                    </div>
                    <div class="plan-side">
                        <form method="POST" action="{{ route('admin.calendar.update', $plan->id) }}">
                            @csrf
                            @method('PUT')
                            <select name="status" class="status-select status-{{ $plan->status }}" onchange="this.form.submit()">
                                <option value="rencana" {{ $plan->status == 'rencana' ? 'selected' : '' }}>Rencana</option>
                                <option value="proses"  {{ $plan->status == 'proses'  ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ $plan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </form>
                        <form method="POST" action="{{ route('admin.calendar.destroy', $plan->id) }}"
                              onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-del">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state">
                <p style="font-size:24px;margin-bottom:0.75rem;">🗓️</p>
                <p>Belum ada jadwal konten.<br>Tambah lewat form di sebelah kiri.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
function calFilter(f, el) {
    document.querySelectorAll('.cal-filter .chip').forEach(function(c){ c.classList.toggle('active', c.getAttribute('data-f') === f); });
    document.querySelectorAll('.day-group').forEach(function(g){
        var items = g.querySelectorAll('.plan-item');
        var shown = 0;
        items.forEach(function(it){
            var vis = (f === 'all') || it.getAttribute('data-type') === f;
            it.style.display = vis ? '' : 'none';
            if (vis) shown++;
        });
        g.style.display = shown ? '' : 'none';
    });
}
</script>

@endsection
