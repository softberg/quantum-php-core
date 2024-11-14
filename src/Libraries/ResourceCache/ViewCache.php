<?php

namespace Quantum\Libraries\ResourceCache;

use Quantum\Exceptions\ConfigException;
use voku\helper\HtmlMin;

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
	 * @param bool $fromCommand
	 * @throws ConfigException
	 */
	public function __construct(bool $fromCommand = false)
	{
		if (!config()->has('view_cache') && !$fromCommand){
			throw ConfigException::configCollision('view_cache');
		}

		$configCacheDir = config()->get('view_cache.cache_dir', 'cache');
		$module = strtolower(current_module());

		$this->cacheDir = base_dir() . DS . $configCacheDir . DS . 'views/';

		if ($module){
			$this->cacheDir = base_dir() . DS . $configCacheDir . DS . 'views' . DS . $module . '/';
		}
		
		if (!is_dir($this->cacheDir) && !$fromCommand) {
			mkdir($this->cacheDir, 0777, true);
		}
	}

	/**
	 * @param string $key
	 * @param string $content
	 * @param string $sessionId
	 * @param int $ttl
	 * @return void
	 */
	public function set(string $key, string $content, string $sessionId, int $ttl = 300): void
	{
		if (config()->has('view_cache.minify')) {
			$content = $this->minify($content);
		}

		$cacheFile = $this->getCacheFile($key, $sessionId);

		$data = [
			'content' => $content,
			'expires_at' => time() + $ttl,
		];

		file_put_contents($cacheFile, serialize($data));
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @return mixed|null
	 */
	public function get(string $key, string $sessionId): ?string
	{
		$cacheFile = $this->getCacheFile($key, $sessionId);
		if (!file_exists($cacheFile)) {
			return null;
		}

		$data = unserialize(file_get_contents($cacheFile));
		if (time() > $data['expires_at']) {
			unlink($cacheFile);
			return null;
		}

		return $data['content'];
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @return void
	 */
	public function delete(string $key, string $sessionId): void
	{
		$cacheFile = $this->getCacheFile($key, $sessionId);
		if (file_exists($cacheFile)) {
			unlink($cacheFile);
		}
	}

	/**
	 * @param string $key
	 * @param string $sessionId
	 * @return bool
	 */
	public function exists(string $key, string $sessionId): bool
	{		
		$cacheFile = $this->getCacheFile($key, $sessionId);

		if (!file_exists($cacheFile)) {
			return false;
		}

		$data = unserialize(file_get_contents($cacheFile));

		if (time() > $data['expires_at']) {
			unlink($cacheFile);
			return false;
		}

		return true;
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