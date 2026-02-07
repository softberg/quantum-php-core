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

/**
 * Class RouteCollection
 * @internal Internal collection of Route descriptors.
 * @package Quantum\Router
 */
final class RouteCollection
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    /**
     * Add a route to the collection.
     * @param Route $route
     * @return void
     */
    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Return all routes in insertion order.
     * @return Route[]
     */
    public function all(): array
    {
        return $this->routes;
    }

    /**
     * Return total number of routes.
     * @return int
     */
    public function count(): int
    {
        return count($this->routes);
    }
}
