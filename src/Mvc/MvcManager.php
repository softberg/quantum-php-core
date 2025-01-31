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

use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Exceptions\ControllerException;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class MvcManager
 * @package Quantum\Mvc
 */
class MvcManager
{

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws ControllerException
     * @throws CryptorException
     * @throws CsrfException
     * @throws DatabaseException
     * @throws DiException
     * @throws LangException
     * @throws MiddlewareException
     * @throws ReflectionException
     * @throws SessionException
     */
    public static function handle(Request $request, Response $response)
    {
        if (current_middlewares()) {
            list($request, $response) = (new MiddlewareManager())->applyMiddlewares($request, $response);
        }

        $callback = route_callback();

        $viewCache = ViewCache::getInstance();

        if ($viewCache->isEnabled() && $viewCache->exists(route_uri())) {
            $content = $viewCache->get(route_uri());
            $response->html($content);
        } elseif ($callback) {
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
     * Get Controller
     * @return QtController
     * @throws ControllerException
     * @throws BaseException
     */
    private static function getController(): QtController
    {
        $fs = FileSystemFactory::get();

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
     * @param QtController $controller
     * @return string|null
     * @throws ControllerException
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
     * @throws DiException
     * @throws ReflectionException
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
}
