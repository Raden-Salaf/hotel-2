<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi untuk integrasi payment gateway Midtrans
    | Nilai diambil dari file .env agar aman (tidak hardcode)
    */

    // Server key untuk komunikasi backend ke Midtrans
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    // Client key untuk Snap.js di frontend
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // true = production, false = sandbox (testing)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // URL Snap.js — berbeda antara sandbox dan production
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
