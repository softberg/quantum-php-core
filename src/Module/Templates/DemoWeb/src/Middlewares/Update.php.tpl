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

use Quantum\Libraries\Validation\Validator;
use Quantum\Http\Constants\StatusCode;
use Quantum\Libraries\Validation\Rule;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Update
 * @package Modules\Web
 */
class Update extends QtMiddleware
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Class constructor
     * @throws \Exception
     */
    public function __construct()
    {
        $this->validator = new Validator();

        $this->validator->addRules([
            'firstname' => [
                Rule::set('required')
            ],
            'lastname' => [
                Rule::set('required')
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if ($request->isMethod('post')) {
            if ($this->validator->isValid($request->all())) {
                session()->setFlash('success', t('common.updated_successfully'));
            } else {
                session()->setFlash('error', $this->validator->getErrors());

                redirectWith(
                    base_url(true) . '/' . current_lang() . '/account-settings#account_profile',
                    $request->all(),
                    StatusCode::UNPROCESSABLE_ENTITY
                );
            }
        }

        return $next($request, $response);
    }
}