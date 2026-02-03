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

use Closure;

/**
 * Class Route
 * @internal Framework routing descriptor.
 * @package Quantum\Router
 */
final class Route
{
    /**
     * @var array
     */
    protected array $methods;

    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @var string|null
     */
    protected ?string $controller;

    /**
     * @var string|null
     */
    protected ?string $action;

    /**
     * @var Closure|null
     */
    protected ?Closure $closure;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string|null
     */
    protected ?string $module = null;

    /**
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var string|null
     */
    protected ?string $group = null;

    /**
     * @var string|null
     */
    protected ?string $prefix = null;

    /**
     * @var array|null
     */
    protected ?array $cache = null;

    /**
     * @var string|null
     */
    protected ?string $compiledPattern = null;

    /**
     * @param array $methods
     * @param string $pattern
     * @param string|null $controller
     * @param string|null $action
     * @param Closure|null $closure
     */
    public function __construct(
        array $methods,
        string $pattern,
        ?string $controller,
        ?string $action,
        Closure $closure = null
    ) {
        if ($methods === []) {
            throw new \InvalidArgumentException('Route must define at least one HTTP method.');
        }

        $this->methods = array_map('strtoupper', $methods);
        $this->pattern = $pattern;

        if ($closure !== null) {
            if ($controller !== null || $action !== null) {
                throw new \InvalidArgumentException(
                    'Closure route cannot define controller or action.'
                );
            }
        } else {
            if ($controller === null || $action === null || $action === '') {
                throw new \InvalidArgumentException(
                    'Controller route must define non-empty controller and action.'
                );
            }
        }

        $this->controller = $controller;
        $this->action = $action;
        $this->closure = $closure;

    }

    /**
     * Check whether this route is handled by a closure.
     * @return bool
     */
    public function isClosure(): bool
    {
        return $this->closure !== null;
    }

    /**
     * Configure response caching settings for this route.
     * @param bool $enabled
     * @param int|null $ttl
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
     * @return array|null
     */
    public function getCache(): ?array
    {
        return $this->cache;
    }

    /**
     * Store compiled regex pattern for this route.
     * @param string $pattern
     * @return $this
     */
    public function setCompiledPattern(string $pattern): self
    {
        $this->compiledPattern = $pattern;
        return $this;
    }

    /**
     * Return compiled regex pattern if set.
     * @return string|null
     */
    public function getCompiledPattern(): ?string
    {
        return $this->compiledPattern;
    }

    /**
     * Assign group name to this route.
     * @param string|null $group
     * @return $this
     */
    public function group(?string $group): self
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Return group name.
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Return URL prefix.
     * @param string|null $prefix
     * @return $this
     */
    public function prefix(?string $prefix): self
    {
        $this->prefix = $prefix !== '' ? trim($prefix, '/') : null;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * Assign unique route name.
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Add middleware(s) with group-aware stacking order.
     * @param array $middlewares
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
     * @param string|null $module
     * @return $this
     */
    public function module(?string $module): self
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Check whether HTTP method is allowed for this route.
     * @param string $method
     * @return bool
     */
    public function allowsMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->methods, true);
    }

    /**
     * Return allowed HTTP methods.
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Return route pattern string.
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Return route closure handler if defined.
     * @return Closure|null
     */
    public function getClosure(): ?Closure
    {
        return $this->closure;
    }

    /**
     * Return controller class name.
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * Return controller action name.
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Return route name.
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Return middleware list.
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Return middleware list.
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Export route definition as array.
     * @return array
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
