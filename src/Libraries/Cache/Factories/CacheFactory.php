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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Cache\Factories;

use Quantum\Libraries\Cache\Exceptions\CacheException;
use Quantum\Libraries\Cache\Adapters\MemcachedAdapter;
use Quantum\Libraries\Cache\Adapters\DatabaseAdapter;
use Quantum\Libraries\Cache\Adapters\RedisAdapter;
use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Cache\Cache;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class CacheFactory
 * @package Quantum\Libraries\Cache
 */
class CacheFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Cache::FILE => FileAdapter::class,
        Cache::DATABASE => DatabaseAdapter::class,
        Cache::MEMCACHED => MemcachedAdapter::class,
        Cache::REDIS => RedisAdapter::class,
    ];

    /**
     * @var Cache|null
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return Cache
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Cache
    {
        if (!config()->has('cache')) {
            config()->import(new Setup('config', 'cache'));
        }

        $adapter = $adapter ?? config()->get('cache.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return Cache
     */
    private static function createInstance(string $adapterClass, string $adapter): Cache
    {
        return new Cache(new $adapterClass(config()->get('cache.' . $adapter)));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw CacheException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}