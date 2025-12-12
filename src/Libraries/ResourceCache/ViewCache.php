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
 * @since 2.9.9
 */

namespace Quantum\Libraries\ResourceCache;

use Quantum\Libraries\ResourceCache\Exceptions\ResourceCacheException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Loader\Setup;
use ReflectionException;
use voku\helper\HtmlMin;
use Exception;

/**
 * ViewCache class
 * @package Quantum\Libraries\ResourceCache
 */
class ViewCache
{

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var int
     */
    private $ttl = 300;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $minification = false;

    /**
     * @var object
     */
    private $fs;

    /**
     * @var ViewCache
     */
    private static $instance = null;

    public static function getInstance(): ViewCache
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws DiException
     * @throws Exception
     */
    public function __construct()
    {
        $this->isEnabled = filter_var(config()->get('resource_cache'), FILTER_VALIDATE_BOOLEAN);

        $this->fs = FileSystemFactory::get();
    }

    /**
     * @throws ReflectionException
     * @throws ConfigException
     * @throws DiException
     */
    public function setup()
    {
        if (!config()->has('view_cache')) {
            config()->import(new Setup('config', 'view_cache'));
        }

        $this->cacheDir = $this->getCacheDir();

        $this->ttl = config()->get('view_cache.ttl', $this->ttl);

        $this->minification = filter_var(config()->get('view_cache.minify', $this->minification), FILTER_VALIDATE_BOOLEAN);

        if (!$this->fs->isDirectory($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * @param string $uri
     * @param Response $response
     * @return bool
     * @throws BaseException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
     */
    public function serveCachedView(string $uri, Response $response): bool
    {
        if ($this->isEnabled() && $this->exists($uri)) {
            $response->html($this->get($uri));
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * @param string $content
     * @return $this
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function set(string $key, string $content): ViewCache
    {
        if ($this->minification) {
            $content = $this->minify($content);
        }

        $this->fs->put($this->getCacheFile($key), $content);

        return $this;
    }

    /**
     * @param string $key
     * @return string|null
     * @throws BaseException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
     */
    public function get(string $key): ?string
    {
        if (!$this->exists($key)) {
            return null;
        }

        return $this->fs->get($this->getCacheFile($key));
    }

    /**
     * @param string $key
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function delete(string $key): void
    {
        $cacheFile = $this->getCacheFile($key);

        if ($this->fs->exists($cacheFile)) {
            $this->fs->remove($cacheFile);
        }
    }

    /**
     * @param string $key
     * @return bool
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function exists(string $key): bool
    {
        $cacheFile = $this->getCacheFile($key);

        if (!$this->fs->exists($cacheFile) || $this->isExpired($cacheFile)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $state
     * @return void
     */
    public function enableCaching(bool $state): void
    {
        $this->isEnabled = $state;
    }

    /**
     * @param int $ttl
     * @return void
     */
    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * @param bool $state
     * @return void
     */
    public function enableMinification(bool $state): void
    {
        $this->minification = $state;
    }

    /**
     * @param $cacheFile
     * @return bool
     */
    private function isExpired($cacheFile): bool
    {
        if (time() > ($this->fs->lastModified($cacheFile) + $this->ttl)) {
            $this->fs->remove($cacheFile);
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function getCacheDir(): string
    {
        $configCacheDir = config()->get('view_cache.cache_dir', 'cache');

        $viewCacheDir = base_dir() . DS . $configCacheDir . DS . 'views';

        if ($module = current_module()) {
            $viewCacheDir = base_dir() . DS . $configCacheDir . DS . 'views' . DS . strtolower($module);
        }

        return $viewCacheDir;
    }

    /**
     * @param string $key
     * @return string
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws BaseException
     */
    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . DS . md5($key . session()->getId());
    }

    /**
     * @param string $content
     * @return string
     * @throws BaseException
     */
    private function minify(string $content): string
    {
        if (!$this->htmlMinifierExists()) {
            throw ResourceCacheException::notFound('Package', 'HtmlMin');
        }

        return (new HtmlMin())->minify($content);
    }

    /**
     * @return bool
     */
    protected function htmlMinifierExists(): bool
    {
        return class_exists(HtmlMin::class);
    }
}