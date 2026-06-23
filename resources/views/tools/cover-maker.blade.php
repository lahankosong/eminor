@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); --green:#22c55e; }
    .cm-page { max-width:880px; margin:0 auto; padding:1.5rem 1rem 4rem; }
    .cm-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .cm-back:hover { color:var(--text,#f0f0f0); }
    .cm-hero { text-align:center;margin-bottom:1.5rem; }
    .cm-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .cm-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .cm-hero p { font-size:13px;color:var(--text-3,#94a3b8);max-width:540px;margin:0 auto;line-height:1.7; }

    .cm-wrap { display:grid;grid-template-columns:minmax(0,360px) 1fr;gap:1.25rem;align-items:start; }
    @@media(max-width:680px){ .cm-wrap{grid-template-columns:1fr;} }

    .cm-stage { position:sticky;top:1rem; }
    .cm-canvas-box { position:relative;border-radius:14px;overflow:hidden;background:#070d18;border:1px solid var(--border,#334155);aspect-ratio:1/1; }
    #cmCanvas { display:block;width:100%;height:100%;cursor:grab;touch-action:none; }
    #cmCanvas:active { cursor:grabbing; }
    .cm-stage-hint { font-size:10px;color:var(--text-3,#94a3b8);text-align:center;margin-top:5px; }

    .cm-ctrl { display:flex;flex-direction:column;gap:.85rem; }
    .cm-fg label { display:block;font-size:11px;font-weight:700;color:var(--text-2,#cbd5e1);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em; }
    .cm-input { width:100%;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:9px 11px;font-size:14px;color:var(--text,#f0f0f0);outline:none;font-family:inherit; }
    .cm-input:focus { border-color:var(--ac); }
    .cm-drop { border:2px dashed var(--border,#334155);border-radius:11px;padding:1rem;text-align:center;cursor:pointer;font-size:12px;color:var(--text-3,#94a3b8);transition:.2s; }
    .cm-drop:hover,.cm-drop.drag-over { border-color:var(--ac);background:var(--ac-lt); }
    #cmImg { display:none; }
    .cm-row { display:flex;gap:6px;flex-wrap:wrap; }
    .cm-seg { display:flex;gap:4px;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:3px; }
    .cm-seg button { flex:1;border:none;background:none;color:var(--text-3,#94a3b8);font-size:12px;font-weight:600;padding:6px 10px;border-radius:6px;cursor:pointer;font-family:inherit; }
    .cm-seg button.on { background:var(--ac);color:#fff; }
    .cm-grads { display:flex;gap:6px;flex-wrap:wrap; }
    .cm-grad { width:30px;height:30px;border-radius:7px;cursor:pointer;border:2px solid transparent; }
    .cm-grad.on { border-color:#fff;box-shadow:0 0 0 2px var(--ac); }
    .cm-range { width:100%; }
    .cm-check { display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text-2,#cbd5e1);cursor:pointer; }
    .cm-go { background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;border:none;border-radius:11px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(56,189,248,.25); }
    .cm-go:hover { transform:translateY(-1px); }
    .cm-status { font-size:12px;color:var(--text-3,#94a3b8);min-height:15px; }

    .cm-cta { margin-top:2.5rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg,#0f172a));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .cm-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.4rem; }
    .cm-cta p { font-size:12.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:470px;margin:0 auto .9rem; }
    .cm-cta-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
    .cm-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:2rem; }
    .cm-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .cm-info-card .i { font-size:1.4rem;margin-bottom:.4rem; }
    .cm-info-card .t { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .cm-info-card .b { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }
</style>
@endpush

@section('content')
<div class="cm-page">
    <a href="{{ route('home') }}" class="cm-back">← Beranda</a>

    <div class="cm-hero">
        <div class="cm-badge">🎨 Tool Gratis</div>
        <h1>Buat Cover Lagu / Album Online</h1>
        <p>Bikin cover art <b>persegi 1:1</b> siap rilis ke <b>Spotify, Apple Music, YouTube</b> — resolusi hingga <b>3000&nbsp;px</b>. Tambah foto, judul, &amp; nama artis. Gratis, tanpa upload, langsung unduh.</p>
    </div>

    <div class="cm-wrap">
        <div class="cm-stage">
            <div class="cm-canvas-box"><canvas id="cmCanvas" width="640" height="640"></canvas></div>
            <div class="cm-stage-hint">Seret foto di dalam kotak untuk atur posisi · preview = hasil</div>
        </div>

        <div class="cm-ctrl">
            <div class="cm-fg">
                <label>Foto / Gambar (opsional)</label>
                <div class="cm-drop" id="cmDrop" onclick="document.getElementById('cmImg').click()">📷 Seret / klik untuk pilih foto</div>
                <input type="file" id="cmImg" accept="image/*">
                <div class="cm-row" id="cmImgCtrl" style="display:none;margin-top:8px;align-items:center;gap:8px;">
                    <span style="font-size:15px;">🔍</span>
                    <input type="range" id="cmZoom" class="cm-range" min="1" max="3" step="0.01" value="1" style="flex:1;">
                    <button onclick="cmRemoveImg()" style="background:var(--card-bg,#1e293b);border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8);border-radius:8px;padding:6px 10px;font-size:11px;cursor:pointer;">🗑 Hapus</button>
                </div>
            </div>

            <div class="cm-fg">
                <label>Latar (bila tanpa foto)</label>
                <div class="cm-grads" id="cmGrads"></div>
            </div>

            <div class="cm-fg">
                <label>Judul Lagu</label>
                <input type="text" id="cmTitle" class="cm-input" maxlength="40" value="Judul Lagu">
            </div>
            <div class="cm-fg">
                <label>Nama Artis</label>
                <input type="text" id="cmArtist" class="cm-input" maxlength="30" value="NAMA ARTIS">
            </div>

            <div class="cm-row">
                <div class="cm-fg" style="flex:1;">
                    <label>Posisi Teks</label>
                    <div class="cm-seg" id="cmPos">
                        <button data-v="top">Atas</button>
                        <button data-v="center">Tengah</button>
                        <button data-v="bottom" class="on">Bawah</button>
                    </div>
                </div>
                <div class="cm-fg" style="flex:1;">
                    <label>Warna Teks</label>
                    <div class="cm-seg" id="cmTC">
                        <button data-v="#ffffff" class="on">Terang</button>
                        <button data-v="#0b0f17">Gelap</button>
                    </div>
                </div>
            </div>

            <label class="cm-check"><input type="checkbox" id="cmScrim" checked> Gradasi gelap di balik teks (biar terbaca)</label>

            <div class="cm-row">
                <div class="cm-fg" style="flex:1;">
                    <label>Ukuran</label>
                    <select id="cmSize" class="cm-input">
                        <option value="3000">3000 × 3000 (Spotify)</option>
                        <option value="2000">2000 × 2000</option>
                        <option value="1600">1600 × 1600 (min)</option>
                    </select>
                </div>
                <div class="cm-fg" style="flex:1;">
                    <label>Format</label>
                    <select id="cmFmt" class="cm-input">
                        <option value="jpg">JPG (ringan)</option>
                        <option value="png">PNG (tajam)</option>
                    </select>
                </div>
            </div>

            <button class="cm-go" onclick="cmDownload()">⬇️ Unduh Cover</button>
            <div class="cm-status" id="cmStatus"></div>
        </div>
    </div>

    <div class="cm-cta">
        <h2>🎸 Mau rilis lagumu?</h2>
        <p>Setelah cover-nya jadi, tampilkan karyamu di <b>Margonoandi</b> — buat <b>profil portofolio gratis</b> (kartu + QR), temukan <b>personil &amp; gig</b>, gabung komunitas musisi Indonesia.</p>
        <a href="{{ route('google.login') }}" class="cm-cta-btn">Buat profil musisi gratis →</a>
    </div>

    <div class="cm-info-grid">
        <div class="cm-info-card"><div class="i">📐</div><div class="t">Sesuai Standar</div><div class="b">Persegi 1:1, 1600–3000 px — memenuhi syarat Spotify, Apple Music, distributor.</div></div>
        <div class="cm-info-card"><div class="i">🔒</div><div class="t">Tanpa Upload</div><div class="b">Foto diproses di browser, tidak dikirim ke server. Privasi aman.</div></div>
        <div class="cm-info-card"><div class="i">⚡</div><div class="t">Instan &amp; Gratis</div><div class="b">Tak perlu Photoshop atau daftar. Atur, lalu unduh PNG/JPG.</div></div>
        <div class="cm-info-card"><div class="i">🎨</div><div class="t">Teks Otomatis Rapi</div><div class="b">Judul &amp; artis dengan gradasi keterbacaan — tampil profesional.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> 🎸 ·
        <a href="{{ route('tools.hapus-vokal') }}" style="color:var(--ac);">Hapus Vokal</a> ·
        <a href="{{ route('tools.potong-lagu') }}" style="color:var(--ac);">Potong Lagu</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
(function(){
'use strict';
var PV=640;
var GRADS=[['#0f2027','#2c5364'],['#16222a','#3a6073'],['#0f0c29','#302b63'],['#232526','#414345'],
           ['#f12711','#f5af19'],['#ee0979','#ff6a00'],['#11998e','#38ef7d'],['#000000','#434343']];
var S={ img:null, zoom:1, panX:0, panY:0, grad:0, title:'Judul Lagu', artist:'NAMA ARTIS',
        pos:'bottom', tc:'#ffffff', scrim:true };
var cv=document.getElementById('cmCanvas'), ctx=cv.getContext('2d');

function g(id){return document.getElementById(id);}
function maxPan(S2){ if(!S.img)return 0; var base=Math.max(S2/S.img.width,S2/S.img.height),dw=S.img.width*base*S.zoom; return Math.max(0,(dw-S2)/(2*S2)); }

function wrap(c,text,maxW,maxLines){
    var words=(text||'').split(/\s+/),line='',lines=[];
    for(var i=0;i<words.length;i++){var t=line?line+' '+words[i]:words[i];
        if(c.measureText(t).width>maxW&&line){lines.push(line);line=words[i];if(lines.length===maxLines)break;}else line=t;}
    if(line&&lines.length<maxLines)lines.push(line);
    if(lines.length>=maxLines){var L=lines[maxLines-1];while(c.measureText(L+'…').width>maxW&&L.length)L=L.slice(0,-1);lines[maxLines-1]=L+'…';}
    return lines;
}

function draw(c,Z){
    // latar gradien
    var gr=GRADS[S.grad],lg=c.createLinearGradient(0,0,Z*0.4,Z);lg.addColorStop(0,gr[0]);lg.addColorStop(1,gr[1]);
    c.fillStyle=lg;c.fillRect(0,0,Z,Z);
    // foto (cover-fit + pan/zoom)
    if(S.img){
        var base=Math.max(Z/S.img.width,Z/S.img.height),ds=base*S.zoom,dw=S.img.width*ds,dh=S.img.height*ds;
        var ox=(Z-dw)/2+S.panX*Z,oy=(Z-dh)/2+S.panY*Z;
        ox=Math.min(0,Math.max(Z-dw,ox));oy=Math.min(0,Math.max(Z-dh,oy));
        c.drawImage(S.img,ox,oy,dw,dh);
    }
    // scrim gradasi
    if(S.scrim){
        var sg=c.createLinearGradient(0,0,0,Z);
        if(S.pos==='bottom'){sg.addColorStop(0,'rgba(0,0,0,0)');sg.addColorStop(.5,'rgba(0,0,0,0)');sg.addColorStop(1,'rgba(0,0,0,.78)');}
        else if(S.pos==='top'){sg.addColorStop(0,'rgba(0,0,0,.78)');sg.addColorStop(.5,'rgba(0,0,0,0)');sg.addColorStop(1,'rgba(0,0,0,0)');}
        else{sg.addColorStop(0,'rgba(0,0,0,0)');sg.addColorStop(.28,'rgba(0,0,0,.6)');sg.addColorStop(.72,'rgba(0,0,0,.6)');sg.addColorStop(1,'rgba(0,0,0,0)');}
        c.fillStyle=sg;c.fillRect(0,0,Z,Z);
    }
    // teks
    var pad=Z*0.075,maxW=Z-pad*2,tSize=Z*0.082,aSize=Z*0.036,gap=Z*0.018;
    c.textAlign='center';c.textBaseline='alphabetic';
    c.shadowColor=S.tc==='#ffffff'?'rgba(0,0,0,.5)':'rgba(255,255,255,.35)';c.shadowBlur=Z*0.012;
    c.font='700 '+tSize+'px "Space Grotesk","Inter",sans-serif';
    var tLines=S.title?wrap(c,S.title,maxW,3):[];
    var tH=tLines.length*tSize*1.08;
    var aH=S.artist?aSize+gap:0;
    var blockH=tH+aH;
    var top;
    if(S.pos==='top')top=pad;
    else if(S.pos==='bottom')top=Z-pad-blockH;
    else top=(Z-blockH)/2;
    c.fillStyle=S.tc;
    var y=top+tSize;
    for(var i=0;i<tLines.length;i++){c.fillText(tLines[i],Z/2,y);y+=tSize*1.08;}
    if(S.artist){
        c.font='600 '+aSize+'px "Space Grotesk","Inter",sans-serif';
        try{c.letterSpacing=(Z*0.012)+'px';}catch(e){}
        c.globalAlpha=.92;
        c.fillText(S.artist.toUpperCase(),Z/2,y+gap+aSize*0.2);
        c.globalAlpha=1;try{c.letterSpacing='0px';}catch(e){}
    }
    c.shadowBlur=0;
}
function redraw(){ draw(ctx,PV); }

// ── kontrol gradien ──
var gw=g('cmGrads');
GRADS.forEach(function(p,i){
    var d=document.createElement('div');d.className='cm-grad'+(i===0?' on':'');
    d.style.background='linear-gradient(135deg,'+p[0]+','+p[1]+')';
    d.onclick=function(){S.grad=i;[].forEach.call(gw.children,function(x){x.classList.remove('on');});d.classList.add('on');redraw();};
    gw.appendChild(d);
});
// segmen posisi & warna
function seg(id,key){var w=g(id);[].forEach.call(w.querySelectorAll('button'),function(b){b.onclick=function(){S[key]=b.getAttribute('data-v');[].forEach.call(w.querySelectorAll('button'),function(x){x.classList.remove('on');});b.classList.add('on');redraw();};});}
seg('cmPos','pos');seg('cmTC','tc');
g('cmTitle').addEventListener('input',function(){S.title=this.value;redraw();});
g('cmArtist').addEventListener('input',function(){S.artist=this.value;redraw();});
g('cmScrim').addEventListener('change',function(){S.scrim=this.checked;redraw();});
g('cmZoom').addEventListener('input',function(){S.zoom=parseFloat(this.value);var m=maxPan(PV)/(PV);
    var mf=maxPan(1);S.panX=Math.max(-mf,Math.min(mf,S.panX));S.panY=Math.max(-mf,Math.min(mf,S.panY));redraw();});

// ── upload foto ──
var drop=g('cmDrop');
drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('drag-over');});
drop.addEventListener('dragleave',function(){drop.classList.remove('drag-over');});
drop.addEventListener('drop',function(e){e.preventDefault();drop.classList.remove('drag-over');if(e.dataTransfer.files[0])loadImg(e.dataTransfer.files[0]);});
g('cmImg').addEventListener('change',function(){if(this.files[0])loadImg(this.files[0]);});
function loadImg(file){
    if(!/^image\//.test(file.type)){g('cmStatus').textContent='File harus gambar.';return;}
    var r=new FileReader();
    r.onload=function(ev){var im=new Image();im.onload=function(){S.img=im;S.zoom=1;S.panX=0;S.panY=0;g('cmZoom').value=1;g('cmImgCtrl').style.display='flex';g('cmStatus').textContent='';redraw();};im.src=ev.target.result;};
    r.readAsDataURL(file);
}
window.cmRemoveImg=function(){S.img=null;g('cmImg').value='';g('cmImgCtrl').style.display='none';redraw();};

// ── geser foto di kanvas ──
var drag=false,lx=0,ly=0;
function pxToPan(dxClient,dyClient){var sc=PV/cv.clientWidth;return [dxClient*sc/PV,dyClient*sc/PV];}
cv.addEventListener('mousedown',function(e){if(!S.img)return;drag=true;lx=e.clientX;ly=e.clientY;});
window.addEventListener('mousemove',function(e){if(!drag)return;var d=pxToPan(e.clientX-lx,e.clientY-ly);lx=e.clientX;ly=e.clientY;applyPan(d);});
window.addEventListener('mouseup',function(){drag=false;});
cv.addEventListener('touchstart',function(e){if(!S.img)return;var t=e.touches[0];drag=true;lx=t.clientX;ly=t.clientY;},{passive:true});
cv.addEventListener('touchmove',function(e){if(!drag)return;var t=e.touches[0];var d=pxToPan(t.clientX-lx,t.clientY-ly);lx=t.clientX;ly=t.clientY;applyPan(d);e.preventDefault();},{passive:false});
cv.addEventListener('touchend',function(){drag=false;});
function applyPan(d){var mf=maxPan(1);S.panX=Math.max(-mf,Math.min(mf,S.panX+d[0]));S.panY=Math.max(-mf,Math.min(mf,S.panY+d[1]));redraw();}

// ── unduh ──
window.cmDownload=function(){
    var st=g('cmStatus');st.textContent='Menyiapkan…';
    var go=function(){
        var Z=parseInt(g('cmSize').value),fmt=g('cmFmt').value;
        var c=document.createElement('canvas');c.width=Z;c.height=Z;
        draw(c.getContext('2d'),Z);
        var mime=fmt==='png'?'image/png':'image/jpeg';
        c.toBlob(function(b){
            if(!b){st.textContent='Gagal membuat gambar.';return;}
            var nm=(S.title||'cover').replace(/[^\w\- ]+/g,'').trim().replace(/\s+/g,'_')||'cover';
            var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=nm+'_'+Z+'.'+(fmt==='png'?'png':'jpg');document.body.appendChild(a);a.click();a.remove();
            setTimeout(function(){URL.revokeObjectURL(a.href);},4000);st.textContent='✅ Terunduh ('+Z+'×'+Z+').';
        },mime,0.92);
    };
    if(document.fonts&&document.fonts.ready)document.fonts.ready.then(go).catch(go);else go();
};

// gambar awal (tunggu font biar teks pas)
redraw();
if(document.fonts&&document.fonts.ready)document.fonts.ready.then(redraw).catch(function(){});
})();
</script>
@endpush
