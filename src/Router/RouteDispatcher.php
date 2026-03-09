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

use Quantum\Router\Exceptions\RouteException;
use Quantum\Csrf\Exceptions\CsrfException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Csrf\Csrf;
use Quantum\Di\Di;

/**
 * Class RouteDispatcher
 * @package Quantum\Router
 */
final class RouteDispatcher
{
    /**
     * Dispatch a matched route.
     * @throws ReflectionException|CsrfException|DiException|RouteException
     */
    public function dispatch(MatchedRoute $matched, Request $request): void
    {
        $route = $matched->getRoute();
        $params = $matched->getParams();

        if ($route->isClosure()) {
            $closure = $route->getClosure();

            if ($closure === null) {
                throw RouteException::closureHandlerMissing();
            }

            $this->invoke($closure, $params);
            return;
        }

        $callable = $this->resolveControllerCallable($route);
        [$controller] = $callable;

        $this->verifyCsrf($controller, $request);

        $this->callHook($controller, '__before', $params);

        $this->invoke($callable, $params);

        $this->callHook($controller, '__after', $params);
    }

    /**
     * Resolve controller callable from the route definition.
     * @throws RouteException
     */
    private function resolveControllerCallable(Route $route): array
    {
        $controllerClass = $route->getController();
        $action = $route->getAction();

        if ($controllerClass === null || $action === null) {
            throw RouteException::incompleteControllerRoute();
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw RouteException::actionNotFound($controllerClass, $action);
        }

        return [$controller, $action];
    }

    /**
     * Invoke a callable with parameters resolved via DI autowiring.
     * @throws DiException|ReflectionException
     */
    private function invoke(callable $callable, array $params): void
    {
        call_user_func_array(
            $callable,
            Di::autowire($callable, $params)
        );
    }

    /**
     * Invoke a controller lifecycle hook if it exists.
     * @throws DiException|ReflectionException
     */
    private function callHook(object $controller, string $hook, array $params): void
    {
        if (method_exists($controller, $hook)) {
            $this->invoke([$controller, $hook], $params);
        }
    }

    /**
     * Verify CSRF token if controller requires it and request method applies.
     * @throws CsrfException
     */
    private function verifyCsrf(?object $controller, Request $request): void
    {
        if (
            $controller !== null &&
            property_exists($controller, 'csrfVerification') &&
            $controller->csrfVerification === true &&
            in_array($request->getMethod(), Csrf::METHODS, true)
        ) {
            csrf()->checkToken($request);
        }
    }
}
