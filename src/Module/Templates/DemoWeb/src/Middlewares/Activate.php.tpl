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

use Quantum\Libraries\Validation\Rule;
use Quantum\Http\Enums\StatusCode;
use {{MODULE_NAMESPACE}}\Models\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Activate
 * @package Modules\{{MODULE_NAME}}
 */
class Activate extends BaseMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        $token = (string)route_param('token');

        $request->set('token', $token);

        $this->validateRequest($request, $response);

        $request->set('activation_token', $token);

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'token' => [
                Rule::required(),
                Rule::exists(User::class, 'activation_token'),
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message)
    {
        $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
        stop();
    }
}