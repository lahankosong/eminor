<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Daftar email admin
    |--------------------------------------------------------------------------
    | Diisi dari .env ADMIN_EMAILS (comma-separated). Dibaca di sini (file config)
    | agar tetap berfungsi setelah `php artisan config:cache` — env() di luar file
    | config akan mengembalikan null setelah config di-cache.
    */

    'emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ADMIN_EMAILS', ''))
    ))),

];
