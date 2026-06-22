@extends('layouts.fanbase')
@section('title', 'Pasang Pengumuman Gig')

@push('styles')
<style>
    .bc-head { display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; }
    .bc-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; }
    .bc-head h2 { font-family: 'Sora',sans-serif; font-size: 1.1rem; color: var(--text-1); }
    .bc-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.25rem; box-shadow: var(--shadow-sm); }
    .bc-fg { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
    .bc-fg label { font-size: 12px; font-weight: 600; color: var(--text-2); }
    .bc-fg .hint { font-size: 11px; color: var(--text-3); }
    .bc-input, .bc-textarea, .bc-select {
        background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
        padding: 10px 12px; font-size: 14px; color: var(--text-1); outline: none; font-family: inherit; width: 100%;
    }
    .bc-input:focus, .bc-textarea:focus, .bc-select:focus { border-color: var(--orange); }
    .bc-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
    .bc-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .bc-save { background: var(--orange); color: #fff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 6px; }
    .bc-save:hover { opacity: .9; }
    @media(max-width:560px){ .bc-row{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="bc-head">
    <a href="{{ route('musisi.index', ['tab' => 'gig']) }}" class="bc-back">← Papan Gig</a>
    <h2>{{ isset($gig) ? 'Edit Pengumuman' : 'Pasang Pengumuman' }}</h2>
</div>

@if($errors->any())
<div style="background:#2e0d0d;color:#f87171;border:1px solid #991b1b;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ isset($gig) ? route('gig.update', $gig->id) : route('gig.store') }}">
    @csrf
    @isset($gig) @method('PUT') @endisset
    <div class="bc-card">
        <div class="bc-fg">
            <label>Kategori *</label>
            <select name="type" class="bc-select" required>
                @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ old('type', $gig->type ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="bc-fg">
            <label>Judul *</label>
            <input type="text" name="title" class="bc-input" value="{{ old('title', $gig->title ?? '') }}"
                   placeholder="Contoh: Open Mic Sabtu Malam di Kota Tua" required>
        </div>
        <div class="bc-fg">
            <label>Deskripsi</label>
            <textarea name="description" class="bc-textarea"
                      placeholder="Jelaskan detail gig, genre yang diinginkan, bayaran, dll...">{{ old('description', $gig->description ?? '') }}</textarea>
        </div>
        <div class="bc-row">
            <div class="bc-fg">
                <label>Lokasi</label>
                <input type="text" name="location" class="bc-input" value="{{ old('location', $gig->location ?? '') }}" placeholder="Jakarta Barat">
            </div>
            <div class="bc-fg">
                <label>Tanggal Acara</label>
                <input type="date" name="date_event" class="bc-input" value="{{ old('date_event', isset($gig) && $gig->date_event ? $gig->date_event->format('Y-m-d') : '') }}">
            </div>
        </div>
        <div class="bc-fg">
            <label>Syarat / Persyaratan</label>
            <textarea name="requirements" class="bc-textarea" style="min-height:70px;"
                      placeholder="Pengalaman minimal, peralatan yang harus dibawa, dll...">{{ old('requirements', $gig->requirements ?? '') }}</textarea>
        </div>
        <button type="submit" class="bc-save">{{ isset($gig) ? '💾 Simpan Perubahan' : '📢 Pasang Pengumuman' }}</button>
    </div>
</form>

@endsection
