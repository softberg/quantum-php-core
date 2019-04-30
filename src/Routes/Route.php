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
class Route
{
    /**
     *
     * @var array
     */
    private $virtualRoutes = [];

    /**
     *
     * @var string
     */
    private $currentGroupName = NULL;

    /**
     * Current module
     *
     * @var string
     */
    private $module;

    /**
     * 
     *
     * @var boolean
     */
    private $isGroupeMiddlewares;

    /**
     * 
     *
     * @var boolean
     */
    private $isGroupe = FALSE;

    /**
     *
     * @var array
     */
    private $currentRoute = [];

    /**
     * Class constructor
     *
     * @param string $module
     */
    public function __construct($module) {
        $this->virtualRoutes['*'] = [];

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

        if ($this->currentGroupName) {
            $this->virtualRoutes[$this->currentGroupName][] = $this->currentRoute;
        } else {
            $this->virtualRoutes['*'][] = $this->currentRoute;
        }

        return $this;
    }

    /**
     *
     *
     * @param string $groupName
     * @param \Closure $callback
     */
    public function group(string $groupName, \Closure $callback) {
        $this->currentGroupName = $groupName;

        $this->isGroupe = TRUE;
        $this->isGroupeMiddlewares = FALSE;
        $callback($this);
        $this->isGroupeMiddlewares = TRUE;
        $this->currentGroupName = NULL;

        return $this;
    }

    /**
     *
     *
     * @param array $middlewares
     */
    public function middlewares(array $middlewares = []) {
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
                        foreach ($middlewares as $middleware) {
                            $route['middlewares'][] = $middleware;
                        }
                    }
                }
            }
        }
    }

    public function getRuntimeRoutes() {
        $runtimeRoutes = [];

        foreach ($this->virtualRoutes as $virtualRoute) {
            foreach ($virtualRoute as $route) {
                $runtimeRoutes[] = $route;
            }
        }
        return $runtimeRoutes;
    }

    public function getVirtualRoutes() {
        return $this->virtualRoutes;
    }
}
