<?php
return [
    'default' => 'file',

    'file' => [
        'prefix' => str_replace(' ', '', env('APP_NAME')),
        'path' => base_dir() . DS . 'cache' . DS . 'data',
        'ttl' => 600
    ]
];