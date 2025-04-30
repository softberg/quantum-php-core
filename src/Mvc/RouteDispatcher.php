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

namespace Quantum\Mvc;

use Quantum\Exceptions\ControllerException;
use Quantum\Middleware\MiddlewareExecutor;
use Quantum\Handlers\ViewCacheHandler;
use Quantum\Router\RouteController;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Loader\Loader;
use Quantum\Http\Request;
use Quantum\Di\Di;

class RouteDispatcher
{
    /**
     * Handles the incoming HTTP request and generates a response.
     *
     * This includes:
     * 1. Executing registered middleware.
     * 2. Attempting to serve a cached view.
     * 3. Handling routing through either a route callback or a controller action.
     *
     * Controller lifecycle hooks `__before` and `__after` will be invoked if defined.
     * CSRF token verification is performed for applicable HTTP methods.
     *
     * @param Request  $request  The incoming HTTP request object.
     * @param Response $response The HTTP response object to be sent.
     *
     * @return void
     */
    public static function handle(Request $request, Response $response): void
    {
        // 1. Apply middleware
        [$request, $response] = (new MiddlewareExecutor())->execute($request, $response);

        // 2. Try serving from view cache
        $viewCacheHandler = new ViewCacheHandler();
        if ($viewCacheHandler->serveCachedView(route_uri(), $response)) {
            return;
        }

        // 3. Route callback or controller handling
        $callback = route_callback();

        if ($callback) {
            call_user_func_array($callback, self::getArgs($callback));
        } else {
            $controller = self::getController();
            $action = self::getAction($controller);

            if ($controller->csrfVerification && in_array($request->getMethod(), Csrf::METHODS)) {
                csrf()->checkToken($request);
            }

            if (method_exists($controller, '__before')) {
                call_user_func_array([$controller, '__before'], self::getArgs([$controller, '__before']));
            }

            call_user_func_array([$controller, $action], self::getArgs([$controller, $action]));

            if (method_exists($controller, '__after')) {
                call_user_func_array([$controller, '__after'], self::getArgs([$controller, '__after']));
            }
        }
    }

    /**
     * Loads and returns the current route's controller instance.
     *
     * Will throw a ControllerException if the controller is not found or not defined properly.
     *
     * @return RouteController The loaded controller instance.
     *
     * @throws ControllerException
     */
    private static function getController(): RouteController
    {
        $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';

        $loader = Di::get(Loader::class);

        return $loader->loadClassFromFile(
            $controllerPath,
            function () {
                return ControllerException::controllerNotFound(current_controller());
            },
            function () {
                return ControllerException::controllerNotDefined(current_controller());
            }
        );
    }

    /**
     * Retrieves the current route's action (method name) for the controller.
     *
     * @param RouteController $controller The controller instance to check against.
     *
     * @return string|null The action method name, or null if none is defined.
     *
     * @throws ControllerException If the action method does not exist in the controller.
     */

    private static function getAction(RouteController $controller): ?string
    {
        $action = current_action();

        if ($action && !method_exists($controller, $action)) {
            throw ControllerException::actionNotDefined($action);
        }

        return $action;
    }

    /**
     * Resolves and returns the arguments for a given callable using dependency injection.
     *
     * @param callable $callable The function or method to be invoked.
     *
     * @return array The resolved arguments for the callable.
     */
    private static function getArgs(callable $callable): array
    {
        return Di::autowire($callable, self::routeParams());
    }

    /**
     * Retrieves the route parameters from the current route.
     *
     * @return array An array of parameter values for the current route.
     */
    private static function routeParams(): array
    {
        return array_map(function ($param) {
            return $param['value'];
        }, route_params());
    }
}
