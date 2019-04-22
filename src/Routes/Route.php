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

use Quantum\Routes\Router;

/**
 * Route Class
 * 
 * Route class allows to add new route entries
 * 
 * @package Quantum
 * @subpackage Routes
 * @category Routes
 */
class Route {

    private $virtualRoutes = [];
    private $currentGroup = NULL;

    /**
     * List of routes
     *
     * @var array 
     */
    private $router;

    /**
     * Current module
     * 
     * @var string 
     */
    private $module;

    /**
     * Class constructor
     * 
     * @param Router $router
     * @param string $module
     */
    public function __construct(Router $router, $module) {
        $this->virtualRoutes['*'] = [];

        $this->router = $router;
        $this->module = $module;
    }

    /**
     * Adds new route entry to routes
     * 
     * @param string $uri
     * @param string $method
     * @param string $controller
     * @param string $action
     * @param array $middlewares
     */
    public function add($uri, $method, $controller, $action) {
        $this->currentRoute = [
            'uri' => $uri,
            'method' => $method,
            'controller' => $controller,
            'action' => $action,
            'module' => $this->module,
        ];

        if ($this->currentGroup) {
            array_push($this->virtualRoutes[$this->currentGroup], $this->currentRoute);
        } else {
            array_push($this->virtualRoutes['*'], $this->currentRoute);
        }

        return $this;

//        array_push($this->router->routes, [
//            'uri' => $uri,
//            'method' => $method,
//            'controller' => $controller,
//            'action' => $action,
//            'module' => $this->module,
//            'middlewares' => $middlewares
//        ]);
    }

    public function group($groupName, $callback) {
        $this->currentGroup = $groupName;
        $this->virtualRoutes[$groupName] = [];
        $callback();
        $this->currentGroup = NULL;
    }

    public function middlewares($middlewares = []) {

    }

}
