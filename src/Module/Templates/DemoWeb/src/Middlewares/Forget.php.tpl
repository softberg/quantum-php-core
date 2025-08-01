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
use Quantum\Model\Factories\ModelFactory;
use Quantum\Http\Constants\StatusCode;
use Quantum\Libraries\Validation\Rule;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Shared\Models\User;
use Exception;
use Closure;

/**
 * Class Forget
 * @package Modules\Web
 */
class Forget extends QtMiddleware
{

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->validator = new Validator();

        $this->validator->addRule('email', [
            Rule::set('required'),
            Rule::set('email')
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if ($request->isMethod('post')) {
            if (!$this->validator->isValid($request->all())) {
                session()->setFlash('error', $this->validator->getErrors());

                redirect(
                    base_url(true) . '/' . current_lang() . '/forget',
                    StatusCode::UNPROCESSABLE_ENTITY
                );
            }

            if (!$this->emailExists($request->get('email'))) {
                session()->setFlash('error', [
                    'email' => [
                        t('validation.nonExistingRecord', $request->get('email'))
                    ]
                ]);

                redirect(
                    base_url(true) . '/' . current_lang() . '/forget',
                    StatusCode::UNPROCESSABLE_ENTITY
                );
            }
        }

        return $next($request, $response);
    }

    /**
     * Check for email existence
     * @param string $email
     * @return bool
     * @throws Exception
     */
    private function emailExists(string $email): bool
    {
        $userModel = ModelFactory::get(User::class);
        return !empty($userModel->findOneBy('email', $email)->asArray());
    }
}