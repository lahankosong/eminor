<?php
/**
 * Deploy script — download ZIP dari GitHub dan update file project
 * Akses: https://margonoandi.my.id/deploy.php?key=margono2026
 */

$secret  = 'margono2026';
$github  = 'https://github.com/lahankosong/margonoandi-fanbase/archive/refs/heads/main.zip';
$base    = realpath(__DIR__ . '/../');
$tmp_zip = sys_get_temp_dir() . '/fanbase_deploy.zip';
$tmp_dir = sys_get_temp_dir() . '/fanbase_extracted';

$preserve = ['vendor', '.env', 'storage', 'node_modules', '.git', 'public/deploy.php'];

if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403); die('403 Forbidden');
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Deploy</title>
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
.badge-warn{display:inline-block;background:#422006;color:#facc15;padding:1px 8px;border-radius:4px;font-size:11px}
table{width:100%;border-collapse:collapse;font-size:12px;margin:6px 0}
td,th{padding:5px 10px;border:1px solid rgba(56,168,204,.15);text-align:left}
th{background:rgba(56,168,204,.1);color:#38A8CC}
tr:nth-child(even) td{background:rgba(255,255,255,.02)}
.alert{margin-top:1.5rem;border:1px solid #F07040;padding:.75rem 1rem;border-radius:8px;background:rgba(240,112,64,.05)}
a.btn{color:#fff;background:#38A8CC;padding:10px 24px;border-radius:8px;text-decoration:none;font-size:1rem;display:inline-block;margin-top:1rem}
</style></head><body>
<h1>&#128640; Deploy — Margonoandi Fanbase</h1>
<div class="subtitle">Server: ' . htmlspecialchars($base) . '</div>';

// ── Mode: Diagnostik saja ─────────────────────────────────────────────────────
if (isset($_GET['diag'])) {
    echo '<h2>&#128203; Diagnostik Database & Server</h2>';
    echo runDiagnostics($base);
    echo '</body></html>'; exit;
}

// ── Halaman awal ──────────────────────────────────────────────────────────────
if (!isset($_GET['run'])) {
    echo '<h2>Info</h2>
    <pre class="info">GitHub  : lahankosong/margonoandi-fanbase (branch: main)
Preserve: ' . implode(', ', $preserve) . '</pre>
    <br>
    <a href="?key=' . $secret . '&run=1" class="btn">&#9654; Mulai Deploy</a>
    &nbsp;&nbsp;
    <a href="?key=' . $secret . '&diag=1" style="color:#38A8CC;text-decoration:none;padding:10px 20px;border:1px solid #38A8CC;border-radius:8px;display:inline-block;margin-top:1rem">&#128203; Diagnostik Saja</a>';
    echo '<div class="alert warn">&#9888;&#65039; Pastikan sudah push ke GitHub sebelum deploy.</div>';
    echo '</body></html>'; exit;
}

// ── Step 1: Download ZIP ──────────────────────────────────────────────────────
echo '<h2>1. Download dari GitHub</h2>'; flush();

$ctx = stream_context_create(['http' => ['timeout' => 60, 'follow_location' => true, 'user_agent' => 'Mozilla/5.0']]);
$zip_data = @file_get_contents($github, false, $ctx);

if (!$zip_data) {
    echo '<pre class="err">&#10060; Gagal download. Cek koneksi atau URL GitHub.</pre>';
    echo '</body></html>'; exit;
}
file_put_contents($tmp_zip, $zip_data);
echo '<pre class="ok">&#10003; Download selesai (' . round(strlen($zip_data) / 1024) . ' KB)</pre>';

// ── Step 2: Extract ZIP ───────────────────────────────────────────────────────
echo '<h2>2. Extract ZIP</h2>'; flush();

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
    echo '<pre class="err">&#10060; Gagal buka ZIP.</pre>';
    echo '</body></html>'; exit;
}
$zip->extractTo($tmp_dir);
$zip->close();

$extracted = glob($tmp_dir . '/*/');
$src = $extracted[0] ?? $tmp_dir;
echo '<pre class="ok">&#10003; Extract ke: ' . $src . '</pre>';

// ── Step 3: Copy files ────────────────────────────────────────────────────────
echo '<h2>3. Copy File ke Project</h2>'; flush();

$copied = 0; $skipped = 0;

function shouldPreserve($relPath, $preserveList) {
    foreach ($preserveList as $p) {
        if ($relPath === $p || str_starts_with($relPath, $p . '/') || str_starts_with($relPath, $p . DIRECTORY_SEPARATOR))
            return true;
    }
    return false;
}

function copyDir($src, $dst, $base_src, $preserve, &$copied, &$skipped) {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..') continue;
        $rel  = ltrim(str_replace($base_src, '', $src . '/' . $item), '/');
        $from = $src . '/' . $item;
        $to   = $dst . '/' . $item;
        if (shouldPreserve($rel, $preserve)) { $skipped++; continue; }
        if (is_dir($from)) copyDir($from, $to, $base_src, $preserve, $copied, $skipped);
        else { copy($from, $to); $copied++; }
    }
}

copyDir(rtrim($src, '/'), $base, rtrim($src, '/'), $preserve, $copied, $skipped);
echo '<pre class="ok">&#10003; File di-copy: ' . $copied . '   |   Dilewati (aman): ' . $skipped . ' (vendor, .env, storage)</pre>';

// ── Step 4: Cleanup ───────────────────────────────────────────────────────────
@unlink($tmp_zip);
echo '<h2>4. Cleanup &#10003;</h2><pre class="ok">Temp files dihapus.</pre>'; flush();

// ── Step 5: Artisan Commands ──────────────────────────────────────────────────
echo '<h2>5. Artisan Commands</h2>'; flush();

$php    = PHP_BINARY ?: 'php';
$artisan = escapeshellarg($base . '/artisan');

$commands = [
    'migrate --force' => 'Jalankan semua migration baru',
    'config:clear'    => 'Hapus config cache lama',
    'config:cache'    => 'Buat config cache baru',
    'route:clear'     => 'Hapus route cache lama',
    'route:cache'     => 'Buat route cache baru',
    'view:clear'      => 'Hapus compiled views',
    'cache:clear'     => 'Hapus application cache',
];

$hasError = false;
foreach ($commands as $cmd => $desc) {
    $output  = shell_exec(escapeshellarg($php) . ' ' . $artisan . ' ' . $cmd . ' 2>&1');
    $outTrim = trim($output ?? '');
    $isError = ($output === null || stripos($output, 'error') !== false || stripos($output, 'failed') !== false);

    // migrate: parse per-line untuk detail
    if (str_starts_with($cmd, 'migrate')) {
        $lines   = explode("\n", $outTrim);
        $migrOk  = []; $migrSkip = []; $migrErr = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (stripos($line, 'migrating') !== false || stripos($line, 'migrated') !== false) $migrOk[]  = $line;
            elseif (stripos($line, 'nothing to migrate') !== false)                             $migrSkip[] = $line;
            elseif (stripos($line, 'error') !== false || stripos($line, 'fail') !== false)     $migrErr[]  = $line;
            else                                                                                $migrSkip[] = $line;
        }

        echo '<pre>';
        echo '<span class="info">$ php artisan ' . htmlspecialchars($cmd) . '  ← ' . $desc . '</span>' . "\n";
        if (!empty($migrOk)) {
            foreach ($migrOk as $l) echo '<span class="ok">  &#10003; ' . htmlspecialchars($l) . '</span>' . "\n";
        }
        if (!empty($migrSkip)) {
            foreach ($migrSkip as $l) echo '<span class="info">  ' . htmlspecialchars($l) . '</span>' . "\n";
        }
        if (!empty($migrErr)) {
            $hasError = true;
            foreach ($migrErr as $l) echo '<span class="err">  &#10060; ' . htmlspecialchars($l) . '</span>' . "\n";
        }
        if (empty($migrOk) && empty($migrErr)) echo '<span class="info">  (tidak ada migration baru)</span>' . "\n";
        echo '</pre>';
    } else {
        $cls = $isError ? 'err' : 'ok';
        $ico = $isError ? '&#10060;' : '&#10003;';
        if ($isError) $hasError = true;
        echo '<pre class="' . $cls . '">' . $ico . ' php artisan ' . htmlspecialchars($cmd)
           . '  <span class="info">← ' . $desc . '</span>' . "\n"
           . htmlspecialchars($outTrim ?: '(ok)') . '</pre>';
    }
    flush();
}

// ── Step 6: Diagnostik ───────────────────────────────────────────────────────
echo '<h2>6. Diagnostik Pasca Deploy</h2>'; flush();
echo runDiagnostics($base);

// ── Selesai ───────────────────────────────────────────────────────────────────
echo '<h2>7. Selesai</h2>';
if ($hasError) {
    echo '<pre class="warn">&#9888;&#65039;  Deploy selesai tapi ada warning/error di atas. Cek bagian migrate.</pre>';
} else {
    echo '<pre class="ok">&#10003; Deploy berhasil! Website sudah menggunakan kode terbaru.</pre>';
}
echo '</body></html>';

// ─────────────────────────────────────────────────────────────────────────────

function runDiagnostics(string $base): string
{
    $out = '';

    // Load .env untuk DB credentials
    $envFile = $base . '/.env';
    $env = [];
    if (file_exists($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (strpos($line, '=') !== false) {
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
    }

    $host   = $env['DB_HOST']     ?? '127.0.0.1';
    $port   = $env['DB_PORT']     ?? '3306';
    $dbname = $env['DB_DATABASE'] ?? '';
    $user   = $env['DB_USERNAME'] ?? '';
    $pass   = $env['DB_PASSWORD'] ?? '';

    // Koneksi DB
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    } catch (Exception $e) {
        return '<pre class="err">&#10060; Tidak bisa konek DB: ' . htmlspecialchars($e->getMessage()) . '</pre>';
    }

    // Tabel kritis yang harus ada
    $criticalTables = [
        'users'                  => 'Auth & profil',
        'conversations'          => 'Percakapan Dia',
        'messages'               => 'Pesan Dia',
        'groups'                 => 'Grup Dia',
        'group_messages'         => 'Pesan Grup',
        'notifications'          => 'Notifikasi lonceng',
        'kamu_notes'             => 'Catatan Kamu',
        'posts'                  => 'Postingan Kita',
        'post_likes'             => 'Like Kita',
        'post_comments'          => 'Komentar Kita',
        'conversation_invites'   => 'Invite @mention',
        'member_logs'            => 'Log member baru bergabung',
    ];

    // Ambil daftar tabel dari DB
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = array_column($stmt->fetchAll(PDO::FETCH_NUM), 0);

    $out .= '<table><tr><th>Tabel</th><th>Status</th><th>Fungsi</th><th>Jumlah baris</th></tr>';
    foreach ($criticalTables as $tbl => $func) {
        $exists = in_array($tbl, $existingTables);
        $count  = '';
        if ($exists) {
            try {
                $c = $pdo->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn();
                $count = number_format($c);
            } catch (Exception $e) { $count = '?'; }
        }
        $badge  = $exists
            ? '<span class="badge-ok">&#10003; Ada</span>'
            : '<span class="badge-err">&#10060; TIDAK ADA</span>';
        $out .= '<tr><td>' . htmlspecialchars($tbl) . '</td><td>' . $badge . '</td>'
             . '<td><span class="info">' . htmlspecialchars($func) . '</span></td>'
             . '<td>' . ($exists ? $count . ' baris' : '—') . '</td></tr>';
    }
    $out .= '</table>';

    // Migration pending
    $out .= '<h2 style="margin-top:1rem">&#128203; Status Migration</h2>';
    $php    = PHP_BINARY ?: 'php';
    $artisan = escapeshellarg($base . '/artisan');
    $migOut = shell_exec(escapeshellarg($php) . ' ' . $artisan . ' migrate:status 2>&1');
    if ($migOut) {
        $lines = explode("\n", trim($migOut));
        $out .= '<table><tr><th>Migration</th><th>Status</th></tr>';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '+') || str_starts_with($line, '|  Migration')) continue;
            // Parse baris: |  migration_name  |  Ran / Pending  |
            $parts = array_map('trim', explode('|', $line));
            $parts = array_values(array_filter($parts, fn($p) => $p !== ''));
            if (count($parts) < 2) continue;
            $name   = $parts[0];
            $status = $parts[count($parts)-1];
            $isRan  = stripos($status, 'Ran') !== false;
            $isPend = stripos($status, 'Pending') !== false;
            $badge  = $isRan
                ? '<span class="badge-ok">&#10003; Ran</span>'
                : ($isPend ? '<span class="badge-warn">&#9650; Pending</span>' : '<span class="info">'.$status.'</span>');
            $out .= '<tr><td>' . htmlspecialchars($name) . '</td><td>' . $badge . '</td></tr>';
        }
        $out .= '</table>';
    } else {
        $out .= '<pre class="warn">Tidak bisa jalankan migrate:status</pre>';
    }

    // Test notifikasi: cek kolom notifications
    $out .= '<h2 style="margin-top:1rem">&#128276; Cek Tabel Notifications</h2>';
    if (in_array('notifications', $existingTables)) {
        $cols = $pdo->query("SHOW COLUMNS FROM `notifications`")->fetchAll(PDO::FETCH_ASSOC);
        $colNames = array_column($cols, 'Field');
        $required = ['id','user_id','from_user_id','type','title','body','url','icon','read_at'];
        $out .= '<table><tr><th>Kolom</th><th>Status</th></tr>';
        foreach ($required as $col) {
            $ok = in_array($col, $colNames);
            $out .= '<tr><td>' . $col . '</td><td>'
                 . ($ok ? '<span class="badge-ok">&#10003; Ada</span>' : '<span class="badge-err">&#10060; TIDAK ADA</span>')
                 . '</td></tr>';
        }
        $out .= '</table>';

        // Notif terbaru
        $recent = $pdo->query("SELECT id, user_id, type, title, read_at, created_at FROM `notifications` ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if ($recent) {
            $out .= '<p style="color:#7A9DB0;font-size:12px;margin-top:.5rem">5 Notifikasi terbaru:</p>';
            $out .= '<table><tr><th>ID</th><th>user_id</th><th>type</th><th>title</th><th>read_at</th><th>created_at</th></tr>';
            foreach ($recent as $r) {
                $out .= '<tr>' . implode('', array_map(fn($v) => '<td>' . htmlspecialchars((string)$v) . '</td>', $r)) . '</tr>';
            }
            $out .= '</table>';
        } else {
            $out .= '<pre class="warn">Tabel notifications kosong — belum ada notifikasi yang dibuat.</pre>';
        }
    } else {
        $out .= '<pre class="err">&#10060; Tabel notifications TIDAK ADA. Migration belum jalan atau gagal!</pre>';
    }

    return $out;
}
