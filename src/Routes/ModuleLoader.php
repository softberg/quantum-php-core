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
 * @since 2.0.0
 */

namespace Quantum\Routes;

use Quantum\Exceptions\ModuleLoaderException;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Routes\Route;
use Closure;

/**
 * ModuleLoader Class
 * @package Quantum
 * @category Routes
 */
class ModuleLoader
{

    /**
     * Load Modules
     * @param Router $router
     * @param FileSystem $fs
     * @throws ModuleLoaderException
     */
    public static function loadModulesRoutes(Router $router, FileSystem $fs)
    {
        $modules = require_once base_dir() . DS . 'config' . DS . 'modules.php';

        foreach ($modules['modules'] as $module) {
            $moduleRoutes = modules_dir() . DS . $module . DS . 'Config' . DS . 'routes.php';

            if (!$fs->exists($moduleRoutes)) {
                throw new ModuleLoaderException(_message(ExceptionMessages::MODULE_NOT_FOUND, $module));
            }

            $routesClosure = require_once $moduleRoutes;

            if (!$routesClosure instanceof Closure) {
                throw new ModuleLoaderException(ExceptionMessages::ROUTES_NOT_CLOSURE);
            }

            $route = new Route($module);

            $routesClosure($route);

            $router->setRoutes(array_merge($router->getRoutes(), $route->getRuntimeRoutes()));
        }
    }

}
