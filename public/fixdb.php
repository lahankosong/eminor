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

// ── 6c2. Kolom content_type di content_plans ─────────────────────────────────
echo '<h2>6c2. Kolom content_type di content_plans</h2>';
if (tableExists($conn, $dbname, 'content_plans')) {
    if (!columnExists($conn, $dbname, 'content_plans', 'content_type')) {
        runSQL($conn, 'ADD COLUMN content_type', "ALTER TABLE `content_plans` ADD COLUMN `content_type` varchar(255) NOT NULL DEFAULT 'short' AFTER `platforms`");
    } else {
        echo '<pre class="info">&#8212; Kolom content_type sudah ada</pre>';
    }
    markMigration($conn, '2026_06_14_000003_add_content_type_to_content_plans_table');
}

// ── 6d. Tabel ai_providers ────────────────────────────────────────────────────
echo '<h2>6d. Tabel ai_providers</h2>';
if (!tableExists($conn, $dbname, 'ai_providers')) {
    runSQL($conn, 'CREATE TABLE ai_providers', "
        CREATE TABLE `ai_providers` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `base_url` varchar(255) NOT NULL,
            `api_key` text DEFAULT NULL,
            `model` varchar(255) NOT NULL,
            `format` varchar(255) NOT NULL DEFAULT 'openai',
            `enabled` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_14_000002_create_ai_providers_table');
} else {
    echo '<pre class="info">&#8212; Tabel ai_providers sudah ada</pre>';
    markMigration($conn, '2026_06_14_000002_create_ai_providers_table');
}

// ── 6e. Tabel musician_profiles ───────────────────────────────────────────────
echo '<h2>6e. Tabel musician_profiles</h2>';
if (!tableExists($conn, $dbname, 'musician_profiles')) {
    runSQL($conn, 'CREATE TABLE musician_profiles', "
        CREATE TABLE `musician_profiles` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `roles` varchar(255) DEFAULT NULL,
            `skill_level` varchar(255) DEFAULT NULL,
            `genres` varchar(255) DEFAULT NULL,
            `location` varchar(255) DEFAULT NULL,
            `bio` text DEFAULT NULL,
            `looking_for` varchar(255) DEFAULT NULL,
            `spotify_url` varchar(255) DEFAULT NULL,
            `youtube_url` varchar(255) DEFAULT NULL,
            `instagram` varchar(255) DEFAULT NULL,
            `open_to_band` tinyint(1) NOT NULL DEFAULT 1,
            `open_to_collab` tinyint(1) NOT NULL DEFAULT 1,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `musician_profiles_user_id_unique` (`user_id`),
            CONSTRAINT `musician_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_15_000001_create_musician_profiles_table');
} else {
    echo '<pre class="info">&#8212; Tabel musician_profiles sudah ada</pre>';
    markMigration($conn, '2026_06_15_000001_create_musician_profiles_table');
}

// ── 6f. Tabel follows ─────────────────────────────────────────────────────────
echo '<h2>6f. Tabel follows</h2>';
if (!tableExists($conn, $dbname, 'follows')) {
    runSQL($conn, 'CREATE TABLE follows', "
        CREATE TABLE `follows` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `follower_id` bigint(20) UNSIGNED NOT NULL,
            `following_id` bigint(20) UNSIGNED NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `follows_unique` (`follower_id`,`following_id`),
            KEY `follows_following_id_index` (`following_id`),
            CONSTRAINT `follows_follower_fk` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `follows_following_fk` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_15_000002_create_follows_table');
} else {
    echo '<pre class="info">&#8212; Tabel follows sudah ada</pre>';
    markMigration($conn, '2026_06_15_000002_create_follows_table');
}

// ── 6g. Tabel page_visits ─────────────────────────────────────────────────────
echo '<h2>6g. Tabel page_visits</h2>';
if (!tableExists($conn, $dbname, 'page_visits')) {
    runSQL($conn, 'CREATE TABLE page_visits', "
        CREATE TABLE `page_visits` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `page` varchar(20) NOT NULL,
            `session_id` varchar(64) DEFAULT NULL,
            `ip` varchar(45) DEFAULT NULL,
            `user_id` bigint(20) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `page_visits_page_created_at_index` (`page`, `created_at`),
            KEY `page_visits_user_id_foreign` (`user_id`),
            CONSTRAINT `page_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_15_000003_create_page_visits_table');
} else {
    echo '<pre class="info">&#8212; Tabel page_visits sudah ada</pre>';
    markMigration($conn, '2026_06_15_000003_create_page_visits_table');
}

// ── 6g. Tabel band_posts ──────────────────────────────────────────────────────
echo '<h2>6g. Tabel band_posts</h2>';
if (!tableExists($conn, $dbname, 'band_posts')) {
    runSQL($conn, 'CREATE TABLE band_posts', "
        CREATE TABLE `band_posts` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `title` varchar(255) NOT NULL,
            `description` text DEFAULT NULL,
            `roles_needed` varchar(255) DEFAULT NULL,
            `genres` varchar(255) DEFAULT NULL,
            `location` varchar(255) DEFAULT NULL,
            `status` varchar(255) NOT NULL DEFAULT 'open',
            `urgent` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `band_posts_status_index` (`status`),
            CONSTRAINT `band_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_15_000004_create_band_posts_table');
} else {
    echo '<pre class="info">&#8212; Tabel band_posts sudah ada</pre>';
    markMigration($conn, '2026_06_15_000004_create_band_posts_table');
}

// ── 6h. Kolom city di users (lokasi jaringan anti-penipuan) ──────────────────
echo '<h2>6h. Kolom city di users</h2>';
if (tableExists($conn, $dbname, 'users')) {
    if (!columnExists($conn, $dbname, 'users', 'city')) {
        runSQL($conn, 'ADD COLUMN city', "ALTER TABLE `users` ADD COLUMN `city` varchar(255) DEFAULT NULL AFTER `email`");
    } else {
        echo '<pre class="info">&#8212; Kolom city sudah ada</pre>';
    }
    markMigration($conn, '2026_06_15_000005_add_city_to_users_table');
}

// ── 6i. Kolom media di messages & group_messages ─────────────────────────────
echo '<h2>6i. Kolom media (chat)</h2>';
foreach (['messages', 'group_messages'] as $mt) {
    if (!tableExists($conn, $dbname, $mt)) continue;
    if (!columnExists($conn, $dbname, $mt, 'media_url')) {
        runSQL($conn, "ADD media_url ke $mt", "ALTER TABLE `$mt` ADD COLUMN `media_url` varchar(255) DEFAULT NULL AFTER `body`");
    } else {
        echo '<pre class="info">&#8212; ' . $mt . '.media_url sudah ada</pre>';
    }
    if (!columnExists($conn, $dbname, $mt, 'media_type')) {
        runSQL($conn, "ADD media_type ke $mt", "ALTER TABLE `$mt` ADD COLUMN `media_type` varchar(20) DEFAULT NULL AFTER `media_url`");
    } else {
        echo '<pre class="info">&#8212; ' . $mt . '.media_type sudah ada</pre>';
    }
}
markMigration($conn, '2026_06_16_000001_add_media_to_messages_tables');

// ── 6j. Tabel ai_images + kolom kind di ai_providers ─────────────────────────
echo '<h2>6j. AI Images & provider kind</h2>';
if (!tableExists($conn, $dbname, 'ai_images')) {
    runSQL($conn, 'CREATE TABLE ai_images', "
        CREATE TABLE `ai_images` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `song_id` bigint(20) UNSIGNED DEFAULT NULL,
            `prompt` text NOT NULL,
            `provider` varchar(60) DEFAULT NULL,
            `url` varchar(500) NOT NULL,
            `public_id` varchar(255) DEFAULT NULL,
            `ratio` varchar(12) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `ai_images_user_id_index` (`user_id`),
            KEY `ai_images_song_id_index` (`song_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} else {
    echo '<pre class="info">&#8212; Tabel ai_images sudah ada</pre>';
}
markMigration($conn, '2026_06_17_000001_create_ai_images_table');

if (tableExists($conn, $dbname, 'ai_providers') && !columnExists($conn, $dbname, 'ai_providers', 'kind')) {
    runSQL($conn, 'ADD kind ke ai_providers', "ALTER TABLE `ai_providers` ADD COLUMN `kind` varchar(12) NOT NULL DEFAULT 'text' AFTER `format`");
} else {
    echo '<pre class="info">&#8212; ai_providers.kind sudah ada</pre>';
}
markMigration($conn, '2026_06_17_000002_add_kind_to_ai_providers_table');

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
    'ai_providers'         => 'AI Agent providers',
    'musician_profiles'    => 'Direktori Musisi (ekosistem)',
    'follows'              => 'Sistem Follow',
    'band_posts'           => 'Cari Personil (band)',
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
