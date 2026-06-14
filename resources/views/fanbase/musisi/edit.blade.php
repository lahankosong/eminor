@extends('layouts.fanbase')
@section('title', 'Profil Musisi')

@push('styles')
<style>
    .mp-head { display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; }
    .mp-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; }
    .mp-head h2 { font-family: 'Sora',sans-serif; font-size: 1.1rem; color: var(--text-1); }
    .mp-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.25rem; box-shadow: var(--shadow-sm); }
    .mp-fg { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
    .mp-fg label { font-size: 12px; font-weight: 600; color: var(--text-2); }
    .mp-fg .hint { font-size: 11px; color: var(--text-3); }
    .mp-input, .mp-textarea, .mp-select {
        background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
        padding: 10px 12px; font-size: 14px; color: var(--text-1); outline: none; font-family: inherit; width: 100%;
    }
    .mp-input:focus, .mp-textarea:focus, .mp-select:focus { border-color: var(--sky); }
    .mp-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }
    .mp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .mp-check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-2); }
    .mp-save { background: var(--sky); color: #fff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 6px; }
    .mp-save:hover { background: var(--sky-dk); }
    @media(max-width:560px){ .mp-row{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="mp-head">
    <a href="{{ route('musisi.index') }}" class="mp-back">← Direktori</a>
    <h2>{{ $profile ? 'Edit Profil Musisi' : 'Lengkapi Profil Musisi' }}</h2>
</div>

@if($errors->any())
<div style="background:#2e0d0d;color:#f87171;border:1px solid #991b1b;padding:10px 14px;border-radius:10px;margin-bottom:1rem;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('musisi.save') }}">
    @csrf
    <div class="mp-card">
        <div class="mp-row">
            <div class="mp-fg">
                <label>Role / Instrumen</label>
                <input type="text" name="roles" class="mp-input" value="{{ old('roles', $profile->roles ?? '') }}" placeholder="Vokalis, Gitaris">
                <span class="hint">Pisahkan dengan koma.</span>
            </div>
            <div class="mp-fg">
                <label>Level</label>
                <select name="skill_level" class="mp-select">
                    @php $lv = old('skill_level', $profile->skill_level ?? ''); @endphp
                    <option value="">— pilih —</option>
                    @foreach(['pemula','menengah','mahir','profesional'] as $l)
                    <option value="{{ $l }}" {{ $lv === $l ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mp-row">
            <div class="mp-fg">
                <label>Genre</label>
                <input type="text" name="genres" class="mp-input" value="{{ old('genres', $profile->genres ?? '') }}" placeholder="Indie, Rock, Folk">
                <span class="hint">Pisahkan dengan koma.</span>
            </div>
            <div class="mp-fg">
                <label>Lokasi</label>
                <input type="text" name="location" class="mp-input" value="{{ old('location', $profile->location ?? '') }}" placeholder="Jakarta">
            </div>
        </div>
        <div class="mp-fg">
            <label>Sedang mencari</label>
            <input type="text" name="looking_for" class="mp-input" value="{{ old('looking_for', $profile->looking_for ?? '') }}" placeholder="Cari band indie / open job session / kolaborator produser">
        </div>
        <div class="mp-fg">
            <label>Bio</label>
            <textarea name="bio" class="mp-textarea" placeholder="Ceritakan pengalaman & gaya bermusikmu...">{{ old('bio', $profile->bio ?? '') }}</textarea>
        </div>
        <div class="mp-row">
            <div class="mp-fg">
                <label>Spotify URL</label>
                <input type="text" name="spotify_url" class="mp-input" value="{{ old('spotify_url', $profile->spotify_url ?? '') }}" placeholder="https://open.spotify.com/...">
            </div>
            <div class="mp-fg">
                <label>YouTube URL</label>
                <input type="text" name="youtube_url" class="mp-input" value="{{ old('youtube_url', $profile->youtube_url ?? '') }}" placeholder="https://youtube.com/@...">
            </div>
        </div>
        <div class="mp-fg">
            <label>Instagram</label>
            <input type="text" name="instagram" class="mp-input" value="{{ old('instagram', $profile->instagram ?? '') }}" placeholder="@username">
        </div>
        <div class="mp-row">
            <label class="mp-check"><input type="checkbox" name="open_to_band" value="1" {{ ($profile->open_to_band ?? true) ? 'checked' : '' }}> Terbuka gabung band</label>
            <label class="mp-check"><input type="checkbox" name="open_to_collab" value="1" {{ ($profile->open_to_collab ?? true) ? 'checked' : '' }}> Terbuka kolaborasi</label>
        </div>
        <button type="submit" class="mp-save">{{ $profile ? 'Simpan Perubahan' : 'Buat Profil' }}</button>
    </div>
</form>

@endsection
