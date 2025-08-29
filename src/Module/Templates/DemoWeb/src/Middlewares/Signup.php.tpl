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
use {{MODULE_NAMESPACE}}\Models\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Signup
 * @package Modules\{{MODULE_NAME}}
 */
class Signup extends BaseMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if ($request->isMethod('post')) {
            $captchaName = captcha()->getName();

            if ($request->has($captchaName . '-response')) {
                $request->set('captcha', $request->get($captchaName . '-response'));
                $request->delete($captchaName . '-response');
            }

            $this->validateRequest($request, $response);

            $request->delete('captcha');
        }

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'email' => [
                Rule::required(),
                Rule::email(),
                Rule::unique(User::class, 'email'),
            ],
            'password' => [
                Rule::required(),
                Rule::minLen(6),
            ],
            'firstname' => [
                Rule::required(),
            ],
            'lastname' => [
                Rule::required(),
            ],
//            'captcha' => [
//                Rule::required(),
//                Rule::captcha(),
//            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message)
    {
        session()->setFlash('error', $message);
        redirectWith(base_url(true) . '/' . current_lang() . '/signup', $request->all());
    }
}