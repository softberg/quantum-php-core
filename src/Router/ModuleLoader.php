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

namespace Quantum\Router;

use Quantum\Exceptions\ModuleLoaderException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\RouteException;
use Quantum\Di\Di;
use Closure;

/**
 * Class ModuleLoader
 * @package Quantum\Router
 */
class ModuleLoader
{

    /**
     * Load Modules
     * @param \Quantum\Router\Router $router
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ModuleLoaderException
     * @throws \Quantum\Exceptions\RouteException
     * @throws \ReflectionException
     */
    public static function loadModulesRoutes(Router $router)
    {
        $fs = Di::get(FileSystem::class);

        $modules = require_once base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        foreach ($modules['modules'] as $module) {
            $moduleRoutes = modules_dir() . DS . $module . DS . 'Config' . DS . 'routes.php';

            if (!$fs->exists($moduleRoutes)) {
                throw ModuleLoaderException::notFound($module);
            }

            $routesClosure = require_once $moduleRoutes;

            if (!$routesClosure instanceof Closure) {
                throw RouteException::notClosure();
            }

            $route = new Route($module);

            $routesClosure($route);

            $router->setRoutes(array_merge($router->getRoutes(), $route->getRuntimeRoutes()));
        }
    }

}
