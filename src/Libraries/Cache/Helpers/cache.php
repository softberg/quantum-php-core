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

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Cache\Factories\CacheFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Cache\Cache;

/**
 * @return Cache
 * @throws ReflectionException
 * @throws BaseException
 * @throws DiException
 * @throws ConfigException
 */
function cache(): Cache
{
    return CacheFactory::get();
}