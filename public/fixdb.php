<?php
/**
 * FixDB — Buat tabel yang hilang langsung via SQL
 * Akses: https://margonoandi.my.id/fixdb.php?key=margono2026
 * HAPUS setelah selesai.
 */

$secret = 'margono2026';
if (($_GET['key'] ?? '') !== $secret) { http_response_code(403); die('403'); }

// Baca .env
$base    = realpath(__DIR__ . '/../');
$envFile = $base . '/.env';
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\"'");
}

$host   = $env['DB_HOST']     ?? '127.0.0.1';
$port   = $env['DB_PORT']     ?? '3306';
$dbname = $env['DB_DATABASE'] ?? '';
$user   = $env['DB_USERNAME'] ?? '';
$pass   = $env['DB_PASSWORD'] ?? '';

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>FixDB</title>
<style>
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem;max-width:800px;margin:0 auto}
h1{color:#38A8CC}h2{color:#F07040;margin:1.2rem 0 .3rem;font-size:13px}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:.6rem .8rem;border-radius:6px;white-space:pre-wrap;margin:3px 0;font-size:12px}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#7A9DB0}.warn{color:#facc15}
</style></head><body>
<h1>&#128295; FixDB — Margonoandi</h1>';

// Koneksi mysqli
$conn = @mysqli_connect($host, $user, $pass, $dbname, (int)$port);
if (!$conn) {
    echo '<pre class="err">&#10060; Gagal koneksi: ' . htmlspecialchars(mysqli_connect_error()) . '</pre>';
    echo '</body></html>'; exit;
}
mysqli_set_charset($conn, 'utf8mb4');
echo '<pre class="ok">&#10003; Konek DB: ' . htmlspecialchars($dbname) . ' @ ' . htmlspecialchars($host) . '</pre>';

// Helper
function runSQL($conn, string $label, string $sql): void {
    if (mysqli_query($conn, $sql)) {
        echo '<pre class="ok">&#10003; ' . htmlspecialchars($label) . '</pre>';
    } else {
        $err = mysqli_error($conn);
        // "already exists" bukan error kritis
        if (stripos($err, 'already exists') !== false || stripos($err, 'Duplicate') !== false) {
            echo '<pre class="info">&#8212; ' . htmlspecialchars($label) . ' (sudah ada, skip)</pre>';
        } else {
            echo '<pre class="err">&#10060; ' . htmlspecialchars($label) . ': ' . htmlspecialchars($err) . '</pre>';
        }
    }
}

function tableExists($conn, string $db, string $table): bool {
    $res = mysqli_query($conn, "SELECT 1 FROM information_schema.tables WHERE table_schema='$db' AND table_name='$table' LIMIT 1");
    return mysqli_num_rows($res) > 0;
}
function columnExists($conn, string $db, string $table, string $col): bool {
    $res = mysqli_query($conn, "SELECT 1 FROM information_schema.columns WHERE table_schema='$db' AND table_name='$table' AND column_name='$col' LIMIT 1");
    return mysqli_num_rows($res) > 0;
}
function migrationRan($conn, string $name): bool {
    $n = mysqli_real_escape_string($conn, $name);
    $res = mysqli_query($conn, "SELECT 1 FROM migrations WHERE migration='$n' LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}
function markMigration($conn, string $name): void {
    if (migrationRan($conn, $name)) return;
    $n = mysqli_real_escape_string($conn, $name);
    mysqli_query($conn, "INSERT INTO migrations (migration, batch) VALUES ('$n', 99)");
}

// ── 1. Tabel notifications ────────────────────────────────────────────────────
echo '<h2>1. Tabel notifications</h2>';
if (!tableExists($conn, $dbname, 'notifications')) {
    runSQL($conn, 'CREATE TABLE notifications', "
        CREATE TABLE `notifications` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `from_user_id` bigint(20) UNSIGNED DEFAULT NULL,
            `type` varchar(255) NOT NULL,
            `title` varchar(255) NOT NULL,
            `body` text DEFAULT NULL,
            `url` varchar(255) DEFAULT NULL,
            `icon` varchar(255) DEFAULT NULL,
            `read_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `notifications_user_id_index` (`user_id`),
            KEY `notifications_from_user_id_index` (`from_user_id`),
            CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `notifications_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_04_28_055637_create_notifications_table');
} else {
    echo '<pre class="info">&#8212; Tabel notifications sudah ada</pre>';
    markMigration($conn, '2026_04_28_055637_create_notifications_table');
}

// ── 2. Tabel kamu_notes ───────────────────────────────────────────────────────
echo '<h2>2. Tabel kamu_notes</h2>';
if (!tableExists($conn, $dbname, 'kamu_notes')) {
    runSQL($conn, 'CREATE TABLE kamu_notes', "
        CREATE TABLE `kamu_notes` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `title` varchar(255) DEFAULT NULL,
            `body` text NOT NULL,
            `color` varchar(255) NOT NULL DEFAULT '#FFF8F0',
            `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `kamu_notes_user_id_foreign` (`user_id`),
            CONSTRAINT `kamu_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_04_28_050626_create_kamu_notes_table');
} else {
    echo '<pre class="info">&#8212; Tabel kamu_notes sudah ada</pre>';
    markMigration($conn, '2026_04_28_050626_create_kamu_notes_table');
}

// ── 3. Kolom users: last_seen, is_online ──────────────────────────────────────
echo '<h2>3. Kolom last_seen & is_online di tabel users</h2>';
if (!columnExists($conn, $dbname, 'users', 'last_seen')) {
    runSQL($conn, 'ADD COLUMN last_seen', "ALTER TABLE `users` ADD COLUMN `last_seen` timestamp NULL DEFAULT NULL");
} else {
    echo '<pre class="info">&#8212; Kolom last_seen sudah ada</pre>';
}
if (!columnExists($conn, $dbname, 'users', 'is_online')) {
    runSQL($conn, 'ADD COLUMN is_online', "ALTER TABLE `users` ADD COLUMN `is_online` tinyint(1) NOT NULL DEFAULT 0");
} else {
    echo '<pre class="info">&#8212; Kolom is_online sudah ada</pre>';
}
markMigration($conn, '2026_06_11_093400_add_online_presence_to_users_table');

// ── 4. Tabel conversation_invites ────────────────────────────────────────────
echo '<h2>4. Tabel conversation_invites</h2>';
if (!tableExists($conn, $dbname, 'conversation_invites')) {
    runSQL($conn, 'CREATE TABLE conversation_invites', "
        CREATE TABLE `conversation_invites` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `conversation_id` bigint(20) UNSIGNED NOT NULL,
            `from_user_id` bigint(20) UNSIGNED NOT NULL,
            `to_user_id` bigint(20) UNSIGNED NOT NULL,
            `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
            `joined_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `conv_invites_unique` (`conversation_id`,`to_user_id`),
            KEY `conversation_invites_from_user_id_foreign` (`from_user_id`),
            KEY `conversation_invites_to_user_id_foreign` (`to_user_id`),
            CONSTRAINT `conversation_invites_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
            CONSTRAINT `conversation_invites_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `conversation_invites_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_11_150000_create_conversation_invites_table');
} else {
    echo '<pre class="info">&#8212; Tabel conversation_invites sudah ada</pre>';
    markMigration($conn, '2026_06_11_150000_create_conversation_invites_table');
}

// ── 5. Kolom parent_id & likes_count di post_comments ─────────────────────────
echo '<h2>5. Kolom like & balasan di post_comments</h2>';
if (tableExists($conn, $dbname, 'post_comments')) {
    if (!columnExists($conn, $dbname, 'post_comments', 'parent_id')) {
        runSQL($conn, 'ADD COLUMN parent_id ke post_comments',
            "ALTER TABLE `post_comments` ADD COLUMN `parent_id` bigint(20) UNSIGNED NULL AFTER `post_id`");
    } else { echo '<pre class="info">&#8212; Kolom parent_id sudah ada</pre>'; }
    if (!columnExists($conn, $dbname, 'post_comments', 'likes_count')) {
        runSQL($conn, 'ADD COLUMN likes_count ke post_comments',
            "ALTER TABLE `post_comments` ADD COLUMN `likes_count` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `body`");
    } else { echo '<pre class="info">&#8212; Kolom likes_count sudah ada</pre>'; }
}

// ── 6. Tabel post_comment_likes ───────────────────────────────────────────────
echo '<h2>6. Tabel post_comment_likes</h2>';
if (!tableExists($conn, $dbname, 'post_comment_likes')) {
    runSQL($conn, 'CREATE TABLE post_comment_likes', "
        CREATE TABLE `post_comment_likes` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `comment_id` bigint(20) UNSIGNED NOT NULL,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `pcl_unique` (`comment_id`,`user_id`),
            KEY `post_comment_likes_user_id_foreign` (`user_id`),
            CONSTRAINT `pcl_comment_fk` FOREIGN KEY (`comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
            CONSTRAINT `pcl_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_11_200000_add_comment_features');
} else {
    echo '<pre class="info">&#8212; Tabel post_comment_likes sudah ada</pre>';
    markMigration($conn, '2026_06_11_200000_add_comment_features');
}

// ── 6b. Tabel member_logs ─────────────────────────────────────────────────────
echo '<h2>6b. Tabel member_logs</h2>';
if (!tableExists($conn, $dbname, 'member_logs')) {
    runSQL($conn, 'CREATE TABLE member_logs', "
        CREATE TABLE `member_logs` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `member_logs_user_id_foreign` (`user_id`),
            CONSTRAINT `member_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_13_000001_create_member_logs_table');
} else {
    echo '<pre class="info">&#8212; Tabel member_logs sudah ada</pre>';
    markMigration($conn, '2026_06_13_000001_create_member_logs_table');
}

// ── 6c. Tabel content_plans ───────────────────────────────────────────────────
echo '<h2>6c. Tabel content_plans</h2>';
if (!tableExists($conn, $dbname, 'content_plans')) {
    runSQL($conn, 'CREATE TABLE content_plans', "
        CREATE TABLE `content_plans` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `plan_date` date NOT NULL,
            `song_id` bigint(20) UNSIGNED DEFAULT NULL,
            `platforms` varchar(255) DEFAULT NULL,
            `title` varchar(255) DEFAULT NULL,
            `status` varchar(255) NOT NULL DEFAULT 'rencana',
            `notes` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `content_plans_plan_date_index` (`plan_date`),
            KEY `content_plans_song_id_foreign` (`song_id`),
            CONSTRAINT `content_plans_song_id_foreign` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_14_000001_create_content_plans_table');
} else {
    echo '<pre class="info">&#8212; Tabel content_plans sudah ada</pre>';
    markMigration($conn, '2026_06_14_000001_create_content_plans_table');
}

// ── 7. Mark remaining pending migrations ─────────────────────────────────────
echo '<h2>7. Tandai migration yang pending sebagai selesai</h2>';
$toMark = [
    '2026_04_25_033848_fix_posts_and_post_likes_tables',
    '2026_04_28_054029_add_edit_fields_to_comments',
    '2026_06_11_120000_ensure_posts_kamu_notes_tables',
];
foreach ($toMark as $m) {
    markMigration($conn, $m);
    echo '<pre class="ok">&#10003; Ditandai: ' . htmlspecialchars($m) . '</pre>';
}

// ── 8. Bersihkan View/Config Cache ────────────────────────────────────────────
echo '<h2>8. Bersihkan Cache Laravel</h2>';
$cacheCleared = 0;
$viewDir  = $base . '/storage/framework/views/';
$routeCache = $base . '/bootstrap/cache/routes-v7.php';
$configCache = $base . '/bootstrap/cache/config.php';
if (is_dir($viewDir)) {
    foreach (glob($viewDir . '*.php') as $f) {
        if (@unlink($f)) $cacheCleared++;
    }
    echo '<pre class="ok">&#10003; View cache dihapus (' . $cacheCleared . ' file)</pre>';
} else {
    echo '<pre class="info">&#8212; Direktori view cache tidak ditemukan</pre>';
}
if (file_exists($routeCache))  { @unlink($routeCache);  echo '<pre class="ok">&#10003; Route cache dihapus</pre>'; }
if (file_exists($configCache)) { @unlink($configCache); echo '<pre class="ok">&#10003; Config cache dihapus</pre>'; }

// ── 9. Log Error Laravel Terakhir ────────────────────────────────────────────
echo '<h2>9. Log Error Laravel (50 baris terakhir)</h2>';
$logFile = $base . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    $last  = array_slice($lines, -50);
    $text  = implode("\n", $last);
    echo '<pre style="max-height:400px;overflow-y:auto;font-size:11px;">' . htmlspecialchars($text) . '</pre>';
} else {
    echo '<pre class="info">&#8212; File log tidak ditemukan</pre>';
}

// ── 10. Verifikasi akhir ──────────────────────────────────────────────────────
echo '<h2>10. Verifikasi Tabel Kritis</h2>';
$check = [
    'notifications'        => 'Notifikasi lonceng',
    'kamu_notes'           => 'Catatan Kamu',
    'conversation_invites' => 'Invite @mention',
    'post_comment_likes'   => 'Like komentar',
    'posts'                => 'Postingan Kita',
    'post_comments'        => 'Komentar Kita',
    'member_logs'          => 'Log member baru',
    'content_plans'        => 'Content Calendar',
];
foreach ($check as $tbl => $label) {
    $exists = tableExists($conn, $dbname, $tbl);
    $count  = '';
    if ($exists) {
        $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM `$tbl`");
        $count = ' (' . mysqli_fetch_assoc($r)['c'] . ' baris)';
    }
    $icon = $exists ? '<span class="ok">&#10003;</span>' : '<span class="err">&#10060; TIDAK ADA</span>';
    echo '<pre>' . $icon . ' ' . htmlspecialchars($tbl) . ' — ' . htmlspecialchars($label) . $count . '</pre>';
}

// Cek kolom users
$hasCols = columnExists($conn, $dbname, 'users', 'last_seen') && columnExists($conn, $dbname, 'users', 'is_online');
echo '<pre>' . ($hasCols ? '<span class="ok">&#10003;</span>' : '<span class="err">&#10060;</span>') . ' users.last_seen &amp; users.is_online — Status online</pre>';

mysqli_close($conn);

echo '<h2 style="color:#4ade80;margin-top:1.5rem">&#10003; Selesai!</h2>';
echo '<pre class="ok">Semua tabel sudah dibuat dan cache dibersihkan. Coba buka /dia lagi sekarang.</pre>';
echo '<pre class="warn">&#9888; Hapus file fixdb.php setelah selesai via cPanel File Manager.</pre>';
echo '</body></html>';
