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
 * @since 2.9.9
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use {{MODULE_NAMESPACE}}\Enums\Role;

/**
 * Class AuthController
 * @package Modules\{{MODULE_NAME}}
 */
class AuthController extends BaseController
{

    /**
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Signin view page
     */
    const VIEW_SIGNIN = 'auth/signin';

    /**
     * Signup view page
     */
    const VIEW_SIGNUP = 'auth/signup';

    /**
     * Forget view page
     */
    const VIEW_FORGET = 'auth/forget';

    /**
     * Reset view page
     */
    const VIEW_RESET = 'auth/reset';

    /**
     * Verify view page
     */
    const VIEW_VERIFY = 'auth/verify';

    /**
     * Action - sign in
     * @param Request $request
     * @param Response $response
     */
    public function signin(Request $request, Response $response)
    {
        if ($request->isMethod('post')) {
            try {
                $code = auth()->signin($request->get('email'), $request->get('password'), !!$request->get('remember'));

                if (filter_var(config()->get('auth.two_fa'), FILTER_VALIDATE_BOOLEAN)) {
                    redirect(base_url(true) . '/' . current_lang() . '/verify/' . $code);
                } else {
                    redirect(base_url(true) . '/' . current_lang());
                }
            } catch (AuthException $e) {
                session()->setFlash('error', $e->getMessage());
                redirect(base_url(true) . '/' . current_lang() . '/signin');
            }
        } else {
            $this->view->setParams([
                'title' => t('common.signin') . ' | ' . config()->get('app.name'),
            ]);

            $response->html($this->view->render(self::VIEW_SIGNIN));
        }
    }

    /**
     * Action - sign out
     */
    public function signout()
    {
        auth()->signout();
        redirect(base_url(true) . '/' . current_lang());
    }

    /**
     * Action - sign up
     * @param Request $request
     * @param Response $response
     */
    public function signup(Request $request, Response $response)
    {
        if ($request->isMethod('post')) {
            $userData = $request->all();

            $userData['uuid'] =  uuid_ordered();
            $userData['role'] = Role::EDITOR;

            auth()->signup($userData);

            session()->setFlash('success', t('common.check_email_signup'));
            redirect(base_url(true) . '/' . current_lang() . '/signup');
        } else {
            $this->view->setParams([
//                'captcha' => captcha(),
                'title' => t('common.signup') . ' | ' . config()->get('app.name'),
            ]);

            $response->html($this->view->render(self::VIEW_SIGNUP));
        }
    }

    /**
     * Action - activate
     * @param Request $request
     */
    public function activate(Request $request)
    {
        auth()->activate($request->get('activation_token'));
        redirect(base_url(true) . '/' . current_lang() . '/signin');
    }

    /**
     * Action - forget
     * @param Request $request
     * @param Response $response
     */
    public function forget(Request $request, Response $response)
    {
        if ($request->isMethod('post')) {
            auth()->forget($request->get('email'));
            session()->setFlash('success', t('common.check_email'));
            redirect(base_url(true) . '/' . current_lang() . '/forget');
        } else {
            $this->view->setParams([
                'title' => t('common.forget_password') . ' | ' . config()->get('app.name'),
            ]);

            $response->html($this->view->render(self::VIEW_FORGET));
        }
    }

    /**
     * Action - reset
     * @param Request $request
     * @param Response $response
     */
    public function reset(Request $request, Response $response)
    {
        if ($request->isMethod('post')) {
            auth()->reset($request->get('reset_token'), $request->get('password'));
            redirect(base_url(true) . '/' . current_lang() . '/signin');
        } else {
            $this->view->setParams([
                'title' => t('common.reset_password') . ' | ' . config()->get('app.name'),
                'reset_token' => $request->get('reset_token')
            ]);

            $response->html($this->view->render(self::VIEW_RESET));
        }
    }

    /**
     * Action - Verify OTP
     * @param Request $request
     * @param Response $response
     */
    public function verify(Request $request, Response $response)
    {
        if ($request->isMethod('post')) {
            try {
                auth()->verifyOtp((int)$request->get('otp'), $request->get('code'));
                redirect(base_url(true) . '/' . current_lang());
            } catch (AuthException $e) {
                session()->setFlash('error', $e->getMessage());
                redirect(base_url(true) . '/' . current_lang() . '/verify/' . $request->get('code'));
            }
        } else {
            $this->view->setParams([
                'title' => t('common.two_fa') . ' | ' . config()->get('app.name'),
                'code' => route_param('code')
            ]);

            $response->html($this->view->render(self::VIEW_VERIFY));
        }
    }

    /**
     * Action - Resend OTP
     */
    public function resend()
    {
        try {
            $otpToken = auth()->resendOtp(route_param('code'));
            redirect(base_url(true) . '/' . current_lang() . '/verify/' . $otpToken);
        } catch (AuthException $e) {
            redirect(base_url(true) . '/' . current_lang() . '/signin');
        }
    }
}