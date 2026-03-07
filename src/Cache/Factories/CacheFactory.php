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

namespace Quantum\Cache\Factories;

use Quantum\Cache\Exceptions\CacheException;
use Quantum\Cache\Adapters\MemcachedAdapter;
use Quantum\Cache\Adapters\DatabaseAdapter;
use Quantum\Cache\Adapters\RedisAdapter;
use Quantum\Cache\Adapters\FileAdapter;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Cache\Cache;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class CacheFactory
 * @package Quantum\Cache
 */
class CacheFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        Cache::FILE => FileAdapter::class,
        Cache::DATABASE => DatabaseAdapter::class,
        Cache::MEMCACHED => MemcachedAdapter::class,
        Cache::REDIS => RedisAdapter::class,
    ];

    /**
     * @var array<string, Cache>
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

        $adapter ??= config()->get('cache.default');

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
