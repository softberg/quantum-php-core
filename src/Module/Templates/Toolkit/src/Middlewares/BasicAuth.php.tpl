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
 * @since 2.9.8
 */

namespace Modules\Toolkit\Middlewares;

use Quantum\Middleware\QtMiddleware;
use Quantum\Loader\Setup;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class BasicAuth
 * @package Modules\Toolkit
 */
class BasicAuth extends QtMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        $userCredentials = $request->getBasicAuthCredentials();

        if (!$userCredentials || !$this->isValidCredentials($userCredentials)) {
            $this->unauthorizedResponse($response);
        }

        return $next($request, $response);
    }

    /**
     * @param array $credentials
     * @return bool
     */
    private function isValidCredentials(array $credentials): bool
    {
        if (!config()->has('basic_auth')) {
            config()->import(new Setup('config', 'basic_auth'));
        }

        $configCredentials = config()->get('basic_auth');

        return $credentials['username'] === $configCredentials['username']
            && $credentials['password'] === $configCredentials['password'];
    }

    /**
     * @param Response $response
     */
    private function unauthorizedResponse(Response $response): void
    {
        $response->setHeader('WWW-Authenticate', 'Basic realm="Quantum Toolkit"');
        $response->html(partial('errors' . DS . '401'), 401);
        stop();
    }
}