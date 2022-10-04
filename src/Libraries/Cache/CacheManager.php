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

use Psr\SimpleCache\CacheInterface;
use Quantum\Loader\Setup;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Cache
 */
class CacheManager
{

    const DRIVERS = [
        'file',
        'memcache',
        'redis'
    ];

    /**
     *  Get Handler
     * @return CacheInterface
     * @throws Exception
     */
    public static function getHandler()
    {
        if (!config()->has('cache')) {
            config()->import(new Setup('Config', 'cache'));
        }

        $cacheDriver = config()->get('cache.current');

        if (!in_array($cacheDriver, self::DRIVERS)) {
            throw new \Exception();
        }

        $cacheAdapterClass = __NAMESPACE__ . '\\Adapters\\' . ucfirst($cacheDriver) . 'Cache';

        $cacheAdapter = new $cacheAdapterClass(config()->get('cache.' . $cacheDriver . '.params'));
        
        return new Cache($cacheAdapter);
    }

}
