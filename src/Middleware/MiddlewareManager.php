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
 * @since 1.4.0
 */

namespace Quantum\Middleware;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\RouteException;
use Quantum\Http\Request;

/**
 * MiddlewareManager Class
 *
 * MiddlewareManager class determine the controller, action of current module based on
 * current route
 *
 * @package Quantum
 * @subpackage Middleware
 * @category Middleware
 */
class MiddlewareManager
{

    /**
     * Middlewares queue
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * Current module
     *
     * @var string
     */
    private $module;

    /**
     * MiddlewareManager constructor.
     *
     * @param string $currentRoute
     */
    public function __construct($currentRoute)
    {
        $this->middlewares = $currentRoute['middlewares'];
        $this->module = $currentRoute['module'];
    }

    /**
     * Apply Middlewares
     *
     * @param Request $request
     * @return mixed|Request
     * @throws RouteException
     * @throws \Exception
     */
    public function applyMiddlewares(Request $request)
    {
        $modifiedRequest = $request;

        $middlewarePath = MODULES_DIR . '/' . $this->module . '/Middlewares/' . current($this->middlewares) . '.php';

        if (!file_exists($middlewarePath)) {
            throw new \Exception(_message(ExceptionMessages::MIDDLEWARE_NOT_FOUND, current($this->middlewares)));
        }

        require_once $middlewarePath;

        $middlewareClass = '\\Modules\\' . $this->module . '\\Middlewares\\' . current($this->middlewares);

        if (!class_exists($middlewareClass, FALSE)) {
            throw new RouteException(_message(ExceptionMessages::MIDDLEWARE_NOT_DEFINED, current($this->middlewares)));
        }

        $currentMiddleware = new $middlewareClass();

        if ($currentMiddleware instanceof Qt_Middleware) {
            $modifiedRequest = $currentMiddleware->apply($request, function ($request) {
                next($this->middlewares);
                return $request;
            });

            if (current($this->middlewares)) {
                try {
                    $modifiedRequest = $this->applyMiddlewares($modifiedRequest);
                } catch (\TypeError $ex) {
                    throw new \Exception(_message(ExceptionMessages::MIDDLEWARE_NOT_HANDLED, current($this->middlewares)));
                }
            }
        }

        return $modifiedRequest;
    }

}
