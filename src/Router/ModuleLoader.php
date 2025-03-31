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

namespace Quantum\Router;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Router\Exceptions\ModuleLoaderException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\BaseException;
use Quantum\App\App;
use Closure;

/**
 * Class ModuleLoader
 * @package Quantum\Router
 */
class ModuleLoader
{

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
     * Load Modules
     * @throws ModuleLoaderException
     * @throws RouteException
     */
    public function loadModulesRoutes()
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

        Router::setRoutes($modulesRoutes);
    }

    /**
     * @return array
     * @throws ModuleLoaderException
     */
    public function getModuleConfigs(): array
    {
        if (empty(self::$moduleConfigs)) {
            $this->loadModuleConfig();
        }

        return self::$moduleConfigs;
    }

    /**
     * @throws ModuleLoaderException
     */
    private function loadModuleConfig()
    {
        $configPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        if (!$this->fs->exists($configPath)) {
            throw ModuleLoaderException::moduleConfigNotFound();
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
     * @throws ModuleLoaderException
     * @throws RouteException
     */
    private function getModuleRoutes(string $module, Route $route): array
    {
        $moduleRoutes = modules_dir() . DS . $module . DS . 'config' . DS . 'routes.php';

        if (!$this->fs->exists($moduleRoutes)) {
            throw ModuleLoaderException::moduleRoutesNotFound($module);
        }

        if(empty(self::$moduleRoutes[$module])) {
            self::$moduleRoutes[$module] = $this->fs->require($moduleRoutes, true);
        }

        if (!self::$moduleRoutes[$module] instanceof Closure) {
            throw RouteException::notClosure();
        }

        self::$moduleRoutes[$module]($route);

        return $route->getRuntimeRoutes();
    }
}