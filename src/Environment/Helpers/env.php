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

use Quantum\Environment\Exceptions\EnvException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\Di\Di;

/**
 * Gets the value of an environment variable
 * @param string $var
 * @param mixed|null $default
 * @return mixed
 * @throws EnvException|DiException|\ReflectionException
 */
function env(string $var, $default = null)
{
    return Di::get(Environment::class)->getValue($var, $default);
}
