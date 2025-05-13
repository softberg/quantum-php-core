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
 * @since 2.9.7
 */

namespace Quantum\Router;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;
use Closure;

/**
 * Route Class
 * @package Quantum\Router
 */
class Route
{

    /**
     * Current module name
     * @var string
     */
    private $moduleName;

    /**
     * Module options
     * @var array
     */
    private $moduleOptions;

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
     * @param array $module
     */
    public function __construct(array $module)
    {
        $this->virtualRoutes['*'] = [];
        $this->moduleName = key($module);
        $this->moduleOptions = $module[$this->moduleName];
    }

    /**
     * @param string $route
     * @param string $method
     * @param ...$params
     * @return $this
     */
    public function add(string $route, string $method, ...$params): Route
    {
        $this->currentRoute = [
            'route' => !empty($this->moduleOptions['prefix']) ? $this->moduleOptions['prefix'] . '/' . $route : $route,
            'prefix' => $this->moduleOptions['prefix'] ?? '',
            'method' => $method,
            'module' => $this->moduleName,
        ];

        if (isset($this->moduleOptions['cacheable'])) {
            $this->currentRoute['cache_settings']['shouldCache'] = (bool)$this->moduleOptions['cacheable'];
        }

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
     * @param string $route
     * @param ...$params
     * @return $this
     */
    public function get(string $route, ...$params): Route
    {
        return $this->add($route, 'GET', ...$params);
    }

    /**
     * @param string $route
     * @param ...$params
     * @return $this
     */
    public function post(string $route, ...$params): Route
    {
        return $this->add($route, 'POST', ...$params);
    }

    /**
     * @param string $route
     * @param ...$params
     * @return $this
     */
    public function put(string $route, ...$params): Route
    {
        return $this->add($route, 'PUT', ...$params);
    }

    /**
     * @param string $route
     * @param ...$params
     * @return $this
     */
    public function delete(string $route, ...$params): Route
    {
        return $this->add($route, 'DELETE', ...$params);
    }

    /**
     * Starts a named group of routes
     * @param string $groupName
     * @param Closure $callback
     * @return Route
     */
    public function group(string $groupName, Closure $callback): Route
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
     * @return Route
     */
    public function middlewares(array $middlewares = []): Route
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
     * @param bool $shouldCache
     * @param int|null $ttl
     * @return $this
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws BaseException
     */
    public function cacheable(bool $shouldCache, ?int $ttl = null): Route
    {
        if (empty(session()->getId())) {
            return $this;
        }

        if (!$this->isGroup) {
            end($this->virtualRoutes['*']);
            $lastKey = key($this->virtualRoutes['*']);

            $this->virtualRoutes['*'][$lastKey]['cache_settings']['shouldCache'] = $shouldCache;

            if ($shouldCache && $ttl) {
                $this->virtualRoutes['*'][$lastKey]['cache_settings']['ttl'] = $ttl;
            }

            return $this;
        }

        end($this->virtualRoutes);
        $lastKeyOfFirstRound = key($this->virtualRoutes);

        foreach ($this->virtualRoutes[$lastKeyOfFirstRound] as &$route) {
            $route['cache_settings']['shouldCache'] = $shouldCache;

            if ($shouldCache && $ttl) {
                $route['cache_settings']['ttl'] = $ttl;
            }
        }

        return $this;
    }

    /**
     * Sets a unique name for a route
     * @param string $name
     * @return Route
     * @throws RouteException
     */
    public function name(string $name): Route
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