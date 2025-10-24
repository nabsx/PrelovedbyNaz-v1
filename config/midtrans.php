<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Midtrans Payment Gateway
    |
    */

    // Merchant Server Key (dari Midtrans Dashboard)
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    // Merchant Client Key (dari Midtrans Dashboard)
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // Merchant ID (dari Midtrans Dashboard)
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    // Environment: 'sandbox' atau 'production'
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Sanitization (default: true)
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    // 3DS (3D Secure) untuk credit card
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];
