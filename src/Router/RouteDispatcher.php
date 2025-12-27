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
 * @since 2.9.9
 */

namespace Quantum\Router;

use Quantum\Router\Exceptions\RouteControllerException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

class RouteDispatcher
{

    /**
     * Handles the incoming HTTP request.
     * @param Request $request
     * @return void
     * @throws RouteControllerException
     * @throws CsrfException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function handle(Request $request): void
    {
        $callback = route_callback();

        if ($callback instanceof \Closure) {
            self::callControllerMethod($callback);
            return;
        }

        $controller = self::resolveController();
        $action = self::resolveAction($controller);

        self::verifyCsrf($controller, $request);

        self::callControllerHook($controller, '__before');
        self::callControllerMethod([$controller, $action]);
        self::callControllerHook($controller, '__after');
    }

    /**
     * Loads and gets the current route's controller instance.
     * @return RouteController
     * @throws RouteControllerException
     */
    private static function resolveController(): RouteController
    {
        $controllerClass = module_base_namespace() . '\\' . current_module() . '\\Controllers\\' . current_controller();

        if (!class_exists($controllerClass)) {
            throw RouteControllerException::controllerNotDefined($controllerClass);
        }

        return new $controllerClass();
    }

    /**
     * Retrieves the current route's action for the controller.
     * @param RouteController $controller
     * @return string|null
     * @throws RouteControllerException
     */
    private static function resolveAction(RouteController $controller): ?string
    {
        $action = current_action();

        if (!$action || !method_exists($controller, $action)) {
            throw RouteControllerException::actionNotDefined($action);
        }

        return $action;
    }

    /**
     * Calls controller method
     * @param callable $callable
     * @return void
     * @throws DiException
     * @throws ReflectionException
     */
    private static function callControllerMethod(callable $callable)
    {
        call_user_func_array($callable, Di::autowire($callable, self::getRouteParams()));
    }

    /**
     * Calls controller lifecycle method if it exists.
     * @param object $controller
     * @param string $method
     * @return void
     * @throws DiException
     * @throws ReflectionException
     */
    private static function callControllerHook(object $controller, string $method): void
    {
        if (method_exists($controller, $method)) {
            self::callControllerMethod([$controller, $method]);
        }
    }

    /**
     * Retrieves the route parameters from the current route.
     * @return array
     */
    private static function getRouteParams(): array
    {
        return array_column(route_params(), 'value');
    }

    /**
     * Verifies CSRF token if required
     * @param RouteController $controller
     * @param Request $request
     * @return void
     * @throws CsrfException
     */
    private static function verifyCsrf(RouteController $controller, Request $request): void
    {
        if ($controller->csrfVerification && in_array($request->getMethod(), Csrf::METHODS, true)) {
            csrf()->checkToken($request);
        }
    }
}
