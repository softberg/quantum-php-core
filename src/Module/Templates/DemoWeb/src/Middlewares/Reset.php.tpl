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
use Quantum\Validation\Rule;
use {{MODULE_NAMESPACE}}\Models\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Reset
 * @package Modules\{{MODULE_NAME}}
 */
class Reset extends BaseMiddleware
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
        $token = (string) route_param('token');

        $request->set('token', $token);

        if ($errorResponse = $this->validateRequest($request, $response)) {
            return $errorResponse;
        }

        $request->set('reset_token', $token);

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request): void
    {
        if ($request->isMethod('post')) {
            $this->validator->setRules([
                'password' => [
                    Rule::required(),
                    Rule::minLen(6),
                ],
                'repeat_password' => [
                    Rule::required(),
                    Rule::minLen(6),
                    Rule::same('password'),
                ],
            ]);
        }

        $this->validator->setRules([
            'token' => [
                Rule::required(),
                Rule::exists(User::class, 'reset_token'),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message): Response
    {
        if ($request->isMethod('get') && isset($message['token'])) {
            return $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
        }

        session()->setFlash('error', $message);
        return redirect(get_referrer() ?? base_url());
    }
}


