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
 * @since 2.9.5
 */
use Quantum\Router\RouteController;
use Quantum\Router\Router;

/**
 * Gets current middlewares
 * @return array|null
 */
function current_middlewares(): ?array
{
    return RouteController::getCurrentRoute()['middlewares'] ?? null;
}

/**
 * Gets current module
 * @return string|null
 */
function current_module(): ?string
{
    return RouteController::getCurrentRoute()['module'] ?? null;
}

/**
 * Get current controller
 * @return string|null
 */
function current_controller(): ?string
{
    return RouteController::getCurrentRoute()['controller'] ?? null;
}

/**
 * Gets current action
 * @return string|null
 */
function current_action(): ?string
{
    return RouteController::getCurrentRoute()['action'] ?? null;
}

/**
 * Get current callback
 * @return Closure $callback|null
 */
function route_callback(): ?Closure
{
    return RouteController::getCurrentRoute()['callback'] ?? null;
}

/**
 * Gets current route
 * @return string|null
 */
function current_route(): ?string
{
    return RouteController::getCurrentRoute()['route'] ?? null;
}

/**
 * Gets current route parameters
 * @return array
 */
function route_params(): array
{
    return RouteController::getCurrentRoute()['params'] ?? [];
}

/**
 * Gets route parameter by name
 * @param string $name
 * @return mixed
 */
function route_param(string $name)
{
    $params = RouteController::getCurrentRoute()['params'];

    if ($params) {
        foreach ($params as $param) {
            if ($param['name'] == $name) {
                return $param['value'];
            }
        }
    }

    return null;
}

/**
 * Gets current route pattern
 * @return string
 */
function route_pattern(): string
{
    return RouteController::getCurrentRoute()['pattern'] ?? '';
}

/**
 * Gets current route method
 * @return string
 */
function route_method(): string
{
    return RouteController::getCurrentRoute()['method'] ?? '';
}

/**
 * Gets the current route uri
 * @return string
 */
function route_uri(): string
{
    return RouteController::getCurrentRoute()['uri'] ?? '';
}

/**
 * Gets the current route cache settings
 * @return array|null
 */
function route_cache_settings(): ?array
{
	return RouteController::getCurrentRoute()['cache_settings'] ?? null;
}

/**
 * Gets the current route name
 * @return string|null
 */
function route_name(): ?string
{
    return RouteController::getCurrentRoute()['name'] ?? null;
}

/**
 * Gets the current route name
 * @return string|null
 */
function route_prefix(): ?string
{
    return RouteController::getCurrentRoute()['prefix'] ?? null;
}

/**
 * Finds the route by name in given module scope
 * @param string $name
 * @param string $module
 * @return array|null
 */
function find_route_by_name(string $name, string $module): ?array
{
    foreach (Router::getRoutes() as $route) {
        if (isset($route['name']) &&
                strtolower($route['name']) == strtolower($name) &&
                strtolower($route['module']) == strtolower($module)) {
            
            return $route;
        }
    }

    return null;
}

/**
 * Checks the existence of the route group by name in given module scope
 * @param string $name
 * @param string $module
 * @return bool
 */
function route_group_exists(string $name, string $module): bool
{
    foreach (Router::getRoutes() as $route) {
        if (isset($route['group']) &&
                strtolower($route['group']) == strtolower($name) &&
                strtolower($route['module']) == strtolower($module)) {

            return true;
        }
    }

    return false;
}