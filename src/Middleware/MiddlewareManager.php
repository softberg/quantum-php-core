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
use Closure;

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
     * Apply Middlewares.
     * @throws MiddlewareException|BaseException|ReflectionException
     */
    public function applyMiddlewares(Request $request, Closure $terminal): Response
    {
        if (!current($this->middlewares)) {
            return $terminal($request);
        }

        $currentMiddleware = $this->getMiddleware($request);
        next($this->middlewares);

        return $currentMiddleware->apply($request, fn (Request $request): Response => $this->applyMiddlewares($request, $terminal));
    }

    /**
     * Loads and gets the current middleware instance
     * @throws MiddlewareException|BaseException|ReflectionException
     */
    private function getMiddleware(Request $request): QtMiddleware
    {
        $middlewareClass = request()->getModuleBaseNamespace() . '\\' . $this->module . '\\Middlewares\\' . current($this->middlewares);

        if (!class_exists($middlewareClass)) {
            throw MiddlewareException::middlewareNotFound($middlewareClass);
        }

        $middleware = new $middlewareClass($request);

        if (!$middleware instanceof QtMiddleware) {
            throw MiddlewareException::notInstanceOf($middlewareClass, QtMiddleware::class);
        }

        return $middleware;
    }

}
