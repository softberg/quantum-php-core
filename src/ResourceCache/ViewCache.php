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

namespace Quantum\ResourceCache;

use Quantum\Loader\Exceptions\LoaderException;
use Quantum\ResourceCache\Exceptions\ResourceCacheException;
use Quantum\Database\Exceptions\DatabaseException;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Storage\FileSystem;
use Quantum\Http\Response;
use Quantum\Loader\Setup;
use ReflectionException;
use voku\helper\HtmlMin;
use Exception;

/**
 * ViewCache class
 * @package Quantum\ResourceCache
 */
class ViewCache
{
    private ?string $cacheDir = null;

    /**
     * @var int
     */
    private $ttl = 300;

    private bool $isEnabled;

    private bool $minification = false;

    private FileSystem $fs;

    private static ?ViewCache $instance = null;

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
     * @throws ConfigException|DiException|LoaderException|ReflectionException
     */
    public function setup(): void
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
            $cachedContent = $this->get($uri);
            if ($cachedContent !== null) {
                $response->html($cachedContent);
                return true;
            }
        }

        return false;
    }

    /**
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

        $content = $this->fs->get($this->getCacheFile($key));

        return is_string($content) ? $content : null;
    }

    /**
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
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function exists(string $key): bool
    {
        $cacheFile = $this->getCacheFile($key);
        return $this->fs->exists($cacheFile) && !$this->isExpired($cacheFile);
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function enableCaching(bool $state): void
    {
        $this->isEnabled = $state;
    }

    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    public function enableMinification(bool $state): void
    {
        $this->minification = $state;
    }

    /**
     * @param $cacheFile
     */
    private function isExpired(string $cacheFile): bool
    {
        if (time() > ($this->fs->lastModified($cacheFile) + $this->ttl)) {
            $this->fs->remove($cacheFile);
            return true;
        }

        return false;
    }

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
     * @throws BaseException
     */
    private function minify(string $content): string
    {
        if (!$this->htmlMinifierExists()) {
            throw ResourceCacheException::notFound('Package', 'HtmlMin');
        }

        return (new HtmlMin())->minify($content);
    }

    protected function htmlMinifierExists(): bool
    {
        return class_exists(HtmlMin::class);
    }
}
