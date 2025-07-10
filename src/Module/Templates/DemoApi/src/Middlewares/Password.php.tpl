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
use Quantum\Libraries\Validation\Rule;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Shared\Models\User;
use Closure;

/**
 * Class Password
 * @package Modules\Api
 */
class Password extends QtMiddleware
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Class constructor
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->validator = new Validator();
        $hasher = new Hasher();
        $user = ModelFactory::get(User::class)->findOneBy('uuid', auth()->user()->uuid);
        $currentPassword = $request->get('current_password');

        $this->validator->addValidation('password_check', function ($value) use ($user, $hasher, $currentPassword) {
            return $hasher->check($currentPassword, $user->password);
        });

        $this->validator->addRules([
            'current_password' => [
                Rule::set('required'),
                Rule::set('password_check')
            ],
            'new_password' => [
                Rule::set('required'),
                Rule::set('minLen', 6)
            ],
            'confirm_password' => [
                Rule::set('required'),
            ],
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
            if (!$this->validator->isValid($request->all())) {
                $response->json([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);

                stop();
            }

            if (!$this->confirmPassword($request->get('new_password'), $request->get('confirm_password'))) {
                $response->json([
                    'status' => 'error',
                    'message' => t('validation.same', [t('validation.confirm_password'), t('validation.new_password')])
                ]);

                stop();
            }
        }

        return $next($request, $response);
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