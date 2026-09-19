<?php

// Hanya fe-portofolio yang boleh memanggil API dari browser.
// Beberapa origin dipisah koma di .env, contoh:
// FRONTEND_URL=http://localhost:5173,https://davidreza.dev
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FRONTEND_URL', 'http://localhost:5173'))
    ))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => true,
];
