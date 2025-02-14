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
 * @since 2.9.5
 */

use Quantum\Environment\Exceptions\EnvException;
use Quantum\Environment\Environment;

/**
 * Gets environment variable
 * @param string $var
 * @param $default
 * @return mixed
 * @throws EnvException
 */
function env(string $var, $default = null)
{
    return Environment::getInstance()->getValue($var, $default);
}