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
 * @since 3.0.0
 */

namespace Quantum\Router;

use Quantum\Router\Exceptions\RouteException;
use InvalidArgumentException;
use LogicException;
use Closure;

/**
 * Class RouteBuilder
 * @internal Fluent DSL interpreter and route composition engine.
 * @package Quantum\Router
 */
final class RouteBuilder
{
    /**
     * @var RouteCollection
     */
    private RouteCollection $collection;

    /**
     * @var PatternCompiler
     */
    private PatternCompiler $patternCompiler;

    /**
     * @var Route|null
     */
    private ?Route $currentRoute = null;

    /**
     * @var string|null
     */
    private ?string $currentModule = null;

    /**
     * @var string|null
     */
    private ?string $currentPrefix = null;

    /**
     * @var bool
     */
    private bool $inGroup = false;

    /**
     * @var string|null
     */
    private ?string $currentGroupName = null;

    /**
     * @var array
     */
    private array $groupRoutes = [];

    /**
     * @var array
     */
    private array $groupMiddlewares = [];

    /**
     * @var Route[]|null
     */
    private ?array $lastGroupRoutes = null;

    public function __construct()
    {
        $this->patternCompiler = new PatternCompiler();

        $this->collection = new RouteCollection();
    }

    /**
     * Execute DSL and return final RouteCollection
     * @param array $moduleRouteClosures
     * @param array $moduleConfigs
     * @return RouteCollection
     */
    public function build(array $moduleRouteClosures, array $moduleConfigs): RouteCollection
    {
        foreach ($moduleRouteClosures as $module => $closure) {
            if (!$closure instanceof Closure) {
                throw new InvalidArgumentException(
                    "Routes for module {$module} must return a Closure"
                );
            }

            $options = $moduleConfigs[$module] ?? [];

            $this->currentModule = $module;
            $this->currentPrefix = trim((string) ($options['prefix'] ?? ''), '/');

            $closure($this);

            $this->currentModule = null;
            $this->currentPrefix = null;
            $this->currentRoute = null;
        }

        return $this->collection;
    }

    /**
     * Define a route with multiple HTTP methods.
     * @param string $path
     * @param string $methods
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws RouteException
     */
    public function add(string $path, string $methods, $handler, string $action = null): self
    {
        $methodList = array_map('trim', explode('|', $methods));

        return $this->addRoute($methodList, $path, $handler, $action);
    }

    /**
     * Define a GET route.
     * @param string $path
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws RouteException
     */
    public function get(string $path, $handler, string $action = null): self
    {
        return $this->addRoute(['GET'], $path, $handler, $action);
    }

    /**
     * Define a POST route.
     * @param string $path
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws RouteException
     */
    public function post(string $path, $handler, string $action = null): self
    {
        return $this->addRoute(['POST'], $path, $handler, $action);
    }

    /**
     * Define a PUT route.
     * @param string $path
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws RouteException
     */
    public function put(string $path, $handler, string $action = null): self
    {
        return $this->addRoute(['PUT'], $path, $handler, $action);
    }

    /**
     * Define a DELETE route.
     * @param string $path
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws RouteException
     */
    public function delete(string $path, $handler, string $action = null): self
    {
        return $this->addRoute(['DELETE'], $path, $handler, $action);
    }

    /**
     * Group routes under a shared name and configuration.
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    public function group(string $name, callable $callback): self
    {
        if ($this->inGroup) {
            throw new LogicException('Nested route groups are not supported.');
        }

        $this->currentGroupName = $name;
        $this->inGroup = true;
        $this->groupRoutes = [];
        $this->groupMiddlewares = [];

        $callback($this);

        /** @phpstan-ignore-next-line */
        foreach ($this->groupRoutes as $route) {
            $route->addMiddlewares($this->groupMiddlewares);
        }

        $this->lastGroupRoutes = $this->groupRoutes;

        $this->inGroup = false;
        $this->groupRoutes = [];
        $this->groupMiddlewares = [];
        $this->currentGroupName = null;
        $this->currentRoute = null;

        return $this;
    }

    /**
     * Apply middlewares to the current route or group.
     * @param array $middlewares
     * @return $this
     */
    public function middlewares(array $middlewares): self
    {
        if ($this->currentRoute !== null) {
            $this->currentRoute->addMiddlewares($middlewares);
            return $this;
        }

        if ($this->inGroup) {
            $this->groupMiddlewares = array_merge(
                $this->groupMiddlewares,
                $middlewares
            );

            return $this;
        }

        if ($this->lastGroupRoutes !== null) {
            foreach ($this->lastGroupRoutes as $route) {
                $route->addMiddlewares($middlewares);
            }

            $this->lastGroupRoutes = null;
            return $this;
        }

        throw new LogicException(
            'middlewares() must be called inside a group or after a route definition.'
        );
    }

    /**
     * Assign a unique name to the current route.
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        if ($this->currentRoute === null) {
            throw new LogicException('No route defined to name.');
        }
        $currentModule = $this->currentModule;

        foreach ($this->collection->all() as $route) {
            if ($route->getName() === null) {
                continue;
            }

            // Enforce uniqueness only within the same module context.
            if ($route->getName() === $name && $route->getModule() === $currentModule) {
                throw new LogicException("Route name '{$name}' must be unique within module.");
            }
        }

        $this->currentRoute->name($name);
        return $this;
    }

    /**
     * Enable or disable caching for the current route or group.
     * @param bool $enabled
     * @param int|null $ttl
     * @return $this
     */
    public function cacheable(bool $enabled, ?int $ttl = null): self
    {
        if ($this->inGroup) {
            foreach ($this->groupRoutes as $route) {
                $route->cache($enabled, $ttl);
            }

            return $this;
        }

        if ($this->lastGroupRoutes !== null) {
            foreach ($this->lastGroupRoutes as $route) {
                $route->cache($enabled, $ttl);
            }

            $this->lastGroupRoutes = null;
            return $this;
        }

        if ($this->currentRoute !== null) {
            $this->currentRoute->cache($enabled, $ttl);
            return $this;
        }

        throw new LogicException(
            'cacheable() must be called inside a group or after a route definition.'
        );
    }

    /**
     * Create and register a Route instance.
     * @param array $methods
     * @param string $path
     * @param $handler
     * @param string|null $action
     * @return self
     * @throws Exceptions\RouteException
     */
    private function addRoute(
        array $methods,
        string $path,
        $handler,
        ?string $action
    ): self {
        if ($methods === []) {
            throw new InvalidArgumentException('At least one HTTP method is required.');
        }

        $pattern = $this->resolvePath($path);

        if ($handler instanceof Closure) {
            $route = new Route(
                $methods,
                $pattern,
                null,
                null,
                $handler
            );
        } else {
            if (!is_string($handler) || $action === null) {
                throw new InvalidArgumentException(
                    'Controller routes require controller class and action name.'
                );
            }

            if (strpos($handler, '\\') === false) {
                if ($this->currentModule === null) {
                    throw new LogicException(
                        'Cannot resolve controller without module context.'
                    );
                }

                $handler =
                    module_base_namespace()
                    . '\\'
                    . $this->currentModule
                    . '\\Controllers\\'
                    . $handler;
            }

            $route = new Route(
                $methods,
                $pattern,
                $handler,
                $action,
                null
            );
        }

        [$compiled, $params] = $this->patternCompiler->compile($route);

        $route
            ->module($this->currentModule)
            ->prefix($this->currentPrefix)
            ->group($this->currentGroupName)
            ->setCompiledPattern($compiled);

        $this->collection->add($route);
        $this->currentRoute = $route;

        if ($this->inGroup) {
            $this->groupRoutes[] = $route;
        }

        return $this;
    }

    /**
     * Resolve path using the current prefix.
     * @param string $path
     * @return string
     */
    private function resolvePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');

        if ($this->currentPrefix === null || $this->currentPrefix === '') {
            return $path;
        }

        return '/' . trim($this->currentPrefix . $path, '/');
    }

}
