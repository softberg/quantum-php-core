<?php

namespace Quantum\Middleware;

use Quantum\Http\Response;
use Quantum\Http\Request;

class MiddlewareExecutor
{
    /**
     * Executes middleware if any are registered, otherwise returns the original request and response.
     *
     * @param Request $request The incoming HTTP request object.
     * @param Response $response The outgoing HTTP response object.
     * 
     * @return array An array containing the (possibly modified) Request and Response objects.
     */
    public function execute(Request $request, Response $response): array
    {
        if (current_middlewares()) {
            return (new MiddlewareManager())->applyMiddlewares($request, $response);
        }

        return [$request, $response];
    }
}
