<?php
$root = dirname(__DIR__);

// 1. Hapus cache files
$cacheFiles = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/routes.php',
    $root . '/bootstrap/cache/packages.php',
    $root . '/bootstrap/cache/services.php',
    $root . '/bootstrap/cache/events.php',
];

header('Content-Type: text/plain; charset=utf-8');
echo "=== Cache Files ===\n";
foreach ($cacheFiles as $f) {
    if (file_exists($f)) { unlink($f); echo "Deleted: " . basename($f) . "\n"; }
    else echo "Not found: " . basename($f) . "\n";
}

// 2. Tampilkan ENV yang relevan
echo "\n=== .env Check ===\n";
$env = file_get_contents($root . '/.env');
foreach (explode("\n", $env) as $line) {
    if (preg_match('/^(APP_URL|APP_KEY|GOOGLE_CLIENT_ID|GOOGLE_CLIENT_SECRET|GOOGLE_REDIRECT_URI|SESSION_DRIVER)=/', trim($line))) {
        // Sembunyikan sebagian client_secret
        if (str_contains($line, 'CLIENT_SECRET')) {
            $line = preg_replace('/=(.{6}).*/', '=$1***', $line);
        }
        echo trim($line) . "\n";
    }
}

// 3. Tampilkan 30 baris terakhir error log
echo "\n=== Last 30 lines of laravel.log ===\n";
$logFile = $root . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last  = array_slice($lines, -80);
    echo implode('', $last);
} else {
    echo "(log file not found)\n";
}
