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

use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Exceptions\ControllerException;
use Quantum\Middleware\MiddlewareExecutor;
use Quantum\Di\Exceptions\DiException;
use Quantum\Handlers\ViewCacheHandler;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Request;
use Quantum\Http\Response;
use Quantum\Loader\Loader;
use ReflectionException;
use Quantum\Di\Di;

class RouteDispatcher
{

    /**
     * Handles the incoming HTTP request and generates a response.
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws ControllerException
     * @throws DiException
     * @throws BaseException
     * @throws ConfigException
     * @throws CsrfException
     * @throws DatabaseException
     * @throws CryptorException
     * @throws SessionException
     * @throws ReflectionException
     */
    public static function handle(Request $request, Response $response): void
    {
        list($request, $response) = (new MiddlewareExecutor())->execute($request, $response);

        $viewCacheHandler = new ViewCacheHandler();
        if ($viewCacheHandler->serveCachedView(route_uri(), $response)) {
            return;
        }

        $callback = route_callback();

        if ($callback) {
            call_user_func_array($callback, self::getArgs($callback));
            return;
        }

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

    /**
     * Loads and returns the current route's controller instance.
     * @return RouteController
     * @throws DiException
     * @throws ReflectionException
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
     * Retrieves the current route's action for the controller.
     * @param RouteController $controller
     * @return string|null
     * @throws ControllerException
     */
    private static function getAction(RouteController $controller): ?string
    {
        $action = current_action();

        if (!$action || !method_exists($controller, $action)) {
            throw ControllerException::actionNotDefined($action);
        }

        return $action;
    }

    /**
     * Resolves and returns the arguments for a given callable using dependency injection.
     * @param callable $callable
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    private static function getArgs(callable $callable): array
    {
        return Di::autowire($callable, self::routeParams());
    }

    /**
     * Retrieves the route parameters from the current route.
     * @return array
     */
    private static function routeParams(): array
    {
        return array_map(function ($param) {
            return $param['value'];
        }, route_params());
    }
}
