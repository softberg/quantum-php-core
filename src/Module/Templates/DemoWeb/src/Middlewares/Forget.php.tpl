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

use Quantum\Validation\Rule;
use {{MODULE_NAMESPACE}}\Models\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Forget
 * @package Modules\{{MODULE_NAME}}
 */
class Forget extends BaseMiddleware
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
        if ($request->isMethod('post')) {
            if ($errorResponse = $this->validateRequest($request, $response)) {
                return $errorResponse;
            }
        }

        return $next($request);
    }

    /**
     * Define validation rules
     * @param Request $request
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'email' => [
                Rule::required(),
                Rule::email(),
                Rule::exists(User::class, 'email'),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(
        Request $request,
        Response $response,
        $message
    ): Response
    {
        $data = $request->all();

        unset($data['image']);

        session()->setFlash('error', $message);
        return redirectWith(base_url(true) . '/' . current_lang() . '/forget', $data);
    }
}
