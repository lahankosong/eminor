{{-- Tombol Bagikan (Web Share API / WhatsApp / fallback salin link). Sertakan sekali per halaman tool. --}}
@isset($origin)
<div style="background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border,#334155);border-radius:14px;padding:1rem 1.1rem;margin:-.2rem 0 1.4rem;font-size:13px;color:var(--text-2,#cbd5e1);line-height:1.6;">
    <span style="font-size:11px;font-weight:700;letter-spacing:.06em;color:var(--accent,#38bdf8);text-transform:uppercase;">Kenapa alat ini ada?</span><br>
    {{ $origin }}
</div>
@endisset
<div style="display:flex;align-items:center;justify-content:center;gap:8px;text-align:center;margin:-.4rem 0 1.4rem;flex-wrap:wrap;">
    <button type="button" onclick="toolShare(this)" style="background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border,#334155);color:var(--text-2,#cbd5e1);padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s;" onmouseover="this.style.borderColor='var(--ac,#38bdf8)'" onmouseout="this.style.borderColor='var(--border,#334155)'">🔗 Bagikan alat ini</button>
    <a id="toolShareWa" href="#" target="_blank" rel="noopener noreferrer" style="background:#25D366;border:1px solid #25D366;color:#fff;padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:inherit;text-decoration:none;transition:.15s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">📲 Kirim via WhatsApp</a>
</div>
<script>
(function(){
    var wa = document.getElementById('toolShareWa');
    if(wa){
        var msg = encodeURIComponent('🎵 Coba alat gratis ini buat musisi: ' + document.title + ' — ' + location.href);
        wa.href = 'https://wa.me/?text=' + msg;
    }
})();
function toolShare(b){
    var data={title:document.title,text:'🎵 Coba alat gratis buat musisi: '+document.title,url:location.href};
    if(navigator.share){navigator.share(data).catch(function(){});}
    else if(navigator.clipboard){navigator.clipboard.writeText(location.href).then(function(){var t=b.textContent;b.textContent='✓ Link tersalin';setTimeout(function(){b.textContent=t;},1600);}).catch(function(){prompt('Salin link:',location.href);});}
    else{prompt('Salin link:',location.href);}
}
</script>
