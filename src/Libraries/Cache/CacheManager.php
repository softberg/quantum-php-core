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

namespace Quantum\Libraries\Cache;

use Quantum\Libraries\Config\ConfigException;
use Quantum\Exceptions\AppException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class CacheManager
 * @package Quantum\Libraries\Cache
 */
class CacheManager
{

    /**
     * Available cache drivers
     */
    const DRIVERS = [
        'file',
        'database',
        'memcached',
        'redis'
    ];

    /**
     * Get Handler
     * @return Cache
     * @throws AppException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function getHandler(): Cache
    {
        if (!config()->has('cache')) {
            config()->import(new Setup('Config', 'cache'));
        }

        $cacheDriver = config()->get('cache.current');

        if (!in_array($cacheDriver, self::DRIVERS)) {
            throw CacheException::unsupportedDriver($cacheDriver);
        }

        $cacheAdapterClass = __NAMESPACE__ . '\\Adapters\\' . ucfirst($cacheDriver) . 'Adapter';

        $cacheAdapter = new $cacheAdapterClass(config()->get('cache.' . $cacheDriver . '.params'));

        return new Cache($cacheAdapter);
    }

}
