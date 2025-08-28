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

use Quantum\Model\Factories\ModelFactory;
use Quantum\Http\Constants\StatusCode;
use Quantum\Libraries\Validation\Rule;
use Modules\{{MODULE_NAME}}\Models\User;
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
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        $token = (string) route_param('token');

        $request->set('token', $token);

        $this->validateRequest($request, $response);

        $request->set('reset_token', $token);

        return $next($request, $response);
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
    protected function respondWithError(Request $request, Response $response, $message)
    {
        if ($request->isMethod('get') && isset($message['token'])) {
            $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
            stop();
        }

        session()->setFlash('error', $message);
        redirect(get_referrer());
    }
}