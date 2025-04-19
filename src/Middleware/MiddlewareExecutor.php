<?php

namespace Quantum\Middleware;

use Quantum\Http\Request;
use Quantum\Http\Response;

class MiddlewareExecutor
{
    public function execute(Request $request, Response $response): array
    {
        if (current_middlewares()) {
            return (new MiddlewareManager())->applyMiddlewares($request, $response);
        }

        return [$request, $response];
    }
}