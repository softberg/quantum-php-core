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

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\RouteException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\DiException;
use ReflectionException;
use Quantum\Di\Di;
use Closure;

/**
 * Class ModuleLoader
 * @package Quantum\Router
 */
class ModuleLoader
{

    private static $instance = null;

    private $fs;

    /**
     * @param FileSystem $fs
     */
    private function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return ModuleLoader
     * @throws DiException
     * @throws ReflectionException
     */
    public static function getInstance(): ModuleLoader
    {
        if (self::$instance === null) {
            self::$instance = new self(Di::get(FileSystem::class));
        }

        return self::$instance;
    }

    /**
     * Load Modules
     * @throws LangException
     * @throws ModuleLoaderException
     * @throws RouteException
     */
    public function loadModulesRoutes()
    {
        $modules = $this->loadModuleConfig();

        $modulesRoutes = [];

        foreach ($modules['modules'] as $module => $options) {
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
     * @throws LangException
     */
    private function loadModuleConfig(): array
    {
        $configPath = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        if (!$this->fs->exists($configPath)) {
            throw ModuleLoaderException::moduleConfigNotFound();
        }

        return $this->fs->require($configPath, true);
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
     * @throws LangException
     * @throws ModuleLoaderException
     * @throws RouteException
     */
    private function getModuleRoutes(string $module, Route $route): array
    {
        $moduleRoutes = modules_dir() . DS . $module . DS . 'Config' . DS . 'routes.php';

        if (!$this->fs->exists($moduleRoutes)) {
            throw ModuleLoaderException::moduleRoutesNotFound($module);
        }

        $routesClosure = $this->fs->require($moduleRoutes, true);

        if (!$routesClosure instanceof Closure) {
            throw RouteException::notClosure();
        }

        $routesClosure($route);

        return $route->getRuntimeRoutes();
    }

}
