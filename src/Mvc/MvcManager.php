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
 * @since 2.3.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ControllerException;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Di\Di;
use Closure;

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
     * @param Request $request
     * @param Response $response
     * @throws ControllerException
     * @throws \Quantum\Exceptions\CsrfException
     * @throws \Quantum\Exceptions\MiddlewareException
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
                call_user_func_array($callback, self::getCallbackArgs($callback, $routeArgs));
            } else {
                self::$controller = self::getController();

                $action = self::getAction();

                if (self::$controller->csrfVerification ?? true) {
                    Csrf::checkToken($request, session());
                }

                if (method_exists(self::$controller, '__before')) {
                    call_user_func_array([self::$controller, '__before'], self::getArgs('__before', $routeArgs));
                }

                call_user_func_array([self::$controller, $action], self::getArgs($action, $routeArgs));

                if (method_exists(self::$controller, '__after')) {
                    call_user_func_array([self::$controller, '__after'], self::getArgs('__after', $routeArgs));
                }
            }
        }
    }

    /**
     * Get Controller
     * @return QtController
     * @throws ControllerException
     */
    private static function getController(): QtController
    {
        $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';

        if (!file_exists($controllerPath)) {
            throw new ControllerException(_message(ControllerException::CONTROLLER_NOT_FOUND, current_controller()));
        }

        require_once $controllerPath;

        $controllerClass = '\\Modules\\' . current_module() . '\\Controllers\\' . current_controller();

        if (!class_exists($controllerClass, false)) {
            throw new ControllerException(_message(ControllerException::CONTROLLER_NOT_DEFINED, current_controller()));
        }

        return new $controllerClass();
    }

    /**
     * Get Action
     * @return string
     * @throws ControllerException
     */
    private static function getAction(): ?string
    {
        $action = current_action();

        if ($action && !method_exists(self::$controller, $action)) {
            throw new ControllerException(_message(ControllerException::ACTION_NOT_DEFINED, $action));
        }

        return $action;
    }

    /**
     * Get Args
     * @param string $action
     * @param array $routeArgs
     * @return array
     */
    private static function getArgs(string $action, array $routeArgs): array
    {
        return Di::autowire(get_class(self::$controller) . ':' . $action, $routeArgs);
    }

    /**
     * Get Callback Args
     * @param  Closure $callback
     * @param  array $routeArgs
     * @return array
     */
    private static function getCallbackArgs(Closure $callback, array $routeArgs): array
    {
        return Di::autowire($callback, $routeArgs);
    }

}
