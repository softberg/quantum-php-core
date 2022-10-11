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

use Quantum\Libraries\Database\Database;
use Psr\SimpleCache\CacheInterface;
use InvalidArgumentException;
use Exception;

/**
 * Class DatabaseAdapter
 * @package Quantum\Libraries\Cache\Adapters
 */
class DatabaseAdapter implements CacheInterface
{

    /**
     * @var int
     */
    private $ttl = 30;

    /**
     * @var \Quantum\Libraries\Database\DbalInterface
     */
    private $cacheModel;

    /**
     * DatabaseAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->ttl = $params['ttl'];
        $this->cacheModel = Database::getInstance()->getOrm($params['table']);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $cacehItem = $this->cacheModel->findOneBy('key', sha1($key));

            try {
                return unserialize($cacehItem->prop('value'));
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
            throw new InvalidArgumentException(t(_message('exception.non_iterable_value', '$keys')), E_WARNING);
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
        $cacheItem = $this->cacheModel->findOneBy('key', sha1($key));

        if (empty($cacheItem->asArray())) {
            return false;
        }

        if (time() - $cacheItem->prop('ttl') > $this->ttl) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheItem = $this->cacheModel->findOneBy('key', sha1($key));

        if (empty($cacheItem->asArray())) {
            $cacheItem = $this->cacheModel->create();
            $cacheItem->prop('key', sha1($key));
            $cacheItem->prop('value', serialize($value));
            $cacheItem->prop('ttl', time());
        } else {
            $cacheItem->prop('key', sha1($key));
            $cacheItem->prop('value', serialize($value));
            $cacheItem->prop('ttl', time());
        }

        return $cacheItem->save();
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
        $cacheItem = $this->cacheModel->findOneBy('key', sha1($key));

        if (!empty($cacheItem->asArray())) {
            return $this->cacheModel->delete();
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException(t(_message('exception.non_iterable_value', '$keys')), E_WARNING);
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
        return $this->cacheModel->deleteMany();
    }

}
