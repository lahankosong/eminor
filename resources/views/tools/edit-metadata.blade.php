@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); --green:#22c55e; }
    .md-page { padding:1.5rem 1rem 4rem; }
    .md-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .md-back:hover { color:var(--text,#f0f0f0); }
    .md-hero { text-align:center;margin-bottom:1.5rem; }
    .md-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .md-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .md-hero p { font-size:13px;color:var(--text-3,#94a3b8);max-width:540px;margin:0 auto;line-height:1.7; }
    .md-drop { border:2px dashed var(--border,#334155);border-radius:18px;padding:2rem 1.5rem;text-align:center;cursor:pointer;transition:.2s;background:var(--card-bg,#0f172a); }
    .md-drop:hover,.md-drop.drag-over { border-color:var(--ac);background:var(--ac-lt); }
    #mdFile { display:none; }
    .md-editor { display:none;margin-top:1rem; }
    .md-editor.show { display:block; }
    .md-info { font-size:12px;color:var(--text-3,#94a3b8);background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:10px;padding:.6rem .8rem;margin-bottom:1rem; }
    .md-cardrow { display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap;margin-bottom:1rem; }
    .md-cover { width:120px;height:120px;border-radius:12px;object-fit:cover;border:1px solid var(--border,#334155);background:#0b1220;flex-shrink:0; }
    .md-coverbtn { display:inline-block;margin-top:7px;font-size:11px;color:var(--ac);cursor:pointer;text-decoration:underline; }
    #mdCoverIn { display:none; }
    .md-grid { display:grid;grid-template-columns:1fr 1fr;gap:11px;flex:1;min-width:240px; }
    .md-fg.full { grid-column:1/-1; }
    .md-fg label { display:block;font-size:11px;font-weight:700;color:var(--text-2,#cbd5e1);margin-bottom:4px;text-transform:uppercase;letter-spacing:.03em; }
    .md-input { width:100%;background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:9px;padding:9px 11px;font-size:14px;color:var(--text,#f0f0f0);outline:none;font-family:inherit; }
    .md-input:focus { border-color:var(--ac); }
    .md-out { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:12px;padding:1rem;margin-top:.25rem; }
    .md-seg { display:flex;gap:4px;background:var(--bg-2,#0f1e2e);border:1px solid var(--border,#334155);border-radius:9px;padding:3px;margin:6px 0; }
    .md-seg button { flex:1;border:none;background:none;color:var(--text-3,#94a3b8);font-size:12.5px;font-weight:600;padding:8px;border-radius:6px;cursor:pointer;font-family:inherit; }
    .md-seg button.on { background:var(--ac);color:#fff; }
    .md-note { font-size:11.5px;color:var(--text-3,#94a3b8);line-height:1.5;min-height:30px; }
    .md-go { width:100%;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;border:none;border-radius:11px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;margin-top:.6rem; }
    .md-go:disabled { opacity:.5;cursor:not-allowed; }
    .md-status { font-size:12px;color:var(--text-3,#94a3b8);min-height:16px;margin-top:6px;text-align:center; }
    .md-cta { margin-top:2.5rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg,#0f172a));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .md-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.4rem; }
    .md-cta p { font-size:12.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:470px;margin:0 auto .9rem; }
    .md-cta-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
    .md-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;margin-top:2rem; }
    .md-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .md-info-card .i { font-size:1.4rem;margin-bottom:.4rem; }
    .md-info-card .t { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .md-info-card .b { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }
</style>
@endpush

@section('content')
<div class="page-rail-wrap">
<div class="md-page">
    <a href="{{ route('home') }}" class="md-back">← Beranda</a>
    @include('partials.tool-share')

    <div class="md-hero">
        <div class="md-badge">🏷️ Tool Gratis</div>
        <h1>Edit Metadata Lagu &amp; Konversi WAV</h1>
        <p>Rapikan <b>tag MP3</b> (judul, artis, album, tahun, genre, track) + <b>tanam cover art</b>, atau konversi ke <b>WAV lossless</b> untuk dikirim ke <b>agregator (DistroKid, dll)</b>. Di browser, tanpa upload, gratis.</p>
    </div>

    <div class="md-drop" id="mdDrop" onclick="document.getElementById('mdFile').click()">
        <div style="font-size:2.2rem;margin-bottom:.5rem;">🎵</div>
        <div style="font-size:14px;font-weight:600;color:var(--text,#f0f0f0);">Seret file lagu ke sini atau klik untuk pilih</div>
        <div style="font-size:11px;color:var(--text-3,#94a3b8);margin-top:.25rem;">MP3 · WAV · M4A · FLAC · OGG &nbsp;|&nbsp; Maks 150 MB &nbsp;|&nbsp; Tidak diunggah</div>
    </div>
    <input type="file" id="mdFile" accept="audio/*">

    <div class="md-editor" id="mdEditor">
        <div class="md-info" id="mdInfo">—</div>

        <div class="md-cardrow">
            <div style="text-align:center;">
                <img id="mdCover" class="md-cover" alt="cover" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Crect width='120' height='120' fill='%230b1220'/%3E%3Ctext x='60' y='66' font-size='34' text-anchor='middle' fill='%23334155'%3E%E2%99%AA%3C/text%3E%3C/svg%3E">
                <label class="md-coverbtn" onclick="document.getElementById('mdCoverIn').click()">🖼️ Ganti cover</label>
                <input type="file" id="mdCoverIn" accept="image/*">
            </div>
            <div class="md-grid">
                <div class="md-fg full"><label>Judul</label><input type="text" id="mdTitle" class="md-input" maxlength="80"></div>
                <div class="md-fg full"><label>Artis</label><input type="text" id="mdArtist" class="md-input" maxlength="80"></div>
                <div class="md-fg"><label>Album</label><input type="text" id="mdAlbum" class="md-input" maxlength="80"></div>
                <div class="md-fg"><label>Tahun</label><input type="number" id="mdYear" class="md-input" min="1900" max="2099" placeholder="2026"></div>
                <div class="md-fg"><label>Genre</label><input type="text" id="mdGenre" class="md-input" maxlength="40" placeholder="Pop, Indie…"></div>
                <div class="md-fg"><label>No. Track</label><input type="text" id="mdTrack" class="md-input" maxlength="7" placeholder="1 atau 1/12"></div>
                <div class="md-fg"><label>Album Artist</label><input type="text" id="mdAlbumArtist" class="md-input" maxlength="80"></div>
                <div class="md-fg"><label>Composer</label><input type="text" id="mdComposer" class="md-input" maxlength="80"></div>
                <div class="md-fg"><label>Label / Publisher</label><input type="text" id="mdPublisher" class="md-input" maxlength="80"></div>
                <div class="md-fg"><label>Copyright</label><input type="text" id="mdCopyright" class="md-input" maxlength="120" placeholder="© 2026 Nama"></div>
                <div class="md-fg full"><label>Komentar</label><input type="text" id="mdComment" class="md-input" maxlength="200"></div>
            </div>
        </div>
        <div id="mdTech" style="font-size:11.5px;color:var(--text-3,#94a3b8);background:var(--bg-2,#0f1e2e);border:1px solid var(--border,#334155);border-radius:9px;padding:.55rem .75rem;margin-bottom:1rem;display:none;"></div>

        <div class="md-out">
            <button type="button" onclick="mdClear()" style="width:100%;background:transparent;border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8);border-radius:8px;padding:8px;font-size:12px;font-weight:600;cursor:pointer;margin-bottom:.85rem;font-family:inherit;">🧹 Bersihkan semua metadata (hapus tag bawaan / reset)</button>
            <label style="font-size:11px;font-weight:700;color:var(--text-2,#cbd5e1);text-transform:uppercase;letter-spacing:.03em;">Format Unduhan</label>
            <div class="md-seg" id="mdFmtSeg">
                <button data-v="mp3" class="on">MP3 (ber-tag + cover)</button>
                <button data-v="wav">WAV (untuk agregator)</button>
            </div>
            <div class="md-note" id="mdNote"></div>
            <button class="md-go" id="mdBtn" onclick="mdExport()">⬇️ Unduh Lagu</button>
            <div class="md-status" id="mdStatus"></div>
        </div>
    </div>

    <div class="md-cta">
        <h2>🎸 Siap rilis lagumu?</h2>
        <p>Tag rapi + WAV lossless = siap dikirim ke agregator. Lalu tampilkan karyamu di <b>EMINOR</b> — profil portofolio gratis, matchmaking personil & gig, komunitas musisi.</p>
        <a href="{{ route('google.login') }}" class="md-cta-btn">Buat profil musisi gratis →</a>
    </div>

    <div class="md-info-grid">
        <div class="md-info-card"><div class="i">🏷️</div><div class="t">Tag Lengkap</div><div class="b">Judul, artis, album artist, composer, tahun, genre, track, label, copyright + baca bitrate/sample-rate.</div></div>
        <div class="md-info-card"><div class="i">🖼️</div><div class="t">Cover Tertanam</div><div class="b">Sematkan cover art langsung ke MP3 (APIC). Pakai gambar dari <a href="{{ route('tools.cover-art') }}" style="color:var(--ac);">Cover Maker</a>.</div></div>
        <div class="md-info-card"><div class="i">🎚️</div><div class="t">WAV untuk Agregator</div><div class="b">DistroKid/TuneCore minta audio lossless — konversi ke WAV langsung di sini.</div></div>
        <div class="md-info-card"><div class="i">🔒</div><div class="t">Tanpa Upload</div><div class="b">File diproses di browser, tidak dikirim ke server.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">EMINOR Ekosistem</a> 🎸 ·
        <a href="{{ route('tools.cover-art') }}" style="color:var(--ac);">Buat Cover</a> ·
        <a href="{{ route('tools.potong-lagu') }}" style="color:var(--ac);">Potong Lagu</a>
    </p>

</div>{{-- .md-page --}}

<aside class="page-rail-aside">
    @include('partials.content-rail')
</aside>

</div>{{-- .page-rail-wrap --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsmediatags@3.9.7/dist/jsmediatags.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/browser-id3-writer@4.4.0/dist/browser-id3-writer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script>
(function(){
'use strict';
var S={file:null,origBuf:null,isMp3:false,audioBuf:null,coverBuf:null,name:'lagu'};
function g(id){return document.getElementById(id);}
function v(id){return (g(id).value||'').trim();}
function st(t){g('mdStatus').textContent=t||'';}

var NOTES={mp3:'MP3 ber-tag + cover tertanam — untuk pemutar, arsip, atau kirim manual. Audio tetap dari sumbernya.',
           wav:'WAV lossless (16-bit) — audio untuk agregator (DistroKid/TuneCore). Metadata cukup diisi lagi di form agregator; WAV memang tak menyimpan cover.'};
function setNote(){g('mdNote').textContent=NOTES[fmt()];}
function fmt(){var b=g('mdFmtSeg').querySelector('button.on');return b?b.getAttribute('data-v'):'mp3';}
[].forEach.call(g('mdFmtSeg').querySelectorAll('button'),function(b){b.onclick=function(){[].forEach.call(g('mdFmtSeg').querySelectorAll('button'),function(x){x.classList.remove('on');});b.classList.add('on');setNote();};});
setNote();

var drop=g('mdDrop');
drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('drag-over');});
drop.addEventListener('dragleave',function(){drop.classList.remove('drag-over');});
drop.addEventListener('drop',function(e){e.preventDefault();drop.classList.remove('drag-over');if(e.dataTransfer.files[0])load(e.dataTransfer.files[0]);});
g('mdFile').addEventListener('change',function(){if(this.files[0])load(this.files[0]);});
g('mdCoverIn').addEventListener('change',function(){if(this.files[0])setCover(this.files[0]);});

function load(file){
    if(file.size>150*1024*1024){alert('Maks 150 MB');return;}
    S.file=file;S.name=file.name.replace(/\.[^.]+$/,'');S.audioBuf=null;S.coverBuf=null;st('Membaca…');
    g('mdCover').src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Crect width='120' height='120' fill='%230b1220'/%3E%3Ctext x='60' y='66' font-size='34' text-anchor='middle' fill='%23334155'%3E%E2%99%AA%3C/text%3E%3C/svg%3E";
    S.isMp3=/mp3|mpeg/i.test(file.type)||/\.mp3$/i.test(file.name);
    var r=new FileReader();
    r.onload=function(ev){
        S.origBuf=ev.target.result;
        g('mdInfo').innerHTML='🎵 <b>'+file.name+'</b> · '+(file.size/1024/1024).toFixed(1)+' MB'+(S.isMp3?'':' <span style="color:#f59e0b">[bukan MP3 — output MP3 akan di-encode ulang]</span>');
        g('mdEditor').classList.add('show');st('');
        // 1) BACA metadata LAMA dulu → 2) isi form untuk diedit
        ['mdTitle','mdArtist','mdAlbum','mdYear','mdGenre','mdTrack','mdAlbumArtist','mdComposer','mdPublisher','mdCopyright','mdComment'].forEach(function(id){g(id).value='';});
        g('mdTitle').value=S.name;
        showTech();
        var noteEl=document.createElement('div');noteEl.style.cssText='margin-top:5px;font-size:11px;';g('mdInfo').appendChild(noteEl);
        function readNote(html,col){noteEl.innerHTML=html;noteEl.style.color=col||'#94a3b8';}
        if(!window.jsmediatags){ readNote('⚠️ Pembaca tag belum termuat — coba muat ulang halaman.','#f59e0b'); }
        else {
            readNote('🔎 Membaca metadata lama…');
            window.jsmediatags.read(file,{onSuccess:function(res){
                var t=res.tags||{},found=[];
                if(t.title){g('mdTitle').value=t.title;found.push('judul');}
                if(t.artist){g('mdArtist').value=t.artist;found.push('artis');}
                if(t.album){g('mdAlbum').value=t.album;found.push('album');}
                if(t.year){g('mdYear').value=(''+t.year).replace(/\D/g,'').slice(0,4);found.push('tahun');}
                if(t.genre){g('mdGenre').value=t.genre;found.push('genre');}
                if(t.track){g('mdTrack').value=(''+t.track);found.push('track');}
                var rt=function(id){var f=t[id];if(!f)return '';var d=f.data;if(typeof d==='string')return d;if(d&&typeof d.text==='string')return d.text;return '';};
                var aa=rt('TPE2');if(aa){g('mdAlbumArtist').value=aa;found.push('album artist');}
                var cm=rt('TCOM');if(cm){g('mdComposer').value=cm;found.push('composer');}
                var pb=rt('TPUB');if(pb){g('mdPublisher').value=pb;found.push('publisher');}
                var cp=rt('TCOP');if(cp){g('mdCopyright').value=cp;found.push('copyright');}
                var co=(t.comment&&t.comment.text)?t.comment.text:rt('COMM');if(co){g('mdComment').value=co;found.push('komentar');}
                if(t.picture&&t.picture.data&&t.picture.data.length){
                    var u8=new Uint8Array(t.picture.data);S.coverBuf=u8.buffer.slice(0);
                    try{g('mdCover').src=URL.createObjectURL(new Blob([u8],{type:t.picture.format||'image/jpeg'}));}catch(e){}
                    found.push('cover');
                }
                readNote(found.length?('✓ Metadata lama dimuat ('+found.join(', ')+') — silakan edit di bawah.'):'ℹ️ File ini belum punya metadata — isi manual lalu unduh.', found.length?'#22c55e':'#94a3b8');
            },onError:function(){ readNote('ℹ️ Tak ada metadata terbaca (mungkin format lain) — isi manual.','#94a3b8'); }});
        }
        window.scrollTo({top:g('mdEditor').offsetTop-20,behavior:'smooth'});
    };
    r.readAsArrayBuffer(file);
}
function setCover(file){
    if(!/^image\//.test(file.type)){st('Cover harus gambar.');return;}
    var r=new FileReader();r.onload=function(ev){S.coverBuf=ev.target.result;g('mdCover').src=URL.createObjectURL(file);};r.readAsArrayBuffer(file);
}

function dl(blob,ext){
    var nm=(v('mdArtist')?v('mdArtist')+' - ':'')+(v('mdTitle')||S.name);
    nm=nm.replace(/[^\w\-. ]+/g,'').trim().replace(/\s+/g,'_')||'lagu';
    var a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=nm+'.'+ext;document.body.appendChild(a);a.click();a.remove();
    setTimeout(function(){URL.revokeObjectURL(a.href);},5000);st('✅ Terunduh ('+ext.toUpperCase()+').');
}
function decode(cb,err){
    if(S.audioBuf){cb(S.audioBuf);return;}
    try{var ac=new(window.AudioContext||window.webkitAudioContext)();
        ac.decodeAudioData(S.origBuf.slice(0),function(b){S.audioBuf=b;cb(b);},function(){err('format tak bisa didekode browser');});}
    catch(e){err(e.message);}
}
function tagMp3(buf){
    if(!window.ID3Writer)throw new Error('Library ID3 belum termuat, muat ulang halaman');
    var w=new window.ID3Writer(buf);
    function setF(f,val){if(val!==''&&val!=null){try{w.setFrame(f,val);}catch(e){}}}
    setF('TIT2',v('mdTitle'));
    if(v('mdArtist'))try{w.setFrame('TPE1',[v('mdArtist')]);}catch(e){}
    setF('TALB',v('mdAlbum'));
    var yr=parseInt(v('mdYear'),10);if(!isNaN(yr))try{w.setFrame('TYER',yr);}catch(e){}
    if(v('mdGenre'))try{w.setFrame('TCON',[v('mdGenre')]);}catch(e){}
    setF('TRCK',v('mdTrack'));
    if(v('mdAlbumArtist'))try{w.setFrame('TPE2',[v('mdAlbumArtist')]);}catch(e){}
    if(v('mdComposer'))try{w.setFrame('TCOM',[v('mdComposer')]);}catch(e){}
    setF('TPUB',v('mdPublisher'));
    setF('TCOP',v('mdCopyright'));
    if(v('mdComment'))try{w.setFrame('COMM',{description:'',text:v('mdComment'),language:'ind'});}catch(e){}
    if(S.coverBuf)try{w.setFrame('APIC',{type:3,data:S.coverBuf,description:'cover'});}catch(e){}
    w.addTag();return w.getBlob();
}
function wavBlob(buf){
    var nCh=Math.min(2,buf.numberOfChannels),n=buf.length,sr=buf.sampleRate;
    var L=buf.getChannelData(0),R=nCh>1?buf.getChannelData(1):L;
    var ab=new ArrayBuffer(44+n*nCh*2),dv=new DataView(ab);
    function ws(o,s){for(var i=0;i<s.length;i++)dv.setUint8(o+i,s.charCodeAt(i));}
    ws(0,'RIFF');dv.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    dv.setUint32(16,16,true);dv.setUint16(20,1,true);dv.setUint16(22,nCh,true);
    dv.setUint32(24,sr,true);dv.setUint32(28,sr*nCh*2,true);dv.setUint16(32,nCh*2,true);dv.setUint16(34,16,true);
    ws(36,'data');dv.setUint32(40,n*nCh*2,true);var off=44;
    for(var i=0;i<n;i++){var x=Math.max(-1,Math.min(1,L[i]));dv.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;if(nCh>1){var y=Math.max(-1,Math.min(1,R[i]));dv.setInt16(off,y<0?y*0x8000:y*0x7FFF,true);off+=2;}}
    return new Blob([ab],{type:'audio/wav'});
}
function encodeMp3(buf){
    if(!window.lamejs)throw new Error('Library MP3 belum termuat');
    var nCh=Math.min(2,buf.numberOfChannels),sr=buf.sampleRate,n=buf.length;
    var L=buf.getChannelData(0),R=nCh>1?buf.getChannelData(1):L;
    function f2i(f){var a=new Int16Array(f.length);for(var i=0;i<f.length;i++)a[i]=Math.max(-32768,Math.min(32767,f[i]*32767));return a;}
    var l16=f2i(L),r16=nCh>1?f2i(R):l16,enc=new lamejs.Mp3Encoder(nCh,sr,320),out=[],BLK=1152;
    for(var i=0;i<n;i+=BLK){var d=nCh>1?enc.encodeBuffer(l16.subarray(i,i+BLK),r16.subarray(i,i+BLK)):enc.encodeBuffer(l16.subarray(i,i+BLK));if(d.length)out.push(new Uint8Array(d));}
    var e=enc.flush();if(e.length)out.push(new Uint8Array(e));
    var len=0;out.forEach(function(u){len+=u.length;});var all=new Uint8Array(len),p=0;out.forEach(function(u){all.set(u,p);p+=u.length;});
    return all.buffer;
}

function parseMp3Header(u8){
    var i=0;
    if(u8.length>10&&u8[0]===0x49&&u8[1]===0x44&&u8[2]===0x33){var sz=((u8[6]&0x7f)<<21)|((u8[7]&0x7f)<<14)|((u8[8]&0x7f)<<7)|(u8[9]&0x7f);i=10+sz;}
    for(;i<u8.length-4;i++){
        if(u8[i]===0xFF&&(u8[i+1]&0xE0)===0xE0){
            var b1=u8[i+1],b2=u8[i+2],verB=(b1>>3)&3,layB=(b1>>1)&3;
            if(verB===1||layB===0)continue;
            var brI=(b2>>4)&0x0F,srI=(b2>>2)&3,ch=(u8[i+3]>>6)&3;
            if(brI===0||brI===15||srI===3)continue;
            var ver=verB===3?'1':(verB===2?'2':'2.5'),lay=layB===3?1:(layB===2?2:3),brTab,srTab;
            if(ver==='1')brTab=lay===1?[0,32,64,96,128,160,192,224,256,288,320,352,384,416,448]:lay===2?[0,32,48,56,64,80,96,112,128,160,192,224,256,320,384]:[0,32,40,48,56,64,80,96,112,128,160,192,224,256,320];
            else brTab=lay===1?[0,32,48,56,64,80,96,112,128,144,160,176,192,224,256]:[0,8,16,24,32,40,48,56,64,80,96,112,128,144,160];
            srTab=ver==='1'?[44100,48000,32000]:(ver==='2'?[22050,24000,16000]:[11025,12000,8000]);
            return {bitrate:brTab[brI],sampleRate:srTab[srI],channels:ch===3?1:2};
        }
    }
    return null;
}
function showTech(){
    var el=g('mdTech');el.style.display='block';el.innerHTML='🎚️ Membaca info teknis…';
    var hdr=null;try{hdr=S.isMp3?parseMp3Header(new Uint8Array(S.origBuf)):null;}catch(e){}
    var base=[((S.file.name.split('.').pop()||'audio').toUpperCase())];
    var au=new Audio();au.preload='metadata';var url=URL.createObjectURL(S.file);
    function render(dur){
        var p=base.slice();
        if(dur&&isFinite(dur)&&dur>0)p.push('~'+Math.round(S.file.size*8/dur/1000)+' kbps');
        else if(hdr)p.push(hdr.bitrate+' kbps');
        if(hdr){p.push((hdr.sampleRate/1000)+' kHz');p.push(hdr.channels===1?'Mono':'Stereo');}
        if(dur&&isFinite(dur)&&dur>0){var m=Math.floor(dur/60),s=Math.floor(dur%60);p.push(m+':'+(s<10?'0':'')+s);}
        el.innerHTML='🎚️ <b>Info teknis</b> (audio, tak diedit): '+p.join(' · ');
    }
    au.onloadedmetadata=function(){var d=au.duration;URL.revokeObjectURL(url);render(d);};
    au.onerror=function(){URL.revokeObjectURL(url);render(0);};
    au.src=url;
}

window.mdClear=function(){
    if(!S.origBuf){st('Pilih file dulu.');return;}
    ['mdTitle','mdArtist','mdAlbum','mdYear','mdGenre','mdTrack','mdAlbumArtist','mdComposer','mdPublisher','mdCopyright','mdComment'].forEach(function(id){g(id).value='';});
    S.coverBuf=null;
    g('mdCover').src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Crect width='120' height='120' fill='%230b1220'/%3E%3Ctext x='60' y='66' font-size='34' text-anchor='middle' fill='%23334155'%3E%E2%99%AA%3C/text%3E%3C/svg%3E";
    st('🧹 Semua tag dikosongkan. Klik "Unduh Lagu" (format MP3) untuk file tanpa metadata bawaan.');
};

window.mdExport=function(){
    if(!S.origBuf){st('Pilih file dulu.');return;}
    var btn=g('mdBtn');btn.disabled=true;st('Memproses…');
    function fin(){btn.disabled=false;}
    var f=fmt();
    setTimeout(function(){
        if(f==='wav'){
            decode(function(b){try{dl(wavBlob(b),'wav');}catch(e){st('⚠️ '+e.message);}fin();},function(e){st('⚠️ '+e);fin();});
        }else{
            if(S.isMp3){try{dl(tagMp3(S.origBuf),'mp3');}catch(e){st('⚠️ '+e.message);}fin();}
            else{decode(function(b){try{dl(tagMp3(encodeMp3(b)),'mp3');}catch(e){st('⚠️ '+e.message);}fin();},function(e){st('⚠️ '+e);fin();});}
        }
    },40);
};
})();
</script>
@endpush

