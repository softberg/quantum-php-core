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
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ModelFactory;
use Quantum\Factory\ViewFactory;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
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
     * @param Request $request
     * @param Response $response
     * @throws RouteException
     * @throws \ReflectionException
     */
    public function runMvc(Request $request, Response $response)
    {

        if ($request->getMethod() != 'OPTIONS') {

            if (current_middlewares()) {
                list($request, $response) = (new MiddlewareManager())->applyMiddlewares($request, $response);
            }

            $routeArgs = current_route_args();

            $callback = route_callback();

            if ($callback){
                call_user_func_array($callback, $this->getCallbackArgs($routeArgs, $callback, $request, $response));
            } else {

                $this->controller = $this->getController();

                $this->action = $this->getAction();

                if ($this->controller->csrfVerification ?? true) {
                    Csrf::checkToken($request, \session());
                }

                if (method_exists($this->controller, '__before')) {
                    call_user_func_array(array($this->controller, '__before'), $this->getArgs($routeArgs, '__before', $request, $response));
                }

                call_user_func_array(array($this->controller, $this->action), $this->getArgs($routeArgs, $this->action, $request, $response));

                if (method_exists($this->controller, '__after')) {
                    call_user_func_array(array($this->controller, '__after'), $this->getArgs($routeArgs, '__after', $request, $response));
                }
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
     * @param  array $routeArgs
     * @param  string $action
     * @param Request $request
     * @param Response $response
     * @return array
     * @throws \ReflectionException
     */
    private function getArgs($routeArgs, $action, Request $request, Response $response)
    {
        $reflaction = new \ReflectionMethod($this->controller, $action);
        $params = $reflaction->getParameters();

        return $this->collectParams($routeArgs, $params, $request, $response);
    }

    /**
     * Get Callback Args
     *
     * @param  array $routeArgs
     * @param  \Closure $callback
     * @param Request $request
     * @param Response $response
     * @return array
     * @throws \ReflectionFunction
     */
    private function getCallbackArgs($routeArgs, $callback, Request $request, Response $response) {

        $reflaction = new \ReflectionFunction($callback);
        $params = $reflaction->getParameters();

        return $this->collectParams($routeArgs, $params, $request, $response);
    }

    /**
     * Collect Params
     *
     * @param  array $routeArgs
     * @param  array $params
     * @param Request $request
     * @param Response $response
     * @return array
     */
    private function collectParams($routeArgs, $params, Request $request, Response $response)
    {
        $args = [];

        if (count($params)) {
            foreach ($params as $param) {
                $paramType = $param->getType();

                if ($paramType) {
                    switch ($paramType) {
                        case Request::class:
                            array_push($args, $request);
                            break;
                        case Response::class:
                            array_push($args, $response);
                            break;
                        case ServiceFactory::class:
                            array_push($args, new ServiceFactory());
                            break;
                        case ModelFactory::class:
                            array_push($args, new ModelFactory());
                            break;
                        case ViewFactory::class:
                            array_push($args, new ViewFactory());
                            break;
                        case Mailer::class:
                            array_push($args, new Mailer());
                            break;
                        case Loader::class:
                            array_push($args, new Loader());
                            break;
                        default :
                            array_push($args, current($routeArgs));
                            next($routeArgs);
                    }
                } else {
                    array_push($args, current($routeArgs));
                    next($routeArgs);
                }
            }
        }

        return $args;
    }

}
