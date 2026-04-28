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

use Quantum\Http\Enums\StatusCode;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Auth
 * @package Modules\{{MODULE_NAME}}
 */
class Auth extends BaseMiddleware
{
    
    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return Response
     */
    public function apply(Request $request, Closure $next): Response
    {
        $response = response();
        if (!auth()->check()) {
            $this->respondWithError(
                $request,
                $response,
                t('validation.unauthorizedRequest'),
                StatusCode::UNAUTHORIZED
            );
        }

        return $next($request);
    }
}


