@extends('layouts.app')

@push('styles')
<style>
    .epk-wrap { max-width: 900px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .epk-title { font-size: clamp(1.4rem,4vw,2rem); font-weight: 300; color: var(--text); margin-bottom: .35rem; }
    .epk-sub { font-size: 14px; color: var(--text-3); margin-bottom: 2rem; }

    .epk-layout { display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start; }
    @media(max-width:760px){ .epk-layout { grid-template-columns: 1fr; } }

    /* STEPS */
    .epk-step { display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--accent); margin-bottom: .4rem; }
    .epk-step-dot { width: 20px; height: 20px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; flex-shrink: 0; }
    .epk-section { background: var(--card-bg, rgba(15,23,42,.5)); border: 1px solid var(--border); border-radius: 16px; padding: 1.2rem 1.25rem; margin-bottom: 1rem; }
    .epk-section-title { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }

    .epk-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-3); margin-bottom: .4rem; display: block; }
    .epk-input, .epk-textarea, .epk-select {
        width: 100%; background: var(--bg, #0f172a); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text); font-size: 13.5px; padding: 10px 13px;
        outline: none; font-family: inherit; transition: .15s; box-sizing: border-box; margin-bottom: 10px;
    }
    .epk-input:focus, .epk-textarea:focus, .epk-select:focus { border-color: var(--accent); }
    .epk-textarea { resize: vertical; min-height: 80px; line-height: 1.6; }
    .epk-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media(max-width:480px){ .epk-row { grid-template-columns: 1fr; } }

    .epk-photo-zone { border: 2px dashed var(--border); border-radius: 14px; padding: 1.5rem; text-align: center; cursor: pointer; transition: .2s; margin-bottom: 10px; }
    .epk-photo-zone:hover { border-color: var(--accent); }
    .epk-photo-zone input { display: none; }
    .epk-photo-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto .5rem; display: block; border: 2px solid var(--accent); }
    .epk-photo-label { font-size: 12px; color: var(--text-3); }

    .epk-genres { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
    .epk-genre-chip { padding: 5px 12px; border-radius: 20px; border: 1px solid var(--border); background: none; color: var(--text-3); font-size: 12px; cursor: pointer; font-family: inherit; transition: .15s; }
    .epk-genre-chip.sel { background: var(--accent); color: #fff; border-color: var(--accent); }

    .epk-links-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    @media(max-width:480px){ .epk-links-grid { grid-template-columns: 1fr; } }

    /* ACTIONS */
    .epk-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 1rem; }
    .epk-btn-dl { padding: 11px 24px; border-radius: 10px; background: var(--accent); color: #fff; border: none; font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; display: flex; align-items: center; gap: 7px; }
    .epk-btn-dl:hover { opacity: .88; }
    .epk-btn-copy { padding: 11px 20px; border-radius: 10px; background: none; color: var(--text-2); border: 1px solid var(--border); font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; }
    .epk-btn-copy:hover { border-color: var(--accent); color: var(--accent); }

    /* PREVIEW */
    .epk-preview-wrap { position: sticky; top: 80px; }
    .epk-preview-label { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--text-4); margin-bottom: .75rem; }
    #epkCanvas { display: block; width: 100%; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 24px 48px -12px rgba(0,0,0,.4); }

    .epk-tips { background: var(--card-bg, rgba(15,23,42,.5)); border: 1px solid var(--border); border-radius: 14px; padding: 1rem 1.1rem; margin-top: 1rem; font-size: 12px; color: var(--text-3); line-height: 1.7; }
    .epk-tips b { color: var(--text-2); }
</style>
@endpush

@section('content')
<div class="epk-wrap">
    <h1 class="epk-title">📄 EPK Generator — Electronic Press Kit Musisi</h1>
    <p class="epk-sub">Buat press kit profesional yang bisa langsung dikirim ke booker, promotor, atau media. Download sebagai gambar siap cetak atau share link.</p>

    @include('partials.tool-share', ['origin' => $origin])

    <div class="epk-layout">

        {{-- FORM --}}
        <div>

            {{-- IDENTITAS --}}
            <div class="epk-section">
                <div class="epk-section-title"><span>🎤</span> Identitas Artis</div>

                <div class="epk-photo-zone" onclick="document.getElementById('epkPhotoInput').click()">
                    <img id="epkPhotoPreview" class="epk-photo-preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%230f172a'/%3E%3Ctext x='50' y='60' text-anchor='middle' font-size='36' fill='%2338bdf8'%3E🎵%3C/text%3E%3C/svg%3E" alt="">
                    <input type="file" id="epkPhotoInput" accept="image/*" onchange="loadPhoto(this)">
                    <div class="epk-photo-label">Klik untuk upload foto artis</div>
                </div>

                <label class="epk-label">Nama Artis / Band</label>
                <input type="text" class="epk-input" id="epkName" placeholder="Margonoandi" oninput="renderEPK()">

                <div class="epk-row">
                    <div>
                        <label class="epk-label">Kota / Domisili</label>
                        <input type="text" class="epk-input" id="epkCity" placeholder="Yogyakarta" oninput="renderEPK()">
                    </div>
                    <div>
                        <label class="epk-label">Tahun Aktif</label>
                        <input type="text" class="epk-input" id="epkYear" placeholder="Sejak 2015" oninput="renderEPK()">
                    </div>
                </div>

                <label class="epk-label">Genre</label>
                <div class="epk-genres" id="epkGenreChips">
                    @foreach(['Indie Pop','Indie Folk','Singer-Songwriter','Acoustic','Alternative','Jazz','R&B','Pop','Rock','Instrumental','Electronic','Dangdut Modern'] as $g)
                    <button type="button" class="epk-genre-chip" onclick="toggleGenre(this, '{{ $g }}')">{{ $g }}</button>
                    @endforeach
                </div>

                <label class="epk-label">Bio Singkat (2–3 kalimat)</label>
                <textarea class="epk-textarea" id="epkBio" placeholder="Musisi indie asal Yogyakarta yang menulis lagu tentang kerinduan, perjalanan, dan hal-hal kecil yang sering luput diperhatikan. Sudah 3 album, 50+ gig, dan terus tumbuh dari panggung ke panggung." oninput="renderEPK()"></textarea>
            </div>

            {{-- PRESTASI --}}
            <div class="epk-section">
                <div class="epk-section-title"><span>🏆</span> Highlights (opsional)</div>
                <label class="epk-label">Pencapaian (pisah dengan koma atau enter)</label>
                <textarea class="epk-textarea" id="epkHighlights" placeholder="50K+ streams Spotify, Featured di playlist Indie Indonesia, Gig di 5 kota, 3 EP self-released" oninput="renderEPK()" style="min-height:60px;"></textarea>
            </div>

            {{-- LINKS --}}
            <div class="epk-section">
                <div class="epk-section-title"><span>🔗</span> Link Platform</div>
                <div class="epk-links-grid">
                    <div>
                        <label class="epk-label">Spotify</label>
                        <input type="text" class="epk-input" id="epkSpotify" placeholder="open.spotify.com/artist/..." oninput="renderEPK()">
                    </div>
                    <div>
                        <label class="epk-label">YouTube</label>
                        <input type="text" class="epk-input" id="epkYoutube" placeholder="youtube.com/@..." oninput="renderEPK()">
                    </div>
                    <div>
                        <label class="epk-label">Instagram</label>
                        <input type="text" class="epk-input" id="epkIg" placeholder="@username" oninput="renderEPK()">
                    </div>
                    <div>
                        <label class="epk-label">Kontak / WA</label>
                        <input type="text" class="epk-input" id="epkContact" placeholder="+62 812 xxxx xxxx" oninput="renderEPK()">
                    </div>
                </div>
            </div>

            <div class="epk-actions">
                <button type="button" class="epk-btn-dl" onclick="downloadEPK()">⬇ Download EPK (PNG)</button>
                <button type="button" class="epk-btn-copy" onclick="copyEPKLink()">🔗 Salin link halaman</button>
            </div>

            <div class="epk-tips">
                <b>Cara kirim EPK:</b><br>
                • Download PNG → attach ke email ke booker/promotor<br>
                • Atau share link halaman ini langsung via WhatsApp<br>
                • Update EPK setiap rilis baru atau setelah gig penting<br><br>
                <b>Yang booker biasanya cari:</b> foto artis, bio singkat, genre, highlight/klaim, dan cara menghubungi.
            </div>
        </div>

        {{-- PREVIEW --}}
        <div class="epk-preview-wrap">
            <p class="epk-preview-label">Preview EPK (update otomatis)</p>
            <canvas id="epkCanvas" width="900" height="1200"></canvas>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
var epkGenres = [];
var epkPhoto  = null;

function toggleGenre(btn, g) {
    if (epkGenres.includes(g)) {
        epkGenres = epkGenres.filter(function(x){ return x !== g; });
        btn.classList.remove('sel');
    } else if (epkGenres.length < 3) {
        epkGenres.push(g);
        btn.classList.add('sel');
    }
    renderEPK();
}

function loadPhoto(input) {
    var file = input.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById('epkPhotoPreview');
        img.src = e.target.result;
        var image = new Image();
        image.onload = function() { epkPhoto = image; renderEPK(); };
        image.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function val(id) { return document.getElementById(id).value.trim(); }

function renderEPK() {
    var canvas = document.getElementById('epkCanvas');
    var ctx = canvas.getContext('2d');
    var W = 900, H = 1200;
    canvas.width = W; canvas.height = H;

    var name       = val('epkName')       || 'Nama Artis';
    var city       = val('epkCity')       || '';
    var year       = val('epkYear')       || '';
    var bio        = val('epkBio')        || 'Bio singkat artis akan muncul di sini...';
    var highlights = val('epkHighlights') || '';
    var spotify    = val('epkSpotify')    || '';
    var youtube    = val('epkYoutube')    || '';
    var ig         = val('epkIg')         || '';
    var contact    = val('epkContact')    || '';

    // BG gradient
    var bg = ctx.createLinearGradient(0, 0, 0, H);
    bg.addColorStop(0, '#0b1120');
    bg.addColorStop(1, '#0f1e35');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    // Accent line top
    ctx.fillStyle = '#38A8CC';
    ctx.fillRect(0, 0, W, 6);

    // Photo circle
    var photoX = 80, photoY = 60, photoR = 90;
    ctx.save();
    ctx.beginPath();
    ctx.arc(photoX + photoR, photoY + photoR, photoR, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(56,168,204,.15)';
    ctx.fill();
    if (epkPhoto) {
        ctx.clip();
        var size = photoR * 2;
        ctx.drawImage(epkPhoto, photoX, photoY, size, size);
    } else {
        ctx.fillStyle = 'rgba(56,168,204,.08)';
        ctx.fill();
        ctx.fillStyle = '#38A8CC';
        ctx.font = '56px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('🎵', photoX + photoR, photoY + photoR + 20);
    }
    ctx.restore();

    // Name
    ctx.fillStyle = '#f0f0f0';
    ctx.font = 'bold 64px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    var nameX = photoX + photoR * 2 + 36;
    ctx.fillText(name, nameX, photoY + 70);

    // City + year
    ctx.fillStyle = 'rgba(255,255,255,.5)';
    ctx.font = '26px "Space Grotesk", sans-serif';
    var meta = [city, year].filter(Boolean).join('  ·  ');
    if (meta) ctx.fillText(meta, nameX, photoY + 108);

    // Genres
    if (epkGenres.length > 0) {
        var gx = nameX;
        epkGenres.forEach(function(g) {
            var tw = ctx.measureText(g).width + 32;
            ctx.fillStyle = 'rgba(56,168,204,.18)';
            roundRectFill(ctx, gx, photoY + 124, tw, 32, 16);
            ctx.fillStyle = '#38A8CC';
            ctx.font = 'bold 18px "Space Grotesk", sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(g, gx + 16, photoY + 145);
            gx += tw + 8;
        });
    }

    // Divider
    var y = photoY + photoR * 2 + 36;
    ctx.strokeStyle = 'rgba(255,255,255,.08)';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(60, y); ctx.lineTo(W-60, y); ctx.stroke();
    y += 32;

    // EPK badge
    ctx.fillStyle = 'rgba(56,168,204,.15)';
    roundRectFill(ctx, 60, y, 180, 36, 18);
    ctx.fillStyle = '#38A8CC';
    ctx.font = 'bold 18px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('ELECTRONIC PRESS KIT', 76, y + 24);
    y += 56;

    // Bio
    ctx.fillStyle = '#e2e8f0';
    ctx.font = '24px "Space Grotesk", sans-serif';
    var bioLines = wrapText(ctx, bio, W - 120);
    bioLines.slice(0, 5).forEach(function(line) {
        ctx.fillText(line, 60, y);
        y += 36;
    });
    y += 16;

    // Highlights
    if (highlights) {
        ctx.fillStyle = 'rgba(255,255,255,.08)';
        roundRectFill(ctx, 60, y, W-120, 10 + highlights.split(/[,\n]/).filter(Boolean).slice(0,4).length * 40 + 20, 16);
        y += 24;
        ctx.fillStyle = '#38A8CC';
        ctx.font = 'bold 18px "Space Grotesk", sans-serif';
        ctx.fillText('HIGHLIGHTS', 80, y);
        y += 32;
        highlights.split(/[,\n]/).filter(Boolean).slice(0, 4).forEach(function(h) {
            ctx.fillStyle = '#94a3b8';
            ctx.font = '22px "Space Grotesk", sans-serif';
            ctx.fillText('✓  ' + h.trim(), 80, y);
            y += 38;
        });
        y += 16;
    }

    // Links
    ctx.strokeStyle = 'rgba(255,255,255,.08)';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(60, y); ctx.lineTo(W-60, y); ctx.stroke();
    y += 32;

    var links = [
        spotify  && ['🎵 Spotify', spotify],
        youtube  && ['▶ YouTube', youtube],
        ig       && ['📷 Instagram', ig],
        contact  && ['📱 Kontak', contact],
    ].filter(Boolean);

    if (links.length > 0) {
        var colW = (W - 120) / 2;
        links.forEach(function(l, i) {
            var lx = 60 + (i % 2) * (colW + 20);
            var ly = y + Math.floor(i / 2) * 56;
            ctx.fillStyle = '#38A8CC';
            ctx.font = 'bold 18px "Space Grotesk", sans-serif';
            ctx.fillText(l[0], lx, ly);
            ctx.fillStyle = 'rgba(255,255,255,.55)';
            ctx.font = '18px "Space Grotesk", sans-serif';
            ctx.fillText(l[1].replace(/^https?:\/\//, '').slice(0, 44), lx, ly + 26);
        });
        y += (Math.ceil(links.length / 2)) * 56 + 16;
    }

    // Footer
    ctx.fillStyle = 'rgba(56,168,204,.12)';
    ctx.fillRect(0, H - 72, W, 72);
    ctx.fillStyle = 'rgba(255,255,255,.3)';
    ctx.font = '18px "Space Grotesk", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('Dibuat dengan EPK Generator gratis · margonoandi.my.id/tools/epk', W/2, H - 28);
}

function roundRectFill(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r);
    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
    ctx.fill();
}

function wrapText(ctx, text, maxW) {
    var words = text.split(' '), lines = [], cur = '';
    words.forEach(function(w) {
        var test = cur ? cur + ' ' + w : w;
        if (ctx.measureText(test).width > maxW && cur) { lines.push(cur); cur = w; }
        else cur = test;
    });
    if (cur) lines.push(cur);
    return lines;
}

function downloadEPK() {
    renderEPK();
    var name = (document.getElementById('epkName').value.trim() || 'epk').replace(/\s+/g,'-').toLowerCase();
    var a = document.createElement('a');
    a.download = 'epk-' + name + '.png';
    a.href = document.getElementById('epkCanvas').toDataURL('image/png');
    a.click();
}

function copyEPKLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        var b = document.querySelector('.epk-btn-copy');
        b.textContent = '✓ Tersalin!';
        setTimeout(function(){ b.textContent = '🔗 Salin link halaman'; }, 1800);
    });
}

document.addEventListener('DOMContentLoaded', renderEPK);
</script>
@endpush
