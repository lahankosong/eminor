<?php
$root = dirname(__DIR__);

header('Content-Type: text/plain; charset=utf-8');

// 1. Hapus bootstrap cache
echo "=== Bootstrap Cache ===\n";
$cacheFiles = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/routes.php',
    $root . '/bootstrap/cache/packages.php',
    $root . '/bootstrap/cache/services.php',
    $root . '/bootstrap/cache/events.php',
];
foreach ($cacheFiles as $f) {
    if (file_exists($f)) { unlink($f); echo "Deleted: " . basename($f) . "\n"; }
    else echo "Not found: " . basename($f) . "\n";
}

// 2. Hapus compiled Blade views
echo "\n=== Compiled Views ===\n";
$viewsDir = $root . '/storage/framework/views';
$deleted = 0;
if (is_dir($viewsDir)) {
    foreach (glob($viewsDir . '/*.php') as $f) {
        unlink($f);
        $deleted++;
    }
}
echo "Deleted $deleted compiled view files\n";

// 3. Tampilkan ENV yang relevan
echo "\n=== .env Check ===\n";
$env = file_get_contents($root . '/.env');
foreach (explode("\n", $env) as $line) {
    if (preg_match('/^(APP_URL|APP_KEY|GOOGLE_REDIRECT_URI|SESSION_DRIVER)=/', trim($line))) {
        echo trim($line) . "\n";
    }
}

// 4. Verifikasi file music-player.blade.php (20 baris pertama)
echo "\n=== music-player.blade.php (20 baris pertama) ===\n";
$mpFile = $root . '/resources/views/partials/music-player.blade.php';
if (file_exists($mpFile)) {
    $lines = file($mpFile);
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($mpFile)) . "\n";
    echo implode('', array_slice($lines, 0, 20));
} else {
    echo "FILE NOT FOUND!\n";
}

// 5. Check apakah tombol folder ada di file
echo "\n=== Cek tombol 📂 ===\n";
if (file_exists($mpFile)) {
    $content = file_get_contents($mpFile);
    echo strpos($content, 'fpFileInput') !== false ? "✅ fpFileInput ADA\n" : "❌ fpFileInput TIDAK ADA\n";
    echo strpos($content, 'fpLoadLocalFiles') !== false ? "✅ fpLoadLocalFiles ADA\n" : "❌ fpLoadLocalFiles TIDAK ADA\n";
    echo strpos($content, 'fpe-open-file-btn') !== false ? "✅ fpe-open-file-btn ADA\n" : "❌ fpe-open-file-btn TIDAK ADA\n";
}

echo "\nSelesai. Refresh halaman aplikasi sekarang.\n";
