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
 * Class Verify
 * @package Modules\{{MODULE_NAME}}
 */
class Verify extends BaseMiddleware
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
        $code = (string) route_param('code');

        $request->set('code', $code);

        if ($errorResponse = $this->validateRequest($request, $response)) {
            return $errorResponse;
        }

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'otp' => [
                Rule::required()
            ],
            'code' => [
                Rule::required(),
                Rule::exists(User::class, 'otp_token'),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message): Response
    {
        if ($request->isMethod('get') && isset($message['code'])) {
            return $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
        }

        session()->setFlash('error', $message);
        return redirectWith(base_url(true) . '/' . current_lang() . '/verify', $request->all());
    }
}
