@extends('layouts.fanbase')
@section('title', $profile->user->name ?? 'Musisi')

@push('styles')
<style>
    .ms-back { font-size: 13px; color: var(--text-2); text-decoration: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 10px; display: inline-block; margin-bottom: 1rem; }
    .ms-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-sm); }
    .ms-top { display: flex; align-items: center; gap: 14px; margin-bottom: 1rem; }
    .ms-avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--border); }
    .ms-name { font-family: 'Sora',sans-serif; font-size: 1.3rem; font-weight: 700; color: var(--text-1); }
    .ms-loc { font-size: 13px; color: var(--text-3); margin-top: 2px; }
    .ms-tags { display: flex; flex-wrap: wrap; gap: 6px; margin: 4px 0 1rem; }
    .ms-tag { font-size: 12px; padding: 3px 10px; border-radius: 20px; background: var(--surface); color: var(--sky-dk); border: 1px solid var(--border-lt); }
    .ms-tag.g { color: var(--text-2); }
    .ms-tag.lv { background: var(--sky); color: #fff; border-color: var(--sky); }
    .ms-sec { margin-top: 1rem; }
    .ms-sec-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-3); margin-bottom: 4px; }
    .ms-bio { font-size: 14px; color: var(--text-1); line-height: 1.6; white-space: pre-wrap; }
    .ms-look { font-size: 14px; color: var(--text-1); }
    .ms-links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .ms-link { font-size: 12px; padding: 6px 12px; border-radius: 10px; background: var(--surface); border: 1px solid var(--border); color: var(--text-2); text-decoration: none; }
    .ms-link:hover { border-color: var(--sky); color: var(--sky-dk); }
    .ms-actions { margin-top: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap; }
    .ms-btn { padding: 11px 22px; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; }
    .ms-btn-primary { background: var(--sky); color: #fff; }
    .ms-btn-primary:hover { background: var(--sky-dk); }
    .ms-btn-ghost { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
</style>
@endpush

@section('content')

<a href="{{ route('musisi.index') }}" class="ms-back">← Direktori Musisi</a>

<div class="ms-card">
    <div class="ms-top">
        <img class="ms-avatar" src="{{ $profile->user->avatar ?? asset('images/default-avatar.png') }}" alt="">
        <div style="min-width:0;">
            <div class="ms-name">{{ $profile->user->name ?? 'Musisi' }}</div>
            @if($profile->location)<div class="ms-loc">📍 {{ $profile->location }}</div>@endif
        </div>
    </div>

    <div class="ms-tags">
        @if($profile->skill_level)<span class="ms-tag lv">{{ ucfirst($profile->skill_level) }}</span>@endif
        @foreach($profile->rolesArray() as $role)<span class="ms-tag">{{ $role }}</span>@endforeach
        @foreach($profile->genresArray() as $g)<span class="ms-tag g">{{ $g }}</span>@endforeach
    </div>

    @if($profile->looking_for)
    <div class="ms-sec">
        <div class="ms-sec-label">Sedang mencari</div>
        <div class="ms-look">🔎 {{ $profile->looking_for }}</div>
    </div>
    @endif

    @if($profile->bio)
    <div class="ms-sec">
        <div class="ms-sec-label">Bio</div>
        <div class="ms-bio">{{ $profile->bio }}</div>
    </div>
    @endif

    @if($profile->spotify_url || $profile->youtube_url || $profile->instagram)
    <div class="ms-sec">
        <div class="ms-sec-label">Portofolio</div>
        <div class="ms-links">
            @if($profile->spotify_url)<a class="ms-link" href="{{ $profile->spotify_url }}" target="_blank" rel="noopener">🎧 Spotify</a>@endif
            @if($profile->youtube_url)<a class="ms-link" href="{{ $profile->youtube_url }}" target="_blank" rel="noopener">▶️ YouTube</a>@endif
            @if($profile->instagram)<a class="ms-link" href="https://instagram.com/{{ ltrim($profile->instagram,'@') }}" target="_blank" rel="noopener">📸 Instagram</a>@endif
        </div>
    </div>
    @endif

    <div class="ms-actions">
        @if($profile->tip_url)
        <a href="{{ $profile->tip_url }}" target="_blank" rel="noopener"
           class="ms-btn" style="background:linear-gradient(135deg,var(--orange),var(--orange-dk));color:#fff;">☕ Dukung / Saweran</a>
        @endif
        @if($profile->user_id === auth()->id())
        <a href="{{ route('musisi.edit') }}" class="ms-btn ms-btn-primary">✏️ Edit profilku</a>
        @else
        <form method="POST" action="{{ route('dia.start', $profile->user_id) }}">
            @csrf
            <button type="submit" class="ms-btn ms-btn-primary">💬 Hubungi</button>
        </form>
        @endif
        <button type="button" class="ms-btn ms-btn-ghost" onclick="msShareProfile()">🔗 Bagikan</button>
        <button type="button" class="ms-btn ms-btn-ghost" id="msCardBtn" onclick="msShareCard()">📸 Kartu Gambar</button>
        <a href="{{ route('musisi.index') }}" class="ms-btn ms-btn-ghost">← Kembali</a>
    </div>
</div>

<script>
function msShareProfile(){
    var url = window.location.href;
    var title = {{ Illuminate\Support\Js::from(($profile->user->name ?? 'Musisi') . ' — Margonoandi') }};
    if (navigator.share) {
        navigator.share({ title: title, text: 'Cek profil musisi ini di Margonoandi', url: url }).catch(function(){});
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){ alert('Link profil disalin!'); }).catch(function(){ prompt('Salin link:', url); });
    } else { prompt('Salin link:', url); }
}
</script>

@php
$cardData = [
    'name'     => $profile->user->name ?? 'Musisi',
    'avatar'   => $profile->user->avatar ?? asset('images/default-avatar.png'),
    'roles'    => array_map('ucfirst', $profile->rolesArray()),
    'genres'   => $profile->genresArray(),
    'location' => $profile->location,
    'skill'    => $profile->skill_level ? ucfirst($profile->skill_level) : '',
    'bio'      => \Illuminate\Support\Str::limit(strip_tags((string) $profile->bio), 170),
    'url'      => route('musisi.show', $profile->id),
];
@endphp
<script>
var MS_CARD = {{ Illuminate\Support\Js::from($cardData) }};

function msRoundRect(ctx,x,y,w,h,r){ ctx.beginPath(); ctx.moveTo(x+r,y); ctx.arcTo(x+w,y,x+w,y+h,r); ctx.arcTo(x+w,y+h,x,y+h,r); ctx.arcTo(x,y+h,x,y,r); ctx.arcTo(x,y,x+w,y,r); ctx.closePath(); }
function msLoadImg(src){ return new Promise(function(res){ var img=new Image(); img.crossOrigin='anonymous'; img.onload=function(){res(img);}; img.onerror=function(){res(null);}; img.src=src; }); }
function msWrap(ctx,text,cx,y,maxW,lh,maxLines){
    var words=(text||'').split(/\s+/), line='', lines=[];
    for(var i=0;i<words.length;i++){ var t=line?line+' '+words[i]:words[i];
        if(ctx.measureText(t).width>maxW && line){ lines.push(line); line=words[i]; if(lines.length===maxLines){break;} } else line=t; }
    if(line && lines.length<maxLines) lines.push(line);
    if(lines.length>=maxLines){ var L=lines[maxLines-1]; while(ctx.measureText(L+'…').width>maxW && L.length){ L=L.slice(0,-1); } lines[maxLines-1]=L+'…'; }
    lines.forEach(function(l,i){ ctx.fillText(l,cx,y+i*lh); });
    return y + lines.length*lh;
}
function msChips(ctx,items,cx,y,bg,fg,fontSize){
    if(!items||!items.length) return y;
    ctx.font=fontSize+'px "DM Sans","Inter",sans-serif';
    var padX=26, gap=14, h=fontSize+26, maxW=1080-120, rows=[], row=[], rowW=0;
    items.forEach(function(it){ var w=ctx.measureText(it).width+padX*2, add=w+(row.length?gap:0);
        if(rowW+add>maxW && row.length){ rows.push({items:row,w:rowW}); row=[]; rowW=0; add=w; }
        row.push({t:it,w:w}); rowW+=add; });
    if(row.length) rows.push({items:row,w:rowW});
    rows.forEach(function(r){ var x=cx-r.w/2;
        r.items.forEach(function(c){ ctx.fillStyle=bg; msRoundRect(ctx,x,y,c.w,h,h/2); ctx.fill();
            ctx.fillStyle=fg; ctx.textAlign='left'; ctx.textBaseline='middle'; ctx.fillText(c.t,x+padX,y+h/2); x+=c.w+gap; });
        y+=h+14; });
    ctx.textAlign='center'; ctx.textBaseline='alphabetic'; return y;
}
async function msShareCard(){
    var btn=document.getElementById('msCardBtn'); var old=btn?btn.textContent:'';
    function reset(){ if(btn){ btn.disabled=false; btn.textContent=old; } }
    if(btn){ btn.disabled=true; btn.textContent='⏳ Membuat...'; }
    try{
        try{ await document.fonts.ready; }catch(e){}
        var W=1080,H=1350, cv=document.createElement('canvas'); cv.width=W; cv.height=H; var ctx=cv.getContext('2d');
        var g=ctx.createLinearGradient(0,0,W,H); g.addColorStop(0,'#0f2236'); g.addColorStop(1,'#0a1622'); ctx.fillStyle=g; ctx.fillRect(0,0,W,H);
        var ga=ctx.createLinearGradient(0,0,W,0); ga.addColorStop(0,'#38A8CC'); ga.addColorStop(1,'#F07040'); ctx.fillStyle=ga; ctx.fillRect(0,0,W,14);
        var cx=W/2, ay=300, ar=132;
        ctx.fillStyle=ga; ctx.beginPath(); ctx.arc(cx,ay,ar+11,0,Math.PI*2); ctx.fill();
        var av=await msLoadImg(MS_CARD.avatar);
        ctx.save(); ctx.beginPath(); ctx.arc(cx,ay,ar,0,Math.PI*2); ctx.clip();
        if(av){ ctx.drawImage(av,cx-ar,ay-ar,ar*2,ar*2); }
        else { ctx.fillStyle='#1b3247'; ctx.fillRect(cx-ar,ay-ar,ar*2,ar*2); ctx.fillStyle='#7EC8E3'; ctx.font='bold 140px sans-serif'; ctx.textAlign='center'; ctx.textBaseline='middle'; ctx.fillText((MS_CARD.name||'M').charAt(0).toUpperCase(),cx,ay); }
        ctx.restore();
        ctx.textAlign='center'; ctx.textBaseline='alphabetic';
        ctx.fillStyle='#fff'; ctx.font='bold 72px "Sora","Inter",sans-serif'; ctx.fillText(MS_CARD.name||'Musisi',cx,545);
        var sub=[]; if(MS_CARD.skill) sub.push(MS_CARD.skill); if(MS_CARD.location) sub.push('📍 '+MS_CARD.location);
        if(sub.length){ ctx.fillStyle='#9bc3d6'; ctx.font='34px "DM Sans","Inter",sans-serif'; ctx.fillText(sub.join('    '),cx,595); }
        var y=680;
        y=msChips(ctx, MS_CARD.roles, cx, y, '#13344a', '#7EC8E3', 38);
        y=msChips(ctx, MS_CARD.genres, cx, y+6, '#3a2417', '#F0A070', 34);
        if(MS_CARD.bio){ ctx.fillStyle='#cfe0ec'; ctx.font='37px "DM Sans","Inter",sans-serif'; ctx.textAlign='center'; msWrap(ctx, MS_CARD.bio, cx, y+72, W-170, 52, 3); }
        // Footer: QR ke profil + branding (business-card strip)
        var qr = await msLoadImg('https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=0&qzone=1&data=' + encodeURIComponent(MS_CARD.url || 'https://margonoandi.my.id'));
        if (qr) {
            var qs=200, qx=80, qy=H-262;
            ctx.fillStyle='#fff'; msRoundRect(ctx, qx-14, qy-14, qs+28, qs+28, 18); ctx.fill();
            ctx.drawImage(qr, qx, qy, qs, qs);
            var tx=qx+qs+50, tcy=qy+qs/2;
            ctx.textAlign='left';
            ctx.fillStyle='#fff';    ctx.font='bold 46px "Sora","Inter",sans-serif'; ctx.fillText('MARGONOANDI', tx, tcy-44);
            ctx.fillStyle='#9bc3d6'; ctx.font='30px "DM Sans","Inter",sans-serif';   ctx.fillText('margonoandi.my.id', tx, tcy+2);
            ctx.fillStyle='#F0A070'; ctx.font='29px "DM Sans","Inter",sans-serif';   ctx.fillText('Scan untuk lihat profilku', tx, tcy+50);
            ctx.fillStyle='#6f96ab'; ctx.font='24px "DM Sans","Inter",sans-serif';   ctx.fillText('Ekosistem musik, dimulai dari kamarmu', tx, tcy+92);
        } else {
            ctx.textAlign='center';
            ctx.fillStyle='#fff';    ctx.font='bold 44px "Sora","Inter",sans-serif'; ctx.fillText('MARGONOANDI',cx,H-160);
            ctx.fillStyle='#9bc3d6'; ctx.font='30px "DM Sans","Inter",sans-serif';   ctx.fillText('margonoandi.my.id',cx,H-112);
            ctx.fillStyle='#6f96ab'; ctx.font='26px "DM Sans","Inter",sans-serif';   ctx.fillText('Ekosistem musik, dimulai dari kamarmu',cx,H-70);
        }
        cv.toBlob(function(blob){
            if(!blob){ alert('Gagal membuat gambar. Coba lagi.'); reset(); return; }
            var file=new File([blob],'profil-margonoandi.png',{type:'image/png'});
            if(navigator.canShare && navigator.canShare({files:[file]})){
                navigator.share({files:[file], title:MS_CARD.name, text:'Profil musisi di Margonoandi 🎶'}).catch(function(){}).finally(reset);
            } else {
                var a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='profil-margonoandi.png'; document.body.appendChild(a); a.click(); a.remove();
                setTimeout(function(){ URL.revokeObjectURL(a.href); },4000); reset();
            }
        },'image/png');
    }catch(e){ alert('Gagal membuat kartu.'); reset(); }
}
</script>

@endsection
