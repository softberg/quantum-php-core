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
        $this->validateRequest($request, $response);

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
                Rule::set('minLen', 6),
            ],
            'firstname' => [
                Rule::required(),
            ],
            'lastname' => [
                Rule::required(),
            ],
        ]);
    }
}