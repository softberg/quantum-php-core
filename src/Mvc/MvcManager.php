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
 * @since 2.8.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ControllerException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Class MvcManager
 * @package Quantum\Mvc
 */
class MvcManager
{

    /**
     * Handles the request
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @throws \Quantum\Exceptions\StopExecutionException
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\MiddlewareException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\SessionException
     * @throws \Quantum\Exceptions\CsrfException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function handle(Request $request, Response $response)
    {
        self::handleCors($response);

        if ($request->getMethod() == 'OPTIONS') {
            stop();
        }

        if (current_middlewares()) {
            list($request, $response) = (new MiddlewareManager())->applyMiddlewares($request, $response);
        }

        $callback = route_callback();

        if ($callback) {
            call_user_func_array($callback, self::getArgs($callback));
        } else {
            $controller = self::getController();
            $action = self::getAction($controller);

            if ($controller->csrfVerification) {
                Csrf::checkToken($request, session());
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
     * Get Controller
     * @return \Quantum\Mvc\QtController
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function getController(): QtController
    {
        $fs = Di::get(FileSystem::class);

        $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';

        if (!$fs->exists($controllerPath)) {
            throw ControllerException::controllerNotFound(current_controller());
        }

        require_once $controllerPath;

        $controllerClass = '\\Modules\\' . current_module() . '\\Controllers\\' . current_controller();

        if (!class_exists($controllerClass, false)) {
            throw ControllerException::controllerNotDefined(current_controller());
        }

        return new $controllerClass();
    }

    /**
     * Get Action
     * @param \Quantum\Mvc\QtController $controller
     * @return string|null
     * @throws \Quantum\Exceptions\ControllerException
     */
    private static function getAction(QtController $controller): ?string
    {
        $action = current_action();

        if ($action && !method_exists($controller, $action)) {
            throw ControllerException::actionNotDefined($action);
        }

        return $action;
    }

    /**
     * Get arguments
     * @param callable $callable
     * @return array
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function getArgs(callable $callable): array
    {
        return Di::autowire($callable, self::routeParams());
    }

    /**
     * Gets the route parameters
     * @return array
     */
    private static function routeParams(): array
    {
        return array_map(function ($param) {
            return $param['value'];
        }, route_params());
    }

    /**
     * Handles CORS
     * @param Response $response
     */
    private static function handleCors(Response $response)
    {
        if (!config()->has('cors')) {
            config()->import(new Setup('config', 'cors'));
        }

        foreach (config()->get('cors') as $key => $value) {
            $response->setHeader($key, $value);
        }
    }

}
