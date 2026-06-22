<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

    // Verifikasi Google Search Console (metode HTML tag). Token bukan rahasia (tampil di HTML).
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION', '1Am36PqGZi0MBcVRaPtvoRjvwa_5qAX0AgRz_0-a8k4'),

    // Google Analytics 4 Measurement ID (G-XXXXXXXXXX). Kosongkan untuk nonaktifkan tracking.
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),

    'vapid' => [
        'public'          => env('VAPID_PUBLIC_KEY'),
        'private_pem_b64' => env('VAPID_PRIVATE_PEM_B64'),
        'subject'         => env('VAPID_SUBJECT', 'mailto:admin@margonoandi.my.id'),
    ],

];
