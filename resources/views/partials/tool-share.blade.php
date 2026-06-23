{{-- Tombol Bagikan (Web Share API / fallback salin link). Sertakan sekali per halaman tool. --}}
<div style="text-align:center;margin:-.4rem 0 1.4rem;">
    <button type="button" onclick="toolShare(this)" style="background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border,#334155);color:var(--text-2,#cbd5e1);padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s;" onmouseover="this.style.borderColor='var(--ac,#38bdf8)'" onmouseout="this.style.borderColor='var(--border,#334155)'">🔗 Bagikan alat ini</button>
</div>
<script>
function toolShare(b){
    var data={title:document.title,text:document.title,url:location.href};
    if(navigator.share){navigator.share(data).catch(function(){});}
    else if(navigator.clipboard){navigator.clipboard.writeText(location.href).then(function(){var t=b.textContent;b.textContent='✓ Link tersalin';setTimeout(function(){b.textContent=t;},1600);}).catch(function(){prompt('Salin link:',location.href);});}
    else{prompt('Salin link:',location.href);}
}
</script>
