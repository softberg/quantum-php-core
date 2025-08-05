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
use Quantum\Libraries\Validation\Rule;
use Quantum\Libraries\Hasher\Hasher;
use Modules\{{MODULE_NAME}}\Models\User;
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

        $this->validator->addRules([
            'current_password' => [
                Rule::set('required'),
                Rule::set('password_check'),
            ],
            'new_password' => [
                Rule::set('required'),
                Rule::set('minLen', 6),
            ],
            'confirm_password' => [
                Rule::set('required'),
                Rule::set('same', 'new_password'),
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
        $this->validator->addValidation('password_check', function () use ($request) {
            $user = ModelFactory::get(User::class)->findOneBy('uuid', auth()->user()->uuid);
            return $user && (new Hasher())->check($request->get('current_password'), $user->password);
        });
    }
}