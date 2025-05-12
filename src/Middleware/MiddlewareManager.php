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

namespace Quantum\Middleware;

use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class MiddlewareManager
 * @package Quantum\Middleware
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
     */
    public function applyMiddlewares(Request $request, Response $response): array
    {
        if (!current($this->middlewares)) {
            return [$request, $response];
        }

        $currentMiddleware = $this->getMiddleware($request, $response);

        list($request, $response) = $currentMiddleware->apply($request, $response, function ($request, $response) {
            next($this->middlewares);
            return [$request, $response];
        });

        return $this->applyMiddlewares($request, $response);
    }

    /**
     * Loads and gets the current middleware instance
     * @param Request $request
     * @param Response $response
     * @return QtMiddleware
     */
    private function getMiddleware(Request $request, Response $response): QtMiddleware
    {
        $middlewareName = current($this->middlewares);

        $middlewarePath = modules_dir() . DS . $this->module . DS . 'Middlewares' . DS . $middlewareName . '.php';

        require_once $middlewarePath;

        $middlewareClass = module_base_namespace() . '\\' . $this->module . '\\Middlewares\\' . $middlewareName;

        return new $middlewareClass($request, $response);
    }
}