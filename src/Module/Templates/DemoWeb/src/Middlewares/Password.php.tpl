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
 * @since 2.9.9
 */

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Libraries\Validation\Rule;
use Quantum\Libraries\Hasher\Hasher;
use {{MODULE_NAMESPACE}}\Models\User;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Password
 * @package Modules\{{MODULE_NAME}}
 */
class Password extends BaseMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if ($request->isMethod('post')) {
            $this->validateRequest($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->registerCustomRules($request);

        $this->validator->setRules([
            'current_password' => [
                Rule::required(),
                Rule::passwordCheck(),
            ],
            'new_password' => [
                Rule::required(),
                Rule::minLen( 6),
            ],
            'confirm_password' => [
                Rule::required(),
                Rule::same('new_password'),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message)
    {
        session()->setFlash('error', $message);
        redirectWith(base_url(true) . '/' . current_lang() . '/account-settings#account_password', $request->all());
    }

    /**
     * Register custom validation rules
     */
    private function registerCustomRules(Request $request)
    {
        $this->validator->addRule('passwordCheck', function () use ($request) {
            $user = model(User::class)->findOneBy('uuid', auth()->user()->uuid);
            return $user && (new Hasher())->check($request->get('current_password'), $user->password);
        });
    }
}