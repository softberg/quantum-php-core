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
use Quantum\Router\MatchedRoute;
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
     * @return array{0: Request, 1: Response}
     * @throws MiddlewareException
     */
    public function applyMiddlewares(Request $request, Response $response): array
    {
        if (!current($this->middlewares)) {
            return [$request, $response];
        }

        $currentMiddleware = $this->getMiddleware($request, $response);

        [$request, $response] = $currentMiddleware->apply($request, $response, function ($request, $response): array {
            next($this->middlewares);
            return [$request, $response];
        });

        return $this->applyMiddlewares($request, $response);
    }

    /**
     * Loads and gets the current middleware instance
     * @throws MiddlewareException
     */
    private function getMiddleware(Request $request, Response $response): QtMiddleware
    {
        $middlewareClass = module_base_namespace() . '\\' . $this->module . '\\Middlewares\\' . current($this->middlewares);

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
