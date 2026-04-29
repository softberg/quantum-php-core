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
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Auth\Exceptions\AuthException;
use {{MODULE_NAMESPACE}}\DTOs\UserDTO;
use {{MODULE_NAMESPACE}}\Enums\Role;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class AuthController
 * @package Modules\{{MODULE_NAME}}
 */
class AuthController extends BaseController
{

    /**
     * Main layout
     */
    protected const LAYOUT = 'layouts/main';

    /**
     * Signin view page
     */
    protected const VIEW_SIGNIN = 'auth/signin';

    /**
     * Signup view page
     */
    protected const VIEW_SIGNUP = 'auth/signup';

    /**
     * Forget view page
     */
    protected const VIEW_FORGET = 'auth/forget';

    /**
     * Reset view page
     */
    protected const VIEW_RESET = 'auth/reset';

    /**
     * Verify view page
     */
    protected const VIEW_VERIFY = 'auth/verify';

    /**
     * Action - sign in
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function signin(Request $request, Response $response): Response
    {
        if ($request->isMethod('post')) {
            try {
                $code = auth()->signin($request->get('email'), $request->get('password'), !!$request->get('remember'));

                if (filter_var(config()->get('auth.two_fa'), FILTER_VALIDATE_BOOLEAN)) {
                    return redirect(base_url(true) . '/' . current_lang() . '/verify/' . $code);
                } else {
                    return redirect(base_url(true) . '/' . current_lang());
                }
            } catch (AuthException $e) {
                session()->setFlash('error', $e->getMessage());
                return redirect(base_url(true) . '/' . current_lang() . '/signin');
            }
        } else {
            $this->view->setParams([
                'title' => t('common.signin') . ' | ' . config()->get('app.name'),
            ]);

            return $response->html($this->view->render(self::VIEW_SIGNIN));
        }
    }

    /**
     * Action - sign out
     * @return Response
     */
    public function signout(): Response
    {
        auth()->signout();
        return redirect(base_url(true) . '/' . current_lang());
    }

    /**
     * Action - sign up
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function signup(Request $request, Response $response): Response
    {
        if ($request->isMethod('post')) {
            $userDto = UserDTO::fromRequest($request, Role::EDITOR, uuid_ordered());

            auth()->signup($userDto->toArray());

            session()->setFlash('success', t('common.check_email_signup'));
            return redirect(base_url(true) . '/' . current_lang() . '/signup');
        } else {
            $this->view->setParams([
//                'captcha' => captcha(),
                'title' => t('common.signup') . ' | ' . config()->get('app.name'),
            ]);

            return $response->html($this->view->render(self::VIEW_SIGNUP));
        }
    }

    /**
     * Action - activate
     * @param Request $request
     * @return Response
     */
    public function activate(Request $request): Response
    {
        auth()->activate($request->get('activation_token'));
        return redirect(base_url(true) . '/' . current_lang() . '/signin');
    }

    /**
     * Action - forget
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function forget(Request $request, Response $response): Response
    {
        if ($request->isMethod('post')) {
            auth()->forget($request->get('email'));
            session()->setFlash('success', t('common.check_email'));
            return redirect(base_url(true) . '/' . current_lang() . '/forget');
        } else {
            $this->view->setParams([
                'title' => t('common.forget_password') . ' | ' . config()->get('app.name'),
            ]);

            return $response->html($this->view->render(self::VIEW_FORGET));
        }
    }

    /**
     * Action - reset
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function reset(Request $request, Response $response): Response
    {
        if ($request->isMethod('post')) {
            auth()->reset($request->get('reset_token'), $request->get('password'));
            return redirect(base_url(true) . '/' . current_lang() . '/signin');
        } else {
            $this->view->setParams([
                'title' => t('common.reset_password') . ' | ' . config()->get('app.name'),
                'reset_token' => $request->get('reset_token')
            ]);

            return $response->html($this->view->render(self::VIEW_RESET));
        }
    }

    /**
     * Action - Verify OTP
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function verify(Request $request, Response $response): Response
    {
        if ($request->isMethod('post')) {
            try {
                auth()->verifyOtp((int)$request->get('otp'), $request->get('code'));
                return redirect(base_url(true) . '/' . current_lang());
            } catch (AuthException $e) {
                session()->setFlash('error', $e->getMessage());
                return redirect(base_url(true) . '/' . current_lang() . '/verify/' . $request->get('code'));
            }
        } else {
            $this->view->setParams([
                'title' => t('common.two_fa') . ' | ' . config()->get('app.name'),
                'code' => route_param('code')
            ]);

            return $response->html($this->view->render(self::VIEW_VERIFY));
        }
    }

    /**
     * Action - Resend OTP
     * @return Response
     */
    public function resend(): Response
    {
        try {
            $otpToken = auth()->resendOtp(route_param('code'));
            return redirect(base_url(true) . '/' . current_lang() . '/verify/' . $otpToken);
        } catch (AuthException $e) {
            return redirect(base_url(true) . '/' . current_lang() . '/signin');
        }
    }
}
