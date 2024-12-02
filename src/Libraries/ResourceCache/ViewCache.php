<?php

namespace Quantum\Libraries\ResourceCache;

use Quantum\Exceptions\DatabaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\SessionException;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;
use voku\helper\HtmlMin;
use Quantum\Di\Di;
use Exception;

class ViewCache
{
	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var string
	 */
	private $sessionId;

	/**
	 * @var string
	 */
	private $mimeType = '.tmp';

	/**
	 * @var int
	 */
	private $ttl = 300;

	/**
	 * @var bool
	 */
	private $isEnabled;

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
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public function __construct()
	{
		if (config()->get('resource_cache')){
			config()->import(new Setup('config','view_cache'));
		}

		$this->fs = Di::get(FileSystem::class);

		$this->cacheDir = $this->getCacheDir();

		$configTtl  = config()->get('view_cache.ttl');
		if (is_int($configTtl)){
			$this->ttl = $configTtl;
		}

		$this->sessionId = session()->getId();

		if (!$this->fs->isDirectory($this->cacheDir)) {
			mkdir($this->cacheDir, 0777, true);
		}
	}

	/**
	 * @param string $key
	 * @param string $content
	 * @return ViewCache
	 */
	public function set(string $key, string $content): ViewCache
	{
		if (config()->has('view_cache.minify')) {
			$content = $this->minify($content);
		}

		$this->fs->put($this->getCacheFile($key), $content);

		return $this;
	}

	/**
	 * @param string $key
	 * @return mixed|null
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
	 * @param $cacheFile
	 * @return bool
	 */
	public function isExpired($cacheFile): bool
	{
		if (time() > ($this->fs->lastModified($cacheFile) + $this->ttl)) {
			$this->fs->remove($cacheFile);
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 * @throws DiException
	 * @throws ReflectionException
	 * @throws ConfigException
	 * @throws DatabaseException
	 * @throws LangException
	 * @throws SessionException
	 */
	public function isEnabled(): bool
	{
		if (!is_null($this->isEnabled)){
			return $this->isEnabled;
		}

		$resourceCache = config()->get('resource_cache');

		if (is_bool($resourceCache) && $resourceCache && !empty(session()->getId())){
			return true;
		}

		return false;
	}

	/**
	 * @param int $ttl
	 * @return void
	 */
	public function setTtl(int $ttl): void
	{
		$this->ttl = $ttl;
	}

	public function enableCaching(bool $state): void
	{
		$this->isEnabled = $state;
	}

	/**
	 * @return string
	 */
	private function getCacheDir(): string
	{
		$configCacheDir = config()->get('view_cache.cache_dir', 'cache');

		$cacheDir = base_dir() . DS . $configCacheDir . DS . 'views' . DS;

		if ($module = current_module()) {
			$cacheDir = base_dir() . DS . $configCacheDir . DS . 'views' . DS . strtolower($module) . DS;
		}

		return $cacheDir;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	private function getCacheFile(string $key): string
	{
		return $this->cacheDir . md5($key . $this->sessionId) . $this->mimeType;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	private function minify(string $content): string
	{
		return (new HtmlMin())->minify($content);
	}
}