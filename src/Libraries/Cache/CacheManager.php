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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Cache;

use Quantum\Exceptions\CacheException;
use Psr\SimpleCache\CacheInterface;
use Quantum\Loader\Setup;

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
        'memcache',
        'redis'
    ];

    /**
     * Get Handler
     * @return CacheInterface
     * @throws \Quantum\Exceptions\CacheException
     */
    public static function getHandler()
    {
        if (!config()->has('cache')) {
            config()->import(new Setup('Config', 'cache'));
        }

        $cacheDriver = config()->get('cache.current');

        if (!in_array($cacheDriver, self::DRIVERS)) {
            throw CacheException::unsupportedDriver($cacheDriver);
        }

        $cacheAdapterClass = __NAMESPACE__ . '\\Adapters\\' . ucfirst($cacheDriver) . 'Cache';

        $cacheAdapter = new $cacheAdapterClass(config()->get('cache.' . $cacheDriver . '.params'));

        return new Cache($cacheAdapter);
    }

}
