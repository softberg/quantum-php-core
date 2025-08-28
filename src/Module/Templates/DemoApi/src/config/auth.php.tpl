<?php

return [
    /**
     * ---------------------------------------------------------
     * Authentication configurations
     * ---------------------------------------------------------
     */
    'default' => 'jwt',

    'session' => [
        'service' => Modules\{{MODULE_NAME}}\Services\AuthService::class,
    ],

    'jwt' => [
        'service' => Modules\{{MODULE_NAME}}\Services\AuthService::class,
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