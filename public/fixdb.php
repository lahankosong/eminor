
<?php
/**
 * FixDB — Buat tabel yang hilang langsung via SQL
 * Akses: https://margonoandi.my.id/fixdb.php?key=<DEPLOY_KEY>
 * Kunci dibaca dari .env (DEPLOY_KEY), TIDAK di-hardcode.
 */

// Baca .env (termasuk DEPLOY_KEY untuk autentikasi)
$base    = realpath(__DIR__ . '/../');
$envFile = $base . '/.env';
$env = [];
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\"'");
    }
}

// Kunci dibaca dari .env (DEPLOY_KEY) — tolak jika belum diset / salah
$secret = $env['DEPLOY_KEY'] ?? '';
if ($secret === '' || !hash_equals($secret, (string) ($_GET['key'] ?? ''))) {
    http_response_code(403);
    die('403 — DEPLOY_KEY belum diset di .env atau kunci salah.');
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

// Koneksi mysqli — matikan mode exception mysqli (default sejak PHP 8.1) supaya
// runSQL bisa memeriksa return value & 1 query gagal tak mematikan seluruh script.
mysqli_report(MYSQLI_REPORT_OFF);
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

// ── 9z. Statistik pemutaran lagu ───────────────────────────────────────────────
echo '<h2>9z. Statistik Lagu</h2>';
if (tableExists($conn, $dbname, 'songs')) {
    runSQL($conn, 'ADD COLUMN play_count ke songs', "ALTER TABLE `songs` ADD COLUMN `play_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0");
} else {
    echo '<pre class="info">&#8212; tabel songs belum ada, skip</pre>';
}

// ── 9y. Web Push (notifikasi Android) ──────────────────────────────────────────
echo '<h2>9y. Web Push</h2>';
runSQL($conn, 'CREATE TABLE push_subscriptions',
    "CREATE TABLE IF NOT EXISTS `push_subscriptions` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` bigint(20) UNSIGNED NOT NULL,
        `endpoint` varchar(500) NOT NULL,
        `p256dh` varchar(255) DEFAULT NULL,
        `auth` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `push_subscriptions_user_id_index` (`user_id`),
        KEY `push_subscriptions_endpoint_index` (`endpoint`(191))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── 9x. Papan Gig + posts.linked_* ─────────────────────────────────────────────
echo '<h2>9x. Papan Gig &amp; Linked Post</h2>';
runSQL($conn, 'CREATE TABLE gig_posts',
    "CREATE TABLE IF NOT EXISTS `gig_posts` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` bigint(20) UNSIGNED NOT NULL,
        `title` varchar(120) NOT NULL,
        `type` varchar(50) NOT NULL DEFAULT 'lainnya',
        `description` text,
        `location` varchar(120) DEFAULT NULL,
        `date_event` date DEFAULT NULL,
        `requirements` text,
        `status` varchar(20) NOT NULL DEFAULT 'open',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `gig_posts_user_id_index` (`user_id`),
        KEY `gig_posts_status_index` (`status`),
        KEY `gig_posts_type_index` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if (tableExists($conn, $dbname, 'posts')) {
    if (!columnExists($conn, $dbname, 'posts', 'linked_type')) {
        runSQL($conn, 'ADD COLUMN linked_type ke posts', "ALTER TABLE `posts` ADD COLUMN `linked_type` varchar(20) DEFAULT NULL");
    } else { echo '<pre class="info">&#8212; posts.linked_type sudah ada, skip</pre>'; }
    if (!columnExists($conn, $dbname, 'posts', 'linked_id')) {
        runSQL($conn, 'ADD COLUMN linked_id ke posts', "ALTER TABLE `posts` ADD COLUMN `linked_id` bigint(20) UNSIGNED DEFAULT NULL");
    } else { echo '<pre class="info">&#8212; posts.linked_id sudah ada, skip</pre>'; }
    runSQL($conn, 'ADD INDEX posts_linked_index', "ALTER TABLE `posts` ADD INDEX `posts_linked_index` (`linked_type`, `linked_id`)");
} else {
    echo '<pre class="info">&#8212; tabel posts belum ada, skip</pre>';
}

// ── 9w. Roles user (onboarding) + Tip Jar musisi ───────────────────────────────
echo '<h2>9w. Peran User &amp; Tip Jar</h2>';
if (!columnExists($conn, $dbname, 'users', 'roles')) {
    runSQL($conn, 'ADD users.roles', "ALTER TABLE `users` ADD COLUMN `roles` varchar(255) DEFAULT NULL");
} else { echo '<pre class="info">&#8212; users.roles sudah ada, skip</pre>'; }
if (tableExists($conn, $dbname, 'musician_profiles')) {
    if (!columnExists($conn, $dbname, 'musician_profiles', 'tip_url')) {
        runSQL($conn, 'ADD musician_profiles.tip_url', "ALTER TABLE `musician_profiles` ADD COLUMN `tip_url` varchar(255) DEFAULT NULL");
    } else { echo '<pre class="info">&#8212; musician_profiles.tip_url sudah ada, skip</pre>'; }
} else {
    echo '<pre class="info">&#8212; tabel musician_profiles belum ada, skip</pre>';
}

// ── 9u. Foto profil musisi (upload manual) ─────────────────────────────────────
echo '<h2>9u. Foto Profil Musisi</h2>';
if (tableExists($conn, $dbname, 'musician_profiles')) {
    if (!columnExists($conn, $dbname, 'musician_profiles', 'photo')) {
        runSQL($conn, 'ADD musician_profiles.photo', "ALTER TABLE `musician_profiles` ADD COLUMN `photo` varchar(255) DEFAULT NULL");
    } else { echo '<pre class="info">&#8212; musician_profiles.photo sudah ada, skip</pre>'; }
} else {
    echo '<pre class="info">&#8212; tabel musician_profiles belum ada, skip</pre>';
}

// ── 9v. Tabel articles + seed 21 artikel ──────────────────────────────────────
echo '<h2>9v. Tabel articles &amp; Materi Musik</h2>';
if (!tableExists($conn, $dbname, 'articles')) {
    runSQL($conn, 'CREATE TABLE articles', "
        CREATE TABLE `articles` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `slug` varchar(255) NOT NULL,
            `title` varchar(255) NOT NULL,
            `category` varchar(50) NOT NULL,
            `batch` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
            `excerpt` text NOT NULL,
            `content_markdown` longtext NOT NULL,
            `reading_time` smallint(5) UNSIGNED NOT NULL DEFAULT 8,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `articles_slug_unique` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    markMigration($conn, '2026_06_25_000001_create_articles_table');
} else {
    echo '<pre class="info">&#8212; Tabel articles sudah ada</pre>';
    markMigration($conn, '2026_06_25_000001_create_articles_table');
}

// Seed artikel
function insertArticle($conn, array $a): void {
    $slug     = mysqli_real_escape_string($conn, $a['slug']);
    $res = mysqli_query($conn, "SELECT id FROM `articles` WHERE `slug`='$slug' LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        echo '<pre class="info">&#8212; Artikel sudah ada: ' . htmlspecialchars($a['slug']) . '</pre>';
        return;
    }
    $title    = mysqli_real_escape_string($conn, $a['title']);
    $category = mysqli_real_escape_string($conn, $a['category']);
    $batch    = (int)$a['batch'];
    $excerpt  = mysqli_real_escape_string($conn, $a['excerpt']);
    $content  = mysqli_real_escape_string($conn, $a['content']);
    $rt       = (int)$a['reading_time'];
    $now      = date('Y-m-d H:i:s');
    $ok = mysqli_query($conn,
        "INSERT INTO `articles` (`slug`,`title`,`category`,`batch`,`excerpt`,`content_markdown`,`reading_time`,`created_at`,`updated_at`)
         VALUES ('$slug','$title','$category',$batch,'$excerpt','$content',$rt,'$now','$now')"
    );
    if ($ok) echo '<pre class="ok">&#10003; Insert: ' . htmlspecialchars($a['slug']) . '</pre>';
    else     echo '<pre class="err">&#10060; Gagal insert ' . htmlspecialchars($a['slug']) . ': ' . htmlspecialchars(mysqli_error($conn)) . '</pre>';
}

if (tableExists($conn, $dbname, 'articles')) {

$articles = [];

// ── BATCH 1: TEORI MUSIK ──
$articles[] = ['slug'=>'chord-untuk-pemula','title'=>'Chord Gitar untuk Pemula: Mulai dari Yang Paling Penting','category'=>'teori','batch'=>1,'reading_time'=>10,
'excerpt'=>'Cuma butuh 4 chord untuk mainkan ratusan lagu pop. Panduan visual dan praktis buat yang baru pegang gitar.',
'content'=>'# Chord Gitar untuk Pemula: Mulai dari Yang Paling Penting

Kalau kamu baru beli gitar dan bingung mau mulai dari mana — baca ini dulu sebelum nyerah.

Fakta yang bikin semangat: hampir semua lagu pop Indonesia cuma pakai **4–6 chord**. Artinya, begitu kamu hafal chord-chord itu, kamu bisa ngiringin ratusan lagu.

## Anatomi Chord

Chord = beberapa nada dibunyikan bersamaan. Di gitar, artinya kamu menekan beberapa senar di posisi tertentu, lalu strum (genjreng) semuanya.

Cara baca diagram chord:
- **Kotak vertikal** = leher gitar, senar dari kiri (tebal/E rendah) ke kanan (tipis/e tinggi)
- **Titik** = jari ditekan di situ
- **X** = senar tidak dibunyikan
- **O** = senar terbuka (tidak ditekan)
- **Angka 1–4** = jari telunjuk sampai kelingking

## 4 Chord Wajib Pertama

### 1. Em (E minor)
Paling gampang. Jari 2 dan 3 di senar A dan D, fret 2. Senar lain terbuka semua.

**Cara latih:** Tekan dua jari itu, petik satu-satu dari bawah ke atas. Tiap senar harus bunyi bersih, tidak fret buzz.

### 2. Am (A minor)
Jari 1 di senar B fret 1, jari 2 di senar D fret 2, jari 3 di senar G fret 2. Senar A dan e terbuka, senar E (paling tebal) tidak dibunyikan.

**Nuansa:** Galau, melankolis. Banyak lagu indie Indonesia pakai Am.

### 3. C (C major)
Jari 1 di senar B fret 1, jari 2 di senar D fret 2, jari 3 di senar A fret 3. Senar G dan e terbuka, senar E tidak dibunyikan.

### 4. G (G major)
Jari 2 di senar A fret 2, jari 1 di senar E bawah fret 2, jari 3 di senar e fret 3.

## Progresi Populer yang Bisa Langsung Dimainkan

Pop ballad: Em - C - G - D
Indie melankolis: Am - F - C - G
Lagu Margonoandi: C - G - Am - F

## Tips Belajar Chord yang Efektif

**Jangan latih satu chord lama-lama.** Yang susah itu perpindahan antar chord. Latih perpindahan: C ke G, G ke D, D ke Em.

**Gunakan metronome.** Mulai lambat, misalnya 60 BPM. Naikkan BPM setelah perpindahan terasa smooth.

**Tekan di ujung jari.** Kalau ada senar yang bunyi mati, biasanya karena jari menempel ke senar di sebelahnya.

**Sabar dengan rasa sakit.** Ujung jari akan sakit minggu pertama — lama-lama terbentuk kapalan.

## Langkah Selanjutnya

Setelah hafal Em, Am, C, G — tambahkan D major, F major (versi mini 4 senar), dan E major. Dengan 7 chord ini kamu bisa mainkan hampir semua lagu pop/indie Indonesia.

Hal terpenting: **mainkan lagu sungguhan dari hari pertama** — bukan cuma latihan teknik, tapi lagu yang kamu suka.'];

$articles[] = ['slug'=>'skala-musik-dasar','title'=>'Skala Musik: Mayor, Minor, dan Pentatonik yang Wajib Kamu Tahu','category'=>'teori','batch'=>1,'reading_time'=>8,
'excerpt'=>'Skala adalah "peta jalan" lagu kamu. Pahami ini dan improvisasi jadi jauh lebih mudah.',
'content'=>'# Skala Musik: Mayor, Minor, dan Pentatonik

Skala musik sering dibayangkan sebagai sesuatu yang rumit dan akademis. Padahal konsepnya simpel: **skala = urutan nada yang terdengar enak bersama**.

## Kenapa Skala Penting?

Skala mendefinisikan "mood" atau nuansa lagu:
- Skala **mayor** → ceria, terang, happy
- Skala **minor** → sedih, serius, dramatis
- Skala **pentatonik** → blues, rock, earthy

## Skala Mayor

Skala C mayor = C D E F G A B C

Formula interval: T T S T T T S (T=tone/2 fret, S=semitone/1 fret)

Formula ini berlaku di semua nada dasar. G mayor? Mulai dari G, ikuti T T S T T T S → G A B C D E F# G.

## Skala Minor Naturel

Skala A minor = A B C D E F G A. Formula: T S T T S T T

A minor adalah "saudara kandung" C mayor — menggunakan nada yang sama, tapi dimulai dari A. Ini yang disebut relative minor.

Setiap kunci mayor punya relative minor: C mayor→A minor, G mayor→E minor, D mayor→B minor.

## Skala Pentatonik

Pentatonik = 5 nada. Versi ringan dari skala mayor/minor.

**A minor pentatonik:** A C D E G A

Di gitar, posisi standar (mulai dari fret 5):
- E: 5-8, A: 5-7, D: 5-7, G: 5-7, B: 5-8, e: 5-8

Hafalkan pola ini, lalu geser ke fret lain sesuai kunci lagu.

## Cara Pakai Skala di Lagu

Kalau lagu di kunci C mayor → melodi bisa menggunakan nada C D E F G A B.
Kalau mau lebih blues-y → pakai pentatonik: C D E G A.

## Latihan Praktis

1. Hafalkan pentatonik minor di satu posisi gitar
2. Mainkan asal di atas backing track YouTube
3. Dengarkan nada mana yang terdengar paling "benar"

Skala bukan aturan kaku — tapi peta. Kamu boleh keluar dari peta, tapi lebih mudah kalau tahu dulu batasnya.'];

$articles[] = ['slug'=>'interval-musik','title'=>'Interval Musik: Memahami Jarak Antar Nada','category'=>'teori','batch'=>1,'reading_time'=>7,
'excerpt'=>'Interval adalah dasar harmoni. Pahami ini dan kamu bisa mengenali chord, melodi, dan progressi hanya dengan telinga.',
'content'=>'# Interval Musik: Memahami Jarak Antar Nada

Kalau kamu pernah ngerasa ada dua nada yang "klop" banget, atau sebaliknya ada dua nada yang bikin kuping serasa dicubit — itu yang namanya interval.

**Interval = jarak antara dua nada.**

## Satuan: Semitone dan Tone

Semitone (setengah nada) = 1 fret di gitar, atau 1 tuts di piano.
Tone (satu nada penuh) = 2 fret atau 2 tuts.

Dari C ke C# = 1 semitone. Dari C ke D = 2 semitone = 1 tone.

## Nama-Nama Interval

| Jarak (semitone) | Nama Interval | Contoh |
|---|---|---|
| 0 | Unison | C–C |
| 2 | Major 2nd | C–D |
| 3 | Minor 3rd | C–Eb |
| 4 | Major 3rd | C–E |
| 5 | Perfect 4th | C–F |
| 7 | Perfect 5th | C–G |
| 9 | Major 6th | C–A |
| 12 | Octave | C–C atas |

## Interval yang Paling Penting

**Major 3rd (4 semitone):** Bikin chord terasa mayor/ceria. C + E = nuansa terang.
**Minor 3rd (3 semitone):** Bikin chord terasa minor/sedih. C + Eb = nuansa gelap.
**Perfect 5th (7 semitone):** Power chord — kokoh, netral.
**Octave (12 semitone):** Nada yang sama, satu level lebih tinggi.

## Interval dan Chord

Chord mayor = root + major 3rd + perfect 5th
Chord minor = root + minor 3rd + perfect 5th

Itu kenapa chord C mayor (C E G) berbeda dari Cm (C Eb G) — cuma beda satu semitone di tengah, tapi nuansanya beda banget.

## Latihan Telinga (Ear Training)

Hafal suara interval dengan referensi lagu:
- Major 2nd = nada pertama "Happy Birthday"
- Perfect 4th = "Here Comes the Bride"
- Perfect 5th = tema Star Wars
- Octave = "Somewhere Over the Rainbow"

Latihan telinga tiap hari. Lama-lama kamu bisa identifikasi interval hanya dari suara.'];

$articles[] = ['slug'=>'nulis-lirik-lagu','title'=>'Cara Nulis Lirik yang Jujur dan Nyambung ke Orang Lain','category'=>'teori','batch'=>1,'reading_time'=>12,
'excerpt'=>'Lirik yang bagus bukan soal vocab keren. Ini tentang kejujuran dan detail spesifik yang bikin orang ngerasa "itu gue banget".',
'content'=>'# Cara Nulis Lirik yang Jujur dan Nyambung ke Orang Lain

Ada satu kesalahan paling umum penulis lagu pemula: **mencoba bikin lirik yang "terdengar seperti lagu"**.

Hasilnya: lirik yang klise, generik, dan tidak bernyawa. Lirik yang bagus tidak berusaha terdengar puitis. Dia **jujur**.

## Prinsip 1: Spesifik > Umum

"Kamu pergi dan hatiku hancur" → umum, tidak berkesan.

"Kamu ninggalin gelas kopiku di mejaku dan aku nggak bisa nyucinya selama tiga minggu" → spesifik, visual, secara emosional lebih kuat.

Paradoksnya: makin spesifik lirikmu, makin banyak orang yang bisa relate. Karena detail spesifik memancing ingatan spesifik orang lain.

## Prinsip 2: Tunjukkan, Jangan Ceritakan

Jangan bilang emosi — **gambarkan situasinya**.

Jangan: "Aku sangat kesepian"
Tapi: "Aku makan malam sendiri, TV nyala supaya ada suaranya"

Pendengar akan merasakan kesepian itu sendiri.

## Prinsip 3: Mulai dari yang Nyata

Buka catatan HP kamu. Buka chat lama. Scroll foto dari setahun lalu. Ada momen yang masih terasa? Satu detail yang masih nempel? Itu starting point-mu.

## Struktur Lirik Lagu (Dasar)

**Verse:** Cerita/konteks. Vokal biasanya lebih rendah dan naratif.
**Pre-chorus:** Pembangun emosi menuju puncak.
**Chorus:** Inti emosi dan pesan. Harus gampang diingat.
**Bridge:** Perspektif baru, atau momen kontras.

**Tips chorus:** Satu kalimat yang mewakili seluruh lagu. Kalau susah jelasin lagu dalam satu kalimat, chorus-mu mungkin terlalu kompleks.

## Teknik Rhyme yang Tidak Klise

Kalau rima mendiktekan arti — itu masalah. Alternatif:
- **Slant rhyme** (rima tak sempurna): "pergi" / "mati"
- **Internal rhyme**: rima di tengah baris, bukan di ujung

## Revisi adalah Prosesnya

Lirik pertama hampir selalu jelek. Itu normal. Tulis draf pertama tanpa filter. Kemudian tanya:
1. Apakah ini jujur?
2. Apakah ada detail yang lebih spesifik?
3. Apakah setiap baris earn-nya sendiri?

## Latihan 5 Menit

Pilih satu momen dari hidupmu yang terasa "nggak selesai" secara emosional. Tulis 4 baris yang mendeskripsikan momen itu tanpa menggunakan kata: cinta, hati, jiwa, rasa, bahagia, sedih.

Itu adalah latihan terbaik untuk nulis lirik yang jujur.'];

$articles[] = ['slug'=>'genre-musik-indonesia','title'=>'Genre Musik Indonesia: Dari Pop ke Indie Folk','category'=>'teori','batch'=>1,'reading_time'=>9,
'excerpt'=>'Kenali genre-genre yang dominan di Indonesia dan temukan di mana musikmu masuk — ini penting untuk promosi dan pitching.',
'content'=>'# Genre Musik Indonesia: Dari Pop ke Indie Folk

Kalau ada yang tanya "musikmu genre apa?" dan kamu jawab "ya, musik biasa" — itu bukan jawaban yang membantu siapapun.

Genre bukan kotak yang mengurung kreativitas. Genre adalah **bahasa** yang memudahkan orang yang tepat menemukan musikmu.

## Pop Indonesia

Genre paling dominan. Ciri-ciri: melodi vokal yang memorable, produksi rapi, lirik tentang hubungan/emosi universal, struktur lagu standar.

**Artis referensi:** Raisa, Isyana Sarasvati, Afgan, Tulus

## Indie Pop / Indie Folk

Sedang boom. Ciri-ciri: produksi lebih "intimate", vokal lebih personal, instrumen akustik dominan, lirik lebih introspektif.

**Artis referensi:** Hindia, Feast, Payung Teduh, Lomba Sihir

## R&B / Soul Indonesia

Berkembang pesat lewat gen-Z creator. Ciri-ciri: groove dan rhythm yang kuat, vokal melismatic, produksi berani dengan bass yang prominent.

**Artis referensi:** Ardhito Pramono, Barry Likumahuwa, Sal Priadi

## Pop-Rock dan Alternative

- Gitar distorsi sebagai warna utama
- Dinamika lagu yang dramatis (soft verse, loud chorus)

**Artis referensi:** Efek Rumah Kaca, The Adams, Morfem

## Dangdut dan Turunannya

Jangan underestimate — ini genre dengan market terbesar di Indonesia. Dangdut koplo, dangdut pop, EDM dangdut semuanya punya market yang loyal.

## Genre Margonoandi

Berada di persimpangan — **indie folk dengan sentuhan soul dan pop-rock**. Ini membuat pitching ke playlist lebih tricky, tapi fanbase lebih loyal karena mereka datang dari berbagai selera.

## Praktis: Pilih 2–3 Genre Referensi

1. List 5 artis yang musikmu paling mirip
2. Lihat genre mereka di Spotify for Artists
3. Ambil 2 genre yang paling konsisten muncul
4. Itulah genre primermu untuk pitching dan bio artis.'];

// ── BATCH 2: PRODUKSI ──
$articles[] = ['slug'=>'studio-di-kamar','title'=>'Bikin Studio Rekaman di Kamar: Setup dari Nol','category'=>'produksi','batch'=>2,'reading_time'=>15,
'excerpt'=>'Tidak perlu studio mahal. Dengan budget Rp 3–10 juta, kamu bisa rekam lagu yang layak upload ke Spotify dari kamar sendiri.',
'content'=>'# Bikin Studio Rekaman di Kamar: Setup dari Nol

Margonoandi merekam beberapa lagunya dari kamar tidur 3x4 meter. Bedroom recording bukan keterbatasan — dengan approach yang benar, itu adalah kebebasan.

## Komponen Dasar (Urutan Prioritas)

### 1. Audio Interface (Paling Krusial)
Interface adalah jembatan antara gitar/mic dengan komputer. Kualitas interface jauh lebih penting dari microphone mahal sekalipun.

**Rekomendasi:**
- Focusrite Scarlett Solo (Rp 1,2–1,5 jt): 1 input mic, 1 instrument
- Focusrite Scarlett 2i2 (Rp 1,7–2 jt): 2 input, lebih fleksibel

### 2. DAW (Digital Audio Workstation)
- **GarageBand** (gratis, Mac/iOS): Paling ramah pemula
- **REAPER** (Rp 280rb lisensi personal): Ringan, powerful, hampir gratis
- **FL Studio** (ada versi lifetime): Kuat untuk beat dan electronic

### 3. Microphone
- Audio-Technica AT2020 (Rp 700rb): Standar industri untuk budget entry
- Pastikan mic kondenser membutuhkan phantom power (+48V) — Focusrite Scarlett sudah punya ini

### 4. Headphone Monitoring
- Sony MDR-7506 (Rp 800rb–1,2 jt): Standar studio, flat response
- Audio-Technica ATH-M20x/M30x: Alternatif budget

### 5. Akustik Ruangan (Sering Diabaikan)
- Gantung selimut tebal di dinding di belakang mic
- Rekam di pojok yang banyak furniturnya (sofa, lemari baju)
- Hindari merekam di ruangan dengan banyak permukaan keras

## Budget Setup

| Kebutuhan | Budget |
|---|---|
| Audio Interface | Rp 1,5 jt |
| Microphone | Rp 700 rb |
| Headphone | Rp 900 rb |
| Kabel XLR | Rp 100 rb |
| DAW | Gratis |
| **Total** | **~Rp 3,2 jt** |

## Alur Rekaman Sederhana

1. Track gitar/instrumen dulu
2. Track vokal (jarak mic 15–20cm)
3. Tambahkan overdub jika perlu
4. Mixing & mastering di DAW
5. Export WAV 44.1kHz/24bit untuk distribusi

## Kesalahan Umum

**Terlalu banyak plugin.** Mulai dengan EQ dan reverb bawaan DAW.
**Monitoring terlalu keras.** Kuping capek = mixing yang buruk.

Hal terpenting: mulai rekam sekarang dengan apa yang kamu punya. Setup bisa diupgrade bertahap.'];

$articles[] = ['slug'=>'teknik-mic-vokal','title'=>'Teknik Mic untuk Vokal: Posisi, Jarak, dan Cara Menghindari Masalah Umum','category'=>'produksi','batch'=>2,'reading_time'=>10,
'excerpt'=>'Cara kamu megang mic lebih berpengaruh ke hasil rekaman dari mic itu sendiri. Pelajari teknik yang benar dari sini.',
'content'=>'# Teknik Mic untuk Vokal: Posisi, Jarak, dan Cara Menghindari Masalah Umum

Mic seharga Rp 5 juta dengan teknik yang salah akan kalah dari mic Rp 700 ribu dengan teknik yang benar.

## Posisi Mic yang Benar

Untuk kondenser cardioid:
- Posisikan mic sedikit di atas mulut, menghadap ke bawah, sekitar 10–15 derajat
- Jarak ideal: 15–25 cm dari mulut
- Jangan terlalu jauh — suara jadi tipis dan banyak ruang
- Jangan terlalu dekat — proximity effect (bass boom) dan popping P/B

**Pop filter wajib** — filter nilon atau logam antara mulut dan mic. Fungsinya mengurangi "plosive" (bunyi P, B, T yang meledak di mic).

## Proximity Effect

Makin dekat ke mic kondenser, makin tebal bass-nya:
- Mau suara vokal intimate dan low? Dekat ke mic
- Mau suara lebih "airy" dan natural? Sedikit menjauh

Margonoandi sering rekam dengan jarak 12cm untuk nuansa yang lebih personal di ballad.

## Masalah Umum dan Solusinya

**Plosive (P/B meledak):**
Solusi: pasang pop filter, atau miringkan mic 10–15 derajat dari garis lurus mulut.

**Sibilance (S keras, menusuk):**
Solusi: arahkan mic sedikit ke samping. Atau gunakan de-esser di mixing.

**Room sound (ruangan terdengar terlalu banyak):**
Solusi: dekatkan mic ke sumber, kurangi gain interface.

**Clipping (distorsi digital):**
Solusi: turunkan gain di interface. Level rekam ideal: peak sekitar -12dB, tidak pernah menyentuh 0dB.

## Monitoring Saat Rekam

Rekam selalu dengan headphone, bukan speaker. Kalau speaker nyala saat rekam, suaranya bisa masuk ke mic dan menciptakan feedback atau bleed.

## Tips Teknis Terakhir

**Gain staging:** Atur gain sampai vokal rata-rata di -18 hingga -12 dBFS.
**Warm up dulu:** Vokal perlu pemanasan 10–15 menit. Rekam setelah warm up.
**Take banyak:** Rekam minimal 3 take per section. Pilih yang terbaik atau comp (gabung bagian terbaik dari beberapa take).
**Emosi > teknik:** Take yang technically perfect tapi flat secara emosi biasanya dibuang.'];

$articles[] = ['slug'=>'mixing-101','title'=>'Mixing 101: EQ, Kompresi, dan Reverb untuk Pemula','category'=>'produksi','batch'=>2,'reading_time'=>15,
'excerpt'=>'Mixing bukan sihir. Ini adalah proses sistematis yang bisa dipelajari. Mulai dari 3 tool ini dan 80% pekerjaan mixing sudah selesai.',
'content'=>'# Mixing 101: EQ, Kompresi, dan Reverb untuk Pemula

Mixing adalah proses membuat semua instrumen terdengar bersama dengan baik — tidak ada yang tenggelam, tidak ada yang nabrak.

## 1. EQ (Equalizer)

EQ = mengatur volume frekuensi tertentu.

**Spektrum frekuensi:**
- Sub-bass (20–80 Hz): Rumble, body bass
- Bass (80–250 Hz): Kehangatan, punch kick
- Low-mid (250–800 Hz): Bodi vokal dan gitar — area sering "muddy"
- Mid (800 Hz–2 kHz): Kejelasan vokal
- High-mid (2–8 kHz): Detail dan "air"
- Treble (8–20 kHz): Shimmer, sparkle

**Aturan dasar EQ:**
- Cut dulu, boost kemudian
- High-pass filter di semua non-bass instrument (potong di bawah 80–120 Hz di vokal/gitar)
- Jangan boost terlalu tinggi — boost 2–3 dB sudah cukup

## 2. Kompresi (Compressor)

Kompresor mengecilkan volume bagian yang terlalu keras secara otomatis.

**Parameter utama:**
- Threshold: Di atas level berapa kompresor mulai bekerja
- Ratio: Seberapa agresif kompresi. 2:1=ringan, 4:1=medium, 8:1+=agresif
- Attack: Seberapa cepat kompresor bereaksi
- Release: Seberapa cepat kompresor berhenti

**Untuk pemula:**
- Vokal: ratio 3:1–4:1, attack medium (10–30ms), gain reduction 3–6 dB
- Kick/snare: ratio 4:1–6:1, attack cepat (1–5ms)

## 3. Reverb

Reverb = efek "ruangan". Tanpa reverb, lagu terdengar kering dan flat.

**Jenis reverb:**
- Room: Ruangan kecil, natural
- Hall: Ruangan besar, dramatis
- Plate: Klasik untuk vokal

**Tips:**
- Jangan taruh reverb langsung di track vokal — buat aux send (bus reverb)
- Pre-delay 20–30ms bikin vokal terasa masih "di depan"
- Hi-pass filter di reverb return untuk jaga clarity

## Urutan Kerja Mixing

1. Gain staging
2. Balance fader (tanpa EQ/efek dulu)
3. EQ: buat ruang untuk tiap instrumen
4. Compression: kontrol dinamik
5. Reverb/delay: tambahkan depth
6. Automation: naik-turunkan di bagian tertentu

## Check Terakhir

Sebelum bounce, cek mix di: headphone, speaker laptop, speaker handphone, dan mobil. Kalau bagus di semua — mix sudah solid.'];

$articles[] = ['slug'=>'mastering-diy','title'=>'Mastering DIY: Bikin Lagu Kamu Siap Upload ke Streaming','category'=>'produksi','batch'=>2,'reading_time'=>10,
'excerpt'=>'Mastering bukan lagi sihir yang butuh studio mahal. Pelajari cara mencapai LUFS target streaming dan membuat lagu kamu bersaing.',
'content'=>'# Mastering DIY: Bikin Lagu Kamu Siap Upload ke Streaming

Mastering adalah langkah terakhir sebelum lagu siap didistribusikan. Tujuannya: level yang kompetitif, konsistensi suara di berbagai playback system, dan format file yang tepat.

## Target Loudness Platform Streaming

**Target:** -14 LUFS integrated (Spotify standar). Range aman: -16 sampai -12 LUFS.

LUFS = Loudness Unit Full Scale. Satuan yang lebih akurat dari decibel untuk mengukur loudness persepsi.

**True peak:** Jaga di bawah -1.0 dBTP. Ini mencegah distorsi saat file di-encode ke format lossy (MP3/AAC).

## Alat Mastering DIY

**Free:**
- Loudness Penalty analyzer (loudnesspenalty.com) — cek gratis
- LUFS-I meter (banyak versi gratis)
- iZotope Ozone Elements (sering gratis sebagai bundle)

**Online mastering:**
- eMastered, LANDR: Upload file, keluar dalam menit. Kualitas cukup untuk indie release.

## Chain Mastering Sederhana

Urutan plugin di mastering chain:
1. EQ (Linear Phase): Koreksi halus
2. Multiband Compressor / MS Compressor: Kontrol dinamik
3. Stereo Widener (opsional): Expand stereo image dengan hati-hati
4. Limiter: Tool paling penting — set ceiling -1.0 dBTP, dorong gain sampai LUFS target

## Limiter: Kunci Utama

**Setting limiter:**
- Ceiling/Output: -1.0 dBTP
- Gain/Input: Naikkan perlahan sampai LUFS meter menunjukkan -14 LUFS
- Release: Medium (50–100ms)

**Tanda terlalu berlebihan:** Kalau harus push gain sampai distorsi terdengar — artinya mix belum cukup siap untuk dimaster. Kembali ke mixing.

## Format Export

- Format: WAV atau FLAC lossless
- Sample rate: 44100 Hz (44.1 kHz)
- Bit depth: 24 bit
- Stereo

## Cek Akhir

Gunakan **Loudness Penalty** (loudnesspenalty.com) untuk simulasi bagaimana lagu terdengar di berbagai platform. Kalau penalty di bawah 1 dB, kamu sudah di tempat yang benar.'];

$articles[] = ['slug'=>'pilih-daw','title'=>'Pilih DAW yang Tepat: GarageBand, FL Studio, REAPER, atau Logic?','category'=>'produksi','batch'=>2,'reading_time'=>8,
'excerpt'=>'Pilihan DAW tidak sepenting cara kamu menggunakannya. Tapi memilih yang tepat bisa hemat banyak frustrasi di awal.',
'content'=>'# Pilih DAW yang Tepat

DAW (Digital Audio Workstation) adalah software tempat kamu rekam, produksi, dan mix lagu. DAW terbaik adalah yang kamu akan konsisten pakai.

## GarageBand (Mac/iOS) — Gratis

Untuk siapa: Pemula, solo artist, Mac/iPhone user.

**Kelebihan:** Gratis, sudah terinstall di semua Mac, interface paling ramah pemula, bisa upgrade ke Logic Pro.
**Kekurangan:** Mac/iOS only, fitur terbatas dibanding DAW profesional.

**Kesimpulan:** Kalau Mac user, tidak ada alasan untuk tidak mulai di sini.

## FL Studio — Rp 1–3 juta (lifetime)

Untuk siapa: Producer beat, elektronik, hip-hop.

**Kelebihan:** Lifetime free update, piano roll terbaik di industri, sangat kuat untuk beat making.
**Kekurangan:** Workflow berbeda, kurang optimal untuk audio recording multi-track.

**Kesimpulan:** Kalau lebih fokus ke produksi elektronik/beat.

## REAPER — ~Rp 280.000 (lisensi personal)

Untuk siapa: Semua tipe, terutama yang mau power dengan budget minimum.

**Kelebihan:** Termurah untuk fitur yang didapat, sangat customizable, ringan di laptop lawas.
**Kekurangan:** Interface kurang "menarik", tidak ada instrument virtual bawaan yang bagus.

**Kesimpulan:** DAW paling cost-effective.

## Logic Pro (Mac) — Rp 450.000 (satu kali)

**Kelebihan:** Workflow recording sangat baik, instrument virtual premium, mastering tools bagus.
**Kekurangan:** Mac only.

## Rekomendasi Berdasarkan Situasi

| Situasi | Pilihan |
|---|---|
| Mac user, baru mulai | GarageBand → Logic |
| Windows user, budget minim | REAPER |
| Beat maker | FL Studio |

DAW adalah alat. Kendrick Lamar direkam di Pro Tools, Bon Iver di GarageBand, banyak hit global dari FL Studio. Yang penting adalah telinga, latihan, dan konsistensi.'];

// ── BATCH 3: KOLABORASI ──
$articles[] = ['slug'=>'cari-kolaborator-musik','title'=>'Cara Cari Kolaborator Musik di Era Digital','category'=>'kolaborasi','batch'=>3,'reading_time'=>10,
'excerpt'=>'Kolaborasi yang tepat bisa membuka pintu yang tidak bisa kamu buka sendiri. Begini cara menemukannya secara strategis.',
'content'=>'# Cara Cari Kolaborator Musik di Era Digital

Salah satu momen terbesar dalam karir musik seringkali datang dari kolaborasi yang tepat — bukan dari kerja keras sendiri.

## Definisikan Dulu: Kolaborator Apa yang Kamu Butuhkan?

- **Co-writer:** Butuh seseorang yang bisa bantu tulis lagu bareng
- **Featured artist:** Ingin suara/gaya berbeda di lagu kamu
- **Produser:** Butuh seseorang yang handle produksi
- **Musisi session:** Butuh yang mainkan instrumen tertentu untuk rekaman

## Platform dan Tempat Mencari

**Instagram/TikTok:** Follow artis dalam genre yang mirip. Interaksi organik: comment thoughtful, share lagu mereka. Bangun relationship dulu sebelum ada pitch.

**SoundCloud/Bandcamp:** Kolaborator serius masih aktif di sini. Comment di track mereka.

**Komunitas Discord/Grup Facebook:** Cari grup "musik indie Indonesia", "bedroom producer", "kolaborasi lagu".

**Fanbase Margonoandi:** Halaman /musisi di sini sudah ada direktori musisi aktif.

**Event Lokal:** Open mic, jam session, workshop produksi. Koneksi offline seringkali lebih solid dari online.

## Seleksi: Bukan Semua Orang Cocok

Kolaborator yang baik bukan hanya yang karyanya bagus. Perhatikan juga:

**Work ethic yang compatible:** Apakah mereka deliver sesuai timeline?
**Visi yang aligned:** Tidak harus identik, tapi pastikan arah yang sama.
**Komunikasi:** Orang yang responsif di awal biasanya lebih reliable.
**Attitude terhadap revisi:** Kolaborasi = kompromi.

## Red Flags

- Langsung bicara soal uang sebelum ada output apapun
- Janji besar tanpa portofolio yang jelas
- Tidak bisa dihubungi atau responnya makin lama
- Tidak pernah bisa hadir sesuai jadwal

## Mulai Kecil

Kolaborasi pertama tidak perlu ambisius:
- Tukar feedback lagu secara informal
- Session rekaman singkat untuk lihat chemistry
- Dari situ kamu bisa nilai apakah worth dilanjutkan'];

$articles[] = ['slug'=>'dm-pertama-ke-musisi','title'=>'DM Pertama ke Musisi Lain: Template yang Berhasil','category'=>'kolaborasi','batch'=>3,'reading_time'=>8,
'excerpt'=>'Pesan pertama yang kamu kirim ke potential collaborator bisa membuat atau menghancurkan peluang. Pelajari cara yang benar.',
'content'=>'# DM Pertama ke Musisi Lain: Template yang Berhasil

Kebanyakan DM kolaborasi diabaikan bukan karena idenya jelek, tapi karena cara penulisannya.

## Yang Bikin DM Diabaikan

"Hai kak, mau kolaborasi dong, musikku bagus lho" — tidak ada context, tidak ada value proposition.

"Halo, saya adalah musisi berbakat dengan 500 followers..." — terlalu formal, memulai dengan credential yang tidak relevan.

"Collab yuk!" — tidak ada effort sama sekali.

## Anatomi DM yang Berhasil

### 1. Referensi Spesifik
Tunjukkan kamu benar-benar kenal karya mereka. Bukan pujian generik.

"Bridge di lagu Memang Begini — bagian ketika vokal masuk tanpa instrumen — itu bikin aku nangis pertama kali dengar" vs "Lagumu bagus banget"

### 2. Konteks Singkat Tentang Kamu
Satu atau dua kalimat. Link ke karya kalau ada.
"Aku Raka, nulis lagu indie folk dari Jogja."

### 3. Tawaran yang Spesifik
"Aku lagi nulis lagu tentang perpindahan kota — feel-nya mirip sama nuansamu. Kalau kamu open, mau kirim rough demo dan lihat apa yang bisa kita kerjain bareng?"

### 4. Tidak Memaksakan
Akhiri dengan open-ended. "No worries kalau lagi sibuk" bikin mereka tidak merasa terjebak.

## Template 1: Sesama Artis

"Hei [Nama], baru dengerin [judul lagu] dan langsung nyangkut di bagian [detail spesifik].

Aku [nama], nulis lagu [genre] dari [kota]. Lagi ada satu project yang kayaknya cocok sama energimu. Kalau kamu open untuk dengerin dan lihat apakah ada yang bisa kita kerjain bareng, aku seneng banget.

[Link karya kamu]

No rush sama sekali."

## Template 2: Ke Produser

"Hei [Nama], nemu [nama beat] di [platform] dan langsung kerasa cocok sama cara aku nulis.

Aku [nama], singer-songwriter [genre]. Kalau kamu open untuk sesi writing/recording bareng, aku tertarik explore itu.

Ini laguku: [link]"

## Setelah DM Pertama

Kalau tidak ada balasan dalam seminggu: satu follow-up itu wajar. Lebih dari itu mulai terasa memaksa.

DM terbaik datang dari tempat tulus: kamu benar-benar suka karyanya dan punya sesuatu yang bisa ditawarkan.'];

$articles[] = ['slug'=>'kolaborasi-jarak-jauh','title'=>'Kolaborasi Jarak Jauh: Workflow dan Tools yang Wajib Tahu','category'=>'kolaborasi','batch'=>3,'reading_time'=>12,
'excerpt'=>'Di era digital, jarak bukan halangan. Pelajari workflow dan tools untuk kolaborasi musik yang efektif tanpa harus ketemu.',
'content'=>'# Kolaborasi Jarak Jauh: Workflow dan Tools yang Wajib Tahu

Sebagian besar kolaborasi musik sekarang terjadi secara remote. Artis di Jakarta bisa record bareng produser di Bandung, musisi di Surabaya bisa kirim verse ke vocalist di Bali.

## Tools yang Dibutuhkan

**Berbagi File Audio:**
- Google Drive/Dropbox: Buat folder bersama dengan struktur rapi (Reference/, Sessions/, Stems/, Export/)
- WeTransfer: Untuk kirim file besar sekali pakai

**Komunikasi:**
- WhatsApp: Untuk diskusi cepat dan share referensi
- Notion/Google Docs: Untuk tracking progress dan keputusan
- Google Meet/Zoom: Untuk sesi kerja virtual

**Kolaborasi DAW:**
- BandLab: Platform kolaborasi musik berbasis cloud, gratis, bisa collab di browser

## Workflow Dasar Remote Collab

### Fase 1: Alignment
Sebelum ada satu nada pun direkam, pastikan:
- Apa visinya? (mood, reference lagu, genre)
- Siapa yang kerja bagian apa?
- Apa timeline yang realistis?
- Format file apa yang dipakai?
Tuliskan di dokumen bersama. Ini mencegah drama di tengah proses.

### Fase 2: Demo Exchange
- Satu pihak kirim rough demo/instrumental
- Pihak lain respond dengan voice note, melodi, atau lirik kasar
- Ini fase paling bebas — ide boleh aneh

### Fase 3: Pengembangan
- Sepakat pada struktur lagu
- Rekam bagian masing-masing
- Kirim stem (track terpisah) bukan bounce final

### Fase 4: Mixing & Mastering
- Tentukan siapa yang mix
- Share referensi loudness dan vibe sebelum mulai

## File Sharing Best Practices

**Selalu kirim WAV, bukan MP3** untuk file yang akan diedit.

**Naming convention yang jelas:**
NamaLagu_Vokal_Takira_v2.wav — BUKAN "file baru.wav"

**Include notes teknis:** BPM lagu, kunci lagu, sample rate file.

## Komunikasi yang Menjaga Energi Positif

Remote collab kehilangan banyak komunikasi non-verbal:
- Mulai feedback dengan yang positif dulu
- Gunakan bahasa spesifik, bukan evaluatif
- Kalau ada keputusan besar, video call lebih baik dari teks
- Beri ruang untuk pihak lain respond tanpa tekanan

Yang terpenting: buat sistem yang clear dari awal, dan jaga komunikasi tetap terbuka.'];

$articles[] = ['slug'=>'kredit-dan-royalti-kolaborasi','title'=>'Kredit dan Royalti di Kolaborasi: Jangan Sampai Drama','category'=>'kolaborasi','batch'=>3,'reading_time'=>10,
'excerpt'=>'Diskusi uang dan kredit sebelum mulai kolaborasi jauh lebih mudah dari diskusi sesudahnya. Panduan lengkap untuk menghindari konflik.',
'content'=>'# Kredit dan Royalti di Kolaborasi: Jangan Sampai Drama

Banyak persahabatan dan kolaborasi musik yang hancur bukan karena kreatif tidak cocok, tapi karena **tidak ada pembicaraan jelas tentang kredit dan uang di awal**.

## Jenis Royalti di Musik

**Publishing/Komposisi Royalti:** Dari hak cipta lagu (melodi + lirik). Dibagi menjadi writer share (50%) dan publisher share (50%).

**Master Royalti:** Dari hak rekaman. Siapapun yang memiliki rekaman (artis atau label yang bayar biaya produksi).

**Royalti Streaming:** Platform bayar ke distributor → distributor bayar ke pemilik master → pemilik master bayar ke penulis lagu.

## Siapa Dapat Berapa?

**Co-writing (dua orang sama-sama tulis lirik dan melodi):** 50/50 adalah titik awal yang wajar.

**Produser:** Sering menerima 20–30% master royalti (kalau tidak ada upfront fee) atau flat fee upfront tanpa royalti ongoing.

**Featured artist:** Biasanya tidak dapat publishing royalti kecuali berkontribusi pada penulisan.

**Musisi session:** Umumnya flat fee per sesi, tanpa royalti ongoing.

## Cara Tentukan Split yang Adil

Pertimbangkan:
1. Siapa yang memulai/datang dengan ide dasar?
2. Berapa banyak lirik yang ditulis siapa?
3. Berapa banyak melodi yang contributed siapa?
4. Siapa yang bayar biaya produksi?

## Dokumen Kolaborasi (Wajib)

Untuk proyek yang diniatkan rilis dan monetisasi, buat dokumen tertulis berisi:
- Judul lagu
- Nama semua kontributor
- Persentase split royalti (publishing dan master)
- Siapa yang handle administrasi

Tidak perlu legal resmi untuk indie project. Tanda tangan di WhatsApp atau email sudah cukup sebagai bukti kesepakatan.

## Percakapan yang Harus Dilakukan Sebelum Mulai

"Sebelum kita mulai, aku mau pastiin kita sama-sama clear soal beberapa hal: kalau lagu ini rilis, gimana kita bagi publishing royalti? Aku propose 50/50 karena kita sama-sama nulis."

Percakapan ini tidak perlu awkward. Lakukan dengan natural di awal.

Prinsip terpenting: **bicarakan sebelum ada output.** Semakin lama kamu tunda percakapan ini, semakin awkward jadinya.'];

// ── BATCH 4 & 5: RILIS & BRANDING ──
$articles[] = ['slug'=>'rilis-lagu-101','title'=>'Rilis Lagu Pertamamu: Panduan Lengkap dari A sampai Z','category'=>'rilis','batch'=>4,'reading_time'=>20,
'excerpt'=>'Dari rekaman selesai sampai lagu ada di Spotify — semua yang perlu kamu tahu untuk rilis pertama yang lancar.',
'content'=>'# Rilis Lagu Pertamamu: Panduan Lengkap dari A sampai Z

Rekaman sudah selesai. Sekarang apa? Banyak musisi terjebak di "parkir" — lagu sudah jadi tapi tidak pernah rilis karena takut atau bingung prosesnya.

## Checklist Sebelum Rilis

**Audio:**
- Mixing selesai dan kamu sudah puas
- Mastering selesai, LUFS di -14 integrated
- File WAV 44.1kHz/16bit atau 24bit siap
- Dengarkan final master di berbagai device

**Artwork:**
- Cover art 3000x3000 pixel minimum (1:1 rasio)
- Format: JPG atau PNG, kurang dari 10MB
- Tidak ada logo platform di cover art
- Nama artis dan judul lagu terlihat jelas

**Metadata:**
- Judul lagu, nama artis, genre, tahun rilis
- Lirik (opsional tapi direkomendasikan untuk Spotify)

## Pilih Distributor

| Distributor | Biaya | Royalti |
|---|---|---|
| DistroKid | ~USD 20/tahun (unlimited) | 100% |
| TuneCore | USD 10-30/lagu/tahun | 100% |
| Netrilis | Gratis/berbayar | 75-85% |
| CDBaby | USD 10-30/lagu (sekali) | 91% |

## Timeline Rilis

**4 minggu sebelum:** Upload ke distributor, pitch ke playlist Spotify, buat konten promo awal.
**2 minggu sebelum:** Umumkan tanggal rilis di semua platform.
**1 minggu sebelum:** Pre-save campaign, interview ke media kecil.
**Hari rilis:** Post di semua platform, balas comment dari fans.
**Minggu pertama:** Monitor data di Spotify for Artists, aktif di semua platform.

## Apa yang Harus Disiapkan Pasca-Rilis

- Spotify for Artists: Klaim profil artis, pasang bio dan photo
- Apple Music for Artists
- YouTube/YouTube Music: Upload lyric video
- Linktree/Linkfire: Satu link ke semua platform

## Ekspektasi yang Realistis

Lagu pertama jarang viral. Tapi itu bukan gagal — itu fondasi. Data pendengar pertama, feedback, pengalaman proses rilis — semua itu adalah aset.

Artis yang konsisten rilis jauh lebih sukses jangka panjang dari artis yang nunggu 2 tahun untuk "rilis yang sempurna."

Rilis pertama adalah latihan. Proses kedua akan lebih mudah. Mulai dari yang tidak sempurna — itu jauh lebih baik dari tidak mulai sama sekali.'];

$articles[] = ['slug'=>'playlist-pitching','title'=>'Playlist Pitching: Cara Masuk Playlist Editorial Spotify','category'=>'rilis','batch'=>4,'reading_time'=>12,
'excerpt'=>'Masuk playlist editorial Spotify bisa mengubah karir. Tapi ada cara yang benar untuk pitch dan cara yang langsung ditolak.',
'content'=>'# Playlist Pitching: Cara Masuk Playlist Editorial Spotify

Satu placement di playlist editorial Spotify bisa memberikan puluhan ribu stream tambahan untuk artis indie.

## Pitching ke Spotify Editorial

**Syarat Minimal:**
- Akun Spotify for Artists yang verified
- Lagu sudah di-upload dan disetujui distributor
- Pitch minimal 7 hari sebelum tanggal rilis (idealnya 2–4 minggu)

**Cara Pitch:**
1. Login ke Spotify for Artists (artists.spotify.com)
2. Klik Music → Upcoming
3. Pilih lagu yang akan dirilis
4. Isi pitch form

**Yang Diisi di Pitch Form:**

Deskripsi lagu (150 karakter) — yang paling penting. Jangan bilang "lagu indie yang keren" — gambarkan feel dan context spesifik.

Contoh yang baik: "Lagu tentang nggak bisa pergi tapi juga nggak bisa tinggal. Mixing gitar akustik dan distorsi tipis, nuansa malam hujan."

Genre dan subgenre (pilih spesifik), instrumen yang dipakai, mood, dan bahasa lirik.

## Playlist Independen (Non-Editorial)

**Cara menemukan playlist kurator:**
- SubmitHub (submithub.com): Platform berbayar untuk submit ke kurator. Ada kredit gratis terbatas.
- Groover (groover.co): Mirip SubmitHub, lebih besar di Eropa
- Banyak playlist memiliki email/link di deskripsi Spotify mereka

**Tips submit ke kurator independen:**
- Selalu personalkan pesan — bukan copy-paste ke semua
- Jelaskan kenapa lagu kamu cocok dengan playlist mereka spesifik
- Sertakan link langsung ke track (Spotify link)

## Playlist Indonesia yang Worth Dipitch

- Indie Pop Indonesia (editorial Spotify)
- Fresh Finds: Indonesia (editorial — untuk breakthrough artis)
- Pesta Indie (editorial Spotify)
- Playlist kurator music blog lokal

## Ekspektasi yang Realistis

Mayoritas pitch tidak berhasil pertama kali. Kurator editorial Spotify menerima ribuan pitch per minggu.

Yang meningkatkan peluang: lagu dengan engagement organik, artis yang sudah punya beberapa rilis, pitch yang ditulis spesifik, timing 4+ minggu sebelum rilis.

Jangan berhenti di satu pitch. Setiap rilis adalah kesempatan baru.'];

$articles[] = ['slug'=>'analitik-streaming','title'=>'Baca Data Streaming: Metrik yang Penting dan yang Tidak','category'=>'rilis','batch'=>4,'reading_time'=>10,
'excerpt'=>'Data Spotify for Artists bisa sangat overwhelming. Ini panduan apa yang benar-benar penting dan apa yang tidak perlu kamu khawatirkan.',
'content'=>'# Baca Data Streaming: Metrik yang Penting dan yang Tidak

Membuka dashboard Spotify for Artists untuk pertama kali bisa bikin kepala pusing. Ada banyak angka, grafik, dan istilah yang tidak familiar.

## Metrik yang Paling Bermakna

**1. Listeners (Pendengar Unik)**
Berapa orang berbeda yang dengar lagumu. Lebih bermakna dari total stream karena satu orang yang putar 100 kali tidak seakurat 100 orang yang putar sekali.

**2. Streams vs. Listeners Ratio**
Kalau rata-rata listener dengarin lagumu 3–5 kali — itu sangat baik. Kalau ratio rendah (1 stream per listener) — orang coba tapi tidak kembali.

**3. Playlist Reach**
Berapa stream yang datang dari playlist. Makin tinggi — artinya lagu ditemukan oleh orang yang tidak mengenalmu sebelumnya.

**4. Save Rate**
Berapa persen listeners yang simpan lagumu ke library. Benchmark: di atas 5% sudah baik, di atas 10% sangat baik. Spotify mempertimbangkan save rate saat evaluasi untuk playlist editorial.

**5. Completion Rate (Lewat 30 Detik)**
Stream dihitung setelah 30 detik. Kalau banyak orang skip sebelum 30 detik, stream tidak terhitung DAN itu signal negatif untuk algoritma.

## Metrik yang Tidak Perlu Terlalu Dipikirkan

**Total stream count:** Vanity metric tanpa konteks.
**Followers:** Penting tapi tidak secepat yang dikira.

## Apple Music for Artists

Tersedia di applemusicforartists.apple.com. Data yang unik: **Shazam discovery** — berapa orang Shazam lagumu. Ini indikator kuat organic discovery.

## Jadwal Review Data

Jangan buka analytics setiap hari:
- Seminggu pertama pasca rilis: cek tiap hari untuk lihat momentum
- Setelahnya: cek mingguan atau bulanan
- Gunakan data untuk keputusan besar (kapan rilis selanjutnya, genre apa yang perform), bukan untuk validasi harian'];

$articles[] = ['slug'=>'re-release-strategi','title'=>'Re-release dan Revamp: Napas Baru untuk Lagu Lama','category'=>'rilis','batch'=>4,'reading_time'=>8,
'excerpt'=>'Lagu yang tidak perform waktu pertama rilis bisa mendapat kesempatan kedua. Begini caranya melakukan re-release yang strategis.',
'content'=>'# Re-release dan Revamp: Napas Baru untuk Lagu Lama

Tidak semua lagu langsung menemukan audiensnya di rilis pertama. Timing, distribusi yang kurang optimal, atau situasi pasar yang tidak mendukung bisa jadi faktor.

Re-release adalah strategi yang dipakai artis di semua level — dari Taylor Swift (Taylor\'s Version) sampai artis indie lokal.

## Kapan Re-release Masuk Akal?

**Lagu yang tidak pernah dapat attention yang layak:**
- Rilis di waktu yang salah (sebelum kamu punya audience)
- Distribusi tidak optimal (tidak ada playlist pitch, tidak ada promosi)
- Artwork atau metadata yang buruk

**Lagu yang punya evergreen quality:**
- Tema yang timeless (bukan tentang tren sesaat)
- Kamu masih bangga dengan lagunya

**Ada sesuatu yang bisa ditambahkan:**
- Versi akustik dari lagu yang sebelumnya full production
- Remix dari artis lain
- Feature artis yang lebih dikenal sekarang

## Opsi Re-release

**1. Re-release Identik:** Upload kembali lagu yang sama dengan metadata yang diperbarui.

**2. Remaster Version:** Sama arrangement, tapi mastering yang lebih baik. Upload sebagai versi tersendiri dengan label "(Remastered 2025/2026)."

**3. Acoustic/Stripped Version:** Versi yang jauh lebih sederhana. Seringkali perform lebih baik di playlist chill/study.

**4. Remix:** Ajak produser untuk reimagine lagu kamu dalam genre yang berbeda.

## Proses Re-release

1. Evaluasi kualitas audio — masih layak atau perlu remaster?
2. Perbarui artwork kalau yang lama sudah tidak mewakili identitas artismu
3. Update metadata: genre yang lebih tepat, lirik yang sudah diinput
4. Pitch ke playlist lagi
5. Buat konten baru di medsos yang menjelaskan "kenapa" lagu ini kembali

## Framing untuk Audiens

"Lagu ini spesial buat aku dan aku mau kasih dia kesempatan yang lebih baik. Ini versinya yang baru."

Atau: "Ini lagu yang terasa semakin relevan seiring waktu."'];

$articles[] = ['slug'=>'artist-branding','title'=>'Artist Branding: Bangun Identitas Artis yang Konsisten','category'=>'rilis','batch'=>5,'reading_time'=>15,
'excerpt'=>'Branding bukan soal logo atau warna. Ini tentang menciptakan kesan yang konsisten tentang siapa kamu sebagai artis — dan kenapa orang harus peduli.',
'content'=>'# Artist Branding: Bangun Identitas Artis yang Konsisten

Kenapa kamu mengikuti artis tertentu di Instagram bahkan ketika mereka tidak rilis lagu baru berbulan-bulan? Karena mereka sudah berhasil membangun **identitas** yang bikin kamu care tentang perjalanan mereka, bukan cuma musiknya. Itu branding.

## Apa Itu Artist Branding?

Artist branding = keseluruhan kesan yang kamu ciptakan tentang dirimu sebagai artis. Ini mencakup:
- Cara kamu bicara (tone of voice)
- Visual yang kamu pilih
- Nilai yang kamu komunikasikan
- Bagaimana kamu membuat fans merasa

## Tentukan Identitas Artismu

**1. Apa yang unik dari perspektifmu?**
Apa yang hanya KAMU bisa tulis dengan otoritas? Margonoandi menulis dari perspektif seseorang yang hidup "dimulai dari kamar tidur."

**2. Siapa yang kamu ajak bicara?**
Bukan "semua orang." Siapa spesifiknya? Pelajar yang ngerasa stuck? Para twenty-something yang nggak yakin sama pilihan hidup mereka?

**3. Bagaimana kamu ingin fans merasa setelah dengarin musikmu?**
"Dipahami." "Semangat lagi." "Boleh nangis." Ini adalah core emotional promise-mu.

## Elemen Visual

**Warna palette:** Pilih 2–3 warna yang konsisten. Bukan asal pilih "yang bagus," tapi yang merepresentasikan nuansa musikmu.
- Musik gelap, introspektif → earth tones, dark blue, hitam
- Musik ceria, energetik → warna cerah, kontras tinggi
- Musik acoustic, intimate → cream, warm brown, dusty rose

**Font:** Satu untuk judul (display font), satu untuk body. Konsisten di semua visual.

**Foto artis:** Gaya foto yang konsisten lebih penting dari foto yang mahal.

## Tone of Voice

Bagaimana kamu nulis caption, membalas komentar, atau ngobrol di stories — itu semua adalah branding.

Contoh tone yang khas:
- Hindia: Reflektif, filosofis, sering nulis tentang psikologi sosial
- Weird Genius: Playful, hype, celebrate kemenangan
- Payung Teduh: Tenang, jarang posting tapi selalu meaningful

## Membangun Branding Tanpa Budget Besar

**Buat mood board:** Kumpulkan gambar, warna, foto, artwork yang resonan. Ini jadi referensi setiap buat konten.
**Konsistensi > kualitas awal:** Lebih baik posting konten "cukup baik" secara konsisten.
**Dokumentasikan proses:** Behind the scenes rekaman — ini konten yang authentic.

## Yang Bikin Branding Hancur

- Tidak konsisten: Satu minggu aesthetic gelap, minggu depan warna-warni
- Terlalu banyak "artis persona," kurang diri sendiri
- Hanya posting waktu rilis

Branding yang baik bukan didesain dari luar ke dalam — tapi dari dalam ke luar.'];

$articles[] = ['slug'=>'sosmed-musisi','title'=>'Social Media untuk Musisi: Strategi yang Realistis','category'=>'rilis','batch'=>5,'reading_time'=>12,
'excerpt'=>'Social media bisa jadi alat paling powerful atau paling menguras energi. Ini framework yang realistis untuk musisi yang waktu dan energinya terbatas.',
'content'=>'# Social Media untuk Musisi: Strategi yang Realistis

"Harus posting setiap hari, konten di semua platform, engage sama semua orang, ikutin semua tren..." Strategi sosmed yang tidak sustainable akan ditinggalkan dalam 3 minggu.

## Pilih Platform Utama (Jangan Semua)

**Instagram:** Visual-first, Reels bisa reach non-followers, Stories untuk daily engagement. Cocok untuk artis dengan aesthetic yang kuat.

**TikTok:** Discovery engine yang paling powerful saat ini. Algoritma lebih demokratis — konten dari 0 followers bisa viral kalau resonan.

**YouTube:** Long-term content: musik video, live session, behind the scenes. Search-based discovery, monetisasi lebih baik per view.

Pilih 1–2 platform utama. Tidak perlu ada di semua platform.

## Tipe Konten yang Perform untuk Musisi

**1. Process content (paling underrated):**
- Rekaman lagu (even just 30 detik voice note)
- Nulis lirik di notepad
- Latihan di kamar
Ini authentic, low-production, dan orang genuinely curious tentang bagaimana lagu dibuat.

**2. Music snippets:**
- Bagian chorus yang catchy
- Verse yang lyrically strong
Optimasi: upload audio yang kuat bahkan dengan visual sederhana.

**3. Behind the artis:**
- Siapa kamu di luar musik
- Referensi yang kamu dengarin
- Tempat yang inspirasimu

**4. CTA content:**
- "Lagu baru besok — pre-save linknya di bio"
Tapi jangan terlalu banyak — kalau setiap post adalah promosi, orang berhenti mau lihat.

## Jadwal yang Realistis

**Minimal viable:** 3–4 post per minggu di satu platform utama.
**Batch content:** Satu sesi produksi konten (2–3 jam) bisa menghasilkan konten untuk seminggu.

## Engagement

Reply ke semua komentar di awal. Orang yang merasa di-acknowledge akan lebih loyal.
Jangan beli followers atau engagement.

## Tren vs. Identitas

Ikut tren kalau align dengan identitas artismu. Jangan korbankan identitas demi tren. Followers dari konten random berbeda dari fans yang datang karena musikmu.

## Metrik yang Perlu Diperhatikan

- Reach: berapa orang baru lihat kontenmu
- Saves: orang simpan post = konten yang dianggap valuable
- Profile visits dari konten

Yang tidak perlu terlalu dipikirkan: like count. Ini vanity metric paling besar di sosmed.'];

$articles[] = ['slug'=>'monetisasi-musik','title'=>'Monetisasi Musik: 7 Cara Dapat Penghasilan dari Musik Kamu','category'=>'rilis','batch'=>5,'reading_time'=>15,
'excerpt'=>'Streaming adalah satu cara, tapi bukan yang paling menguntungkan untuk artis indie. Ini 7 sumber pendapatan musik yang realistis.',
'content'=>'# Monetisasi Musik: 7 Cara Dapat Penghasilan dari Musik Kamu

"Musisi tidak bisa dapat uang dari musik" — ini mitos. Yang benar: musisi tidak bisa dapat uang dari hanya satu sumber. Dengan multiple stream of income, musik bisa menjadi karir yang sustainable.

## 1. Royalti Streaming

Rate rata-rata (2024):
- Spotify: $0.003–0.005 per stream
- Apple Music: $0.008–0.012 per stream
- YouTube Music: $0.002 per stream

Untuk dapat Rp 1 juta/bulan dari Spotify, butuh sekitar 400.000–600.000 stream per bulan. Ini realistis setelah punya katalog yang solid (10+ lagu).

Daftar ke KCI (Karya Cipta Indonesia) atau WAMI untuk collect performing rights royalti.

## 2. Sync Licensing

Lisensi lagu untuk film, iklan, serial TV, podcast, game.

Satu placement di iklan nasional bisa senilai Rp 5–50 juta.

**Cara masuk:**
- Daftarkan ke platform sync: Musicbed, Artlist, Epidemic Sound
- Buat instrumental version dari semua lagumu — lebih mudah di-license

## 3. Merchandise

T-shirt, tote bag, poster, sticker. Merchandise yang terhubung ke lagu atau lirik spesifik perform lebih baik dari logo artis saja.

Platform print on demand: Printful/Teespring — tidak perlu stok.

## 4. Live Performance

- Open mic: exposure, jarang berbayar
- Venue kecil: Rp 200rb–1 jt per gig
- Event corporate/wedding: Rp 2–15 jt per show
- Festival: Rp 5–50 jt

## 5. Lesson / Workshop / Mentoring

- Kelas privat: Rp 100–300rb/jam
- Workshop online via Zoom: bisa mengajar puluhan orang sekaligus
- Kursus online di platform berbayar

## 6. Crowdfunding / Patron

Platform Saweria (Indonesia) atau Patreon memungkinkan fans membayar langganan bulanan untuk konten eksklusif.

Mulai dari 10–50 patrons saja sudah bisa memberikan income tambahan yang bermakna.

## 7. Brand Deals / Endorsement

Kalau kamu sudah punya audience (meski kecil tapi engaged), brand akan mulai tertarik.

Mulai dari brand yang align: brand instrumen lokal, brand audio, brand lifestyle yang cocok dengan identity kamu.

## Kombinasi yang Realistis

Tidak ada satu sumber yang cukup di awal:
- Tahun 1: Lesson + gig lokal + streaming (kecil)
- Tahun 2: Gig lebih besar + merchandise + streaming yang tumbuh
- Tahun 3+: Tambahkan sync, patron, workshop

Musik sebagai karir adalah maraton, bukan sprint. Tapi dengan track yang jelas, itu sangat realistis.'];

foreach ($articles as $a) {
    insertArticle($conn, $a);
}
} // end if tableExists articles

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
    'gig_posts'            => 'Papan Gig / Manggung',
    'push_subscriptions'   => 'Web Push (notif Android)',
    'articles'             => 'Materi Musik (21 artikel)',
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
