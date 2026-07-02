@extends('layouts.fanbase')
@section('title', 'Pasang Lowongan')

@push('styles')
<style>
    .bc-head { display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; }
    .bc-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; }
    .bc-head h2 { font-family: 'Sora',sans-serif; font-size: 1.1rem; color: var(--text-1); }
    .bc-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.25rem; box-shadow: var(--shadow-sm); }
    .bc-fg { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
    .bc-fg label { font-size: 12px; font-weight: 600; color: var(--text-2); }
    .bc-fg .hint { font-size: 11px; color: var(--text-3); }
    .bc-input, .bc-textarea {
        background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
        padding: 10px 12px; font-size: 14px; color: var(--text-1); outline: none; font-family: inherit; width: 100%;
    }
    .bc-input:focus, .bc-textarea:focus { border-color: var(--orange); }
    .bc-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
    .bc-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .bc-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-2); }
    .bc-save { background: var(--orange); color: #fff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 6px; }
    @media(max-width:560px){ .bc-row{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="bc-head">
    <a href="{{ route('band.index') }}" class="bc-back">← Cari Personil</a>
    <h2>Pasang Lowongan</h2>
</div>

@if($errors->any())
<div style="background:#2e0d0d;color:#f87171;border:1px solid #991b1b;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('band.store') }}">
    @csrf
    <div class="bc-card">
        <div class="bc-fg">
            <label>Judul *</label>
            <input type="text" name="title" class="bc-input" value="{{ old('title') }}" placeholder="Butuh gitaris untuk band indie" required>
        </div>
        <div class="bc-fg">
            <label>Deskripsi</label>
            <textarea name="description" class="bc-textarea" placeholder="Jelaskan band, referensi, komitmen latihan, dll...">{{ old('description') }}</textarea>
        </div>
        <div class="bc-row">
            <div class="bc-fg">
                <label>Role dibutuhkan</label>
                <input type="text" name="roles_needed" class="bc-input" value="{{ old('roles_needed') }}" placeholder="Gitaris, Drummer">
                <span class="hint">Pisahkan dengan koma.</span>
            </div>
            <div class="bc-fg">
                <label>Genre</label>
                <input type="text" name="genres" class="bc-input" value="{{ old('genres') }}" placeholder="Indie, Rock">
                <span class="hint">Pisahkan dengan koma.</span>
            </div>
        </div>
        <div class="bc-fg">
            <label>Lokasi</label>
            <input type="text" name="location" class="bc-input" value="{{ old('location') }}" placeholder="Jakarta">
        </div>
        <label class="bc-check"><input type="checkbox" name="urgent" value="1" {{ old('urgent') ? 'checked' : '' }}> Tandai URGENT</label>
        <button type="submit" class="bc-save">Pasang Lowongan</button>
    </div>
</form>

@endsection
