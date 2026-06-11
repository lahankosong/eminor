<?php
/**
 * Deploy script — download ZIP dari GitHub dan update file project
 * Akses: https://margonoandi.my.id/deploy.php?key=margono2026
 * HAPUS FILE INI setelah tidak dibutuhkan lagi.
 */

$secret  = 'margono2026';
$github  = 'https://github.com/lahankosong/margonoandi-fanbase/archive/refs/heads/main.zip';
$base    = realpath(__DIR__ . '/../');
$tmp_zip = sys_get_temp_dir() . '/fanbase_deploy.zip';
$tmp_dir = sys_get_temp_dir() . '/fanbase_extracted';

// Folder/file yang TIDAK akan ditimpa (data penting server)
$preserve = ['vendor', '.env', 'storage', 'node_modules', '.git', 'public/deploy.php'];

if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403); die('403 Forbidden');
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Deploy</title>
<style>
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem;max-width:800px}
h1{color:#38A8CC}h2{color:#F07040;margin:1.5rem 0 .4rem}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.75rem;border-radius:8px;white-space:pre-wrap;word-break:break-all;margin:0}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#7A9DB0}
.warn{color:#facc15;margin-top:1.5rem;border:1px solid #F07040;padding:.75rem;border-radius:8px}
</style></head><body>
<h1>🚀 Deploy — Margonoandi Fanbase</h1>';

if (!isset($_GET['run'])) {
    echo '<h2>Info</h2>
    <pre class="info">Project : ' . $base . '
GitHub  : lahankosong/margonoandi-fanbase (branch: main)
Preserve: ' . implode(', ', $preserve) . '</pre>
    <br><a href="?key=' . $secret . '&run=1"
       style="color:#fff;background:#38A8CC;padding:10px 24px;border-radius:8px;text-decoration:none;font-size:1rem">
       ▶ Mulai Deploy</a>';
    echo '<div class="warn">⚠️ Pastikan sudah push ke GitHub sebelum deploy.</div>';
    echo '</body></html>'; exit;
}

// ── Step 1: Download ZIP ──────────────────────────────────────────────────────
echo '<h2>1. Download dari GitHub</h2>';
flush();

$ctx = stream_context_create(['http' => [
    'timeout'          => 60,
    'follow_location'  => true,
    'user_agent'       => 'Mozilla/5.0',
]]);
$zip_data = @file_get_contents($github, false, $ctx);

if (!$zip_data) {
    echo '<pre class="err">❌ Gagal download. Cek koneksi atau URL GitHub.</pre>';
    echo '</body></html>'; exit;
}
file_put_contents($tmp_zip, $zip_data);
echo '<pre class="ok">✅ Download selesai (' . round(strlen($zip_data) / 1024) . ' KB)</pre>';

// ── Step 2: Extract ZIP ───────────────────────────────────────────────────────
echo '<h2>2. Extract ZIP</h2>';
flush();

if (is_dir($tmp_dir)) {
    // Hapus tmp dir lama
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tmp_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iter as $f) $f->isDir() ? rmdir($f) : unlink($f);
    rmdir($tmp_dir);
}

$zip = new ZipArchive();
if ($zip->open($tmp_zip) !== true) {
    echo '<pre class="err">❌ Gagal buka ZIP.</pre>';
    echo '</body></html>'; exit;
}
$zip->extractTo($tmp_dir);
$zip->close();

// ZIP GitHub menghasilkan subfolder "margonoandi-fanbase-main/"
$extracted = glob($tmp_dir . '/*/') ;
$src = $extracted[0] ?? $tmp_dir;
echo '<pre class="ok">✅ Extract ke: ' . $src . '</pre>';

// ── Step 3: Copy files ────────────────────────────────────────────────────────
echo '<h2>3. Copy File ke Project</h2>';
flush();

$copied  = 0;
$skipped = 0;

function shouldPreserve($relPath, $preserveList) {
    foreach ($preserveList as $p) {
        if ($relPath === $p || str_starts_with($relPath, $p . '/') || str_starts_with($relPath, $p . DIRECTORY_SEPARATOR)) {
            return true;
        }
    }
    return false;
}

function copyDir($src, $dst, $base_src, $base_dst, $preserve, &$copied, &$skipped) {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $rel  = ltrim(str_replace($base_src, '', $src . '/' . $item), '/');
        $from = $src . '/' . $item;
        $to   = $dst . '/' . $item;
        if (shouldPreserve($rel, $preserve)) { $skipped++; continue; }
        if (is_dir($from)) {
            copyDir($from, $to, $base_src, $base_dst, $preserve, $copied, $skipped);
        } else {
            copy($from, $to);
            $copied++;
        }
    }
}

copyDir(rtrim($src, '/'), $base, rtrim($src, '/'), $base, $preserve, $copied, $skipped);

echo '<pre class="ok">✅ Selesai!
File di-copy  : ' . $copied . '
File dilewati : ' . $skipped . ' (vendor, .env, storage — aman)</pre>';

// ── Step 4: Cleanup ───────────────────────────────────────────────────────────
@unlink($tmp_zip);
echo '<h2>4. Cleanup ✅</h2>';
echo '<pre class="ok">Temp files dihapus.</pre>';
flush();

// ── Step 5: Artisan Commands ──────────────────────────────────────────────────
echo '<h2>5. Artisan Cache & Clear</h2>';
flush();

$php    = PHP_BINARY ?: 'php';
$artisan = escapeshellarg($base . '/artisan');

$commands = [
    'migrate --force' => 'Jalankan migration baru (aman, skip yang sudah ada)',
    'config:clear'    => 'Hapus config cache lama',
    'config:cache'    => 'Buat config cache baru (timezone, locale, dll.)',
    'route:clear'     => 'Hapus route cache lama',
    'route:cache'     => 'Buat route cache baru',
    'view:clear'      => 'Hapus compiled views',
    'cache:clear'     => 'Hapus application cache',
];

foreach ($commands as $cmd => $desc) {
    $output = shell_exec(escapeshellarg($php) . ' ' . $artisan . ' ' . $cmd . ' 2>&1');
    $ok     = ($output !== null && stripos($output, 'error') === false);
    $cls    = $ok ? 'ok' : 'err';
    echo '<pre class="' . $cls . '">$ php artisan ' . htmlspecialchars($cmd) . '  <span class="info">← ' . $desc . '</span>
' . htmlspecialchars(trim($output ?? 'shell_exec tidak tersedia')) . '</pre>';
    flush();
}

echo '<h2>6. Selesai 🎉</h2>';
echo '<pre class="ok">Deploy berhasil! Website sudah menggunakan kode terbaru dari GitHub.</pre>';

echo '<div class="warn">⚠️ File ini aman untuk tetap ada (terlindungi password), tapi disarankan hapus jika tidak dibutuhkan lagi.</div>';
echo '</body></html>';
