<?php
$secret = 'margono2026';
if (($_GET['key'] ?? '') !== $secret) { http_response_code(403); die('403'); }

$base = '/home/parr4187/public_html/margonoandi-fanbase';
$git  = "$base/.git";

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Git Patch</title>
<style>
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem}
h1{color:#38A8CC}h2{color:#F07040;margin:1.5rem 0 .4rem}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.75rem;border-radius:8px;white-space:pre-wrap;word-break:break-all;margin:0}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#7A9DB0}
a{color:#38A8CC;font-size:1.1rem;display:inline-block;margin:.5rem 0}
.warn{color:#facc15;margin-top:2rem;border:1px solid #F07040;padding:.75rem;border-radius:8px}
</style></head><body><h1>Git Patch — Margonoandi</h1>';

// ── Tampilkan semua info diagnostik ──────────────────────────────────────────

$show = function($label, $content, $class = 'info') {
    echo "<h2>$label</h2><pre class='$class'>" . htmlspecialchars(trim($content) ?: '(kosong)') . '</pre>';
};

// HEAD
$show('HEAD saat ini', file_exists("$git/HEAD") ? file_get_contents("$git/HEAD") : 'FILE TIDAK ADA');

// packed-refs
$show('packed-refs', file_exists("$git/packed-refs") ? file_get_contents("$git/packed-refs") : 'FILE TIDAK ADA');

// FETCH_HEAD
$show('FETCH_HEAD', file_exists("$git/FETCH_HEAD") ? file_get_contents("$git/FETCH_HEAD") : 'FILE TIDAK ADA');

// refs/remotes/origin/
$originDir = "$git/refs/remotes/origin";
if (is_dir($originDir)) {
    $files = scandir($originDir);
    $content = '';
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $content .= "$f: " . trim(file_get_contents("$originDir/$f")) . "\n";
    }
    $show('refs/remotes/origin/', $content ?: '(direktori kosong)');
} else {
    $show('refs/remotes/origin/', 'DIREKTORI TIDAK ADA');
}

// refs/heads/
$headsDir = "$git/refs/heads";
if (is_dir($headsDir)) {
    $files = scandir($headsDir);
    $content = '';
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $content .= "$f: " . trim(file_get_contents("$headsDir/$f")) . "\n";
    }
    $show('refs/heads/', $content ?: '(direktori kosong — belum ada branch lokal)');
} else {
    $show('refs/heads/', 'DIREKTORI TIDAK ADA');
}

// ── Cari hash main dari semua sumber ─────────────────────────────────────────
$hash = '';

// 1. refs/remotes/origin/main
if (!$hash && file_exists("$git/refs/remotes/origin/main")) {
    $hash = trim(file_get_contents("$git/refs/remotes/origin/main"));
}

// 2. packed-refs — cari baris yang ada 'main'
if (!$hash && file_exists("$git/packed-refs")) {
    foreach (file("$git/packed-refs") as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#')) continue;
        if (str_ends_with($line, 'refs/remotes/origin/main') || str_ends_with($line, 'refs/heads/main')) {
            [$hash] = explode(' ', $line);
            break;
        }
    }
}

// 3. FETCH_HEAD — baris pertama (biasanya HEAD yang di-fetch)
if (!$hash && file_exists("$git/FETCH_HEAD")) {
    $line = trim(fgets(fopen("$git/FETCH_HEAD", 'r')));
    if (preg_match('/^([a-f0-9]{40})/', $line, $m)) {
        $hash = $m[1];
    }
}

echo "<h2>Hash yang ditemukan</h2>";
echo '<pre class="' . ($hash ? 'ok' : 'err') . '">' . ($hash ?: 'Tidak ditemukan — perlu Update from Remote dulu di cPanel') . '</pre>';

// ── Jalankan Fix jika hash ada ───────────────────────────────────────────────
if ($hash && isset($_GET['fix'])) {
    echo '<h2>Menjalankan Fix...</h2>';

    // Buat refs/heads/ jika belum ada
    if (!is_dir("$git/refs/heads")) mkdir("$git/refs/heads", 0755, true);

    // Tulis refs/heads/main
    $r1 = file_put_contents("$git/refs/heads/main", $hash . "\n");

    // Update HEAD ke main
    $r2 = file_put_contents("$git/HEAD", "ref: refs/heads/main\n");

    $ok = $r1 !== false && $r2 !== false;
    echo '<pre class="' . ($ok ? 'ok' : 'err') . '">';
    echo $ok
        ? "✅ BERHASIL!\nHEAD sekarang: ref: refs/heads/main\nHash: $hash\n\nSekarang:\n1. Buka cPanel → Git Version Control → Basic Information\n2. Klik Update — branch 'main' akan muncul\n3. Pilih main → Update\n4. Tab Pull or Deploy → Deploy HEAD Commit"
        : "❌ Gagal menulis file. Cek permission folder .git/";
    echo '</pre>';
} elseif ($hash) {
    echo '<br><a href="?key=margono2026&fix=1">→ Klik di sini untuk jalankan Fix</a>';
}

echo '<div class="warn">⚠️ Hapus file ini setelah selesai: <strong>public/gitpatch.php</strong></div>';
echo '</body></html>';
