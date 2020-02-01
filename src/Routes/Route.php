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

/**
 * Route Class
 *
 * Route class allows to add new route entries
 *
 * @package Quantum
 * @subpackage Routes
 * @category Routes
 */
class Route
{

    /**
     * Current module
     *
     * @var string
     */
    private $module;

    /**
     * Identifies the group middleware 
     *
     * @var boolean
     */
    private $isGroupeMiddlewares;

    /**
     * Identifies the group
     *
     * @var boolean
     */
    private $isGroupe = FALSE;

    /**
     * The group name
     * 
     * @var string
     */
    private $currentGroupName = NULL;

    /**
     * Current route
     * 
     * @var array
     */
    private $currentRoute = [];

    /**
     * Virtual routes
     * 
     * @var array
     */
    private $virtualRoutes = [];

    /**
     * Class constructor
     *
     * @param string $module
     */
    public function __construct($module)
    {
        $this->virtualRoutes['*'] = [];

        $this->module = $module;
    }

    /**
     * Adds new route entry to routes
     *
     * @param string $route
     * @param string $method
     * @param string $controller
     * @param string $action
     * @param array $middlewares
     * @return $this
     */
    public function add($route, $method, $controller, $action)
    {
        $this->currentRoute = [
            'route' => $route,
            'method' => $method,
            'controller' => $controller,
            'action' => $action,
            'module' => $this->module,
        ];

        if ($this->currentGroupName) {
            $this->virtualRoutes[$this->currentGroupName][] = $this->currentRoute;
        } else {
            $this->virtualRoutes['*'][] = $this->currentRoute;
        }

        return $this;
    }

    /**
     * Starts a named group of routes
     * 
     * @param string $groupName
     * @param \Closure $callback
     * @return $this
     */
    public function group(string $groupName, \Closure $callback)
    {
        $this->currentGroupName = $groupName;

        $this->isGroupe = TRUE;
        $this->isGroupeMiddlewares = FALSE;
        $callback($this);
        $this->isGroupeMiddlewares = TRUE;
        $this->currentGroupName = NULL;

        return $this;
    }

    /**
     * Adds middlewares to routes and route groups
     *
     * @param array $middlewares
     * @return void
     */
    public function middlewares(array $middlewares = [])
    {
        if (!$this->isGroupe) {
            end($this->virtualRoutes['*']);
            $lastKey = key($this->virtualRoutes['*']);
            $this->virtualRoutes['*'][$lastKey]['middlewares'] = $middlewares;
        } else {
            end($this->virtualRoutes);
            $lastKeyOfFirstRound = key($this->virtualRoutes);

            if (!$this->isGroupeMiddlewares) {
                end($this->virtualRoutes[$lastKeyOfFirstRound]);
                $lastKeyOfSecondRound = key($this->virtualRoutes[$lastKeyOfFirstRound]);
                $this->virtualRoutes[$lastKeyOfFirstRound][$lastKeyOfSecondRound]['middlewares'] = $middlewares;
            } else {
                $this->isGroupe = FALSE;
                foreach ($this->virtualRoutes[$lastKeyOfFirstRound] as &$route) {
                    $hasMiddleware = end($route);
                    if (!is_array($hasMiddleware)) {
                        $route['middlewares'] = $middlewares;
                    } else {
                        $reversedMiddlewares = array_reverse($middlewares);
                        foreach ($reversedMiddlewares as $middleware) {
                            array_unshift($route['middlewares'], $middleware);
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets the runtime routes
     * 
     * @return array
     */
    public function getRuntimeRoutes()
    {
        $runtimeRoutes = [];

        foreach ($this->virtualRoutes as $virtualRoute) {
            foreach ($virtualRoute as $route) {
                $runtimeRoutes[] = $route;
            }
        }
        return $runtimeRoutes;
    }

    /**
     * Gets virtual routes
     * 
     * @return array
     */
    public function getVirtualRoutes()
    {
        return $this->virtualRoutes;
    }

}
