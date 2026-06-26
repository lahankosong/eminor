<?php
/**
 * Deploy script — tarik update terbaru dari GitHub via `git pull` + bersihkan & cache ulang Laravel.
 * Akses: https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>            (halaman konfirmasi)
 *        https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>&run=1      (jalankan deploy)
 *        https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>&diag=1     (diagnostik DB saja)
 *
 * Kunci dibaca dari .env (DEPLOY_KEY), TIDAK di-hardcode.
 * Script ini TIDAK menjalankan migrasi DB — kalau ada tabel/kolom baru, jalankan fixdb.php sesudahnya.
 */

@set_time_limit(180);

$base    = realpath(__DIR__ . '/../');
$envFile = $base . '/.env';

// Kunci deploy dibaca dari .env (DEPLOY_KEY) — tolak jika belum diset / salah
$secret = '';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strncmp($line, 'DEPLOY_KEY=', 11) === 0) { $secret = trim(substr($line, 11), " \t\"'"); break; }
    }
}
if ($secret === '' || !hash_equals($secret, (string) ($_GET['key'] ?? ''))) {
    http_response_code(403);
    die('403 Forbidden — DEPLOY_KEY belum diset di .env atau kunci salah.');
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Deploy</title>
<style>
*{box-sizing:border-box}
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem;max-width:900px;margin:0 auto}
h1{color:#38A8CC;margin-bottom:.25rem}
.subtitle{color:#7A9DB0;font-size:12px;margin-bottom:1.5rem}
h2{color:#F07040;margin:1.5rem 0 .4rem;font-size:14px;border-bottom:1px solid rgba(240,112,64,.2);padding-bottom:4px}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.75rem;border-radius:8px;white-space:pre-wrap;word-break:break-word;margin:4px 0;font-size:12px}
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
a{color:#38A8CC}
</style></head><body>
<h1>&#128640; Deploy — Margonoandi Fanbase</h1>
<div class="subtitle">Server: ' . htmlspecialchars($base) . ' &nbsp;|&nbsp; Metode: git pull origin main</div>';

$keyEnc = urlencode($secret);

// ── Mode: Diagnostik saja ─────────────────────────────────────────────────────
if (isset($_GET['diag'])) {
    echo '<h2>&#128203; Diagnostik Database & Server</h2>';
    echo runDiagnostics($base);
    echo '</body></html>'; exit;
}

// ── Halaman awal (konfirmasi) ─────────────────────────────────────────────────
if (!isset($_GET['run'])) {
    echo '<h2>Info</h2>
    <pre class="info">Repo    : lahankosong/margonoandi-fanbase (branch: main)
Aksi    : git pull origin main, lalu clear &amp; cache ulang (config/route/view/cache)
DB      : TIDAK disentuh — jalankan fixdb.php bila ada migrasi baru</pre>
    <a href="?key=' . $keyEnc . '&run=1" class="btn">&#9654; Mulai Deploy</a>
    &nbsp;&nbsp;
    <a href="?key=' . $keyEnc . '&diag=1" style="color:#38A8CC;text-decoration:none;padding:10px 20px;border:1px solid #38A8CC;border-radius:8px;display:inline-block;margin-top:1rem">&#128203; Diagnostik Saja</a>
    <div class="alert warn">&#9888;&#65039; Pastikan perubahan sudah di-push ke GitHub sebelum deploy.</div>';
    echo '</body></html>'; exit;
}

// ── Cek exec() tersedia ───────────────────────────────────────────────────────
$disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
if (!function_exists('exec') || in_array('exec', $disabled, true)) {
    echo '<pre class="err">&#10060; Fungsi exec() dinonaktifkan di hosting ini. '
       . 'Aktifkan exec di pengaturan PHP cPanel, atau jalankan git pull manual via Terminal/SSH.</pre>';
    echo '</body></html>'; exit;
}

/** Jalankan perintah shell, tampilkan output + exit code. Return true bila exit 0. */
function runCmd(string $label, string $cmd): bool {
    echo '<h2>' . htmlspecialchars($label) . '</h2>'; flush();
    $out = []; $code = 0;
    exec($cmd . ' 2>&1', $out, $code);
    $text = trim(implode("\n", $out));
    if ($text === '') $text = '(tanpa output)';
    $cls  = $code === 0 ? 'ok' : 'err';
    $mark = $code === 0 ? '&#10003;' : '&#10060;';
    echo '<pre class="' . $cls . '">' . $mark . ' exit=' . (int) $code . '</pre>';
    echo '<pre class="info">' . htmlspecialchars($text) . '</pre>'; flush();
    return $code === 0;
}

$baseArg = escapeshellarg($base);

// ── Step 1: git pull ──────────────────────────────────────────────────────────
// safe.directory='*' mencegah error "dubious ownership" yang umum di shared hosting.
$gitOk = runCmd(
    '1. git pull origin main',
    'cd ' . $baseArg . ' && git -c safe.directory=' . escapeshellarg('*') . ' pull origin main'
);
if (!$gitOk) {
    echo '<pre class="warn">&#9888; git pull gagal. Cek: folder ini hasil clone git? binari git tersedia? '
       . 'ada perubahan lokal yang menghalangi (jalankan: git status)?</pre>';
}

// ── Step 2: Bersihkan & cache ulang (tanpa migrate) ──────────────────────────
$php     = PHP_BINARY ?: 'php';
if (!empty($_GET['php'])) $php = $_GET['php'];                  // override: ?php=/path/ke/php
$artisan = escapeshellarg($php) . ' ' . escapeshellarg($base . '/artisan');

runCmd('2a. config:clear', 'cd ' . $baseArg . ' && ' . $artisan . ' config:clear');
runCmd('2b. cache:clear',  'cd ' . $baseArg . ' && ' . $artisan . ' cache:clear');
runCmd('2c. view:clear',   'cd ' . $baseArg . ' && ' . $artisan . ' view:clear');
runCmd('2d. route:clear',  'cd ' . $baseArg . ' && ' . $artisan . ' route:clear');
runCmd('2e. config:cache', 'cd ' . $baseArg . ' && ' . $artisan . ' config:cache');
runCmd('2f. route:cache',  'cd ' . $baseArg . ' && ' . $artisan . ' route:cache');

// ── Step 3: Diagnostik pasca deploy ──────────────────────────────────────────
echo '<h2>3. Diagnostik Pasca Deploy</h2>'; flush();
echo runDiagnostics($base);

// ── Selesai ───────────────────────────────────────────────────────────────────
echo '<h2>4. Selesai</h2>';
if ($gitOk) {
    echo '<pre class="ok">&#10003; Kode terbaru sudah ditarik & cache diperbarui.</pre>';
} else {
    echo '<pre class="warn">&#9888;&#65039; Deploy selesai tapi git pull bermasalah — cek bagian 1 di atas.</pre>';
}
echo '<pre class="warn">&#9888; Ada migrasi/tabel baru? Jalankan <a href="/fixdb.php?key=' . htmlspecialchars($keyEnc) . '">/fixdb.php?key=…</a> untuk menyesuaikan database.</pre>';
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
        'gig_posts'              => 'Papan Gig',
        'articles'               => 'Library / Materi Musik',
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
             . '<td>' . ($exists ? $count . ' baris' : '&#8212;') . '</td></tr>';
    }
    $out .= '</table>';

    // Migration pending (read-only)
    $out .= '<h2 style="margin-top:1rem">&#128203; Status Migration</h2>';
    $php    = PHP_BINARY ?: 'php';
    $artisan = escapeshellarg($php) . ' ' . escapeshellarg($base . '/artisan');
    $migOut = @shell_exec($artisan . ' migrate:status 2>&1');
    if ($migOut) {
        $lines = explode("\n", trim($migOut));
        $out .= '<table><tr><th>Migration</th><th>Status</th></tr>';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '+') || str_starts_with($line, '|  Migration')) continue;
            $parts = array_map('trim', explode('|', $line));
            $parts = array_values(array_filter($parts, fn($p) => $p !== ''));
            if (count($parts) < 2) continue;
            $name   = $parts[0];
            $status = $parts[count($parts)-1];
            $isRan  = stripos($status, 'Ran') !== false;
            $isPend = stripos($status, 'Pending') !== false;
            $badge  = $isRan
                ? '<span class="badge-ok">&#10003; Ran</span>'
                : ($isPend ? '<span class="badge-warn">&#9650; Pending</span>' : '<span class="info">'.htmlspecialchars($status).'</span>');
            $out .= '<tr><td>' . htmlspecialchars($name) . '</td><td>' . $badge . '</td></tr>';
        }
        $out .= '</table>';
        $out .= '<pre class="info">Migration yang masih "Pending" → jalankan fixdb.php untuk membuat tabel/kolomnya.</pre>';
    } else {
        $out .= '<pre class="warn">Tidak bisa jalankan migrate:status (binari PHP CLI mungkin beda — coba tambah ?php=/path/ke/php).</pre>';
    }

    return $out;
}
