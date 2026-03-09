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
    private Route $route;

    private array $params;

    public function __construct(Route $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * Return the original route definition.
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Return parameters extracted from the URI at match time.
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
