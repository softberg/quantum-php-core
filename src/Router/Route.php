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

use Quantum\Exceptions\RouteException;
use Closure;

/**
 * Route Class
 * Route class allows to add new route entries
 * @package Quantum\Router
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
    private $isGroupMiddlewares;

    /**
     * Identifies the group
     * @var boolean
     */
    private $isGroup = false;

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
            $this->currentRoute['group'] = $this->currentGroupName;
            $this->virtualRoutes[$this->currentGroupName][] = $this->currentRoute;
        } else {
            $this->isGroup = false;
            $this->isGroupMiddlewares = false;
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

        $this->isGroup = true;
        $this->isGroupMiddlewares = false;
        $callback($this);
        $this->isGroupMiddlewares = true;
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
        if (!$this->isGroup) {
            end($this->virtualRoutes['*']);
            $lastKey = key($this->virtualRoutes['*']);
            $this->assignMiddlewaresToRoute($this->virtualRoutes['*'][$lastKey], $middlewares);
            return $this;
        }

        end($this->virtualRoutes);
        $lastKeyOfFirstRound = key($this->virtualRoutes);

        if (!$this->isGroupMiddlewares) {
            end($this->virtualRoutes[$lastKeyOfFirstRound]);
            $lastKeyOfSecondRound = key($this->virtualRoutes[$lastKeyOfFirstRound]);
            $this->assignMiddlewaresToRoute($this->virtualRoutes[$lastKeyOfFirstRound][$lastKeyOfSecondRound], $middlewares);
            return $this;
        }

        foreach ($this->virtualRoutes[$lastKeyOfFirstRound] as &$route) {
            $this->assignMiddlewaresToRoute($route, $middlewares);
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

        if ($this->isGroupMiddlewares) {
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

    /**
     * Assigns middlewares to the route
     * @param array $route
     * @param array $middlewares
     */
    private function assignMiddlewaresToRoute(array &$route, array $middlewares)
    {
        if (!key_exists('middlewares', $route)) {
            $route['middlewares'] = $middlewares;
        } else {
            $middlewares = array_reverse($middlewares);

            foreach ($middlewares as $middleware) {
                array_unshift($route['middlewares'], $middleware);
            }
        }
    }

}
