<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

use Quantum\Config\Config;
use Quantum\Di\Di;
use Quantum\Di\Exceptions\DiException;

/**
 * Config facade
 * @throws DiException|ReflectionException
 */
function config(): Config
{
    if (!Di::isRegistered(Config::class)) {
        Di::register(Config::class);
    }

    return Di::get(Config::class);
}
