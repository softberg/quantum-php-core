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
use Quantum\Factory\Factory;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Libraries\Session\Session;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Exceptions\RouteException;
use Quantum\Hooks\HookManager;
use Quantum\Http\HttpRequest;
use Quantum\Http\Request;

/**
 * MvcManager Class
 *
 * MvcManager class determine the controller, action of current module based on
 * current route
 *
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
class MvcManager
{
    /**
     * @var object
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * Run MVC
     * @param array $currentRoute
     * @throws RouteException
     * @throws \ReflectionException
     */
    public function runMvc($currentRoute)
    {
        HttpRequest::init();
        $request = new Request();

        if ($request->getMethod() != 'OPTIONS') {

            if (current_middlewares()) {
                $request = (new MiddlewareManager($currentRoute))->applyMiddlewares($request);
            }

            $this->controller = $this->getController();

            $this->action = $this->getAction();

            if ($this->controller->csrfVerification ?? true) {
                Csrf::checkToken($request, \session());
            }

            $routeArgs = current_route_args();

            if (method_exists($this->controller, '__before')) {
                call_user_func_array(array($this->controller, '__before'), $routeArgs);
            }

            call_user_func_array(array($this->controller, $this->action), $this->getArgs($routeArgs, $request));

            if (method_exists($this->controller, '__after')) {
                call_user_func_array(array($this->controller, '__after'), $routeArgs);
            }
        }
    }

    /**
     * Get Controller
     *
     * @return object
     * @throws RouteException
     */
    private function getController()
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
     *
     * @return string
     * @throws RouteException
     */
    private function getAction()
    {
        $action = current_action();

        if (!method_exists($this->controller, $action)) {
            throw new RouteException(_message(ExceptionMessages::ACTION_NOT_DEFINED, $action));
        }

        return $action;
    }

    /**
     * Get Args
     *
     * @param  array $routeArgs
     * @param Request $request
     * @return array
     * @throws \ReflectionException
     */
    private function getArgs($routeArgs, Request $request)
    {
        $args = [];

        $reflaction = new \ReflectionMethod($this->controller, $this->action);
        $params = $reflaction->getParameters();

        foreach ($params as $param) {
            $paramType = $param->getType();
            if ($paramType) {
                switch ($paramType) {
                    case 'Quantum\Http\Request':
                        array_push($args, $request);
                        break;
                    case 'Quantum\Factory\Factory':
                        array_push($args, new Factory());
                        break;
                }
            } else {
                array_push($args, current($routeArgs));
                next($routeArgs);
            }
        }

        return $args;
    }

}
