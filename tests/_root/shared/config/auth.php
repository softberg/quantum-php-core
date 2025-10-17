<?php

return [
    'default' => 'session',

    'session' => [
        'service' => Quantum\Tests\_root\modules\Test\Services\AuthService::class
    ],

    'jwt' => [
        'service' => Quantum\Tests\_root\modules\Test\Services\AuthService::class,
        'claims' => [
            'jti' => uniqid(),
            'iss' => 'issuer',
            'aud' => 'audience',
            'iat' => time(),
            'nbf' => time() + 1,
            'exp' => time() + 3600 // 1 hour
        ]
    ],

    'two_fa' => env('TWO_FA', true),
    'otp_expires' => 2,
];
