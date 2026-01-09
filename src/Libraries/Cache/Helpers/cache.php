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

use Quantum\Libraries\Cache\Factories\CacheFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Cache\Cache;

/**
 * @param string|null $adapter
 * @return Cache
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function cache(?string $adapter = null): Cache
{
    return CacheFactory::get($adapter);
}
