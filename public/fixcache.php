<?php
// Hapus file cache Laravel langsung — tanpa bootstrap app
// Akses: https://www.eminor.margonoandi.my.id/fixcache.php

$root  = dirname(__DIR__);
$files = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/routes.php',
    $root . '/bootstrap/cache/packages.php',
    $root . '/bootstrap/cache/services.php',
    $root . '/bootstrap/cache/events.php',
];

header('Content-Type: text/plain; charset=utf-8');
echo "=== Laravel Cache Clear ===\n\n";

foreach ($files as $f) {
    if (file_exists($f)) {
        unlink($f);
        echo "Deleted: " . basename($f) . "\n";
    } else {
        echo "Not found (ok): " . basename($f) . "\n";
    }
}

echo "\nDone. Coba login lagi.\n";
