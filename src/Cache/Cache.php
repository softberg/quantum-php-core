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

namespace Quantum\Cache;

use Quantum\Cache\Exceptions\CacheException;
use Quantum\App\Exceptions\BaseException;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Cache
 * @package Quantum\Cache
 * @method mixed get($key, $default = null)
 * @method iterable<string, mixed> getMultiple($keys, $default = null)
 * @method has($key): bool
 * @method bool set($key, $value, $ttl = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method bool delete($key)
 * @method bool deleteMultiple($keys)
 * @method bool clear()
 */
class Cache
{
    /**
     * File adapter
     */
    public const FILE = 'file';

    /**
     * Database adapter
     */
    public const DATABASE = 'database';

    /**
     * Memcached adapter
     */
    public const MEMCACHED = 'memcached';

    /**
     * Redis adapter
     */
    public const REDIS = 'redis';

    private CacheInterface $adapter;

    /**
     * Cache constructor
     */
    public function __construct(CacheInterface $cacheAdapter)
    {
        $this->adapter = $cacheAdapter;
    }

    /**
     * Gets the current adapter
     */
    public function getAdapter(): CacheInterface
    {
        return $this->adapter;
    }

    /**
     * @param array<mixed>|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw CacheException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
