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
        $this->registerCustomRules();

        $this->validator->addRules([
            'password' => [
                Rule::set('required'),
                Rule::set('minLen', 6),
            ],
            'repeat_password' => [
                Rule::set('required'),
                Rule::set('minLen', 6),
                Rule::set('same', 'password'),
            ],
            'token' => [
                Rule::set('required'),
                Rule::set('token_exists'),
            ],
        ]);
    }

    /**
     * Registers custom validation rules
     */
    private function registerCustomRules(Request $request): void
    {
        $this->validator->addValidation('token_exists', function ($token) {
            $userModel = ModelFactory::get(User::class)->findOneBy('reset_token', $token);
            return $userModel && !$userModel->isEmpty();
        });
    }
}