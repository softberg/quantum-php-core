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
 * @since 1.0.0
 */

namespace Quantum\Routes;

use Quantum\Exceptions\ModuleLoaderException;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Routes\Route;

/**
 * ModuleLoader Class
 * 
 * ModuleLoader class allows loads modules
 * 
 * @package Quantum
 * @subpackage Routes
 * @category Routes
 */
class ModuleLoader
{

    /**
     * List of loaded modules
     * 
     * @var array 
     */
    public $modules = [];

    /**
     * Load Modules
     * 
     * @param \Quantum\Routes\Router $router
     * @throws \Exception When module file is not found
     */
    public function loadModules(Router $router)
    {
        $this->modules = require_once BASE_DIR . DS . 'config' . DS . 'modules.php';

        foreach ($this->modules['modules'] as $module) {
            if (!file_exists(MODULES_DIR . DS . $module . DS . 'Config' . DS . 'routes.php')) {
                throw new ModuleLoaderException(_message(ExceptionMessages::MODULE_NOT_FOUND, $module));
            }

            $routesClosure = require_once MODULES_DIR . DS . $module . DS . 'Config' . DS . 'routes.php';

            if (!$routesClosure instanceof \Closure) {
                throw new ModuleLoaderException(ExceptionMessages::ROUTES_NOT_CLOSURE);
            }

            $route = new Route($module);
            $routesClosure($route);
            $router->setRoutes(array_merge($router->getRoutes(), $route->getRuntimeRoutes()));
        }
    }

}
