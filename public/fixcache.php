<?php
// Clear Laravel config/route/view cache — jalankan sekali setelah edit .env
// Akses: https://www.eminor.margonoandi.my.id/fixcache.php

define('LARAVEL_START', microtime(true));
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$results = [];
foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $cmd) {
    $kernel->call($cmd);
    $results[$cmd] = '✅ done';
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== Laravel Cache Cleared ===\n\n";
foreach ($results as $cmd => $status) {
    echo "$cmd → $status\n";
}
echo "\nSelesai. Hapus file ini setelah dipakai.\n";
