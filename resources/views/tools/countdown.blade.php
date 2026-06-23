@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); }
    .cd-page { max-width:680px; margin:0 auto; padding:1.5rem 1rem 4rem; }
    .cd-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .cd-hero { text-align:center;margin-bottom:1.5rem; }
    .cd-badge2 { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .cd-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,1.9rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .cd-hero p { font-size:13px;color:var(--text-3,#94a3b8);max-width:520px;margin:0 auto;line-height:1.7; }

    .cd-fg { margin-bottom:.85rem; }
    .cd-fg label { display:block;font-size:11px;font-weight:700;color:var(--text-2,#cbd5e1);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em; }
    .cd-input { width:100%;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:10px 12px;font-size:14px;color:var(--text,#f0f0f0);outline:none;font-family:inherit; }
    .cd-input:focus { border-color:var(--ac); }
    .cd-grads { display:flex;gap:6px;flex-wrap:wrap; }
    .cd-grad { width:30px;height:30px;border-radius:7px;cursor:pointer;border:2px solid transparent; }
    .cd-grad.on { border-color:#fff;box-shadow:0 0 0 2px var(--ac); }
    .cd-go { width:100%;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;border:none;border-radius:11px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;margin-top:.4rem; }
    .cd-linkbox { display:none;margin-top:1rem;background:var(--card-bg,#0f172a);border:1px solid var(--ac);border-radius:12px;padding:1rem; }
    .cd-linkbox.show { display:block; }
    .cd-linkrow { display:flex;gap:6px;margin-top:6px; }
    .cd-copy { background:var(--ac);color:#fff;border:none;border-radius:8px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap; }
    .cd-open { display:inline-block;margin-top:8px;font-size:12px;color:var(--ac);text-decoration:none;font-weight:600; }

    .cd-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:2rem; }
    .cd-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .cd-info-card .i { font-size:1.4rem;margin-bottom:.4rem; }
    .cd-info-card .t { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .cd-info-card .b { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }

    /* ── DISPLAY (layar penuh hitung mundur) ── */
    .cd-disp { position:fixed;inset:0;z-index:50;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem 1.25rem;color:#fff;overflow:auto; }
    .cd-disp-badge { font-size:13px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.25);padding:6px 16px;border-radius:30px;backdrop-filter:blur(4px); }
    .cd-disp-title { font-family:'Space Grotesk','Sora',sans-serif;font-weight:800;font-size:clamp(1.8rem,8vw,3.2rem);line-height:1.05;margin-top:1.1rem;text-shadow:0 2px 18px rgba(0,0,0,.4); }
    .cd-disp-artist { font-size:clamp(.85rem,3.5vw,1.15rem);font-weight:600;letter-spacing:.18em;text-transform:uppercase;opacity:.9;margin-top:.5rem; }
    .cd-timer { display:flex;gap:clamp(8px,3vw,22px);margin:2rem 0 1.25rem; }
    .cd-seg { min-width:clamp(60px,18vw,108px); }
    .cd-seg span { display:block;font-family:'Space Grotesk',monospace;font-weight:800;font-size:clamp(2.2rem,11vw,4.5rem);line-height:1;font-variant-numeric:tabular-nums;text-shadow:0 4px 20px rgba(0,0,0,.45); }
    .cd-seg small { display:block;font-size:clamp(9px,2.4vw,12px);font-weight:700;letter-spacing:.16em;opacity:.78;margin-top:.45rem; }
    .cd-disp-date { font-size:clamp(.85rem,3.4vw,1.05rem);font-weight:600;opacity:.92; }
    .cd-btn { display:none;margin-top:1.6rem;background:#fff;color:#0b0f17;padding:13px 30px;border-radius:30px;font-size:15px;font-weight:800;text-decoration:none;box-shadow:0 10px 30px rgba(0,0,0,.35);transition:.15s; }
    .cd-btn:hover { transform:translateY(-2px); }
    .cd-made { position:absolute;bottom:14px;left:0;right:0;text-align:center;font-size:11px;opacity:.6; }
    .cd-made a { color:#fff;text-decoration:underline; }
</style>
@endpush

@section('content')

@if($hasParams)
{{-- ═══ MODE DISPLAY ═══ --}}
<div class="cd-disp" id="cdDisp">
    <div class="cd-disp-badge" id="cdBadge">Segera Rilis</div>
    <div class="cd-disp-title" id="cdTitle">—</div>
    <div class="cd-disp-artist" id="cdArtist"></div>
    <div class="cd-timer" id="cdTimer">
        <div class="cd-seg"><span id="cdD">00</span><small>HARI</small></div>
        <div class="cd-seg"><span id="cdH">00</span><small>JAM</small></div>
        <div class="cd-seg"><span id="cdM">00</span><small>MENIT</small></div>
        <div class="cd-seg"><span id="cdS">00</span><small>DETIK</small></div>
    </div>
    <div class="cd-disp-date" id="cdDate"></div>
    <a class="cd-btn" id="cdBtn" target="_blank" rel="noopener">🔔 Pre-save / Dengarkan</a>
    <div class="cd-made">dibuat gratis di <a href="{{ route('tools.countdown') }}">margonoandi.my.id</a></div>
</div>

@else
{{-- ═══ MODE GENERATOR ═══ --}}
<div class="cd-page">
    <a href="{{ route('home') }}" class="cd-back">← Beranda</a>
    <div class="cd-hero">
        <div class="cd-badge2">⏳ Tool Gratis</div>
        <h1>Countdown Rilis Lagu</h1>
        <p>Buat <b>link hitung mundur</b> yang berdetak <b>real-time</b> — taruh di <b>bio Instagram / link-in-bio / WhatsApp</b>. Saat jam rilis tiba, otomatis jadi <b>"Out Now"</b> + tombol dengarkan. Gratis, tanpa daftar.</p>
    </div>

    <div class="cd-fg"><label>Judul Lagu</label><input type="text" id="gJ" class="cd-input" maxlength="60" placeholder="Judul lagumu"></div>
    <div class="cd-fg"><label>Nama Artis</label><input type="text" id="gA" class="cd-input" maxlength="40" placeholder="Nama artis / band"></div>
    <div class="cd-fg"><label>Tanggal &amp; Jam Rilis</label><input type="datetime-local" id="gD" class="cd-input"></div>
    <div class="cd-fg"><label>Link Dengar / Pre-save (opsional)</label><input type="url" id="gL" class="cd-input" placeholder="https://… (Spotify / smart link)"></div>
    <div class="cd-fg"><label>Tema Warna</label><div class="cd-grads" id="gGrads"></div></div>

    <button class="cd-go" onclick="cdBuild()">🔗 Buat &amp; Salin Link</button>

    <div class="cd-linkbox" id="gLinkBox">
        <div style="font-size:12px;font-weight:700;color:#22c55e;margin-bottom:2px;">✅ Link siap dibagikan:</div>
        <div class="cd-linkrow">
            <input type="text" id="gLink" class="cd-input" readonly style="font-size:12px;">
            <button class="cd-copy" onclick="cdCopy()">Salin</button>
        </div>
        <a class="cd-open" id="gOpen" target="_blank" rel="noopener">👁️ Buka pratinjau layar penuh →</a>
    </div>

    <div class="cd-info-grid">
        <div class="cd-info-card"><div class="i">⏱️</div><div class="t">Berdetak Real-Time</div><div class="b">Hari · jam · menit · detik mundur otomatis tiap detik saat link dibuka.</div></div>
        <div class="cd-info-card"><div class="i">🔔</div><div class="t">Auto "Out Now"</div><div class="b">Begitu jam rilis tiba, kartu berganti jadi ajakan dengarkan + tombol link.</div></div>
        <div class="cd-info-card"><div class="i">🔗</div><div class="t">Tanpa Daftar</div><div class="b">Semua data ada di link — tinggal salin & tempel di bio/story. Tak perlu akun.</div></div>
        <div class="cd-info-card"><div class="i">🖼️</div><div class="t">Butuh gambar?</div><div class="b">Untuk feed/story statis, pakai <a href="{{ route('tools.kartu-rilis') }}" style="color:var(--ac);">Kartu Promo Rilis</a>.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> 🎸 ·
        <a href="{{ route('tools.kartu-rilis') }}" style="color:var(--ac);">Kartu Promo Rilis</a>
    </p>
</div>
@endif
@endsection

@push('scripts')
<script>
(function(){
'use strict';
var GRADS=[['#0f0c29','#302b63'],['#0f2027','#2c5364'],['#f12711','#f5af19'],['#ee0979','#ff6a00'],['#8E2DE2','#4A00E0'],['#11998e','#38ef7d'],['#16222a','#3a6073'],['#000000','#434343']];
var B=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function pad(n){return n<10?'0'+n:''+n;}
function fmtDateTime(dt){if(isNaN(dt))return '';return dt.getDate()+' '+B[dt.getMonth()]+' '+dt.getFullYear()+' · '+pad(dt.getHours())+':'+pad(dt.getMinutes())+' WIB';}
var P=new URLSearchParams(location.search);

@if($hasParams)
// ── DISPLAY ──
(function(){
    var j=P.get('j')||'Rilis Baru',a=P.get('a')||'',d=P.get('d')||'',l=P.get('l')||'',gi=parseInt(P.get('g')||'0',10)||0;
    var disp=document.getElementById('cdDisp'),g=GRADS[gi]||GRADS[0];
    disp.style.background='linear-gradient(150deg,'+g[0]+' 0%,'+g[1]+' 100%)';
    document.getElementById('cdTitle').textContent=j;
    var ae=document.getElementById('cdArtist');if(a)ae.textContent=a;else ae.style.display='none';
    var target=new Date(d).getTime();
    var de=document.getElementById('cdDate');if(!isNaN(target))de.textContent='🗓 '+fmtDateTime(new Date(target));
    var btn=document.getElementById('cdBtn');
    function showBtn(txt){if(l){btn.href=l;btn.textContent=txt;btn.style.display='inline-block';}}
    var done=false;
    function tick(){
        var diff=target-Date.now();
        if(isNaN(target)){document.getElementById('cdTimer').style.display='none';document.getElementById('cdBadge').textContent='Tanggal tak valid';return;}
        if(diff<=0){
            if(!done){done=true;
                document.getElementById('cdBadge').textContent='🔥 Out Now';
                document.getElementById('cdTimer').style.display='none';
                document.getElementById('cdTitle').insertAdjacentHTML('afterend','<div class="cd-disp-date" style="margin-top:1rem;font-size:clamp(1rem,4vw,1.3rem);font-weight:700;">Sudah rilis — dengarkan sekarang! 🎉</div>');
                showBtn('▶ Dengarkan Sekarang');
            }
            return;
        }
        var dd=Math.floor(diff/86400000),hh=Math.floor(diff%86400000/3600000),mm=Math.floor(diff%3600000/60000),ss=Math.floor(diff%60000/1000);
        document.getElementById('cdD').textContent=pad(dd);
        document.getElementById('cdH').textContent=pad(hh);
        document.getElementById('cdM').textContent=pad(mm);
        document.getElementById('cdS').textContent=pad(ss);
        showBtn('🔔 Pre-save / Dengarkan');
    }
    tick();setInterval(tick,1000);
})();
@else
// ── GENERATOR ──
(function(){
    var grad=0,gw=document.getElementById('gGrads');
    GRADS.forEach(function(p,i){var el=document.createElement('div');el.className='cd-grad'+(i===0?' on':'');el.style.background='linear-gradient(135deg,'+p[0]+','+p[1]+')';el.onclick=function(){grad=i;[].forEach.call(gw.children,function(x){x.classList.remove('on');});el.classList.add('on');};gw.appendChild(el);});
    // default: besok jam 00:00
    (function(){var dt=new Date();dt.setDate(dt.getDate()+7);dt.setHours(0,0,0,0);var v=dt.getFullYear()+'-'+pad(dt.getMonth()+1)+'-'+pad(dt.getDate())+'T'+pad(dt.getHours())+':'+pad(dt.getMinutes());document.getElementById('gD').value=v;})();
    function val(id){return (document.getElementById(id).value||'').trim();}
    window.cdBuild=function(){
        var d=val('gD');if(!d){alert('Isi tanggal & jam rilis dulu.');return;}
        var base='{{ route('tools.countdown') }}';
        var qs='?j='+encodeURIComponent(val('gJ'))+'&a='+encodeURIComponent(val('gA'))+'&d='+encodeURIComponent(d)+'&g='+grad;
        var l=val('gL');if(l)qs+='&l='+encodeURIComponent(l);
        var url=base+qs;
        document.getElementById('gLink').value=url;
        document.getElementById('gOpen').href=url;
        document.getElementById('gLinkBox').classList.add('show');
        document.getElementById('gLinkBox').scrollIntoView({behavior:'smooth',block:'center'});
    };
    window.cdCopy=function(){var i=document.getElementById('gLink');i.select();i.setSelectionRange(0,99999);
        var ok=function(){var b=event&&event.target;if(b){var t=b.textContent;b.textContent='✓ Tersalin';setTimeout(function(){b.textContent=t;},1500);}};
        if(navigator.clipboard){navigator.clipboard.writeText(i.value).then(ok).catch(function(){document.execCommand('copy');ok();});}
        else{document.execCommand('copy');ok();}
    };
})();
@endif
})();
</script>
@endpush
