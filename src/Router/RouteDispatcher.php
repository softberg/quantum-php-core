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

use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class RouteDispatcher
 * @package Quantum\Router
 */
final class RouteDispatcher
{
    /**
     * Dispatch a matched route.
     * @param MatchedRoute $matched
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws ReflectionException|CsrfException|DiException
     */
    public function dispatch(MatchedRoute $matched, Request $request, Response $response): void
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
     * @param Route $route
     * @return array
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
     * @param callable $callable
     * @param array $params
     * @return void
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
     * @param object $controller
     * @param string $hook
     * @param array $params
     * @return void
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
     * @param object|null $controller
     * @param Request $request
     * @return void
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
