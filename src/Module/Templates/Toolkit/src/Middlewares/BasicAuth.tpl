<?php

namespace Modules\Toolkit\Middlewares;

use Quantum\Loader\Setup;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Editor
 * @package Modules\Web
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

        if (!$userCredentials) {
            $response->setHeader('WWW-Authenticate', 'Basic realm="Quantum Toolkit"');

            $response->html(
                partial('errors' . DS . '401'),
                401
            );

            stop();
        }else{
            if (!config()->has('basic_auth')) {
                config()->import(new Setup('config', 'basic_auth'));
            }

            $configCredentials = config()->get('basic_auth');

            if ($userCredentials['username'] !== $configCredentials['username'] || $userCredentials['password'] !== $configCredentials['password']) {
                $response->setHeader('WWW-Authenticate', 'Basic realm="Quantum Toolkit"');

                $response->html(
                    partial('errors' . DS . '401'),
                    401
                );

                stop();
            }
        }

        return $next($request, $response);
    }

}