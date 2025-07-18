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
use Closure;

/**
 * Class Reset
 * @package Modules\Web
 */
class Reset extends QtMiddleware
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

        $this->validator->addRule('password', [
            Rule::set('required'),
            Rule::set('minLen', 6)
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
        $token = route_param('token');

        if ($token && $request->isMethod('post')) {
            if (!$this->checkToken($token)) {
                session()->setFlash('error', [
                    'password' => [
                        t('validation.nonExistingRecord', 'token')
                    ]
                ]);

                redirect(get_referrer(), StatusCode::UNAUTHORIZED);
            }

            if (!$this->validator->isValid($request->all())) {
                session()->setFlash('error', $this->validator->getErrors());

                redirect(get_referrer(), StatusCode::UNPROCESSABLE_ENTITY);
            }

            if (!$this->confirmPassword($request->get('password'), $request->get('repeat_password'))) {
                session()->setFlash('error', t('validation.nonEqualValues'));

                redirect(get_referrer(), StatusCode::UNPROCESSABLE_ENTITY);
            }
        } elseif ($request->isMethod('get')) {
            if (!$this->checkToken($token)) {
                $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
                stop();
            }
        }

        $request->set('reset_token', $token);

        return $next($request, $response);
    }

    /**
     * Check token
     * @param string $token
     * @return bool
     */
    private function checkToken(string $token): bool
    {
        $userModel = ModelFactory::get(User::class);
        return !empty($userModel->findOneBy('reset_token', $token)->asArray());
    }

    /**
     * Checks the password and repeat password
     * @param string $newPassword
     * @param string $repeatPassword
     * @return bool
     */
    private function confirmPassword(string $newPassword, string $repeatPassword): bool
    {
        return $newPassword == $repeatPassword;
    }
}