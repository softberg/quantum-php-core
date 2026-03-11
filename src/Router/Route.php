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
use Closure;

/**
 * Class Route
 * @internal Framework routing descriptor.
 * @package Quantum\Router
 */
final class Route
{
    protected array $methods;

    protected string $pattern;

    protected ?string $controller;

    protected ?string $action;

    protected ?Closure $closure;

    protected ?string $name = null;

    protected ?string $module = null;

    protected array $middlewares = [];

    protected ?string $group = null;

    protected ?string $prefix = null;

    protected ?array $cache = null;

    protected ?string $compiledPattern = null;

    /**
     * @throws RouteException
     */
    public function __construct(
        array $methods,
        string $pattern,
        ?string $controller,
        ?string $action,
        ?Closure $closure = null
    ) {
        if ($methods === []) {
            throw RouteException::noHttpMethods();
        }

        $this->methods = array_map('strtoupper', $methods);
        $this->pattern = $pattern;

        if ($closure !== null) {
            if ($controller !== null || $action !== null) {
                throw RouteException::closureWithController();
            }
        } else {
            if ($controller === null || $action === null || $action === '') {
                throw RouteException::incompleteControllerRoute();
            }
        }

        $this->controller = $controller;
        $this->action = $action;
        $this->closure = $closure;

    }

    /**
     * Check whether this route is handled by a closure.
     */
    public function isClosure(): bool
    {
        return $this->closure !== null;
    }

    /**
     * Configure response caching settings for this route.
     * @return $this
     */
    public function cache(bool $enabled, ?int $ttl = null): self
    {
        $this->cache = [
            'enabled' => $enabled,
            'ttl' => $ttl,
        ];

        return $this;
    }

    /**
     * Return caching configuration for this route.
     */
    public function getCache(): ?array
    {
        return $this->cache;
    }

    /**
     * Store compiled regex pattern for this route.
     * @return $this
     */
    public function setCompiledPattern(string $pattern): self
    {
        $this->compiledPattern = $pattern;
        return $this;
    }

    /**
     * Return compiled regex pattern if set.
     */
    public function getCompiledPattern(): ?string
    {
        return $this->compiledPattern;
    }

    /**
     * Assign group name to this route.
     * @return $this
     */
    public function group(?string $group): self
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Return group name.
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Return URL prefix.
     * @return $this
     */
    public function prefix(?string $prefix): self
    {
        $this->prefix = $prefix !== '' ? trim($prefix, '/') : null;
        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * Assign unique route name.
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Add middleware(s) with group-aware stacking order.
     * @return $this
     */
    public function addMiddlewares(array $middlewares): self
    {
        if (empty($this->middlewares)) {
            $this->middlewares = $middlewares;
        } else {
            $middlewares = array_reverse($middlewares);

            foreach ($middlewares as $middleware) {
                array_unshift($this->middlewares, $middleware);
            }
        }

        return $this;
    }

    /**
     * Assign module name to this route.
     * @return $this
     */
    public function module(?string $module): self
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Check whether HTTP method is allowed for this route.
     */
    public function allowsMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->methods, true);
    }

    /**
     * Return allowed HTTP methods.
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Return route pattern string.
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Return route closure handler if defined.
     */
    public function getClosure(): ?Closure
    {
        return $this->closure;
    }

    /**
     * Return controller class name.
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * Return controller action name.
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Return route name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Return middleware list.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Return middleware list.
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Export route definition as array.
     */
    public function toArray(): array
    {
        return [
            'methods' => $this->methods,
            'route' => $this->pattern,
            'controller' => $this->controller,
            'action' => $this->action,
            'middlewares' => $this->middlewares,
            'name' => $this->name,
            'module' => $this->module,
            'group' => $this->group,
            'prefix' => $this->prefix,
            'cache' => $this->cache,
        ];
    }
}
