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
 * @since 2.5.0
 */

namespace Quantum\Routes;

use Quantum\Exceptions\RouteException;
use Closure;

/**
 * Route Class
 *
 * Route class allows to add new route entries
 *
 * @package Quantum
 * @category Routes
 */

class Route
{

    /**
     * Current module
     * @var string
     */
    private $module;

    /**
     * Identifies the group middleware
     * @var bool
     */
    private $isGroupeMiddlewares;

    /**
     * Identifies the group
     * @var boolean
     */
    private $isGroupe = false;

    /**
     * Current group name
     * @var string
     */
    private $currentGroupName = null;

    /**
     * Current route
     * @var array
     */
    private $currentRoute = [];

    /**
     * Virtual routes
     * @var array
     */
    private $virtualRoutes = [];

    /**
     * Class constructor
     * @param string $module
     */
    public function __construct(string $module)
    {
        $this->virtualRoutes['*'] = [];
        $this->module = $module;
    }

    /**
     * Adds new route entry to routes
     * @param string $route
     * @param string $method
     * @param array $params
     * @return $this
     */
    public function add(string $route, string $method, ...$params): self
    {
        $this->currentRoute = [
            'route' => $route,
            'method' => $method,
            'module' => $this->module
        ];

        if (is_callable($params[0])) {
            $this->currentRoute['callback'] = $params[0];
        } else {
            $this->currentRoute['controller'] = $params[0];
            $this->currentRoute['action'] = $params[1];
        }

        if ($this->currentGroupName) {
            $this->virtualRoutes[$this->currentGroupName][] = $this->currentRoute;
        } else {
            $this->virtualRoutes['*'][] = $this->currentRoute;
        }

        return $this;
    }

    /**
     * Adds new get route entry to routes
     * @param string $route
     * @param array $params
     * @return $this
     */
    public function get(string $route, ...$params): self
    {
        return $this->add($route, 'GET', ...$params);
    }

    /**
     * Adds new post route entry to routes
     * @param string $route
     * @param array $params
     * @return $this
     */
    public function post(string $route, ...$params): self
    {
        return $this->add($route, 'POST', ...$params);
    }

    /**
     * Starts a named group of routes
     * @param string $groupName
     * @param Closure $callback
     * @return $this
     */
    public function group(string $groupName, Closure $callback): self
    {
        $this->currentGroupName = $groupName;

        $this->isGroupe = true;
        $this->isGroupeMiddlewares = false;
        $callback($this);
        $this->isGroupeMiddlewares = true;
        $this->currentGroupName = null;

        return $this;
    }

    /**
     * Adds middlewares to routes and route groups
     * @param array $middlewares
     * @return $this
     */
    public function middlewares(array $middlewares = []): self
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
                $this->isGroupe = false;
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

        return $this;
    }

    /**
     * Sets a unique name for a route
     * @param string $name
     * @return $this
     * @throws \Quantum\Exceptions\RouteException
     */
    public function name(string $name): self
    {
        if (empty($this->currentRoute)) {
            throw RouteException::nameBeforeDefinition();
        }

        if ($this->isGroupeMiddlewares) {
            throw RouteException::nameOnGroup();
        }

        foreach ($this->virtualRoutes as &$virtualRoute) {
            foreach ($virtualRoute as &$route) {
                if (isset($route['name']) && $route['name'] == $name) {
                    throw RouteException::nonUniqueName();
                }

                if ($route['route'] == $this->currentRoute['route']) {
                    $route['name'] = $name;
                }
            }
        }

        return $this;
    }

    /**
     * Gets the run-time routes
     * @return array
     */
    public function getRuntimeRoutes(): array
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
     * Gets the virtual routes
     * @return array
     */
    public function getVirtualRoutes(): array
    {
        return $this->virtualRoutes;
    }

}
