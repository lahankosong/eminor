<?php
/**
 * Deploy script — PURE PHP (tanpa exec/shell/git, cocok untuk shared hosting murah).
 * Download ZIP terbaru dari GitHub → extract → copy file → bersihkan cache Laravel
 * dengan menghapus file cache langsung (bukan `php artisan`).
 *
 * Akses: https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>            (halaman konfirmasi)
 *        https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>&run=1      (jalankan deploy)
 *        https://margonoandi.my.id/deploy.php?key=<DEPLOY_KEY>&diag=1     (diagnostik DB saja)
 *
 * Kunci dibaca dari .env (DEPLOY_KEY), TIDAK di-hardcode.
 * Migrasi DB ditangani terpisah lewat fixdb.php (juga pure PHP / mysqli).
 */

@set_time_limit(300);
@ini_set('memory_limit', '256M');

$github  = 'https://github.com/lahankosong/margonoandi-fanbase/archive/refs/heads/main.zip';
$base    = realpath(__DIR__ . '/../');
$envFile = $base . '/.env';
$tmp_zip = sys_get_temp_dir() . '/fanbase_deploy.zip';
$tmp_dir = sys_get_temp_dir() . '/fanbase_extracted';

// File/folder yang TIDAK boleh ditimpa saat copy
$preserve = ['vendor', '.env', 'storage', 'node_modules', '.git', 'public/deploy.php', 'public/fixdb.php'];

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
<div class="subtitle">Server: ' . htmlspecialchars($base) . ' &nbsp;|&nbsp; Metode: download ZIP GitHub (pure PHP, tanpa exec/git)</div>';

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
    <pre class="info">Repo     : lahankosong/margonoandi-fanbase (branch: main)
Metode   : download ZIP &rarr; extract &rarr; copy file (tanpa exec/git/SSH)
Cache    : dibersihkan dengan hapus file cache langsung (tanpa artisan)
Preserve : ' . htmlspecialchars(implode(', ', $preserve)) . '
DB       : TIDAK disentuh — jalankan fixdb.php bila ada migrasi baru</pre>
    <a href="?key=' . $keyEnc . '&run=1" class="btn">&#9654; Mulai Deploy</a>
    &nbsp;&nbsp;
    <a href="?key=' . $keyEnc . '&diag=1" style="color:#38A8CC;text-decoration:none;padding:10px 20px;border:1px solid #38A8CC;border-radius:8px;display:inline-block;margin-top:1rem">&#128203; Diagnostik Saja</a>
    <div class="alert warn">&#9888;&#65039; Pastikan perubahan sudah di-push ke GitHub sebelum deploy.</div>';
    echo '</body></html>'; exit;
}

// ── Step 1: Download ZIP ──────────────────────────────────────────────────────
echo '<h2>1. Download dari GitHub</h2>'; flush();
$zip_data = downloadFile($github);
if ($zip_data === null || strlen($zip_data) < 1024) {
    echo '<pre class="err">&#10060; Gagal download ZIP. Cek koneksi, atau allow_url_fopen/cURL di hosting.</pre>';
    echo '</body></html>'; exit;
}
if (@file_put_contents($tmp_zip, $zip_data) === false) {
    echo '<pre class="err">&#10060; Gagal menulis file sementara di ' . htmlspecialchars($tmp_zip) . '</pre>';
    echo '</body></html>'; exit;
}
echo '<pre class="ok">&#10003; Download selesai (' . round(strlen($zip_data) / 1024) . ' KB)</pre>'; flush();

// ── Step 2: Extract ZIP ───────────────────────────────────────────────────────
echo '<h2>2. Extract ZIP</h2>'; flush();
if (!class_exists('ZipArchive')) {
    echo '<pre class="err">&#10060; Ekstensi ZipArchive tidak aktif di hosting ini.</pre>';
    echo '</body></html>'; exit;
}
if (is_dir($tmp_dir)) rrmdir($tmp_dir);
$zip = new ZipArchive();
if ($zip->open($tmp_zip) !== true) {
    echo '<pre class="err">&#10060; Gagal buka ZIP.</pre>';
    echo '</body></html>'; exit;
}
$zip->extractTo($tmp_dir);
$zip->close();
$extracted = glob($tmp_dir . '/*/');
$src = rtrim($extracted[0] ?? $tmp_dir, '/');
echo '<pre class="ok">&#10003; Extract ke: ' . htmlspecialchars($src) . '</pre>'; flush();

// ── Step 3: Copy file ke project ──────────────────────────────────────────────
echo '<h2>3. Copy File ke Project</h2>'; flush();
$copied = 0; $skipped = 0;
copyDir($src, $base, $src, $preserve, $copied, $skipped);
echo '<pre class="ok">&#10003; File di-copy: ' . $copied . '   |   Dilewati (aman): ' . $skipped . '</pre>'; flush();

// ── Step 4: Cleanup temp ──────────────────────────────────────────────────────
@unlink($tmp_zip);
if (is_dir($tmp_dir)) rrmdir($tmp_dir);
echo '<h2>4. Cleanup</h2><pre class="ok">&#10003; File sementara dihapus.</pre>'; flush();

// ── Step 5: Bersihkan cache Laravel (pure PHP, hapus file cache langsung) ─────
echo '<h2>5. Bersihkan Cache (tanpa artisan)</h2>'; flush();
$cacheTargets = [
    'bootstrap/cache/config.php'    => 'config cache',
    'bootstrap/cache/routes-v7.php' => 'route cache (v7)',
    'bootstrap/cache/routes.php'    => 'route cache',
    'bootstrap/cache/events.php'    => 'event cache',
];
foreach ($cacheTargets as $rel => $label) {
    $path = $base . '/' . $rel;
    if (is_file($path)) {
        echo '<pre class="' . (@unlink($path) ? 'ok">&#10003; hapus ' : 'err">&#10060; gagal hapus ') . htmlspecialchars($label) . ' (' . htmlspecialchars($rel) . ')</pre>';
    } else {
        echo '<pre class="info">&#8212; ' . htmlspecialchars($label) . ' tidak ada (sudah bersih)</pre>';
    }
}
// Compiled views & application cache (isi folder, sisakan .gitignore)
$viewCleared = clearDirContents($base . '/storage/framework/views', ['.gitignore']);
echo '<pre class="ok">&#10003; compiled views dihapus: ' . $viewCleared . ' file</pre>';
$dataCleared = clearDirContents($base . '/storage/framework/cache/data', ['.gitignore']);
echo '<pre class="ok">&#10003; application cache dihapus: ' . $dataCleared . ' item</pre>'; flush();

// ── Step 6: Diagnostik pasca deploy ──────────────────────────────────────────
echo '<h2>6. Diagnostik Pasca Deploy</h2>'; flush();
echo runDiagnostics($base);

// ── Selesai ───────────────────────────────────────────────────────────────────
echo '<h2>7. Selesai</h2>';
echo '<pre class="ok">&#10003; Kode terbaru sudah dipasang & cache dibersihkan.</pre>';
echo '<pre class="warn">&#9888; Ada migrasi/tabel baru (lihat diagnostik di atas)? Jalankan <a href="/fixdb.php?key=' . htmlspecialchars($keyEnc) . '">/fixdb.php?key=…</a> untuk menyesuaikan database.</pre>';
echo '</body></html>';

// ═════════════════════════════════════ HELPERS ══════════════════════════════

/** Download URL via file_get_contents (allow_url_fopen) atau fallback cURL. */
function downloadFile(string $url): ?string {
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => [
            'timeout' => 90, 'follow_location' => true, 'user_agent' => 'Mozilla/5.0',
        ], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data !== false) return $data;
    }
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 90,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data !== false) return $data;
    }
    return null;
}

/** Apakah path relatif termasuk daftar preserve? */
function shouldPreserve(string $relPath, array $preserveList): bool {
    foreach ($preserveList as $p) {
        if ($relPath === $p
            || str_starts_with($relPath, $p . '/')
            || str_starts_with($relPath, $p . DIRECTORY_SEPARATOR)) return true;
    }
    return false;
}

/** Copy rekursif dari $src ke $dst, lewati file/folder di $preserve. */
function copyDir(string $src, string $dst, string $base_src, array $preserve, int &$copied, int &$skipped): void {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..') continue;
        $from = $src . '/' . $item;
        $to   = $dst . '/' . $item;
        $rel  = ltrim(str_replace($base_src, '', $from), '/\\');
        $rel  = str_replace('\\', '/', $rel);
        if (shouldPreserve($rel, $preserve)) { $skipped++; continue; }
        if (is_dir($from)) copyDir($from, $to, $base_src, $preserve, $copied, $skipped);
        else { if (@copy($from, $to)) $copied++; else $skipped++; }
    }
}

/** Hapus folder beserta isinya. */
function rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) $f->isDir() ? @rmdir($f) : @unlink($f);
    @rmdir($dir);
}

/** Kosongkan isi folder (rekursif), sisakan nama file di $keep. Return jumlah item dihapus. */
function clearDirContents(string $dir, array $keep = []): int {
    if (!is_dir($dir)) return 0;
    $n = 0;
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $keep, true)) continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) { rrmdir($path); $n++; }
        elseif (@unlink($path)) $n++;
    }
    return $n;
}

/** Diagnostik DB + status migrasi — semua via PDO (pure PHP, tanpa exec). */
function runDiagnostics(string $base): string {
    $out = '';
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
    $host = $env['DB_HOST'] ?? '127.0.0.1'; $port = $env['DB_PORT'] ?? '3306';
    $dbname = $env['DB_DATABASE'] ?? ''; $user = $env['DB_USERNAME'] ?? ''; $pass = $env['DB_PASSWORD'] ?? '';

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5,
        ]);
    } catch (Exception $e) {
        return '<pre class="err">&#10060; Tidak bisa konek DB: ' . htmlspecialchars($e->getMessage()) . '</pre>';
    }

    $criticalTables = [
        'users' => 'Auth & profil', 'conversations' => 'Percakapan Dia', 'messages' => 'Pesan Dia',
        'groups' => 'Grup Dia', 'group_messages' => 'Pesan Grup', 'notifications' => 'Notifikasi lonceng',
        'kamu_notes' => 'Catatan Kamu', 'posts' => 'Postingan Kita', 'post_likes' => 'Like Kita',
        'post_comments' => 'Komentar Kita', 'conversation_invites' => 'Invite @mention',
        'member_logs' => 'Log member baru', 'gig_posts' => 'Papan Gig', 'articles' => 'Library / Materi Musik',
    ];
    $existingTables = array_column($pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM), 0);

    $out .= '<table><tr><th>Tabel</th><th>Status</th><th>Fungsi</th><th>Jumlah baris</th></tr>';
    foreach ($criticalTables as $tbl => $func) {
        $exists = in_array($tbl, $existingTables);
        $count = '';
        if ($exists) {
            try { $count = number_format($pdo->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn()); }
            catch (Exception $e) { $count = '?'; }
        }
        $badge = $exists ? '<span class="badge-ok">&#10003; Ada</span>' : '<span class="badge-err">&#10060; TIDAK ADA</span>';
        $out .= '<tr><td>' . htmlspecialchars($tbl) . '</td><td>' . $badge . '</td>'
             . '<td><span class="info">' . htmlspecialchars($func) . '</span></td>'
             . '<td>' . ($exists ? $count . ' baris' : '&#8212;') . '</td></tr>';
    }
    $out .= '</table>';

    // Status migrasi via PDO: bandingkan file di database/migrations dengan tabel `migrations`
    $out .= '<h2 style="margin-top:1rem">&#128203; Status Migration</h2>';
    $ran = [];
    if (in_array('migrations', $existingTables)) {
        try { $ran = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN); }
        catch (Exception $e) {}
    }
    $files = glob($base . '/database/migrations/*.php') ?: [];
    $pending = [];
    foreach ($files as $f) {
        $name = basename($f, '.php');
        if (!in_array($name, $ran, true)) $pending[] = $name;
    }
    if (empty($pending)) {
        $out .= '<pre class="ok">&#10003; Semua migration sudah dijalankan (' . count($ran) . ' migration). Tidak ada yang Pending.</pre>';
    } else {
        $out .= '<pre class="warn">&#9650; ' . count($pending) . ' migration PENDING — jalankan fixdb.php untuk membuat tabel/kolomnya:</pre>';
        $out .= '<table><tr><th>Migration Pending</th></tr>';
        foreach ($pending as $p) $out .= '<tr><td>' . htmlspecialchars($p) . '</td></tr>';
        $out .= '</table>';
    }

    return $out;
}
