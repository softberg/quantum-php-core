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
 * Class Forget
 * @package Modules\{{MODULE_NAME}}
 */
class Forget extends BaseMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if ($request->isMethod('post')) {
            $this->validateRequest($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * Define validation rules
     * @param Request $request
     */
    protected function defineValidationRules(Request $request)
    {
        $this->registerCustomRules();

        $this->validator->addRules([
            'email' => [
                Rule::set('required'),
                Rule::set('email'),
                Rule::set('email_exists'),
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(
        Request $request,
        Response $response,
        $message
    )
    {
        $data = $request->all();

        unset($data['image']);

        session()->setFlash('error', $message);
        redirectWith(base_url(true) . '/' . current_lang() . '/forget', $data);
    }

    /**
     * Register custom validation rules
     */
    private function registerCustomRules(): void
    {
        $this->validator->addValidation('email_exists', function ($email) {
            $userModel = ModelFactory::get(User::class);
            return !empty($userModel->findOneBy('email', $email)->asArray());
        });
    }
}