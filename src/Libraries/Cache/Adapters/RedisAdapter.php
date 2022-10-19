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

namespace Quantum\Libraries\Cache\Adapters;

use Psr\SimpleCache\CacheInterface;
use InvalidArgumentException;
use Exception;
use Redis;

/**
 * Class RedisAdapter
 * @package Quantum\Libraries\Cache\Adapters
 */
class RedisAdapter implements CacheInterface
{

    /**
     * @var int
     */
    private $ttl = 30;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * RedisAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->ttl = $params['ttl'];
        $this->prefix = $params['prefix'];

        $this->redis = new Redis();
        $this->redis->connect($params['host'], $params['port']);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $cacheItem = $this->redis->get($this->keyHash($key));

            try {
                return unserialize($cacheItem);
            } catch (Exception $e) {
                $this->delete($key);
                return $default;
            }
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException(t(_message('exception.non_iterable_value', '$values')), E_WARNING);
        }

        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $cacheItem = $this->redis->get($this->keyHash($key));

        if (!$cacheItem) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->redis->set($this->keyHash($key), serialize($value), $this->ttl);
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values)) {
            throw new InvalidArgumentException(t(_message('exception.non_iterable_value', '$values')), E_WARNING);
        }

        $results = [];

        foreach ($values as $key => $value) {
            $results[] = $this->set($key, $value, $ttl);
        }

        return !in_array(false, $results, true);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return (bool) $this->redis->del($this->keyHash($key));
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException(t(_message('exception.non_iterable_value', '$values')), E_WARNING);
        }

        $results = [];

        foreach ($keys as $key) {
            $results[] = $this->delete($key);
        }

        return !in_array(false, $results, true);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->redis->flushdb();
    }

    /**
     * Gets the hashed key
     * @param string $key
     * @return string
     */
    private function keyHash(string $key): string
    {
        return sha1($this->prefix . $key);
    }

}
