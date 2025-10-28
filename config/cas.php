<?php

return [
    /**
     * URL dasar dari server CAS Anda
     * contoh: 'https://sso.universitas.ac.id/cas'
     */
    'cas_server_url' => env('CAS_SERVER_URL', ''),

    /**
     * Path untuk login dan validasi
     */
    'paths' => [
        'login'    => 'login',          // Akan menjadi 'https://.../cas/login'
        'logout'   => 'logout',
        'validate' => 'serviceValidate', // Akan menjadi 'https://.../cas/serviceValidate'
    ],
];
