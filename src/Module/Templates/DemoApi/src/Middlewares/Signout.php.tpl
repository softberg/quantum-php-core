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
 
namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Signout
 * @package Modules\{{MODULE_NAME}}
 */
class Signout extends BaseMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if (!$request->hasHeader('refresh_token')) {
            $this->respondWithError(
                $request,
                $response,
                [t('validation.nonExistingRecord', 'token')]
            );
        }

        return $next($request, $response);
    }
}