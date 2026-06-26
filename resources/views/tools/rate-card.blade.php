@extends('layouts.app')

@push('styles')
<style>
    .rc-wrap { max-width: 860px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .rc-title { font-size: clamp(1.4rem,4vw,2rem); font-weight: 300; color: var(--text); margin-bottom: .35rem; }
    .rc-sub { font-size: 14px; color: var(--text-3); margin-bottom: 2rem; }

    .rc-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; }
    @media(max-width:700px){ .rc-layout { grid-template-columns: 1fr; } }

    /* FORM */
    .rc-form-section { margin-bottom: 1.4rem; }
    .rc-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-3); margin-bottom: .5rem; display: block; }
    .rc-input, .rc-select, .rc-textarea {
        width: 100%; background: var(--card-bg, rgba(15,23,42,.6)); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text); font-size: 13.5px; padding: 10px 13px;
        outline: none; font-family: inherit; transition: .15s; box-sizing: border-box;
    }
    .rc-input:focus, .rc-select:focus, .rc-textarea:focus { border-color: var(--accent); }
    .rc-textarea { resize: vertical; min-height: 72px; }
    .rc-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M0 0l6 8 6-8z' fill='%2394a3b8'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }

    .rc-services { display: flex; flex-direction: column; gap: 8px; }
    .rc-service-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 8px; align-items: center; }
    .rc-remove-btn { background: none; border: 1px solid var(--border); border-radius: 8px; color: var(--text-3); width: 34px; height: 34px; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: .15s; }
    .rc-remove-btn:hover { border-color: #ef4444; color: #ef4444; }
    .rc-add-btn { display: flex; align-items: center; gap: 6px; background: none; border: 1px dashed var(--border); border-radius: 10px; color: var(--text-3); font-size: 12.5px; padding: 9px 14px; cursor: pointer; font-family: inherit; transition: .15s; margin-top: 6px; }
    .rc-add-btn:hover { border-color: var(--accent); color: var(--accent); }

    .rc-note { font-size: 11px; color: var(--text-4); margin-top: 4px; }

    .rc-actions { display: flex; gap: 10px; margin-top: 1.5rem; flex-wrap: wrap; }
    .rc-btn-dl { padding: 11px 24px; border-radius: 10px; background: var(--accent); color: #fff; border: none; font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; display: flex; align-items: center; gap: 7px; }
    .rc-btn-dl:hover { opacity: .88; }
    .rc-btn-wa { padding: 11px 20px; border-radius: 10px; background: #25D366; color: #fff; border: none; font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; display: flex; align-items: center; gap: 7px; text-decoration: none; }
    .rc-btn-wa:hover { opacity: .88; }
    .rc-btn-copy { padding: 11px 20px; border-radius: 10px; background: var(--card-bg, rgba(15,23,42,.6)); color: var(--text-2); border: 1px solid var(--border); font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .15s; }

    /* PREVIEW CARD */
    .rc-preview-wrap { position: sticky; top: 80px; }
    .rc-preview-label { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--text-4); margin-bottom: .75rem; }
    #rcCanvas { display: block; width: 100%; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 24px 48px -12px rgba(0,0,0,.4); }

    .rc-tips { background: var(--card-bg, rgba(15,23,42,.5)); border: 1px solid var(--border); border-radius: 14px; padding: 1rem 1.1rem; margin-top: 1rem; font-size: 12px; color: var(--text-3); line-height: 1.7; }
    .rc-tips b { color: var(--text-2); }
</style>
@endpush

@section('content')
<div class="rc-wrap">

    <h1 class="rc-title">💼 Rate Card Generator Musisi</h1>
    <p class="rc-sub">Buat daftar harga jasamu yang profesional — download sebagai gambar, kirim ke klien, share di IG Story.</p>

    @include('partials.tool-share', ['origin' => $origin])

    <div class="rc-layout">

        {{-- FORM --}}
        <div>

            <div class="rc-form-section">
                <label class="rc-label">Nama Artis / Band</label>
                <input type="text" class="rc-input" id="rcName" placeholder="Margonoandi" oninput="updatePreview()">
            </div>

            <div class="rc-form-section">
                <label class="rc-label">Tagline / Deskripsi Singkat</label>
                <input type="text" class="rc-input" id="rcTagline" placeholder="Indie Singer-Songwriter · Yogyakarta" oninput="updatePreview()">
            </div>

            <div class="rc-form-section">
                <label class="rc-label">Kontak (WhatsApp / Email)</label>
                <input type="text" class="rc-input" id="rcContact" placeholder="+62 812 xxxx xxxx" oninput="updatePreview()">
            </div>

            <div class="rc-form-section">
                <label class="rc-label">Warna Tema</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;">
                    @foreach(['#38A8CC','#a855f7','#f59e0b','#22c55e','#ef4444','#f97316','#ec4899','#1a1a2e'] as $color)
                    <button type="button" onclick="setTheme('{{ $color }}')"
                        style="width:28px;height:28px;border-radius:50%;background:{{ $color }};border:2px solid transparent;cursor:pointer;transition:.15s;"
                        data-color="{{ $color }}" class="rc-color-btn"></button>
                    @endforeach
                    <input type="color" id="rcColorPicker" value="#38A8CC" oninput="setTheme(this.value)" style="width:28px;height:28px;border-radius:50%;border:2px solid var(--border);cursor:pointer;padding:0;background:none;">
                </div>
            </div>

            <div class="rc-form-section">
                <label class="rc-label">Layanan & Harga</label>
                <div class="rc-services" id="rcServices">
                    <div class="rc-service-row">
                        <input type="text" class="rc-input" placeholder="Wedding Gig (2 jam)" oninput="updatePreview()">
                        <input type="text" class="rc-input" placeholder="Rp 2.500.000" oninput="updatePreview()">
                        <button type="button" class="rc-remove-btn" onclick="removeService(this)" title="Hapus">×</button>
                    </div>
                    <div class="rc-service-row">
                        <input type="text" class="rc-input" placeholder="Studio Session (1 jam)" oninput="updatePreview()">
                        <input type="text" class="rc-input" placeholder="Rp 500.000" oninput="updatePreview()">
                        <button type="button" class="rc-remove-btn" onclick="removeService(this)" title="Hapus">×</button>
                    </div>
                    <div class="rc-service-row">
                        <input type="text" class="rc-input" placeholder="Mixing & Mastering" oninput="updatePreview()">
                        <input type="text" class="rc-input" placeholder="Rp 750.000/lagu" oninput="updatePreview()">
                        <button type="button" class="rc-remove-btn" onclick="removeService(this)" title="Hapus">×</button>
                    </div>
                </div>
                <button type="button" class="rc-add-btn" onclick="addService()">+ Tambah layanan</button>
                <p class="rc-note">Klik × untuk hapus baris. Minimal 1 layanan.</p>
            </div>

            <div class="rc-form-section">
                <label class="rc-label">Catatan / Syarat (opsional)</label>
                <textarea class="rc-textarea" id="rcNotes" placeholder="Harga belum termasuk transport. DP 50% di muka. Hubungi untuk paket bundling." oninput="updatePreview()"></textarea>
            </div>

            <div class="rc-actions">
                <button type="button" class="rc-btn-dl" onclick="downloadCard()">⬇ Download Gambar</button>
                <a id="rcWaBtn" href="#" target="_blank" class="rc-btn-wa" onclick="updateWaLink()">📲 Kirim via WhatsApp</a>
                <button type="button" class="rc-btn-copy" onclick="copyCardLink()">🔗 Salin link</button>
            </div>

            <div class="rc-tips">
                <b>Tips penggunaan:</b><br>
                • Download gambar → upload ke IG Story / Feed, atau kirim langsung ke klien via WA<br>
                • Perbarui rate card 2× setahun — Januari dan Juli<br>
                • Jangan takut pasang harga — rate card yang jelas = kesan profesional
            </div>
        </div>

        {{-- PREVIEW --}}
        <div class="rc-preview-wrap">
            <p class="rc-preview-label">Preview (update otomatis)</p>
            <canvas id="rcCanvas" width="1080" height="1350"></canvas>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
var rcTheme = '#38A8CC';

function setTheme(color) {
    rcTheme = color;
    document.querySelectorAll('.rc-color-btn').forEach(function(b) {
        b.style.borderColor = b.dataset.color === color ? '#fff' : 'transparent';
    });
    updatePreview();
}

function addService() {
    var row = document.createElement('div');
    row.className = 'rc-service-row';
    row.innerHTML = '<input type="text" class="rc-input" placeholder="Nama layanan" oninput="updatePreview()">' +
        '<input type="text" class="rc-input" placeholder="Harga" oninput="updatePreview()">' +
        '<button type="button" class="rc-remove-btn" onclick="removeService(this)" title="Hapus">\xd7</button>';
    document.getElementById('rcServices').appendChild(row);
    row.querySelector('input').focus();
    updatePreview();
}

function removeService(btn) {
    var rows = document.querySelectorAll('.rc-service-row');
    if (rows.length <= 1) return;
    btn.closest('.rc-service-row').remove();
    updatePreview();
}

function getServices() {
    var rows = document.querySelectorAll('#rcServices .rc-service-row');
    var list = [];
    rows.forEach(function(r) {
        var inputs = r.querySelectorAll('input');
        var name = inputs[0].value.trim();
        var price = inputs[1].value.trim();
        if (name || price) list.push({ name: name || '—', price: price || '—' });
    });
    return list;
}

function hexToRgb(hex) {
    var r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
    return { r: r, g: g, b: b };
}

function luminance(hex) {
    var c = hexToRgb(hex);
    var r = c.r/255, g = c.g/255, b = c.b/255;
    return 0.299*r + 0.587*g + 0.114*b;
}

function updatePreview() {
    var canvas = document.getElementById('rcCanvas');
    var ctx = canvas.getContext('2d');
    var W = 1080, H = 1350;
    canvas.width = W; canvas.height = H;

    var name    = document.getElementById('rcName').value.trim()    || 'Nama Artis / Band';
    var tagline = document.getElementById('rcTagline').value.trim() || 'Indie Musician · Indonesia';
    var contact = document.getElementById('rcContact').value.trim() || 'hubungi untuk info';
    var notes   = document.getElementById('rcNotes').value.trim();
    var services= getServices();
    var theme   = rcTheme;
    var rgb     = hexToRgb(theme);
    var isDark  = luminance(theme) < 0.4;
    var textOnTheme = isDark ? '#fff' : '#0f172a';

    // BG
    ctx.fillStyle = '#0f172a';
    ctx.fillRect(0, 0, W, H);

    // Subtle glow
    var grd = ctx.createRadialGradient(W/2, 0, 0, W/2, 0, H*0.7);
    grd.addColorStop(0, 'rgba('+rgb.r+','+rgb.g+','+rgb.b+',.12)');
    grd.addColorStop(1, 'rgba('+rgb.r+','+rgb.g+','+rgb.b+',0)');
    ctx.fillStyle = grd;
    ctx.fillRect(0, 0, W, H);

    // Top accent bar
    ctx.fillStyle = theme;
    ctx.fillRect(0, 0, W, 8);

    // Header block
    var hdrH = 220;
    ctx.fillStyle = 'rgba('+rgb.r+','+rgb.g+','+rgb.b+',.15)';
    roundRect(ctx, 60, 40, W-120, hdrH, 24);
    ctx.fill();

    // Badge
    ctx.fillStyle = theme;
    roundRect(ctx, 80, 58, 180, 34, 17);
    ctx.fill();
    ctx.fillStyle = textOnTheme;
    ctx.font = 'bold 18px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('RATE CARD', 96, 81);

    // Artis name
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 68px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText(name.toUpperCase(), 80, 170);

    // Tagline
    ctx.fillStyle = 'rgba(255,255,255,.6)';
    ctx.font = '28px "Space Grotesk", sans-serif';
    ctx.fillText(tagline, 80, 212);

    // Divider
    var y = 290;
    ctx.strokeStyle = 'rgba(255,255,255,.08)';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(60, y); ctx.lineTo(W-60, y); ctx.stroke();
    y += 36;

    // Services header
    ctx.fillStyle = theme;
    ctx.font = 'bold 20px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('LAYANAN & HARGA', 60, y);
    y += 36;

    // Service rows
    services.forEach(function(s, i) {
        var rowY = y + i * 92;
        var isEven = i % 2 === 0;
        ctx.fillStyle = isEven ? 'rgba(255,255,255,.04)' : 'rgba(255,255,255,.02)';
        roundRect(ctx, 60, rowY - 24, W-120, 80, 14);
        ctx.fill();

        // Dot indicator
        ctx.fillStyle = theme;
        ctx.beginPath();
        ctx.arc(86, rowY + 16, 6, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#e2e8f0';
        ctx.font = '28px "Space Grotesk", sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText(s.name, 108, rowY + 24);

        ctx.fillStyle = theme;
        ctx.font = 'bold 28px "Space Grotesk", sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText(s.price, W-80, rowY + 24);
    });

    y = y + services.length * 92 + 20;

    // Divider
    ctx.strokeStyle = 'rgba(255,255,255,.08)';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(60, y); ctx.lineTo(W-60, y); ctx.stroke();
    y += 32;

    // Notes
    if (notes) {
        ctx.fillStyle = 'rgba(255,255,255,.45)';
        ctx.font = '22px "Space Grotesk", sans-serif';
        ctx.textAlign = 'left';
        var noteLines = wrapText(ctx, notes, W - 140);
        noteLines.slice(0, 3).forEach(function(line) {
            ctx.fillText(line, 60, y);
            y += 32;
        });
        y += 8;
    }

    // Contact footer
    var footerY = H - 120;
    ctx.fillStyle = 'rgba('+rgb.r+','+rgb.g+','+rgb.b+',.2)';
    ctx.fillRect(0, footerY, W, H - footerY);

    ctx.fillStyle = theme;
    ctx.font = 'bold 24px "Space Grotesk", sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('📱 ' + contact, 60, footerY + 44);

    ctx.fillStyle = 'rgba(255,255,255,.35)';
    ctx.font = '20px "Space Grotesk", sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText('margonoandi.my.id', W - 60, footerY + 44);

    ctx.fillStyle = 'rgba(255,255,255,.2)';
    ctx.font = '17px "Space Grotesk", sans-serif';
    ctx.fillText('Dibuat dengan Rate Card Generator gratis', W - 60, footerY + 76);
}

function wrapText(ctx, text, maxWidth) {
    var words = text.split(' '), lines = [], current = '';
    words.forEach(function(w) {
        var test = current ? current + ' ' + w : w;
        if (ctx.measureText(test).width > maxWidth && current) {
            lines.push(current);
            current = w;
        } else {
            current = test;
        }
    });
    if (current) lines.push(current);
    return lines;
}

function roundRect(ctx, x, y, w, h, r) {
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
}

function downloadCard() {
    updatePreview();
    var canvas = document.getElementById('rcCanvas');
    var name = document.getElementById('rcName').value.trim().replace(/\s+/g, '-').toLowerCase() || 'rate-card';
    var a = document.createElement('a');
    a.download = 'rate-card-' + name + '.png';
    a.href = canvas.toDataURL('image/png');
    a.click();
}

function updateWaLink() {
    var name = document.getElementById('rcName').value.trim() || 'Musisi';
    var contact = document.getElementById('rcContact').value.trim();
    var services = getServices();
    var txt = '💼 Rate Card ' + name + '\n\n';
    services.forEach(function(s) { txt += '• ' + s.name + ': ' + s.price + '\n'; });
    if (contact) txt += '\n📱 ' + contact;
    txt += '\n\n🎵 Dibuat di margonoandi.my.id/tools/rate-card';
    document.getElementById('rcWaBtn').href = 'https://wa.me/?text=' + encodeURIComponent(txt);
}

function copyCardLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        var b = document.querySelector('.rc-btn-copy');
        b.textContent = '✓ Tersalin!';
        setTimeout(function() { b.textContent = '🔗 Salin link'; }, 1600);
    });
}

// Init
setTheme('#38A8CC');
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
