@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38bdf8; --ac-dk:#0ea5e9; --ac-lt:rgba(56,189,248,.12); --green:#22c55e; }
    .vr-page { max-width:760px; margin:0 auto; padding:1.5rem 1rem 4rem; }
    .vr-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .vr-back:hover { color:var(--text,#f0f0f0); }
    .vr-hero { text-align:center;margin-bottom:1.75rem; }
    .vr-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,189,248,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .vr-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text,#f0f0f0);line-height:1.2;margin-bottom:.5rem; }
    .vr-hero p { font-size:13px;color:var(--text-3,#94a3b8);max-width:520px;margin:0 auto;line-height:1.7; }
    .vr-drop { border:2px dashed var(--border,#334155);border-radius:18px;padding:2rem 1.5rem;text-align:center;cursor:pointer;transition:.2s;background:var(--card-bg,#0f172a); }
    .vr-drop:hover,.vr-drop.drag-over { border-color:var(--ac);background:var(--ac-lt); }
    .vr-drop-icon { font-size:2.2rem;margin-bottom:.6rem; }
    .vr-drop-text { font-size:14px;font-weight:600;color:var(--text,#f0f0f0);margin-bottom:.25rem; }
    .vr-drop-sub { font-size:11px;color:var(--text-3,#94a3b8); }
    #vrFile { display:none; }
    .vr-editor { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:18px;padding:1.1rem;margin-top:1rem;display:none; }
    .vr-editor.show { display:block; }
    .vr-info { font-size:12px;color:var(--text-3,#94a3b8);margin-bottom:.85rem; }
    .vr-row1 { display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:.75rem; }
    .vr-sel { padding:8px 11px;border-radius:9px;border:1px solid var(--border,#334155);background:var(--bg-2,#0f1e2e);color:var(--text,#f0f0f0);font-size:13px;cursor:pointer; }
    .vr-btn { display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 16px;border-radius:10px;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:.15s; }
    .vr-btn-go { flex:1;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;box-shadow:0 4px 14px rgba(56,189,248,.25); }
    .vr-btn-go:disabled { opacity:.5;cursor:not-allowed; }
    .vr-btn-ghost { background:var(--card-bg,#1e293b);border:1px solid var(--border,#334155);color:var(--text-3,#94a3b8); }
    .vr-prog { display:none;margin-bottom:.75rem; }
    .vr-prog-bar { background:var(--border,#334155);border-radius:6px;height:8px;overflow:hidden;margin-bottom:5px; }
    .vr-prog-fill { height:100%;width:0;background:linear-gradient(90deg,var(--ac),#818cf8);border-radius:6px;transition:width .3s ease; }
    .vr-prog-lbl { font-size:11px;color:var(--text-3,#94a3b8);text-align:center; }
    .vr-status { font-size:12px;color:var(--text-3,#94a3b8);min-height:16px; }
    .vr-status.err { color:#f87171; }
    .vr-result { display:none;margin-top:.5rem; }
    .vr-result.show { display:block; }
    .vr-track { display:flex;align-items:center;gap:9px;background:rgba(0,0,0,.25);border-radius:11px;padding:.55rem .7rem;margin-bottom:8px; }
    .vr-track-ic { font-size:1.2rem;width:24px;text-align:center; }
    .vr-track-lbl { font-size:12.5px;font-weight:700;min-width:120px; }
    .vr-track audio { flex:1;height:30px;min-width:0; }
    .vr-dl { padding:6px 11px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;white-space:nowrap; }
    .vr-zip { width:100%;margin-top:4px;background:linear-gradient(135deg,#6366f1,#4338ca);color:#fff;border:none;border-radius:10px;padding:10px;font-size:13px;font-weight:700;cursor:pointer; }
    .vr-cta { margin-top:2.5rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg,#0f172a));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .vr-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.4rem; }
    .vr-cta p { font-size:12.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:460px;margin:0 auto .9rem; }
    .vr-cta-btn { display:inline-block;background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none; }
    .vr-info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:2rem; }
    .vr-info-card { background:var(--card-bg,#0f172a);border:1px solid var(--border,#334155);border-radius:14px;padding:1rem; }
    .vr-info-card .i { font-size:1.4rem;margin-bottom:.4rem; }
    .vr-info-card .t { font-weight:700;font-size:12px;color:var(--text,#f0f0f0);margin-bottom:.3rem; }
    .vr-info-card .b { font-size:11px;color:var(--text-3,#94a3b8);line-height:1.6; }
    @@media(max-width:480px){ .vr-track-lbl{min-width:88px;} }
</style>
@endpush

@section('content')
<div class="vr-page">
    <a href="{{ route('home') }}" class="vr-back">← Beranda</a>
    @include('partials.tool-share')

    <div class="vr-hero">
        <div class="vr-badge">🎤 Tool Gratis</div>
        <h1>Penghapus Vokal Online — Bikin Karaoke</h1>
        <p>Hapus vokal dari lagu untuk <b>karaoke / minus one</b>, atau ambil vokalnya saja — langsung di browser, <b>tanpa upload</b>, tanpa install, gratis. Hasil terbaik untuk lagu <b>stereo</b>.</p>
    </div>

    <div class="vr-drop" id="vrDrop" onclick="document.getElementById('vrFile').click()">
        <div class="vr-drop-icon">🎵</div>
        <div class="vr-drop-text">Seret file lagu ke sini atau klik untuk pilih</div>
        <div class="vr-drop-sub">MP3 · WAV · OGG · FLAC · M4A &nbsp;|&nbsp; Maks 150 MB &nbsp;|&nbsp; Tidak diunggah ke server</div>
    </div>
    <input type="file" id="vrFile" accept="audio/*">

    <div class="vr-editor" id="vrEditor">
        <div class="vr-info" id="vrInfo">—</div>
        <div class="vr-row1">
            <select id="vrFmt" class="vr-sel">
                <option value="mp3-128">MP3 128 kbps</option>
                <option value="mp3-192">MP3 192 kbps</option>
                <option value="mp3-320">MP3 320 kbps (HQ)</option>
                <option value="wav">WAV (lossless)</option>
            </select>
            <button class="vr-btn vr-btn-go" id="vrBtn" onclick="vrProcess()">🎤 Hapus Vokal</button>
            <button class="vr-btn vr-btn-ghost" onclick="vrReset()">🔄 Ganti</button>
        </div>
        <div class="vr-prog" id="vrProg">
            <div class="vr-prog-bar"><div class="vr-prog-fill" id="vrProgFill"></div></div>
            <div class="vr-prog-lbl" id="vrProgLbl"></div>
        </div>
        <div class="vr-status" id="vrStatus"></div>
        <div class="vr-result" id="vrResult">
            <div style="font-size:12px;font-weight:700;color:var(--green);margin-bottom:.55rem;">✅ Hasil siap — dengarkan &amp; unduh:</div>
            <div id="vrTracks"></div>
            <button class="vr-zip" onclick="vrDownloadAll()">📦 Unduh Keduanya (ZIP)</button>
        </div>
    </div>

    <div class="vr-cta">
        <h2>🎸 Kamu musisi atau penyanyi?</h2>
        <p>Tampilkan karyamu di <b>Margonoandi</b> — buat <b>profil portofolio gratis</b> (kartu + QR yang bisa dibagikan), temukan <b>personil band &amp; gig</b> lewat matchmaking, dan gabung komunitas musisi Indonesia. Dimulai dari kamarmu.</p>
        <a href="{{ route('google.login') }}" class="vr-cta-btn">Buat profil musisi gratis →</a>
    </div>

    <section style="margin-top:2.5rem;">
        <h2 style="font-family:'Space Grotesk','Sora',sans-serif;font-size:1rem;font-weight:700;color:var(--text,#f0f0f0);margin-bottom:.75rem;">Cara Pakai</h2>
        <ol style="font-size:13px;color:var(--text-3,#94a3b8);line-height:2.2;padding-left:1.2rem;">
            <li>Seret atau pilih file lagu (MP3, WAV, OGG, dll.) — gunakan versi <b>stereo</b> untuk hasil terbaik.</li>
            <li>Pilih format unduhan (MP3 atau WAV).</li>
            <li>Klik <b>🎤 Hapus Vokal</b> — diproses di perangkatmu (beberapa detik).</li>
            <li>Dengarkan <b>Instrumen (karaoke)</b> &amp; <b>Vokal</b>, lalu unduh satuan atau ZIP.</li>
        </ol>
    </section>

    <div class="vr-info-grid">
        <div class="vr-info-card"><div class="i">🔒</div><div class="t">Privasi 100%</div><div class="b">Lagu tidak pernah diunggah. Semua diproses di browser (Web Audio + FFT).</div></div>
        <div class="vr-info-card"><div class="i">🎚️</div><div class="t">Per-Pita Frekuensi</div><div class="b">Bukan sekadar L−R: bass dipertahankan, vokal di pita tengah dibuang lebih bersih.</div></div>
        <div class="vr-info-card"><div class="i">🎼</div><div class="t">Karaoke / Minus One</div><div class="b">Latihan vokal, cover, atau bikin minus one untuk panggung &amp; konten.</div></div>
        <div class="vr-info-card"><div class="i">💸</div><div class="t">Gratis &amp; Tanpa Install</div><div class="b">Tak perlu daftar atau bayar. Buka, proses, unduh.</div></div>
    </div>

    <p style="text-align:center;margin-top:2.5rem;font-size:11px;color:var(--text-3,#94a3b8);">
        Bagian dari <a href="{{ route('home') }}" style="color:var(--ac);">Margonoandi Fanbase</a> — komunitas musisi Indonesia 🎸 ·
        <a href="{{ route('tools.potong-lagu') }}" style="color:var(--ac);">Pemotong Lagu</a>
    </p>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vocal-remover.js') }}?v={{ @filemtime(public_path('js/vocal-remover.js')) ?: 1 }}"></script>
<script src="https://cdn.jsdelivr.net/npm/lamejs@1.2.1/lame.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function(){
'use strict';
var _ctx=null,_buf=null,_name='lagu',_out=null,_urls=[];
function g(id){return document.getElementById(id);}
function st(t,e){var s=g('vrStatus');s.textContent=t||'';s.className='vr-status'+(e?' err':'');}
function prog(v,l){g('vrProg').style.display='block';g('vrProgFill').style.width=v+'%';g('vrProgLbl').textContent=l||'';}

var drop=g('vrDrop');
drop.addEventListener('dragover',function(e){e.preventDefault();drop.classList.add('drag-over');});
drop.addEventListener('dragleave',function(){drop.classList.remove('drag-over');});
drop.addEventListener('drop',function(e){e.preventDefault();drop.classList.remove('drag-over');if(e.dataTransfer.files[0])vrLoad(e.dataTransfer.files[0]);});
g('vrFile').addEventListener('change',function(){if(this.files[0])vrLoad(this.files[0]);});

function vrLoad(file){
    if(file.size>150*1024*1024){st('Maks 150 MB',true);return;}
    _name=file.name.replace(/\.[^.]+$/,'');_out=null;st('Memuat…');
    if(_ctx){try{_ctx.close();}catch(e){}}_ctx=new(window.AudioContext||window.webkitAudioContext)();
    var r=new FileReader();
    r.onload=function(ev){
        _ctx.decodeAudioData(ev.target.result.slice(0),function(buf){
            _buf=buf;var d=buf.duration,m=Math.floor(d/60),x=Math.floor(d%60);
            g('vrInfo').innerHTML='🎵 <b>'+_name+'</b> · '+(file.size/1024/1024).toFixed(1)+' MB · '+m+':'+(x<10?'0':'')+x
                +(buf.numberOfChannels<2?' <span style="color:#f59e0b">[Mono — tak bisa hapus vokal]</span>':'');
            g('vrEditor').classList.add('show');g('vrResult').classList.remove('show');g('vrProg').style.display='none';st('');
            window.scrollTo({top:g('vrEditor').offsetTop-20,behavior:'smooth'});
        },function(){st('Gagal mendekode file.',true);});
    };
    r.readAsArrayBuffer(file);
}

window.vrProcess=function(){
    if(!_buf){st('Pilih file dulu.',true);return;}
    if(_buf.numberOfChannels<2){st('⚠️ Butuh lagu STEREO untuk hapus vokal (file ini mono).',true);return;}
    if(!window.VocalRemover){st('⚠️ Modul belum termuat, muat ulang halaman.',true);return;}
    var btn=g('vrBtn');btn.disabled=true;g('vrResult').classList.remove('show');st('');prog(5,'Memproses…');
    window.VocalRemover.process(_buf,function(v,l){prog(v,l);},function(out){
        _out=out;g('vrProg').style.display='none';vrRender(out.sr);btn.disabled=false;
    },function(e){st('⚠️ '+(e&&e.message||e),true);btn.disabled=false;g('vrProg').style.display='none';});
};

var TRACKS=[
    {k:'instL',k2:'instR',ic:'🎸',lbl:'Instrumen (Karaoke)',col:'#22c55e',nm:'01_instrumen'},
    {k:'vocL', k2:'vocR', ic:'🎤',lbl:'Vokal (eksperimen)', col:'#38bdf8',nm:'02_vokal'}
];
function vrRender(sr){
    _urls.forEach(function(u){URL.revokeObjectURL(u);});_urls=[];
    var fv=g('vrFmt').value,isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3';
    var wrap=g('vrTracks');wrap.innerHTML='';
    TRACKS.forEach(function(t){
        var bl=isWav?encWav(_out[t.k],_out[t.k2],sr):encMp3(_out[t.k],_out[t.k2],sr,kbps);
        var u=URL.createObjectURL(bl);_urls.push(u);
        var row=document.createElement('div');row.className='vr-track';
        row.innerHTML='<span class="vr-track-ic">'+t.ic+'</span>'
            +'<span class="vr-track-lbl" style="color:'+t.col+';">'+t.lbl+'</span>'
            +'<audio controls src="'+u+'"></audio>'
            +'<a class="vr-dl" style="background:'+t.col+';color:'+(t.col==='#22c55e'?'#04210f':'#fff')+';" href="'+u+'" download="'+_name+'_'+t.nm+'.'+ext+'">⬇ '+ext.toUpperCase()+'</a>';
        wrap.appendChild(row);
    });
    g('vrResult').classList.add('show');
}
window.vrDownloadAll=function(){
    if(!_out){st('Belum ada hasil.',true);return;}
    st('Membuat ZIP…');
    var fv=g('vrFmt').value,isWav=fv==='wav',kbps=isWav?0:parseInt(fv.split('-')[1])||128,ext=isWav?'wav':'mp3',sr=_out.sr;
    var zip=new JSZip();
    TRACKS.forEach(function(t){zip.file(_name+'_'+t.nm+'.'+ext,isWav?encWav(_out[t.k],_out[t.k2],sr):encMp3(_out[t.k],_out[t.k2],sr,kbps));});
    zip.generateAsync({type:'blob'}).then(function(z){var a=document.createElement('a');a.href=URL.createObjectURL(z);a.download=_name+'_karaoke.zip';a.click();st('');});
};
window.vrReset=function(){
    _buf=null;_out=null;_urls.forEach(function(u){URL.revokeObjectURL(u);});_urls=[];
    g('vrFile').value='';g('vrEditor').classList.remove('show');g('vrResult').classList.remove('show');g('vrProg').style.display='none';st('');
};

function encWav(c0,c1,sr){
    var nCh=c1?2:1,n=c0.length,ab=new ArrayBuffer(44+n*nCh*2),v=new DataView(ab);
    function ws(o,s){for(var i=0;i<s.length;i++)v.setUint8(o+i,s.charCodeAt(i));}
    ws(0,'RIFF');v.setUint32(4,36+n*nCh*2,true);ws(8,'WAVE');ws(12,'fmt ');
    v.setUint32(16,16,true);v.setUint16(20,1,true);v.setUint16(22,nCh,true);
    v.setUint32(24,sr,true);v.setUint32(28,sr*nCh*2,true);v.setUint16(32,nCh*2,true);v.setUint16(34,16,true);
    ws(36,'data');v.setUint32(40,n*nCh*2,true);var off=44;
    for(var i=0;i<n;i++){var x=Math.max(-1,Math.min(1,c0[i]));v.setInt16(off,x<0?x*0x8000:x*0x7FFF,true);off+=2;if(c1){var y=Math.max(-1,Math.min(1,c1[i]));v.setInt16(off,y<0?y*0x8000:y*0x7FFF,true);off+=2;}}
    return new Blob([ab],{type:'audio/wav'});
}
function encMp3(c0,c1,sr,kbps){
    var nCh=c1?2:1,n=c0.length,enc=new lamejs.Mp3Encoder(nCh,sr,kbps||128),mp3=[],BLK=1152;
    function f2i(f){var a=new Int16Array(f.length);for(var i=0;i<f.length;i++)a[i]=Math.max(-32768,Math.min(32767,f[i]*32767));return a;}
    var l16=f2i(c0),r16=c1?f2i(c1):l16;
    for(var i=0;i<n;i+=BLK){var d=enc.encodeBuffer(l16.subarray(i,i+BLK),r16.subarray(i,i+BLK));if(d.length)mp3.push(new Uint8Array(d));}
    var end=enc.flush();if(end.length)mp3.push(new Uint8Array(end));
    return new Blob(mp3,{type:'audio/mpeg'});
}
})();
</script>
@endpush
