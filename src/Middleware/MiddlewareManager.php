<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Middleware;

use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Router\MatchedRoute;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class MiddlewareManager
 * @package Quantum\Middleware
 */
class MiddlewareManager
{
    /**
     * Middlewares queue
     * @var array<string>
     */
    private array $middlewares = [];

    /**
     * Current module
     */
    private ?string $module;

    public function __construct(MatchedRoute $matchedRoute)
    {
        $route = $matchedRoute->getRoute();

        $this->middlewares = array_values($route->getMiddlewares() ?? []);
        $this->module = $route->getModule();
    }

    /**
     * Apply Middlewares
     * @return Response
     * @throws MiddlewareException|BaseException|ReflectionException
     */
    public function applyMiddlewares(Request $request, Response $response): Response
    {
        if (!current($this->middlewares)) {
            return $response;
        }

        $currentMiddleware = $this->getMiddleware($request, $response);
        next($this->middlewares);

        return $currentMiddleware->apply($request, $response, function (Request $request, Response $response): Response {
            return $this->applyMiddlewares($request, $response);
        });
    }

    /**
     * Loads and gets the current middleware instance
     * @throws MiddlewareException|BaseException|ReflectionException
     */
    private function getMiddleware(Request $request, Response $response): QtMiddleware
    {
        $middlewareClass = request()->getModuleBaseNamespace() . '\\' . $this->module . '\\Middlewares\\' . current($this->middlewares);

        if (!class_exists($middlewareClass)) {
            throw MiddlewareException::middlewareNotFound($middlewareClass);
        }

        $middleware = new $middlewareClass($request, $response);

        if (!$middleware instanceof QtMiddleware) {
            throw MiddlewareException::notInstanceOf($middlewareClass, QtMiddleware::class);
        }

        return $middleware;
    }
}
