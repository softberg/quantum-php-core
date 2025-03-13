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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Cache\Factories;

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Cache\Exceptions\CacheException;
use Quantum\Libraries\Cache\Adapters\MemcachedAdapter;
use Quantum\Libraries\Cache\Adapters\DatabaseAdapter;
use Quantum\Libraries\Cache\Adapters\RedisAdapter;
use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
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
    private static $instance = null;

    /**
     * @return Cache
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(): Cache
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
     * @return Cache
     * @throws BaseException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException
     */
    private static function createInstance(): Cache
    {
        if (!config()->has('cache')) {
            config()->import(new Setup('Config', 'cache'));
        }

        $adapter = config()->get('cache.default');

        $adapterClass = self::getAdapterClass($adapter);

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