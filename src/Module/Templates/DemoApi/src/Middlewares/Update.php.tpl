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
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Update
 * @package Modules\{{MODULE_NAME}}
 */
class Update extends BaseMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
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
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'firstname' => [
                Rule::required()
            ],
            'lastname' => [
                Rule::required()
            ],
        ]);
    }
}


