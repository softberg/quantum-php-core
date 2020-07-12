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
 * 
 * ModuleLoader class allows loads modules
 * 
 * @package Quantum
 * @category Routes
 */
class ModuleLoader
{

    /**
     * List of loaded modules
     * @var array 
     */
    public $modules = [];
    
    /**
     * Router instance
     * @var Quantum\Routes\Router 
     */
    private $router;
    
    /**
     * FileSystem instance
     * @var Quantum\Libraries\Storage\FileSystem
     */
    private $fileSystem;

    /**
     * Class constructor.
     * @param \Quantum\Routes\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->fileSystem = new FileSystem();
    }

    /**
     * Load Modules
     * @param Quantum\Routes\Router $router
     * @throws ModuleLoaderException
     */
    public function loadModulesRoutes()
    {
        $this->modules = require_once base_dir() . DS . 'config' . DS . 'modules.php';

        foreach ($this->modules['modules'] as $module) {
            if (!$this->fileSystem->exists(modules_dir() . DS . $module . DS . 'Config' . DS . 'routes.php')) {
                throw new ModuleLoaderException(_message(ExceptionMessages::MODULE_NOT_FOUND, $module));
            }

            $routesClosure = require_once modules_dir() . DS . $module . DS . 'Config' . DS . 'routes.php';

            if (!$routesClosure instanceof Closure) {
                throw new ModuleLoaderException(ExceptionMessages::ROUTES_NOT_CLOSURE);
            }

            $route = new Route($module);
            $routesClosure($route);
            $this->router->setRoutes(array_merge($this->router->getRoutes(), $route->getRuntimeRoutes()));
        }
    }

}
