<?php

declare(strict_types=1);

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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Cache\Exceptions\CacheException;
use Quantum\Cache\Adapters\MemcachedAdapter;
use Quantum\Cache\Adapters\DatabaseAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Cache\Adapters\RedisAdapter;
use Quantum\Cache\Adapters\FileAdapter;
use Quantum\Di\Exceptions\DiException;
use Psr\SimpleCache\CacheInterface;
use Quantum\Cache\Enums\CacheType;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Cache\Cache;

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
        CacheType::FILE => FileAdapter::class,
        CacheType::DATABASE => DatabaseAdapter::class,
        CacheType::MEMCACHED => MemcachedAdapter::class,
        CacheType::REDIS => RedisAdapter::class,
    ];

    /**
     * @var array<string, Cache>
     */
    private static array $instances = [];

    /**
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
     * @throws CacheException
     */
    private static function createInstance(string $adapterClass, string $adapter): Cache
    {
        $cacheAdapter = new $adapterClass(config()->get('cache.' . $adapter));

        if (!$cacheAdapter instanceof CacheInterface) {
            throw CacheException::adapterNotSupported($adapter);
        }

        return new Cache($cacheAdapter);
    }

    /**
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
