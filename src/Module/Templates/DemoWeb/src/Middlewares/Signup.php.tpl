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
 * Class Signup
 * @package Modules\Web
 */
class Signup extends QtMiddleware
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

        $this->validator->addValidation('uniqueUser', function ($value) {
            $userModel = ModelFactory::get(User::class);
            return empty($userModel->findOneBy('email', $value)->asArray());
        });

        $this->validator->addRules([
            'email' => [
                Rule::set('required'),
                Rule::set('email'),
                Rule::set('uniqueUser')
            ],
            'password' => [
                Rule::set('required'),
                Rule::set('minLen', 6)
            ],
            'firstname' => [
                Rule::set('required')
            ],
            'lastname' => [
                Rule::set('required')
            ],
//            'captcha' => [
//                Rule::set('required'),
//                Rule::set('captcha')
//            ]
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
            $captchaName = captcha()->getName();

            if($request->has($captchaName . '-response')) {
                $request->set('captcha', $request->get($captchaName . '-response'));
                $request->delete($captchaName . '-response');
            }

            if (!$this->validator->isValid($request->all())) {
                session()->setFlash('error', $this->validator->getErrors());

                redirectWith(
                    base_url(true) . '/' . current_lang() . '/signup',
                    $request->all(),
                    StatusCode::UNPROCESSABLE_ENTITY
                );
            }

            $request->delete('captcha');
        }

        return $next($request, $response);
    }
}