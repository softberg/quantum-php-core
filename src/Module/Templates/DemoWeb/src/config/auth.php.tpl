<?php

return [
    /**
     * ---------------------------------------------------------
     * Authentication configurations
     * ---------------------------------------------------------
     */
    'default' => 'session',

    'session' => [
        'service' => {{MODULE_NAMESPACE}}\Services\AuthService::class,
    ],

    'jwt' => [
        'service' => {{MODULE_NAMESPACE}}\Services\AuthService::class,
        'claims' => [
            'jti' => uniqid(),
            'iss' => 'issuer',
            'aud' => 'audience',
            'iat' => time(),
            'nbf' => time() + 1,
            'exp' => time() + 3600,
        ]
    ]
];