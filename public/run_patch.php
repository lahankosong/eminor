<?php
/**
 * run_patch.php — Download dari GitHub & update project (sama seperti deploy.php)
 * Akses: https://margonoandi.my.id/run_patch.php?key=<DEPLOY_KEY>&run=1
 * Kunci dibaca dari .env (DEPLOY_KEY). Hapus file ini setelah selesai.
 */

$github  = 'https://github.com/lahankosong/margonoandi-fanbase/archive/refs/heads/main.zip';
$base    = realpath(__DIR__ . '/../');
$tmp_zip = sys_get_temp_dir() . '/fanbase_patch.zip';
$tmp_dir = sys_get_temp_dir() . '/fanbase_patch_extracted';

$preserve = ['vendor', '.env', 'storage', 'node_modules', '.git', 'public/deploy.php', 'public/run_patch.php'];

// ── Auth via DEPLOY_KEY dari .env ─────────────────────────────────────────────
$secret  = '';
$envFile = $base . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strncmp($line, 'DEPLOY_KEY=', 11) === 0) {
            $secret = trim(substr($line, 11), " \t\"'");
            break;
        }
    }
}
if ($secret === '' || !hash_equals($secret, (string)($_GET['key'] ?? ''))) {
    http_response_code(403);
    die('403 Forbidden — DEPLOY_KEY belum diset di .env atau kunci salah.');
}

// ── Shared CSS / HTML head (sama seperti deploy.php) ─────────────────────────
echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Patch Deploy</title>
<style>
*{box-sizing:border-box}
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem;max-width:900px;margin:0 auto}
h1{color:#38A8CC;margin-bottom:.25rem}
.subtitle{color:#7A9DB0;font-size:12px;margin-bottom:1.5rem}
h2{color:#F07040;margin:1.5rem 0 .4rem;font-size:14px;border-bottom:1px solid rgba(240,112,64,.2);padding-bottom:4px}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.75rem;border-radius:8px;white-space:pre-wrap;word-break:break-all;margin:4px 0;font-size:12px}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#7A9DB0}.warn{color:#facc15}
.badge-ok{display:inline-block;background:#14532d;color:#4ade80;padding:1px 8px;border-radius:4px;font-size:11px}
.badge-err{display:inline-block;background:#450a0a;color:#f87171;padding:1px 8px;border-radius:4px;font-size:11px}
table{width:100%;border-collapse:collapse;font-size:12px;margin:6px 0}
td,th{padding:5px 10px;border:1px solid rgba(56,168,204,.15);text-align:left}
th{background:rgba(56,168,204,.1);color:#38A8CC}
tr:nth-child(even) td{background:rgba(255,255,255,.02)}
.alert{margin-top:1.5rem;border:1px solid #F07040;padding:.75rem 1rem;border-radius:8px;background:rgba(240,112,64,.05)}
a.btn{color:#fff;background:#38A8CC;padding:10px 24px;border-radius:8px;text-decoration:none;font-size:1rem;display:inline-block;margin-top:1rem}
</style></head><body>
<h1>&#128640; Patch Deploy — Margonoandi Fanbase</h1>
<div class="subtitle">feat: Pemotong Lagu Online + fix admin audio-cut (ffmpeg → Web Audio API)</div>';

// ── Halaman konfirmasi ────────────────────────────────────────────────────────
if (!isset($_GET['run'])) {
    echo '<h2>Info</h2>
    <pre class="info">GitHub  : lahankosong/margonoandi-fanbase (branch: main)
Preserve: ' . implode(', ', $preserve) . '</pre>
    <br>
    <a href="?key=' . htmlspecialchars($secret) . '&run=1" class="btn">&#9654; Mulai Patch Deploy</a>';
    echo '<div class="alert warn">&#9888;&#65039; Pastikan commit terbaru sudah ada di GitHub sebelum jalan.</div>';
    echo '</body></html>';
    exit;
}

// ── Step 1: Download ZIP dari GitHub ─────────────────────────────────────────
echo '<h2>1. Download dari GitHub</h2>';
flush();

$ctx      = stream_context_create(['http' => ['timeout' => 90, 'follow_location' => true, 'user_agent' => 'Mozilla/5.0']]);
$zip_data = @file_get_contents($github, false, $ctx);

if (!$zip_data) {
    echo '<pre class="err">&#10060; Gagal download. Cek koneksi atau URL GitHub.</pre></body></html>';
    exit;
}
file_put_contents($tmp_zip, $zip_data);
echo '<pre class="ok">&#10003; Download selesai (' . round(strlen($zip_data) / 1024) . ' KB)</pre>';

// ── Step 2: Extract ZIP ───────────────────────────────────────────────────────
echo '<h2>2. Extract ZIP</h2>';
flush();

if (is_dir($tmp_dir)) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tmp_dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iter as $f) $f->isDir() ? rmdir($f) : unlink($f);
    rmdir($tmp_dir);
}

$zip = new ZipArchive();
if ($zip->open($tmp_zip) !== true) {
    echo '<pre class="err">&#10060; Gagal buka ZIP.</pre></body></html>';
    exit;
}
$zip->extractTo($tmp_dir);
$zip->close();

$extracted = glob($tmp_dir . '/*/');
$src = rtrim($extracted[0] ?? $tmp_dir, '/');
echo '<pre class="ok">&#10003; Extract selesai</pre>';

// ── Step 3: Copy files ────────────────────────────────────────────────────────
echo '<h2>3. Copy File ke Project</h2>';
flush();

$copied = 0;
$skipped = 0;

function shouldPreserve(string $relPath, array $preserveList): bool {
    foreach ($preserveList as $p) {
        if ($relPath === $p
            || str_starts_with($relPath, $p . '/')
            || str_starts_with($relPath, $p . DIRECTORY_SEPARATOR))
            return true;
    }
    return false;
}

function copyDir(string $from, string $to, string $baseSrc, array $preserve, int &$copied, int &$skipped): void {
    if (!is_dir($to)) mkdir($to, 0755, true);
    foreach (scandir($from) as $item) {
        if ($item === '.' || $item === '..') continue;
        $rel  = ltrim(str_replace($baseSrc, '', $from . '/' . $item), '/');
        $fromPath = $from . '/' . $item;
        $toPath   = $to   . '/' . $item;
        if (shouldPreserve($rel, $preserve)) { $skipped++; continue; }
        if (is_dir($fromPath)) copyDir($fromPath, $toPath, $baseSrc, $preserve, $copied, $skipped);
        else { copy($fromPath, $toPath); $copied++; }
    }
}

copyDir($src, $base, $src, $preserve, $copied, $skipped);
echo '<pre class="ok">&#10003; File di-copy: ' . $copied . ' &nbsp;|&nbsp; Dilewati (aman): ' . $skipped . '</pre>';

// ── Step 4: Cleanup ───────────────────────────────────────────────────────────
@unlink($tmp_zip);
echo '<h2>4. Cleanup &#10003;</h2><pre class="ok">Temp files dihapus.</pre>';
flush();

// ── Step 5: Artisan Commands ──────────────────────────────────────────────────
echo '<h2>5. Artisan Commands</h2>';
flush();

$php    = PHP_BINARY ?: 'php';
$artisan = escapeshellarg($base . '/artisan');

$commands = [
    'migrate --force' => 'Jalankan migration baru',
    'config:clear'    => 'Hapus config cache',
    'config:cache'    => 'Buat config cache baru',
    'route:clear'     => 'Hapus route cache',
    'route:cache'     => 'Buat route cache baru',
    'view:clear'      => 'Hapus compiled views',
    'cache:clear'     => 'Hapus application cache',
];

$hasError = false;
foreach ($commands as $cmd => $desc) {
    $output  = shell_exec(escapeshellarg($php) . ' ' . $artisan . ' ' . $cmd . ' 2>&1');
    $outTrim = trim($output ?? '');
    $isError = ($output === null
        || stripos($output, 'error') !== false
        || stripos($output, 'failed') !== false);

    if (str_starts_with($cmd, 'migrate')) {
        $lines = explode("\n", $outTrim);
        $ok = []; $skip = []; $err = [];
        foreach ($lines as $line) {
            $line = trim($line); if ($line === '') continue;
            if (stripos($line,'migrating') !== false || stripos($line,'migrated') !== false) $ok[]   = $line;
            elseif (stripos($line,'error') !== false || stripos($line,'fail') !== false)      $err[]  = $line;
            else                                                                               $skip[] = $line;
        }
        echo '<pre><span class="info">$ php artisan ' . htmlspecialchars($cmd) . '  ← ' . $desc . '</span>' . "\n";
        foreach ($ok   as $l) echo '<span class="ok">  &#10003; '  . htmlspecialchars($l) . '</span>' . "\n";
        foreach ($skip as $l) echo '<span class="info">  '         . htmlspecialchars($l) . '</span>' . "\n";
        foreach ($err  as $l) { echo '<span class="err">  &#10060; ' . htmlspecialchars($l) . '</span>' . "\n"; $hasError = true; }
        if (empty($ok) && empty($err)) echo '<span class="info">  (tidak ada migration baru)</span>' . "\n";
        echo '</pre>';
    } else {
        if ($isError) $hasError = true;
        $cls = $isError ? 'err' : 'ok';
        $ico = $isError ? '&#10060;' : '&#10003;';
        echo '<pre class="' . $cls . '">' . $ico . ' php artisan ' . htmlspecialchars($cmd)
           . '  <span class="info">← ' . $desc . '</span>' . "\n"
           . htmlspecialchars($outTrim ?: '(ok)') . '</pre>';
    }
    flush();
}

// ── Selesai ───────────────────────────────────────────────────────────────────
echo '<h2>6. Selesai</h2>';
if ($hasError) {
    echo '<pre class="warn">&#9888;&#65039;  Patch selesai tapi ada warning/error di atas.</pre>';
} else {
    echo '<pre class="ok">&#10003; Patch berhasil! Fitur Pemotong Lagu Online sudah aktif.
&#10003; Tool publik: /tools/potong-lagu
&#10003; Admin audio-cut: ffmpeg diganti Web Audio API</pre>';
}
echo '<div class="alert warn">&#9888;&#65039; Segera hapus <code>public/run_patch.php</code> dari hosting setelah ini.</div>';
echo '</body></html>';
