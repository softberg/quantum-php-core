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

use Quantum\Http\Request;

/**
 * Class RouteFinder
 * @internal Resolves an incoming request to a matched route.
 * @package Quantum\Router
 */
final class RouteFinder
{
    /**
     * @var PatternCompiler
     */
    private PatternCompiler $patternCompiler;

    /**
     * @var RouteCollection
     */
    private RouteCollection $routes;

    /**
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->patternCompiler = new PatternCompiler();
        $this->routes = $routes;
    }

    /**
     * Find the first route that matches request method and URI.
     * @param Request $request
     * @return MatchedRoute|null
     * @throws Exceptions\RouteException
     */
    public function find(Request $request): ?MatchedRoute
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes->all() as $route) {
            if (!$route->allowsMethod($method)) {
                continue;
            }

            if (!$this->patternCompiler->match($route, $uri)) {
                continue;
            }

            $params = $this->patternCompiler->getParams();

            return new MatchedRoute($route, $params);
        }

        return null;
    }
}
