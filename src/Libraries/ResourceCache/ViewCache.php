<?php

namespace Quantum\Libraries\ResourceCache;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\DiException;
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
	private $mimeType = '.tmp';

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
		$this->fs = Di::get(FileSystem::class);

		if (!config()->has('view_cache')) {
			throw new Exception('The config "view_cache" does not exists.');
		}

		$this->cacheDir = self::getCacheDir();

		if (!$this->fs->isDirectory($this->cacheDir)) {
			mkdir($this->cacheDir, 0777, true);
		}
	}

	/**
	 * @param string $key
	 * @param string $content
	 * @param string $sessionId
	 * @return ViewCache
	 */
	public function set(string $key, string $content, string $sessionId): ViewCache
	{
		if (config()->has('view_cache.minify')) {
			$content = $this->minify($content);
		}

		$cacheFile = $this->getCacheFile($key, $sessionId);
		$this->fs->put($cacheFile, $content);

		return $this;
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @param int $ttl
	 * @return mixed|null
	 */
	public function get(string $key, string $sessionId, int $ttl): ?string
	{
		$cacheFile = $this->getCacheFile($key, $sessionId);
		if (!$this->fs->exists($cacheFile)) {
			return null;
		}

		$data = $this->fs->get($cacheFile);
		if (time() > ($this->fs->lastModified($cacheFile) + $ttl)) {
			$this->fs->remove($cacheFile);
			return null;
		}

		return $data;
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @return void
	 */
	public function delete(string $key, string $sessionId): void
	{
		$cacheFile = $this->getCacheFile($key, $sessionId);
		if ($this->fs->exists($cacheFile)) {
			$this->fs->remove($cacheFile);
		}
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @param int $ttl
	 * @return bool
	 */
	public function exists(string $key, string $sessionId, int $ttl): bool
	{
		$cacheFile = $this->getCacheFile($key, $sessionId);

		if (!$this->fs->exists($cacheFile)) {
			return false;
		}

		if (time() > ($this->fs->lastModified($cacheFile) + $ttl)) {
			$this->fs->remove($cacheFile);
			return false;
		}

		return true;
	}

	private static function getCacheDir(): string
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
	 * @param string $sessionId
	 * @return string
	 */
	private function getCacheFile(string $key, string $sessionId): string
	{
		return $this->cacheDir . md5($key . $sessionId) . $this->mimeType;
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