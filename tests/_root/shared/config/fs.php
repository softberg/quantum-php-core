<?php

return [
    'default' => 'local',

    'dropbox' => [
        'service' => Quantum\Tests\_root\shared\Services\TokenService::class,
        'params' => [
            'app_key' => '',
            'app_secret' => '',
        ],
    ],

    'gdrive' => [
        'service' => Quantum\Tests\_root\shared\Services\TokenService::class,
        'params' => [
            'app_key' => '',
            'app_secret' => '',
        ],
    ],
];
