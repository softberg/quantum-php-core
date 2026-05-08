<?php

return [
    'default' => 'file',
    'file' => [
        'prefix' => str_replace(' ', '', env('APP_NAME') ?? ''),
        'path' => base_dir() . DS . 'cache' . DS . 'data',
        'ttl' => 600,
    ],
    'redis' => [
        'prefix' => str_replace(' ', '', env('APP_NAME') ?? ''),
        'host' => '127.0.0.1',
        'port' => 6379,
        'ttl' => 60,
    ],
];
