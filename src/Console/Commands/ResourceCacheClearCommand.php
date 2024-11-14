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

namespace Quantum\Console\Commands;

use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\DiException;
use Quantum\Console\QtCommand;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class EnvCommand
 * @package Quantum\Console\Commands
 */
class ResourceCacheClearCommand extends QtCommand
{

	/**
	 * Command name
	 * @var string
	 */
	protected $name = 'resource_cache:clear';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Clearing resource caches';

	/**
	 * Command help text
	 * @var string
	 */
	protected $help = 'The command will clear the resource caches.';

	/**
	 * Command options
	 * @var array
	 */
	protected $options = [
		['all', 'all', 'none', ''],
		['type', 't', 'required', ''],
		['module', 'm', 'required', '']
	];

	/**
	 * @var array
	 */
	protected $types = ['views', 'asserts'];

	/**
	 * @var array
	 */
	protected $modules;

	/**
	 * @var string|null
	 */
	protected $type = null;

	/**
	 * @var string|null
	 */
	protected $module = null;

	/**
	 * @var string
	 */
	protected $cacheDir;

	/**
	 * @return void
	 */
	public function exec()
	{
		$this->importConfig();
		$this->initModule();
		$this->initType();

		if (is_dir($this->cacheDir)) {
			if ($this->module || $this->type) {
				$this->clearResourceFiles($this->cacheDir, $this->module, $this->type);
			} elseif (!empty($this->getOption('all'))) {
				$this->removeFilesInDirectory($this->cacheDir);
			}
		} else {
			$this->error('Cache directory does not exist or is not accessible.');
			exit();
		}

		$this->info('Resource cache cleared successfully.');
	}

	/**
	 * @return void
	 */
	private function importModules()
	{
		try {
			if (!config()->has('modules')) {
				config()->import(new Setup('config', 'modules'));
			}
			$this->modules = array_keys(array_change_key_case(config()->get('modules')['modules']));
		} catch (ConfigException|DiException|ReflectionException $e) {
			$this->error($e->getMessage());
			exit();
		}
	}

	/**
	 * @return void
	 */
	private function importConfig(): void
	{
		if (!config()->has('view_cache')) {
			try {
				config()->import(new Setup('config', 'view_cache'));
			} catch (\Exception $e) {
				$this->error('Error loading configuration for view_cache.');
			}
		}

		$this->cacheDir = base_dir() . DS . config()->get('view_cache.cache_dir', 'cache');
	}

	/**
	 * @return void
	 */
	private function initModule(): void
	{
		$moduleOption = $this->getOption('module');

		if (!empty($moduleOption)) {
			$this->importModules();
			$module = strtolower($moduleOption);

			if (in_array($module, $this->modules)) {
				$this->module = $module;
			} else {
				$this->error("Module '{$module}' does not exist.");
				exit();
			}
		}
	}

	/**
	 * @return void
	 */
	private function initType(): void
	{
		$typeOption = $this->getOption('type');

		if (!empty($typeOption)) {
			$type = strtolower($typeOption);

			if (in_array($type, $this->types)) {
				$this->type = $type;
			} else {
				$this->error("Cache type '{$type}' is invalid.");
				exit();
			}
		}
	}

	/**
	 * @param string $dir
	 * @param string|null $moduleName
	 * @param string|null $type
	 * @return void
	 */
	private function clearResourceFiles(string $dir, ?string $moduleName = null, ?string $type = null): void
	{
		if ($type) {
			$dir = $dir . DS . strtolower($type);
		}

		if ($moduleName) {
			if (!$type) {
				$dir = $dir . DS . '*';
			}
			$dir = $dir . DS . strtolower($moduleName);
		}

		$this->removeFilesInDirectory($dir);
	}

	/**
	 * @param string $dir
	 * @return void
	 */
	private function removeFilesInDirectory(string $dir): void
	{
		$folders = glob($dir);
		$files = glob($dir . '/*');

		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->removeFilesInDirectory($file);
			} else {
				unlink($file);
			}
		}

		foreach ($folders as $folder) {
			if (count(glob($folder . '/*')) === 0 &&
				basename($dir) !== config()->get('view_cache.cache_dir', 'cache')
			) {
				rmdir($folder);
			}
		}
	}
}
