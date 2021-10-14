<?php

return [
    'current' => 'mysql',
    'mysql' => array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'charset',
        'orm' => \Quantum\Libraries\Database\Idiorm\IdiormDbal::class
    ),
    'sqlite' => array(
        'driver' => 'sqlite',
        'database' => ':memory:',
        'orm' => \Quantum\Libraries\Database\Idiorm\IdiormDbal::class
    ),
];