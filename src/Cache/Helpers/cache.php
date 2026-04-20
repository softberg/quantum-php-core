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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Cache\Factories\CacheFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Cache\Cache;

/**
 * @throws ConfigException|BaseException|DiException|ReflectionException
 */
function cache(?string $adapter = null): Cache
{
    return CacheFactory::get($adapter);
}
