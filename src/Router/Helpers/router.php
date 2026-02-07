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

use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\Router\RouteCollection;
use Quantum\Router\Route;
use Quantum\Http\Request;
use Quantum\Di\Di;

/**
 * Gets current route middlewares
 * @return array|null
 * @throws DiException|ReflectionException
 */
function current_middlewares(): ?array
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getMiddlewares() : null;
}

/**
 * Gets current route module
 * @return string|null
 * @throws DiException|ReflectionException
 */
function current_module(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getModule() : null;
}

/**
 * Gets current route controller
 * @return string|null
 * @throws DiException|ReflectionException
 */
function current_controller(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getController() : null;
}

/**
 * Gets current route action
 * @return string|null
 * @throws DiException|ReflectionException
 */
function current_action(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getAction() : null;
}

/**
 * Gets current route callback
 * @return Closure|null
 * @throws DiException|ReflectionException
 */
function route_callback(): ?Closure
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getClosure() : null;
}

/**
 * Gets current route DSL pattern
 * @return string|null
 * @throws DiException|ReflectionException
 */
function current_route(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getPattern() : null;
}

/**
 * Gets current route complied pattern
 * @return string
 * @throws DiException|ReflectionException
 */
function route_pattern(): string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getCompiledPattern() : '';
}

/**
 * Gets current route parameters
 * @return array
 * @throws DiException|ReflectionException
 */
function route_params(): array
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getParams() : [];
}

/**
 * Gets route parameter by name
 * @param string $name
 * @return mixed|null
 * @throws DiException|ReflectionException
 */
function route_param(string $name)
{
    $params = route_params();
    return $params[$name] ?? null;
}

/**
 * Gets current route method
 * @return string
 * @throws DiException|ReflectionException
 */
function route_method(): string
{
    $request = Di::get(Request::class);
    return $request->getMethod();
}

/**
 * Gets the current route uri
 * @return string|null
 * @throws DiException|ReflectionException
 */
function route_uri(): ?string
{
    $request = Di::get(Request::class);
    return $request->getUri();
}

/**
 * Gets the current route cache settings
 * @return array|null
 * @throws DiException|ReflectionException
 */
function route_cache_settings(): ?array
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getCache() : null;
}

/**
 * Gets the current route name
 * @return string|null
 * @throws DiException|ReflectionException
 */
function route_name(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getName() : null;
}

/**
 * Gets the current route name
 * @return string|null
 * @throws DiException|ReflectionException
 */
function route_prefix(): ?string
{
    $request = Di::get(Request::class);
    $matchedRoute = $request->getMatchedRoute();

    return $matchedRoute ? $matchedRoute->getRoute()->getPrefix() : null;
}

/**
 * Finds the route by name in given module scope
 * @param string $name
 * @param string $module
 * @return Route|null
 * @throws DiException|ReflectionException
 */
function find_route_by_name(string $name, string $module): ?Route
{
    if (!Di::isRegistered(RouteCollection::class)) {
        return null;
    }

    $collection = Di::get(RouteCollection::class);

    foreach ($collection->all() as $route) {
        if (
            $route->getName() !== null &&
            strcasecmp($route->getName(), $name) === 0 &&
            strcasecmp((string) $route->getModule(), $module) === 0
        ) {
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
 * @throws DiException|ReflectionException
 */
function route_group_exists(string $name, string $module): bool
{
    if (!Di::isRegistered(RouteCollection::class)) {
        return false;
    }

    $collection = Di::get(RouteCollection::class);

    foreach ($collection->all() as $route) {
        if (
            $route->getGroup() !== null &&
            strcasecmp($route->getGroup(), $name) === 0 &&
            strcasecmp((string) $route->getModule(), $module) === 0
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Gets the module base namespace depending on env
 * @return string
 */
function module_base_namespace(): string
{
    return Environment::getInstance()->getAppEnv() === 'testing'
        ? 'Quantum\\Tests\\_root\\modules'
        : 'Modules';
}
