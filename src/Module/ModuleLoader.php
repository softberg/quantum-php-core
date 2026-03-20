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

namespace Quantum\Module;

use Quantum\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Storage\FileSystem;
use ReflectionException;
use Quantum\App\App;
use Quantum\Di\Di;
use Closure;

/**
 * Class ModuleLoader
 * @package Quantum\Module
 */
class ModuleLoader
{
    /**
     * @var array<string, array<string>>
     */
    private static array $moduleDependencies = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private static array $moduleConfigs = [];

    /** @var array<string, Closure> */
    private static array $moduleRouteClosures = [];

    private FileSystem $fs;

    private static ?ModuleLoader $instance = null;

    /**
     * @throws BaseException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException|ModuleException
     */
    private function __construct()
    {
        $this->fs = FileSystemFactory::get();
        Di::registerDependencies($this->loadModulesDependencies());
    }

    public static function getInstance(): ModuleLoader
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array<string, Closure>
     * @throws ModuleException
     * @throws RouteException
     */
    public function loadModulesRoutes(): array
    {
        if (empty(self::$moduleConfigs)) {
            $this->loadModuleConfig();
        }

        $modulesRoutes = [];

        foreach (self::$moduleConfigs as $module => $options) {
            if (!$this->isModuleEnabled($options)) {
                continue;
            }

            $modulesRoutes[$module] = $this->getModuleRouteDefinitions($module);
        }

        return $modulesRoutes;
    }

    /**
     * @throws ModuleException
     * @throws RouteException
     */
    private function getModuleRouteDefinitions(string $module): Closure
    {
        if (isset(self::$moduleRouteClosures[$module])) {
            return self::$moduleRouteClosures[$module];
        }

        $moduleRoutesPath = modules_dir() . DS . $module . DS . 'routes' . DS . 'routes.php';

        if (!$this->fs->exists($moduleRoutesPath)) {
            throw ModuleException::moduleRoutesNotFound($module);
        }

        $closure = $this->fs->require($moduleRoutesPath, true);

        if (!$closure instanceof Closure) {
            throw RouteException::notClosure();
        }

        return self::$moduleRouteClosures[$module] = $closure;
    }

    /**
     * @return array<string, string>
     * @throws ModuleException
     */
    public function loadModulesDependencies(): array
    {
        if (empty(self::$moduleConfigs)) {
            $this->loadModuleConfig();
        }

        $modulesDependencies = [];

        foreach (self::$moduleConfigs as $module => $options) {
            $modulesDependencies = array_merge($modulesDependencies, $this->getModuleDependencies($module));
        }

        return $modulesDependencies;
    }

    /**
     * @return array<string>
     */
    public function getModuleDependencies(string $module): array
    {
        if (!isset(self::$moduleDependencies[$module])) {
            $file = modules_dir() . DS . $module . DS . 'config' . DS . 'dependencies.php';

            if ($this->fs->exists($file)) {
                $deps = $this->fs->require($file);

                self::$moduleDependencies[$module] = is_array($deps) ? $deps : [];
            } else {
                self::$moduleDependencies[$module] = [];
            }
        }

        return self::$moduleDependencies[$module];
    }

    /**
     * @throws ModuleException
     */
    private function loadModuleConfig(): void
    {
        $configPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        if (!$this->fs->exists($configPath)) {
            throw ModuleException::moduleConfigNotFound();
        }

        self::$moduleConfigs = $this->fs->require($configPath);
    }

    /**
     * @return array<string, array<string, mixed>>
     * @throws ModuleException
     */
    public function getModuleConfigs(): array
    {
        if (empty(self::$moduleConfigs)) {
            $this->loadModuleConfig();
        }

        return self::$moduleConfigs;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function isModuleEnabled(array $options): bool
    {
        return (bool) ($options['enabled'] ?? false);
    }
}
