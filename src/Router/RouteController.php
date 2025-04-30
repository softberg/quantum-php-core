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

use Quantum\Exceptions\ControllerException;

/**
 * RouterController Class
 * @package Quantum\Router
 */
abstract class RouteController
{

    /**
     * List of routes
     * @var array
     */
    protected static $routes = [];

    /**
     * Contains current route information
     * @var array
     */
    protected static $currentRoute = null;

    /**
     * @var bool
     */
    public $csrfVerification = true;

    /**
     * Gets the current route
     * @return array
     */
    public static function getCurrentRoute(): ?array
    {
        return self::$currentRoute;
    }

    /**
     * @param array $route
     */
    public static function setCurrentRoute(array $route)
    {
        self::$currentRoute = $route;
    }

    /**
     * Set Routes
     * @param array $routes
     */
    public static function setRoutes(array $routes)
    {
        static::$routes = $routes;
    }

    /**
     * Get Routes
     * @return array
     */
    public static function getRoutes(): array
    {
        return static::$routes;
    }

    /**
     * Handles the missing methods of the controller
     * @param string $method
     * @param array $arguments
     * @throws ControllerException
     */
    public function __call(string $method, array $arguments)
    {
        throw ControllerException::undefinedMethod($method);
    }
}
