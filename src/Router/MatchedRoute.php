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
 * Class MatchedRoute
 * @internal Resolves an incoming request to a matched route.
 * @package Quantum\Router
 */
final class MatchedRoute
{
    /**
     * @var Route
     */
    private Route $route;

    /**
     * @var array
     */
    private array $params;

    /**
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * Return the original route definition.
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Runtime values extracted from URI
     * e.g. ['id' => '42']
     */

    /**
     * Return parameters extracted from the URI at match time.
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
