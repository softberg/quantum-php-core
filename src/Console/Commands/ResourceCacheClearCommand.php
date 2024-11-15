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

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\DiException;
use Quantum\Console\QtCommand;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;
use Exception;

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
	protected $name = 'cache:clear';

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
	 * @var object
	 */
	protected $fs;

	/**
	 * @return void
	 * @throws DiException
	 * @throws ReflectionException
	 */
	public function exec()
	{
		$this->fs = Di::get(FileSystem::class);

		try {
			$this->importConfig();
			$this->initModule();
			$this->initType();
		}catch (Exception $e){
			$this->error($e->getMessage());
			return;
		}


		if ($this->fs->isDirectory($this->cacheDir)) {
			if ($this->module || $this->type) {
				$this->clearResourceFiles($this->cacheDir, $this->module, $this->type);
			} elseif (!empty($this->getOption('all'))) {
				$this->removeFilesInDirectory($this->cacheDir);
			} else {
				$this->error('You must set at least one of these options {--all, --module=moduleName, --type=typeName}!');
				return;
			}
		} else {
			$this->error('Cache directory does not exist or is not accessible.');
			return;
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
			$this->modules = array_keys(array_change_key_case(config()->get('modules.modules')));
		} catch (ConfigException|DiException|ReflectionException $e) {
			$this->error($e->getMessage());
			return;
		}
	}

	/**
	 * @return void
	 * @throws ConfigException
	 * @throws DiException
	 * @throws ReflectionException
	 */
	private function importConfig(): void
	{
		if (!config()->has('view_cache')) {
			config()->import(new Setup('config', 'view_cache'));
		}

		$this->cacheDir = base_dir() . DS . config()->get('view_cache.cache_dir', 'cache');
	}

	/**
	 * @return void
	 * @throws Exception
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
				throw new Exception('Module {'. $module .'} does not exist.');
			}
		}
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function initType(): void
	{
		$typeOption = $this->getOption('type');

		if (!empty($typeOption)) {
			$type = strtolower($typeOption);

			if (in_array($type, $this->types)) {
				$this->type = $type;
			} else {
				throw new Exception('Cache type {'. $type .'} is invalid.');
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
		$folders = $this->fs->glob($dir);
		$files = $this->fs->glob($dir . DS . '*');

		foreach ($files as $file) {
			if ($this->fs->isDirectory($file)) {
				$this->removeFilesInDirectory($file);
			} else {
				$this->fs->remove($file);
			}
		}

		foreach ($folders as $folder) {
			if (count($this->fs->glob($folder . DS . '*')) === 0 &&
				basename($dir) !== config()->get('view_cache.cache_dir', 'cache')
			) {
				$this->fs->removeDirectory($folder);
			}
		}
	}
}
