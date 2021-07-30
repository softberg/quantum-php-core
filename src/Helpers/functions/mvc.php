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
 * @since 2.5.0
 */

use Quantum\Routes\RouteController;


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
 * @return \Closure $callback|null
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
 * Gets current route args
 * @return array
 */
function current_route_args(): array
{
    return array_values(RouteController::getCurrentRoute()['args']) ?? [];
}

/**
 * Gets current route pattern
 * @return string
 */
function current_route_pattern(): string
{
    return RouteController::getCurrentRoute()['pattern'] ?? '';
}

/**
 * Gets current route method
 * @return string
 */
function current_route_method(): string
{
    return RouteController::getCurrentRoute()['method'] ?? '';
}

/**
 * Gets the current route uri
 * @return string
 */
function current_route_uri(): string
{
    return RouteController::getCurrentRoute()['uri'] ?? '';
}

