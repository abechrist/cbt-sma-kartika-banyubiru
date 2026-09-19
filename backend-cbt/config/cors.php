<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Arsitektur split domain:
    |   - Backend (API + Filament admin)  : https://ipcbtkartika.koomit.com
    |   - Frontend (SPA siswa)            : https://cbtkatika.koomit.com
    |
    | Frontend perlu akses API backend, jadi origin frontend harus
    | diizinkan dan supports_credentials harus true (karena memakai
    | cookie session Sanctum).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://cbtkatika.koomit.com')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
