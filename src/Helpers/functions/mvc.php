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
 * @since 2.0.0
 */

use Quantum\Routes\RouteController;

if (!function_exists('current_middlewares')) {

    /**
     * Gets current middlewares
     * @return array|null
     */
    function current_middlewares(): ?array
    {
        return RouteController::getCurrentRoute()['middlewares'] ?? null;
    }

}

if (!function_exists('current_module')) {

    /**
     * Gets current module
     * @return string|null
     */
    function current_module(): ?string
    {
        return RouteController::getCurrentRoute()['module'] ?? null;
    }

}

if (!function_exists('current_controller')) {

    /**
     * Get current controller
     * @return string|null
     */
    function current_controller(): ?string
    {
        return RouteController::getCurrentRoute()['controller'] ?? null;
    }

}

if (!function_exists('current_action')) {

    /**
     * Gets current action
     * @return string|null
     */
    function current_action(): ?string
    {
        return RouteController::getCurrentRoute()['action'] ?? null;
    }

}

if (!function_exists('route_callback')) {

    /**
     * Get current callback
     * @return \Closure $callback|null
     */
    function route_callback(): ?Closure
    {
        return RouteController::getCurrentRoute()['callback'] ?? null;
    }

}

if (!function_exists('current_route')) {

    /**
     * Gets current route
     * @return string|null
     */
    function current_route(): ?string
    {
        return RouteController::getCurrentRoute()['route'] ?? null;
    }

}

if (!function_exists('current_route_args')) {

    /**
     * Gets current route args
     * @return array
     */
    function current_route_args(): array
    {
        return array_values(RouteController::getCurrentRoute()['args']) ?? [];
    }

}

if (!function_exists('current_route_pattern')) {

    /**
     * Gets current route pattern
     * @return string
     */
    function current_route_pattern(): string
    {
        return RouteController::getCurrentRoute()['pattern'] ?? '';
    }

}

if (!function_exists('current_route_method')) {

    /**
     * Gets current route method
     * @return string
     */
    function current_route_method(): string
    {
        return RouteController::getCurrentRoute()['method'] ?? '';
    }

}

if (!function_exists('current_route_uri')) {

    /**
     * Gets the current route uri
     * @return string
     */
    function current_route_uri(): string
    {
        return RouteController::getCurrentRoute()['uri'] ?? '';
    }

}