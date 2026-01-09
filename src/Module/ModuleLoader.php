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
 * @since 3.0.0
 */

namespace Quantum\Module;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;
use Quantum\Router\Route;
use ReflectionException;
use Quantum\App\App;
use Closure;

/**
 * Class ModuleLoader
 * @package Quantum\Module
 */
class ModuleLoader
{
    /**
     * @var array
     */
    private static $moduleDependencies = [];

    /**
     * @var array
     */
    private static $moduleConfigs = [];

    /**
     * @var array<Closure>
     */
    private static $moduleRoutes = [];

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var ModuleLoader|null
     */
    private static $instance = null;

    /**
     * @throws BaseException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException
     */
    private function __construct()
    {
        $this->fs = FileSystemFactory::get();
    }

    /**
     * @return ModuleLoader
     */
    public static function getInstance(): ModuleLoader
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Load modules routes
     * @return array
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

            $modulesRoutes = array_merge($modulesRoutes, $this->getModuleRoutes($module, new Route([$module => $options])));
        }

        return $modulesRoutes;
    }

    /**
     * @return array
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
     * @param string $module
     * @return array
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
     * @return array
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
     * @throws ModuleException
     */
    private function loadModuleConfig()
    {
        $configPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        if (!$this->fs->exists($configPath)) {
            throw ModuleException::moduleConfigNotFound();
        }

        self::$moduleConfigs = $this->fs->require($configPath);
    }

    /**
     * @param array $options
     * @return bool
     */
    private function isModuleEnabled(array $options): bool
    {
        return $options['enabled'] ?? false;
    }

    /**
     * @param string $module
     * @param Route $route
     * @return array
     * @throws ModuleException
     * @throws RouteException
     */
    private function getModuleRoutes(string $module, Route $route): array
    {
        $moduleRoutes = modules_dir() . DS . $module . DS . 'routes' . DS . 'routes.php';

        if (!$this->fs->exists($moduleRoutes)) {
            throw ModuleException::moduleRoutesNotFound($module);
        }

        if (empty(self::$moduleRoutes[$module])) {
            self::$moduleRoutes[$module] = $this->fs->require($moduleRoutes, true);
        }

        if (!self::$moduleRoutes[$module] instanceof Closure) {
            throw RouteException::notClosure();
        }

        self::$moduleRoutes[$module]($route);

        return $route->getRuntimeRoutes();
    }
}
