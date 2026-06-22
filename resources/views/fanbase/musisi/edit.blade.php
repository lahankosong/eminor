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

<form method="POST" action="{{ route('musisi.save') }}" enctype="multipart/form-data">
    @csrf
    <div class="mp-card">
        @php
            $prevSrc = ($profile && $profile->photo)
                ? (\Illuminate\Support\Str::startsWith($profile->photo, ['http://','https://']) ? $profile->photo : asset($profile->photo))
                : (auth()->user()->avatar ?? asset('images/default-avatar.png'));
        @endphp
        <div class="mp-fg">
            <label>Foto Profil</label>
            <div style="display:flex;align-items:center;gap:14px;">
                <img id="mpPhotoPrev" src="{{ $prevSrc }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'"
                     style="width:74px;height:74px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0;">
                <div style="flex:1;min-width:0;">
                    <input type="file" id="mpPhotoInput" name="photo" accept="image/*" class="mp-input" onchange="mpOpenCrop(this)" style="padding:7px 10px;">
                    <span class="hint">JPG/PNG/WEBP, maks 3 MB. Kosongkan = pakai foto akun Google.</span>
                </div>
            </div>
            @if($profile && $profile->photo)
            <label class="mp-check" style="margin-top:10px;"><input type="checkbox" name="remove_photo" value="1"> Hapus foto, kembali ke foto Google</label>
            @endif
        </div>
        <div class="mp-row">
            <div class="mp-fg">
                <label>Role / Instrumen</label>
                <input type="text" name="roles" class="mp-input" value="{{ old('roles', ($profile->roles ?? '') ?: ($defaultRoles ?? '')) }}" placeholder="Vokalis, Gitaris">
                <span class="hint">Pisahkan dengan koma.{{ (empty($profile->roles ?? null) && !empty($defaultRoles ?? '')) ? ' Sudah kami isi dari peran pilihanmu — boleh diubah.' : '' }}</span>
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
        <div class="mp-fg">
            <label>☕ Link Dukungan / Saweran <span style="font-weight:400;color:var(--text-3);">(Saweria, Trakteer, dll)</span></label>
            <input type="text" name="tip_url" class="mp-input" value="{{ old('tip_url', $profile->tip_url ?? '') }}" placeholder="https://saweria.co/username">
        </div>
        <div class="mp-row">
            <label class="mp-check"><input type="checkbox" name="open_to_band" value="1" {{ ($profile->open_to_band ?? true) ? 'checked' : '' }}> Terbuka gabung band</label>
            <label class="mp-check"><input type="checkbox" name="open_to_collab" value="1" {{ ($profile->open_to_collab ?? true) ? 'checked' : '' }}> Terbuka kolaborasi</label>
        </div>
        <button type="submit" class="mp-save">{{ $profile ? 'Simpan Perubahan' : 'Buat Profil' }}</button>
    </div>
</form>

<div id="mpCropOv" style="display:none;position:fixed;inset:0;z-index:4000;background:rgba(0,0,0,.82);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1.25rem;max-width:340px;width:100%;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;color:var(--text-1);margin-bottom:.85rem;">Atur foto profil</div>
        <div id="mpCropView" style="position:relative;width:280px;height:280px;max-width:78vw;max-height:78vw;margin:0 auto;border-radius:50%;overflow:hidden;background:#0d1620;touch-action:none;cursor:grab;box-shadow:0 0 0 3px var(--border);">
            <img id="mpCropImg" style="position:absolute;top:0;left:0;transform-origin:0 0;user-select:none;-webkit-user-drag:none;pointer-events:none;max-width:none;" alt="">
        </div>
        <p style="font-size:11px;color:var(--text-3);text-align:center;margin-top:.6rem;">Geser untuk atur posisi · slider untuk zoom</p>
        <div style="display:flex;align-items:center;gap:10px;margin-top:.5rem;">
            <span style="font-size:16px;">🔍</span>
            <input id="mpCropZoom" type="range" min="1" max="3" step="0.01" value="1" style="flex:1;" oninput="mpCropZoomChange(this.value)">
        </div>
        <div style="display:flex;gap:10px;margin-top:1rem;">
            <button type="button" onclick="mpCropCancel()" style="flex:1;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--text-2);border-radius:12px;padding:11px;font-size:14px;font-weight:600;">Batal</button>
            <button type="button" onclick="mpCropApply()" style="flex:1;cursor:pointer;border:none;background:var(--sky);color:#fff;border-radius:12px;padding:11px;font-size:14px;font-weight:600;">Pakai foto</button>
        </div>
    </div>
</div>

<script>
(function(){
    var V=280, img=document.getElementById('mpCropImg'), view=document.getElementById('mpCropView');
    var nw=0, nh=0, base=1, zoom=1, tx=0, ty=0, drag=false, lx=0, ly=0;

    function dispScale(){ return base*zoom; }
    function apply(){ img.style.transform='translate('+tx+'px,'+ty+'px) scale('+dispScale()+')'; }
    function clamp(){
        var dw=nw*dispScale(), dh=nh*dispScale();
        tx=Math.min(0, Math.max(V-dw, tx)); ty=Math.min(0, Math.max(V-dh, ty));
    }

    window.mpOpenCrop=function(input){
        if(!input.files||!input.files[0]) return;
        var r=new FileReader();
        r.onload=function(e){
            img.onload=function(){
                nw=img.naturalWidth; nh=img.naturalHeight;
                base=Math.max(V/nw, V/nh); zoom=1;
                document.getElementById('mpCropZoom').value=1;
                tx=(V-nw*base)/2; ty=(V-nh*base)/2; apply();
                document.getElementById('mpCropOv').style.display='flex';
            };
            img.src=e.target.result;
        };
        r.readAsDataURL(input.files[0]);
    };
    window.mpCropZoomChange=function(v){
        var cxSrc=(V/2-tx)/dispScale(), cySrc=(V/2-ty)/dispScale(); // titik tengah view (koord sumber)
        zoom=parseFloat(v);
        tx=V/2-cxSrc*dispScale(); ty=V/2-cySrc*dispScale(); clamp(); apply();
    };
    function start(x,y){ drag=true; lx=x; ly=y; view.style.cursor='grabbing'; }
    function move(x,y){ if(!drag) return; tx+=x-lx; ty+=y-ly; lx=x; ly=y; clamp(); apply(); }
    function end(){ drag=false; view.style.cursor='grab'; }
    view.addEventListener('mousedown',function(e){ start(e.clientX,e.clientY); });
    window.addEventListener('mousemove',function(e){ move(e.clientX,e.clientY); });
    window.addEventListener('mouseup',end);
    view.addEventListener('touchstart',function(e){ var t=e.touches[0]; if(t) start(t.clientX,t.clientY); },{passive:true});
    view.addEventListener('touchmove',function(e){ var t=e.touches[0]; if(t){ move(t.clientX,t.clientY); e.preventDefault(); } },{passive:false});
    view.addEventListener('touchend',end);

    window.mpCropCancel=function(){
        document.getElementById('mpCropOv').style.display='none';
        var input=document.getElementById('mpPhotoInput'); if(input) input.value='';
    };
    window.mpCropApply=function(){
        var out=512, cv=document.createElement('canvas'); cv.width=out; cv.height=out; var ctx=cv.getContext('2d');
        var s=dispScale(), sx=-tx/s, sy=-ty/s, sSize=V/s;
        ctx.fillStyle='#0d1620'; ctx.fillRect(0,0,out,out);
        try{ ctx.drawImage(img, sx, sy, sSize, sSize, 0, 0, out, out); }catch(e){}
        cv.toBlob(function(blob){
            if(!blob){ alert('Gagal memproses foto.'); return; }
            try{
                var dt=new DataTransfer(); dt.items.add(new File([blob],'foto-profil.jpg',{type:'image/jpeg'}));
                document.getElementById('mpPhotoInput').files=dt.files;
            }catch(e){}
            var prev=document.getElementById('mpPhotoPrev'); if(prev) prev.src=cv.toDataURL('image/jpeg',0.9);
            document.getElementById('mpCropOv').style.display='none';
        },'image/jpeg',0.9);
    };
})();
</script>

@endsection
