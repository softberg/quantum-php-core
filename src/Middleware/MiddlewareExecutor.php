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

use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

class MiddlewareExecutor
{

    /**
     * Executes middleware if any are registered, otherwise returns the original request and response.
     * @param Request $request
     * @param Response $response
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    public function execute(Request $request, Response $response): array
    {
        if (current_middlewares()) {
            return (new MiddlewareManager())->applyMiddlewares($request, $response);
        }

        return [$request, $response];
    }
}
