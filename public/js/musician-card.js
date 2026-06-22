/* Generator kartu portofolio musisi (gambar PNG 1080x1350) — dipakai di profil
   member & profil publik. Butuh window.MS_CARD = {name,avatar,roles[],genres[],
   location,skill,bio,url} dan sebuah tombol #msCardBtn. */
(function () {
    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath(); ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r); ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r); ctx.arcTo(x, y, x + w, y, r); ctx.closePath();
    }
    function loadImg(src) {
        return new Promise(function (res) {
            var img = new Image(); img.crossOrigin = 'anonymous';
            img.onload = function () { res(img); }; img.onerror = function () { res(null); };
            img.src = src;
        });
    }
    function wrap(ctx, text, cx, y, maxW, lh, maxLines) {
        var words = (text || '').split(/\s+/), line = '', lines = [];
        for (var i = 0; i < words.length; i++) {
            var t = line ? line + ' ' + words[i] : words[i];
            if (ctx.measureText(t).width > maxW && line) { lines.push(line); line = words[i]; if (lines.length === maxLines) break; }
            else line = t;
        }
        if (line && lines.length < maxLines) lines.push(line);
        if (lines.length >= maxLines) { var L = lines[maxLines - 1]; while (ctx.measureText(L + '…').width > maxW && L.length) L = L.slice(0, -1); lines[maxLines - 1] = L + '…'; }
        lines.forEach(function (l, i) { ctx.fillText(l, cx, y + i * lh); });
        return y + lines.length * lh;
    }
    function chips(ctx, items, cx, y, bg, fg, fontSize) {
        if (!items || !items.length) return y;
        ctx.font = fontSize + 'px "DM Sans","Inter",sans-serif';
        var padX = 26, gap = 14, h = fontSize + 26, maxW = 1080 - 120, rows = [], row = [], rowW = 0;
        items.forEach(function (it) {
            var w = ctx.measureText(it).width + padX * 2, add = w + (row.length ? gap : 0);
            if (rowW + add > maxW && row.length) { rows.push({ items: row, w: rowW }); row = []; rowW = 0; add = w; }
            row.push({ t: it, w: w }); rowW += add;
        });
        if (row.length) rows.push({ items: row, w: rowW });
        rows.forEach(function (r) {
            var x = cx - r.w / 2;
            r.items.forEach(function (c) {
                ctx.fillStyle = bg; roundRect(ctx, x, y, c.w, h, h / 2); ctx.fill();
                ctx.fillStyle = fg; ctx.textAlign = 'left'; ctx.textBaseline = 'middle'; ctx.fillText(c.t, x + padX, y + h / 2); x += c.w + gap;
            });
            y += h + 14;
        });
        ctx.textAlign = 'center'; ctx.textBaseline = 'alphabetic'; return y;
    }

    window.msShareCard = async function () {
        var M = window.MS_CARD || {};
        var btn = document.getElementById('msCardBtn'); var old = btn ? btn.textContent : '';
        function reset() { if (btn) { btn.disabled = false; btn.textContent = old; } }
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Membuat...'; }
        try {
            try { await document.fonts.ready; } catch (e) {}
            var W = 1080, H = 1350, cv = document.createElement('canvas'); cv.width = W; cv.height = H; var ctx = cv.getContext('2d');
            var g = ctx.createLinearGradient(0, 0, W, H); g.addColorStop(0, '#0f2236'); g.addColorStop(1, '#0a1622'); ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);
            var ga = ctx.createLinearGradient(0, 0, W, 0); ga.addColorStop(0, '#38A8CC'); ga.addColorStop(1, '#F07040'); ctx.fillStyle = ga; ctx.fillRect(0, 0, W, 14);
            var cx = W / 2, ay = 300, ar = 132;
            ctx.fillStyle = ga; ctx.beginPath(); ctx.arc(cx, ay, ar + 11, 0, Math.PI * 2); ctx.fill();
            var av = await loadImg(M.avatar);
            ctx.save(); ctx.beginPath(); ctx.arc(cx, ay, ar, 0, Math.PI * 2); ctx.clip();
            if (av) { ctx.drawImage(av, cx - ar, ay - ar, ar * 2, ar * 2); }
            else { ctx.fillStyle = '#1b3247'; ctx.fillRect(cx - ar, ay - ar, ar * 2, ar * 2); ctx.fillStyle = '#7EC8E3'; ctx.font = 'bold 140px sans-serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillText((M.name || 'M').charAt(0).toUpperCase(), cx, ay); }
            ctx.restore();
            ctx.textAlign = 'center'; ctx.textBaseline = 'alphabetic';
            ctx.fillStyle = '#fff'; ctx.font = 'bold 72px "Sora","Inter",sans-serif'; ctx.fillText(M.name || 'Musisi', cx, 545);
            var sub = []; if (M.skill) sub.push(M.skill); if (M.location) sub.push('📍 ' + M.location);
            if (sub.length) { ctx.fillStyle = '#9bc3d6'; ctx.font = '34px "DM Sans","Inter",sans-serif'; ctx.fillText(sub.join('    '), cx, 595); }
            var y = 680;
            y = chips(ctx, M.roles, cx, y, '#13344a', '#7EC8E3', 38);
            y = chips(ctx, M.genres, cx, y + 6, '#3a2417', '#F0A070', 34);
            if (M.bio) { ctx.fillStyle = '#cfe0ec'; ctx.font = '37px "DM Sans","Inter",sans-serif'; ctx.textAlign = 'center'; wrap(ctx, M.bio, cx, y + 72, W - 170, 52, 3); }
            var qr = await loadImg('https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=0&qzone=1&data=' + encodeURIComponent(M.url || 'https://margonoandi.my.id'));
            if (qr) {
                var qs = 200, qx = 80, qy = H - 262;
                ctx.fillStyle = '#fff'; roundRect(ctx, qx - 14, qy - 14, qs + 28, qs + 28, 18); ctx.fill();
                ctx.drawImage(qr, qx, qy, qs, qs);
                var tx = qx + qs + 50, tcy = qy + qs / 2;
                ctx.textAlign = 'left';
                ctx.fillStyle = '#fff';    ctx.font = 'bold 46px "Sora","Inter",sans-serif'; ctx.fillText('MARGONOANDI', tx, tcy - 44);
                ctx.fillStyle = '#9bc3d6'; ctx.font = '30px "DM Sans","Inter",sans-serif';   ctx.fillText('margonoandi.my.id', tx, tcy + 2);
                ctx.fillStyle = '#F0A070'; ctx.font = '29px "DM Sans","Inter",sans-serif';   ctx.fillText('Scan untuk lihat profil', tx, tcy + 50);
                ctx.fillStyle = '#6f96ab'; ctx.font = '24px "DM Sans","Inter",sans-serif';   ctx.fillText('Ekosistem musik, dimulai dari kamarmu', tx, tcy + 92);
            } else {
                ctx.textAlign = 'center';
                ctx.fillStyle = '#fff';    ctx.font = 'bold 44px "Sora","Inter",sans-serif'; ctx.fillText('MARGONOANDI', cx, H - 160);
                ctx.fillStyle = '#9bc3d6'; ctx.font = '30px "DM Sans","Inter",sans-serif';   ctx.fillText('margonoandi.my.id', cx, H - 112);
                ctx.fillStyle = '#6f96ab'; ctx.font = '26px "DM Sans","Inter",sans-serif';   ctx.fillText('Ekosistem musik, dimulai dari kamarmu', cx, H - 70);
            }
            cv.toBlob(function (blob) {
                if (!blob) { alert('Gagal membuat gambar. Coba lagi.'); reset(); return; }
                var file = new File([blob], 'profil-margonoandi.png', { type: 'image/png' });
                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    navigator.share({ files: [file], title: M.name, text: 'Profil musisi di Margonoandi 🎶' }).catch(function () {}).finally(reset);
                } else {
                    var a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'profil-margonoandi.png'; document.body.appendChild(a); a.click(); a.remove();
                    setTimeout(function () { URL.revokeObjectURL(a.href); }, 4000); reset();
                }
            }, 'image/png');
        } catch (e) { alert('Gagal membuat kartu.'); reset(); }
    };
})();
