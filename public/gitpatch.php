<?php
$secret = 'margono2026';
if (($_GET['key'] ?? '') !== $secret) { http_response_code(403); die('403'); }

// Auto-detect: naik dari public/ ke root project
$base = realpath(__DIR__ . '/../');
$git  = $base . '/.git';

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Git Patch</title>
<style>
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem}
h1{color:#38A8CC}h2{color:#F07040;margin:1.5rem 0 .4rem}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.75rem;border-radius:8px;white-space:pre-wrap;word-break:break-all;margin:0}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#7A9DB0}
a{color:#38A8CC;font-size:1.1rem;display:inline-block;margin:.5rem 0;text-decoration:none}
.warn{color:#facc15;margin-top:2rem;border:1px solid #F07040;padding:.75rem;border-radius:8px}
</style></head><body><h1>Git Patch — Margonoandi</h1>';

// ── Diagnostik path ───────────────────────────────────────────────────────────
echo '<h2>Path Info</h2><pre class="info">';
echo "PHP file   : " . __FILE__ . "\n";
echo "Project dir: $base\n";
echo "Git dir    : $git\n";
echo ".git exists: " . (is_dir($git) ? 'YA ✅' : 'TIDAK ❌') . "\n";
echo ".git readable: " . (is_readable($git) ? 'YA ✅' : 'TIDAK ❌ (permission issue)') . "\n";
echo '</pre>';

// Jika .git tidak ada atau tidak bisa dibaca
if (!is_dir($git) || !is_readable($git)) {
    echo '<h2>⚠️ .git Tidak Bisa Diakses</h2>';
    echo '<pre class="err">Kemungkinan penyebab:
1. Folder .git/ permission 0700 dan dimiliki user lain (cPanel git daemon)
2. .git/ tidak ada di lokasi ini

Solusi: Ubah permission .git/ menjadi 0755 via File Manager cPanel:
- Masuk ke public_html/margonoandi-fanbase/
- Klik kanan folder .git → Change Permissions → 755
- Lalu refresh halaman ini</pre>';

    // Coba list isi folder parent untuk konfirmasi
    echo '<h2>Isi Folder Project</h2><pre class="info">';
    if (is_dir($base)) {
        $items = scandir($base);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $perm = substr(sprintf('%o', fileperms($base . '/' . $item)), -4);
            $type = is_dir($base . '/' . $item) ? '[DIR]' : '[FILE]';
            echo "$type  $perm  $item\n";
        }
    } else {
        echo "Folder project tidak bisa dibaca!";
    }
    echo '</pre>';

    echo '<div class="warn">⚠️ Hapus file ini setelah selesai: <strong>public/gitpatch.php</strong></div>';
    echo '</body></html>';
    exit;
}

// ── Baca file git ─────────────────────────────────────────────────────────────
$read = fn($f) => file_exists($f) ? file_get_contents($f) : '(FILE TIDAK ADA)';
$show = function($label, $content, $class = 'info') {
    echo "<h2>$label</h2><pre class='$class'>" . htmlspecialchars(trim($content)) . '</pre>';
};

$show('HEAD saat ini', $read("$git/HEAD"));
$show('packed-refs',   $read("$git/packed-refs"));
$show('FETCH_HEAD',    $read("$git/FETCH_HEAD"));

// Scan refs/remotes/origin/
$originDir = "$git/refs/remotes/origin";
$content = is_dir($originDir) ? '' : '(DIREKTORI TIDAK ADA)';
if (is_dir($originDir)) {
    foreach (scandir($originDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $content .= "$f → " . trim(file_get_contents("$originDir/$f")) . "\n";
    }
    $content = $content ?: '(kosong)';
}
$show('refs/remotes/origin/', $content);

// ── Cari hash main ────────────────────────────────────────────────────────────
$hash = '';

if (!$hash && file_exists("$git/refs/remotes/origin/main"))
    $hash = trim(file_get_contents("$git/refs/remotes/origin/main"));

if (!$hash && file_exists("$git/packed-refs")) {
    foreach (file("$git/packed-refs") as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#')) continue;
        if (str_ends_with($line, 'refs/remotes/origin/main') || str_ends_with($line, 'refs/heads/main')) {
            [$hash] = explode(' ', $line); break;
        }
    }
}

if (!$hash && file_exists("$git/FETCH_HEAD")) {
    $line = trim(fgets(fopen("$git/FETCH_HEAD", 'r')));
    if (preg_match('/^([a-f0-9]{40})/', $line, $m)) $hash = $m[1];
}

echo '<h2>Hash yang Ditemukan</h2>';
echo '<pre class="' . ($hash ? 'ok' : 'err') . '">'
    . ($hash ?: 'Tidak ditemukan — klik Update from Remote di cPanel lalu refresh halaman ini')
    . '</pre>';

// ── Jalankan Fix ──────────────────────────────────────────────────────────────
if ($hash && isset($_GET['fix'])) {
    if (!is_dir("$git/refs/heads")) mkdir("$git/refs/heads", 0755, true);
    $r1 = file_put_contents("$git/refs/heads/main", $hash . "\n");
    $r2 = file_put_contents("$git/HEAD", "ref: refs/heads/main\n");
    $ok = $r1 !== false && $r2 !== false;
    echo '<h2>Hasil Fix</h2><pre class="' . ($ok ? 'ok' : 'err') . '">';
    echo $ok
        ? "✅ BERHASIL!\nHEAD → ref: refs/heads/main\nHash: $hash\n\nLangkah selanjutnya:\n1. cPanel → Git Version Control → Basic Information\n2. Klik Update → pilih 'main' → Update\n3. Tab Pull or Deploy → Deploy HEAD Commit"
        : "❌ Gagal tulis ke .git/ — coba chmod .git/ menjadi 0755 dulu";
    echo '</pre>';
} elseif ($hash) {
    echo '<br><a href="?key=margono2026&fix=1">▶ Klik di sini untuk jalankan Fix (checkout main)</a>';
}

echo '<div class="warn">⚠️ Hapus file ini setelah selesai: <strong>public/gitpatch.php</strong></div>';
echo '</body></html>';
