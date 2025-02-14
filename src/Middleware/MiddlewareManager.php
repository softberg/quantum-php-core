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

namespace Quantum\Middleware;

use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

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
     * @throws DiException
     * @throws ReflectionException
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
     * @param Request $request
     * @param Response $response
     * @return QtMiddleware
     * @throws DiException
     * @throws ReflectionException
     */
    private function getMiddleware(Request $request, Response $response): QtMiddleware
    {
        $middlewarePath = modules_dir() . DS . $this->module . DS . 'Middlewares' . DS . current($this->middlewares) . '.php';

        $loader = Di::get(Loader::class);

        return $loader->loadClassFromFile(
            $middlewarePath,
            function () { return MiddlewareException::middlewareNotFound(current($this->middlewares)); },
            function () { return MiddlewareException::notDefined(current($this->middlewares)); },
            [$request, $response]
        );
    }
}