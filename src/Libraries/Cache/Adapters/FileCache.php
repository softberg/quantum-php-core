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

use Quantum\Libraries\Storage\FileSystem;
use Psr\SimpleCache\CacheInterface;
use InvalidArgumentException;
use Quantum\Di\Di;

/**
 * Class FileCache
 * @package Quantum\Libraries\Cache\Adapters
 */
class FileCache implements CacheInterface
{

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var int
     */
    private $ttl = 30;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * FileCache constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->fs = Di::get(FileSystem::class);

        $this->ttl = $params['ttl'];

        $this->cacheDir = $params['path'];
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $path = $this->getPath($key);

            $content = $this->fs->get($path);

            if (!$content) {
                return $default;
            }

            try {
                return unserialize($content);
            } catch (\Exception $e) {
                $this->fs->remove($path);
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
        $path = $this->getPath($key);

        if (!$this->fs->exists($path)) {
            return false;
        }

        if (time() - $this->fs->lastModified($path) > $this->ttl) {
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
        return $this->fs->put($this->getPath($key), serialize($value));
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
        $path = $this->getPath($key);

        if ($this->fs->exists($path)) {
            return $this->fs->remove($path);
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
        if (!$this->fs->isDirectory($this->cacheDir)) {
            return false;
        }

        $files = $this->fs->glob($this->cacheDir . DS . '*');

        if (!$files) {
            return false;
        }

        foreach ($files as $file) {
            $this->fs->remove($file);
        }

        return true;
    }

    /**
     * Gets the path for Get for the given cache key
     * @param string $key
     * @return string
     */
    private function getPath(string $key): string
    {
        return $this->cacheDir . DS . sha1($key);
    }

}
