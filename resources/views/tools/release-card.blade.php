@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); }
    .rc-page { max-width:900px; margin:0 auto; padding:1.5rem 1rem 4rem; }
    .rc-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .rc-back:hover { color:var(--text,#f0f0f0); }
    .rc-hero { text-align:center;margin-bottom:1.5rem; }
    .rc-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .rc-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,1.9rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .rc-hero p { font-size:13px;color:var(--text-3,#94a3b8);max-width:560px;margin:0 auto;line-height:1.7; }

    .rc-wrap { display:grid;grid-template-columns:minmax(0,300px) 1fr;gap:1.25rem;align-items:start; }
    @@media(max-width:680px){ .rc-wrap{grid-template-columns:1fr;} }
    .rc-stage { position:sticky;top:1rem;text-align:center; }
    #rcCanvas { max-width:100%;max-height:62vh;border-radius:12px;border:1px solid var(--border,#334155);cursor:grab;touch-action:none;background:#070d18; }
    #rcCanvas:active { cursor:grabbing; }
    .rc-stage-hint { font-size:10px;color:var(--text-3,#94a3b8);margin-top:5px; }

    .rc-ctrl { display:flex;flex-direction:column;gap:.85rem; }
    .rc-fg label { display:block;font-size:11px;font-weight:700;color:var(--text-2,#cbd5e1);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em; }
    .rc-input { width:100%;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:9px 11px;font-size:14px;color:var(--text,#f0f0f0);outline:none;font-family:inherit; }
    .rc-input:focus { border-color:var(--ac); }
    .rc-seg { display:flex;gap:4px;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:3px;flex-wrap:wrap; }
    .rc-seg button { flex:1;min-width:60px;border:none;background:none;color:var(--text-3,#94a3b8);font-size:12px;font-weight:600;padding:7px 8px;border-radius:6px;cursor:pointer;font-family:inherit; }
    .rc-seg button.on { background:var(--ac);color:#fff; }
    .rc-drop { border:2px dashed var(--border,#334155);border-radius:11px;padding:.9rem;text-align:center;cursor:pointer;font-size:12px;color:var(--text-3,#94a3b8);transition:.2s; }
    .rc-drop:hover { border-color:var(--ac); }
    #rcImg { display:none; }
    .rc-grads { display:flex;gap:6px;flex-wrap:wrap; }
    .rc-grad { width:28px;height:28px;border-radius:7px;cursor:pointer;border:2px solid transparent; }
    .rc-grad.on { border-color:#fff;box-shadow:0 0 0 2px var(--ac); }
    .rc-row { display:flex;gap:8px; }
    .rc-check { display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-2,#cbd5e1);cursor:pointer; }
    .rc-plats { display:flex;gap:8px;flex-wrap:wrap; }
    .rc-go { background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;border:none;border-radius:11px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(56,189,248,.25); }
    .rc-status { font-size:12px;color:var(--text-3,#94a3b8);min-height:15px; }
    .rc-hideable { display:none; }
    .rc-hideable.show { display:block; }

    .rc-cta { margin-top:2.5rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg,#0f172a));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .rc-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.4rem; }
    .rc-cta p { font-size:12.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:470px;margin:0 auto .9rem; }
    .rc-cta-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
    .rc-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;margin-top:2rem; }
    .rc-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .rc-info-card .i { font-size:1.4rem;margin-bottom:.4rem; }
    .rc-info-card .t { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .rc-info-card .b { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }
</style>
@endpush

@section('content')
<div class="rc-page">
    <a href="{{ route('home') }}" class="rc-back">← Beranda</a>
    @include('partials.tool-share')
    <div class="rc-hero">
        <div class="rc-badge">🚀 Tool Gratis</div>
        <h1>Kartu Promo Rilis Lagu</h1>
        <p>Bikin kartu promo untuk <b>Instagram / WhatsApp Story</b> di tiap fase rilis — <b>pra-rilis (hitung mundur)</b>, <b>rilis (out now + QR/platform)</b>, <b>pasca-rilis</b>. Feed 1:1 &amp; Story 9:16. Gratis, tanpa upload.</p>
    </div>

    <div class="rc-wrap">
        <div class="rc-stage">
            <canvas id="rcCanvas" width="720" height="720"></canvas>
            <div class="rc-stage-hint">Seret foto di kartu untuk atur posisi · preview = hasil</div>
        </div>

        <div class="rc-ctrl">
            <div class="rc-fg">
                <label>Fase Rilis</label>
                <div class="rc-seg" id="rcPhase">
                    <button data-v="pre" class="on">🔜 Pra-rilis</button>
                    <button data-v="release">🔥 Rilis</button>
                    <button data-v="post">💜 Pasca</button>
                </div>
            </div>
            <div class="rc-row">
                <div class="rc-fg" style="flex:1;">
                    <label>Format</label>
                    <div class="rc-seg" id="rcAspect">
                        <button data-v="square" class="on">Feed 1:1</button>
                        <button data-v="story">Story 9:16</button>
                    </div>
                </div>
            </div>

            <div class="rc-fg">
                <label>Foto / Gambar (opsional)</label>
                <div class="rc-drop" id="rcDrop" onclick="document.getElementById('rcImg').click()">📷 Seret / klik pilih foto</div>
                <input type="file" id="rcImg" accept="image/*">
                <div class="rc-row" id="rcImgCtrl" style="display:none;margin-top:8px;align-items:center;gap:8px;">
                    <span>🔍</span><input type="range" id="rcZoom" min="1" max="3" step="0.01" value="1" style="flex:1;">
                    <button onclick="rcRemoveImg()" style="background:var(--card-bg,#1e293b);border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8);border-radius:8px;padding:6px 9px;font-size:11px;cursor:pointer;">🗑</button>
                </div>
            </div>
            <div class="rc-fg"><label>Latar (bila tanpa foto)</label><div class="rc-grads" id="rcGrads"></div></div>

            <div class="rc-fg"><label>Judul Lagu</label><input type="text" id="rcTitle" class="rc-input" maxlength="40" value="Judul Lagu"></div>
            <div class="rc-fg"><label>Nama Artis</label><input type="text" id="rcArtist" class="rc-input" maxlength="30" value="NAMA ARTIS"></div>

            <div class="rc-fg" id="rcDateFg"><label>Tanggal Rilis</label><input type="date" id="rcDate" class="rc-input"></div>
            <div class="rc-fg rc-hideable" id="rcMilestoneFg"><label>Pencapaian (opsional)</label><input type="text" id="rcMilestone" class="rc-input" maxlength="36" placeholder="mis. Sudah didengar 10.000+ kali"></div>

            <div class="rc-fg">
                <label>Ajakan Dengar — pilih tampilan</label>
                <div class="rc-seg" id="rcMode"><button data-v="qr" class="on">QR Code</button><button data-v="platform">Ikon Platform</button></div>
            </div>
            <div class="rc-fg rc-hideable show" id="rcLinkFg"><label>Link Lagu (untuk QR)</label><input type="url" id="rcLink" class="rc-input" placeholder="https://… (Spotify / smart link)"></div>
            <div class="rc-fg rc-hideable" id="rcPlatFg">
                <label>Platform yang tampil</label>
                <div class="rc-plats" id="rcPlats">
                    <label class="rc-check"><input type="checkbox" data-p="spotify" checked> Spotify</label>
                    <label class="rc-check"><input type="checkbox" data-p="apple" checked> Apple Music</label>
                    <label class="rc-check"><input type="checkbox" data-p="youtube" checked> YouTube</label>
                </div>
            </div>

            <div class="rc-row">
                <div class="rc-fg" style="flex:1;"><label>Warna Teks</label><div class="rc-seg" id="rcTC"><button data-v="#ffffff" class="on">Terang</button><button data-v="#0b0f17">Gelap</button></div></div>
                <div class="rc-fg" style="flex:1;"><label>Format File</label><select id="rcFmt" class="rc-input"><option value="jpg">JPG</option><option value="png">PNG</option></select></div>
            </div>
            <label class="rc-check"><input type="checkbox" id="rcScrim" checked> Gradasi gelap (biar teks terbaca)</label>

            <button class="rc-go" onclick="rcDownload()">⬇️ Unduh Kartu</button>
            <div class="rc-status" id="rcStatus"></div>
        </div>
    </div>

    <div class="rc-cta">
        <h2>🎸 Rilis lagumu, bangun basis penggemar</h2>
        <p>Setelah promo siap, tampilkan karyamu di <b>Margonoandi</b> — profil portofolio gratis (kartu + QR), temukan personil &amp; gig, gabung komunitas musisi Indonesia.</p>
        <a href="{{ route('google.login') }}" class="rc-cta-btn">Buat profil musisi gratis →</a>
    </div>

    <div class="rc-info-grid">
        <div class="rc-info-card"><div class="i">⏳</div><div class="t">Hitung Mundur Otomatis</div><div class="b">Mode pra-rilis menghitung "H-berapa" dari tanggal rilis — tinggal regenerate tiap hari untuk story.</div></div>
        <div class="rc-info-card"><div class="i">🔗</div><div class="t">QR atau Ikon Platform</div><div class="b">Tempel link (Spotify/smart link) jadi QR, atau tampilkan badge Spotify · Apple · YouTube.</div></div>
        <div class="rc-info-card"><div class="i">📱</div><div class="t">Feed &amp; Story</div><div class="b">Ekspor 1:1 (feed) atau 9:16 (Instagram/WhatsApp Story) siap unggah.</div></div>
        <div class="rc-info-card"><div class="i">🔒</div><div class="t">Tanpa Upload</div><div class="b">Semua dibuat di browser, foto tak dikirim ke server.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> 🎸 ·
        <a href="{{ route('tools.countdown') }}" style="color:var(--ac);">Countdown Live</a> ·
        <a href="{{ route('tools.cover-art') }}" style="color:var(--ac);">Buat Cover</a> ·
        <a href="{{ route('tools.hapus-vokal') }}" style="color:var(--ac);">Hapus Vokal</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
(function(){
'use strict';
var GRADS=[['#0f0c29','#302b63'],['#0f2027','#2c5364'],['#16222a','#3a6073'],['#232526','#414345'],
           ['#f12711','#f5af19'],['#ee0979','#ff6a00'],['#8E2DE2','#4A00E0'],['#000000','#434343']];
var PLATS={spotify:['#1DB954','#04170c','Spotify'],apple:['#fa233b','#fff','Apple Music'],youtube:['#FF0000','#fff','YouTube']};
var S={phase:'pre',aspect:'square',img:null,zoom:1,panX:0,panY:0,grad:0,
       title:'Judul Lagu',artist:'NAMA ARTIS',date:'',milestone:'',mode:'qr',link:'',
       plats:{spotify:true,apple:true,youtube:true},tc:'#ffffff',scrim:true};
var cv=document.getElementById('rcCanvas'),ctx=cv.getContext('2d');
var qrImg=null,qrFor='',qrTimer=null;
function g(id){return document.getElementById(id);}
function dims(){ return S.aspect==='story'?[720,1280]:[720,720]; }

// default tanggal: hari ini + 7
(function(){var d=new Date();d.setDate(d.getDate()+7);S.date=d.toISOString().slice(0,10);g('rcDate').value=S.date;})();

function rr(c,x,y,w,h,r){c.beginPath();c.moveTo(x+r,y);c.arcTo(x+w,y,x+w,y+h,r);c.arcTo(x+w,y+h,x,y+h,r);c.arcTo(x,y+h,x,y,r);c.arcTo(x,y,x+w,y,r);c.closePath();}
function wrap(c,t,maxW,maxLines){var ws=(t||'').split(/\s+/),ln='',out=[];for(var i=0;i<ws.length;i++){var s=ln?ln+' '+ws[i]:ws[i];if(c.measureText(s).width>maxW&&ln){out.push(ln);ln=ws[i];if(out.length===maxLines)break;}else ln=s;}if(ln&&out.length<maxLines)out.push(ln);if(out.length>=maxLines){var L=out[maxLines-1];while(c.measureText(L+'…').width>maxW&&L.length)L=L.slice(0,-1);out[maxLines-1]=L+'…';}return out;}
function fmtDate(iso){if(!iso)return '';var d=new Date(iso+'T00:00:00');if(isNaN(d))return '';var b=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];return d.getDate()+' '+b[d.getMonth()]+' '+d.getFullYear();}
function daysLeft(iso){if(!iso)return null;var d=new Date(iso+'T00:00:00');if(isNaN(d))return null;var n=new Date();n.setHours(0,0,0,0);return Math.round((d-n)/86400000);}
function pill(c,cx,cy,text,fs,bg,fg){c.font='800 '+fs+'px "Space Grotesk","Inter",sans-serif';var w=c.measureText(text).width,px=fs*0.85,h=fs*2.0,rw=w+px*2;c.fillStyle=bg;rr(c,cx-rw/2,cy-h/2,rw,h,h/2);c.fill();c.fillStyle=fg;c.textBaseline='middle';c.fillText(text,cx,cy+fs*0.04);c.textBaseline='alphabetic';return rw;}

function phaseInfo(){
    if(S.phase==='release')return{badge:'🔥 OUT NOW',bc:'#22c55e',bf:'#04210f'};
    if(S.phase==='post')return{badge:'💜 SUDAH RILIS',bc:'#a855f7',bf:'#fff'};
    return{badge:'🔜 SEGERA RILIS',bc:'#f59e0b',bf:'#1a1203'};
}

function draw(c,W,H){
    var U=Math.min(W,H),cx=W/2;
    var gr=GRADS[S.grad],lg=c.createLinearGradient(0,0,W*0.3,H);lg.addColorStop(0,gr[0]);lg.addColorStop(1,gr[1]);
    c.fillStyle=lg;c.fillRect(0,0,W,H);
    if(S.img){var base=Math.max(W/S.img.width,H/S.img.height),ds=base*S.zoom,dw=S.img.width*ds,dh=S.img.height*ds;
        var ox=(W-dw)/2+S.panX*W,oy=(H-dh)/2+S.panY*H;ox=Math.min(0,Math.max(W-dw,ox));oy=Math.min(0,Math.max(H-dh,oy));c.drawImage(S.img,ox,oy,dw,dh);}
    if(S.scrim){var sg=c.createLinearGradient(0,0,0,H);sg.addColorStop(0,'rgba(0,0,0,.5)');sg.addColorStop(.35,'rgba(0,0,0,.15)');sg.addColorStop(.6,'rgba(0,0,0,.3)');sg.addColorStop(1,'rgba(0,0,0,.85)');c.fillStyle=sg;c.fillRect(0,0,W,H);}
    c.textAlign='center';c.shadowColor='rgba(0,0,0,.5)';c.shadowBlur=U*0.012;
    var info=phaseInfo();
    pill(c,cx,H*0.085,info.badge,U*0.03,info.bc,info.bf);
    // judul + artis
    c.fillStyle=S.tc;c.font='800 '+(U*0.072)+'px "Space Grotesk","Inter",sans-serif';
    var tl=wrap(c,S.title,W*0.84,2),ty=H*0.2;
    for(var i=0;i<tl.length;i++){c.fillText(tl[i],cx,ty);ty+=U*0.078;}
    if(S.artist){c.font='600 '+(U*0.034)+'px "Space Grotesk","Inter",sans-serif';c.globalAlpha=.92;try{c.letterSpacing=(U*0.01)+'px';}catch(e){}c.fillText(S.artist.toUpperCase(),cx,ty+U*0.01);c.globalAlpha=1;try{c.letterSpacing='0px';}catch(e){}}
    // FOKUS tengah
    var fy=H*0.5;
    if(S.phase==='pre'){
        var dl=daysLeft(S.date);
        c.fillStyle=S.tc;
        if(dl===null){c.font='800 '+(U*0.07)+'px "Space Grotesk",sans-serif';c.fillText('SEGERA',cx,fy);}
        else if(dl>0){c.font='900 '+(U*0.2)+'px "Space Grotesk",sans-serif';c.fillText('H-'+dl,cx,fy+U*0.04);
            c.font='700 '+(U*0.04)+'px "Space Grotesk",sans-serif';c.fillText(dl+' HARI LAGI',cx,fy+U*0.11);}
        else if(dl===0){c.font='900 '+(U*0.11)+'px "Space Grotesk",sans-serif';c.fillText('HARI INI!',cx,fy+U*0.03);}
        else {c.font='800 '+(U*0.08)+'px "Space Grotesk",sans-serif';c.fillText('SUDAH RILIS',cx,fy);}
        if(S.date){c.font='600 '+(U*0.032)+'px "Space Grotesk",sans-serif';c.globalAlpha=.9;c.fillText('🗓 '+fmtDate(S.date),cx,fy+U*0.17);c.globalAlpha=1;}
    } else if(S.phase==='release'){
        c.fillStyle=S.tc;c.font='900 '+(U*0.13)+'px "Space Grotesk",sans-serif';c.fillText('OUT NOW',cx,fy);
        c.font='600 '+(U*0.036)+'px "Space Grotesk",sans-serif';c.globalAlpha=.92;c.fillText('Dengarkan sekarang',cx,fy+U*0.07);c.globalAlpha=1;
    } else {
        c.fillStyle=S.tc;c.font='800 '+(U*0.062)+'px "Space Grotesk",sans-serif';c.fillText('DENGARKAN SEKARANG',cx,fy);
        if(S.milestone){c.font='700 '+(U*0.04)+'px "Space Grotesk",sans-serif';c.globalAlpha=.95;c.fillText('✨ '+S.milestone,cx,fy+U*0.08);c.globalAlpha=1;}
    }
    c.shadowBlur=0;
    // bawah: QR atau platform
    var by=H*0.8;
    if(S.mode==='qr'&&qrImg&&S.link){
        var qs=U*0.2,qx=cx-qs/2,qy=by-qs*0.3;
        c.fillStyle='#fff';rr(c,qx-qs*0.06,qy-qs*0.06,qs*1.12,qs*1.12,qs*0.08);c.fill();
        c.drawImage(qrImg,qx,qy,qs,qs);
        c.fillStyle=S.tc;c.font='700 '+(U*0.03)+'px "Space Grotesk",sans-serif';c.fillText('Scan untuk dengarkan',cx,qy+qs*1.18);
    } else if(S.mode==='platform'){
        var act=[];for(var k in S.plats){if(S.plats[k]&&PLATS[k])act.push(k);}
        c.font='700 '+(U*0.03)+'px "Space Grotesk",sans-serif';
        var gap=U*0.02,padX=U*0.028,hh=U*0.066,total=0,wid=[];
        act.forEach(function(k){var w=c.measureText(PLATS[k][2]).width+padX*2;wid.push(w);total+=w;});
        total+=gap*(act.length-1);
        var x=cx-total/2;
        c.fillStyle=S.tc;c.globalAlpha=.85;c.fillText('Dengarkan di',cx,by-hh*0.9);c.globalAlpha=1;
        act.forEach(function(k,i){var w=wid[i],p=PLATS[k];c.fillStyle=p[0];rr(c,x,by,w,hh,hh/2);c.fill();c.fillStyle=p[1];c.textBaseline='middle';c.fillText(p[2],x+w/2,by+hh/2);c.textBaseline='alphabetic';x+=w+gap;});
    }
    // brand
    c.fillStyle=S.tc;c.globalAlpha=.72;c.font='600 '+(U*0.026)+'px "Space Grotesk",sans-serif';c.fillText('margonoandi.my.id',cx,H*0.96);c.globalAlpha=1;
}
function redraw(){var d=dims();if(cv.width!==d[0]||cv.height!==d[1]){cv.width=d[0];cv.height=d[1];}draw(ctx,cv.width,cv.height);}

// QR loader
function loadQR(){
    if(S.mode!=='qr'||!S.link){qrImg=null;redraw();return;}
    if(S.link===qrFor&&qrImg){redraw();return;}
    var im=new Image();im.crossOrigin='anonymous';
    im.onload=function(){qrImg=im;qrFor=S.link;redraw();};im.onerror=function(){qrImg=null;redraw();};
    im.src='https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=0&qzone=1&data='+encodeURIComponent(S.link);
}

// kontrol
var gw=g('rcGrads');GRADS.forEach(function(p,i){var d=document.createElement('div');d.className='rc-grad'+(i===0?' on':'');d.style.background='linear-gradient(135deg,'+p[0]+','+p[1]+')';d.onclick=function(){S.grad=i;[].forEach.call(gw.children,function(x){x.classList.remove('on');});d.classList.add('on');redraw();};gw.appendChild(d);});
function seg(id,key,cb){var w=g(id);[].forEach.call(w.querySelectorAll('button'),function(b){b.onclick=function(){S[key]=b.getAttribute('data-v');[].forEach.call(w.querySelectorAll('button'),function(x){x.classList.remove('on');});b.classList.add('on');if(cb)cb();redraw();};});}
seg('rcPhase','phase',function(){g('rcMilestoneFg').classList.toggle('show',S.phase==='post');});
seg('rcAspect','aspect');
seg('rcTC','tc');
seg('rcMode','mode',function(){g('rcLinkFg').classList.toggle('show',S.mode==='qr');g('rcPlatFg').classList.toggle('show',S.mode==='platform');loadQR();});
g('rcTitle').oninput=function(){S.title=this.value;redraw();};
g('rcArtist').oninput=function(){S.artist=this.value;redraw();};
g('rcDate').onchange=function(){S.date=this.value;redraw();};
g('rcMilestone').oninput=function(){S.milestone=this.value;redraw();};
g('rcLink').oninput=function(){S.link=this.value.trim();clearTimeout(qrTimer);qrTimer=setTimeout(loadQR,500);};
g('rcScrim').onchange=function(){S.scrim=this.checked;redraw();};
g('rcZoom').oninput=function(){S.zoom=parseFloat(this.value);redraw();};
[].forEach.call(g('rcPlats').querySelectorAll('input'),function(c){c.onchange=function(){S.plats[this.getAttribute('data-p')]=this.checked;redraw();};});

// upload + drag
var drop=g('rcDrop');
drop.addEventListener('dragover',function(e){e.preventDefault();});
drop.addEventListener('drop',function(e){e.preventDefault();if(e.dataTransfer.files[0])loadImg(e.dataTransfer.files[0]);});
g('rcImg').addEventListener('change',function(){if(this.files[0])loadImg(this.files[0]);});
function loadImg(f){if(!/^image\//.test(f.type))return;var r=new FileReader();r.onload=function(ev){var im=new Image();im.onload=function(){S.img=im;S.zoom=1;S.panX=0;S.panY=0;g('rcZoom').value=1;g('rcImgCtrl').style.display='flex';redraw();};im.src=ev.target.result;};r.readAsDataURL(f);}
window.rcRemoveImg=function(){S.img=null;g('rcImg').value='';g('rcImgCtrl').style.display='none';redraw();};
var drag=false,lx=0,ly=0;
function mp(dx,dy){var sc=cv.width/cv.clientWidth;return[dx*sc/cv.width,dy*sc/cv.height];}
function pan(d){var mfx=0,mfy=0;if(S.img){var base=Math.max(cv.width/S.img.width,cv.height/S.img.height),dw=S.img.width*base*S.zoom,dh=S.img.height*base*S.zoom;mfx=Math.max(0,(dw-cv.width)/(2*cv.width));mfy=Math.max(0,(dh-cv.height)/(2*cv.height));}S.panX=Math.max(-mfx,Math.min(mfx,S.panX+d[0]));S.panY=Math.max(-mfy,Math.min(mfy,S.panY+d[1]));redraw();}
cv.addEventListener('mousedown',function(e){if(!S.img)return;drag=true;lx=e.clientX;ly=e.clientY;});
window.addEventListener('mousemove',function(e){if(!drag)return;var d=mp(e.clientX-lx,e.clientY-ly);lx=e.clientX;ly=e.clientY;pan(d);});
window.addEventListener('mouseup',function(){drag=false;});
cv.addEventListener('touchstart',function(e){if(!S.img)return;var t=e.touches[0];drag=true;lx=t.clientX;ly=t.clientY;},{passive:true});
cv.addEventListener('touchmove',function(e){if(!drag)return;var t=e.touches[0];var d=mp(t.clientX-lx,t.clientY-ly);lx=t.clientX;ly=t.clientY;pan(d);e.preventDefault();},{passive:false});
cv.addEventListener('touchend',function(){drag=false;});

window.rcDownload=function(){
    var st=g('rcStatus');st.textContent='Menyiapkan…';
    var go=function(){var d=dims(),fmt=g('rcFmt').value;var c=document.createElement('canvas');c.width=d[0]*1.5;c.height=d[1]*1.5;draw(c.getContext('2d'),c.width,c.height);
        c.toBlob(function(b){if(!b){st.textContent='Gagal.';return;}var nm=(S.title||'rilis').replace(/[^\w\- ]+/g,'').trim().replace(/\s+/g,'_')||'rilis';var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=nm+'_'+S.phase+'_'+(S.aspect==='story'?'story':'feed')+'.'+(fmt==='png'?'png':'jpg');document.body.appendChild(a);a.click();a.remove();setTimeout(function(){URL.revokeObjectURL(a.href);},4000);st.textContent='✅ Terunduh.';},fmt==='png'?'image/png':'image/jpeg',0.92);};
    if(document.fonts&&document.fonts.ready)document.fonts.ready.then(go).catch(go);else go();
};

redraw();
if(document.fonts&&document.fonts.ready)document.fonts.ready.then(redraw).catch(function(){});
})();
</script>
@endpush
