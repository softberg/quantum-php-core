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
 * @since 2.0.0
 */

namespace Quantum\Middleware;

use Quantum\Exceptions\MiddlewareException;
use Quantum\Exceptions\ExceptionMessages;
use Quantum\Http\Request;
use Quantum\Http\Response;
use Exception;

/**
 * MiddlewareManager Class
 *
 * MiddlewareManager class determine the controller, action of current module based on
 * current route
 *
 * @package Quantum
 * @category Middleware
 */
class MiddlewareManager
{

    /**
     * Middlewares queue
     * @var array
     */
    private $middlewares = [];

    /**
     * Current module
     * @var string
     */
    private $module;

    /**
     * MiddlewareManager constructor.
     */
    public function __construct()
    {
        $this->middlewares = current_middlewares();
        $this->module = current_module();
    }

    /**
     * Apply Middlewares
     * @param Request $request
     * @param Response $response
     * @return array
     * @throws Exception
     * @throws MiddlewareException
     */
    public function applyMiddlewares(Request $request, Response $response)
    {
        $modifiedRequest = $request;
        $modifiedResponse = $response;

        $middlewarePath = modules_dir() . DS . $this->module . DS . 'Middlewares' . DS . current($this->middlewares) . '.php';

        if (!file_exists($middlewarePath)) {
            throw new MiddlewareException(_message(ExceptionMessages::MIDDLEWARE_NOT_FOUND, current($this->middlewares)));
        }

        require_once $middlewarePath;

        $middlewareClass = '\\Modules\\' . $this->module . '\\Middlewares\\' . current($this->middlewares);

        if (!class_exists($middlewareClass, false)) {
            throw new MiddlewareException(_message(ExceptionMessages::MIDDLEWARE_NOT_DEFINED, current($this->middlewares)));
        }

        $currentMiddleware = new $middlewareClass();

        if ($currentMiddleware instanceof QtMiddleware) {
            list($modifiedRequest, $modifiedResponse) = $currentMiddleware->apply($request, $response, function ($request, $response) {
                next($this->middlewares);
                return [$request, $response];
            });

            if (current($this->middlewares)) {
                try {
                    list($modifiedRequest, $modifiedResponse) = $this->applyMiddlewares($modifiedRequest, $modifiedResponse);
                } catch (\TypeError $ex) {
                    throw new \Exception(_message(ExceptionMessages::MIDDLEWARE_NOT_HANDLED, current($this->middlewares)));
                }
            }
        }

        return [$modifiedRequest, $modifiedResponse];
    }

}
