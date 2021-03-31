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
 * @since 1.0.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Exceptions\RouteException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Di\Di;

/**
 * MvcManager Class
 * 
 * @package Quantum
 * @category MVC
 */
class MvcManager
{

    /**
     * @var QtController
     */
    private static $controller;

    /**
     * Run MVC
     * @param Request $request
     * @param Response $response
     * @throws RouteException
     * @throws \ReflectionException
     */
    public static function runMvc(Request $request, Response $response)
    {

        if ($request->getMethod() != 'OPTIONS') {

            if (current_middlewares()) {
                list($request, $response) = (new MiddlewareManager())->applyMiddlewares($request, $response);
            }

            $routeArgs = current_route_args();

            $callback = route_callback();

            if ($callback) {
                call_user_func_array($callback, self::getCallbackArgs($routeArgs, $callback, $request, $response));
            } else {
                self::$controller = self::getController();

                $action = self::getAction();

                if (self::$controller->csrfVerification ?? true) {
                    Csrf::checkToken($request, session());
                }

                if (method_exists(self::$controller, '__before')) {
                    call_user_func_array([self::$controller, '__before'], self::getArgs($routeArgs, '__before', $request, $response));
                }

                call_user_func_array([self::$controller, $action], self::getArgs($routeArgs, $action, $request, $response));

                if (method_exists(self::$controller, '__after')) {
                    call_user_func_array([self::$controller, '__after'], self::getArgs($routeArgs, '__after', $request, $response));
                }
            }
        }
    }

    /**
     * Get Controller
     * @return object
     * @throws RouteException
     */
    private static function getController()
    {
        $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';

        if (!file_exists($controllerPath)) {
            throw new RouteException(_message(ExceptionMessages::CONTROLLER_NOT_FOUND, current_controller()));
        }

        require_once $controllerPath;

        $controllerClass = '\\Modules\\' . current_module() . '\\Controllers\\' . current_controller();

        if (!class_exists($controllerClass, false)) {
            throw new RouteException(_message(ExceptionMessages::CONTROLLER_NOT_DEFINED, current_controller()));
        }

        return new $controllerClass();
    }

    /**
     * Get Action
     * @return string
     * @throws RouteException
     */
    private static function getAction()
    {
        $action = current_action();

        if ($action && !method_exists(self::$controller, $action)) {
            throw new RouteException(_message(ExceptionMessages::ACTION_NOT_DEFINED, $action));
        }

        return $action;
    }

    /**
     * Get Args
     * @param  array $routeArgs
     * @param  string $action
     * @return array
     * @throws \ReflectionException
     */
    private static function getArgs($routeArgs, $action)
    {
        return Di::autowire(get_class(self::$controller) . ':' . $action, $routeArgs);
    }

    /**
     * Get Callback Args
     * @param  array $routeArgs
     * @param  \Closure $callback
     * @return array
     * @throws \ReflectionException
     */
    private static function getCallbackArgs($routeArgs, $callback)
    {
        return Di::autowire($callback, $routeArgs);
    }

}
