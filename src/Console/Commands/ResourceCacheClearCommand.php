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
 * @since 2.9.5
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Storage\FileSystem;
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
	protected $description = 'Clears resource cache';

	/**
	 * Command help text
	 * @var string
	 */
	protected $help = 'The command will clear the resource cache';

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
		try {
			$this->importConfig();
			$this->initModule($this->getOption('module'));
			$this->initType($this->getOption('type'));
		}catch (Exception $e){
			$this->error($e->getMessage());
			return;
		}

		$this->fs = Di::get(FileSystem::class);

		if (!$this->fs->isDirectory($this->cacheDir)) {
			$this->error('Cache directory does not exist or is not accessible.');
			return;
		}

		if ($this->module || $this->type) {
			$this->clearResourceModuleAndType($this->module, $this->type);
		} elseif (!empty($this->getOption('all'))) {
			$this->removeFilesFromDirectory($this->cacheDir);
		} else {
			$this->error('Please specify at least one of the following options: --all, --module=moduleName or --type=typeName!');
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

			if (config()->has('modules') && is_array(config()->get('modules.modules'))){
				$this->modules = array_keys(array_change_key_case(config()->get('modules.modules')));
			}
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
	 * @param string|null $moduleOption
	 * @return void
	 * @throws Exception
	 */
	private function initModule(?string $moduleOption): void
	{
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
	 * @param string|null $typeOption
	 * @return void
	 * @throws Exception
	 */
	private function initType(?string $typeOption): void
	{
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
	 * @param string|null $moduleName
	 * @param string|null $type
	 * @return void
	 */
	private function clearResourceModuleAndType(?string $moduleName = null, ?string $type = null): void
	{
		$dir = $this->cacheDir;

		if ($type) {
			$dir = $dir . DS . strtolower($type);
		}

		if ($moduleName) {
			if (!$type) {
				$dir = $dir . DS . '*';
			}

			$dir = $dir . DS . strtolower($moduleName);
		}

		$this->removeFilesFromDirectory($dir);
	}

	/**
	 * @param string $dir
	 * @return void
	 */
	private function removeFilesFromDirectory(string $dir): void
	{
		$entries = $this->fs->glob($dir . DS . '*');

		foreach ($entries as $entry) {
			if ($this->fs->isDirectory($entry)) {
				$this->removeFilesFromDirectory($entry);

				if ($this->fs->fileName($entry) !== config()->get('view_cache.cache_dir', 'cache') &&
					count($this->fs->glob($entry . DS . '*')) === 0
				) {
					$this->fs->removeDirectory($entry);
				}
			} else {
				$this->fs->remove($entry);
			}
		}
	}
}
