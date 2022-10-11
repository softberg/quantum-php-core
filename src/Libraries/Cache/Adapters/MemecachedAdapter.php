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

use Quantum\Exceptions\CacheException;
use Psr\SimpleCache\CacheInterface;
use InvalidArgumentException;
use Memcached;
use Exception;

/**
 * Class MemecachedAdapter
 * @package Quantum\Libraries\Cache\Adapters
 */
class MemecachedAdapter implements CacheInterface
{

    /**
     * @var int
     */
    private $ttl = 30;

    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * MemecachedAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->ttl = $params['ttl'];

        $this->memcached = new Memcached();
        $this->memcached->addServer($params['host'], $params['port']);

        if (!$this->memcached->getStats()) {
            throw CacheException::cantConnect('Memcached server');
        }
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $cacheItem = $this->memcached->get(sha1($key));

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
        $cacheItem = $this->memcached->get(sha1($key));

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
        return $this->memcached->set(sha1($key), serialize($value), $this->ttl);
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
        return $this->memcached->delete(sha1($key));
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
        return $this->memcached->flush();
    }

}
