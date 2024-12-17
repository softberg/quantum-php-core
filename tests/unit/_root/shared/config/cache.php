<?php
return [
    'current' => 'file',

    'file' => [
        'params' => [
            'prefix' => str_replace(' ', '', env('APP_NAME')),
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 600
        ]
    ]
];