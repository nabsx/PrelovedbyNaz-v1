<?php
return [
    'serverKey' => env('MIDTRANS_SERVER_KEY', ''),
    'clientKey' => env('MIDTRANS_CLIENT_KEY', ''),
    'isProduction' => env('MIDTRANS_ENV', false),
    'isSanitized' => true,
    'is3ds' => true,
];
