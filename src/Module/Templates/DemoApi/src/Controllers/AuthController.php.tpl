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
use Quantum\Http\Constants\StatusCode;
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
     * Action - sign in
     * @param Request $request
     * @param Response $response
     */
    public function signin(Request $request, Response $response)
    {
        try {
            $code = auth()->signin($request->get('email'), $request->get('password'));

            if (filter_var(config()->get('auth.two_fa'), FILTER_VALIDATE_BOOLEAN)) {
                $response->set('code', $code);
            }

            $response->json([
                'status' => self::STATUS_SUCCESS
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ], StatusCode::UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Action - me
     * @param Response $response
     */
    public function me(Response $response)
    {
        $response->json([
            'status' => self::STATUS_SUCCESS,
            'data' => [
                'firstname' => auth()->user()->firstname,
                'lastname' => auth()->user()->lastname,
                'email' => auth()->user()->email
            ]
        ]);
    }

    /**
     * Action - sign out
     * @param Response $response
     */
    public function signout(Response $response)
    {
        if (auth()->signout()) {
            $response->json([
                'status' => self::STATUS_SUCCESS
            ]);
        } else {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => t('validation.unauthorizedRequest')
            ], StatusCode::UNAUTHORIZED);
        }
    }

    /**
     *  Action - sign up
     * @param Request $request
     * @param Response $response
     */
    public function signup(Request $request, Response $response)
    {
        $userData = $request->all();

        $userData['uuid'] =  uuid_ordered();
        $userData['role'] = Role::EDITOR;

        auth()->signup($userData);

        $response->json([
            'status' => self::STATUS_SUCCESS,
            'message' => t('common.successfully_signed_up')
        ]);
    }

    /**
     * Action - activate
     * @param Request $request
     * @param Response $response
     */
    public function activate(Request $request, Response $response)
    {
        auth()->activate($request->get('activation_token'));

        $response->json([
            'status' => self::STATUS_SUCCESS,
            'message' => t('common.account_activated')
        ]);
    }

    /**
     * Action - forget
     * @param Request $request
     * @param Response $response
     */
    public function forget(Request $request, Response $response)
    {
        auth()->forget($request->get('email'));

        $response->json([
            'status' => self::STATUS_SUCCESS,
            'message' => t('common.check_email')
        ]);
    }

    /**
     * Action - reset
     * @param Request $request
     * @param Response $response
     */
    public function reset(Request $request, Response $response)
    {
        auth()->reset($request->get('reset_token'), $request->get('password'));

        $response->json([
            'status' => self::STATUS_SUCCESS
        ]);
    }

    /**
     * Action - Verify OTP
     * @param Request $request
     * @param Response $response
     */
    public function verify(Request $request, Response $response)
    {
        try {
            auth()->verifyOtp((int)$request->get('otp'), $request->get('code'));

            $response->json([
                'status' => self::STATUS_SUCCESS
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ], StatusCode::UNAUTHORIZED);
        }
    }

    /**
     *  Action - Resend OTP
     * @param Response $response
     */
    public function resend(Response $response)
    {
        try {
            $response->json([
                'status' => self::STATUS_SUCCESS,
                'code' => auth()->resendOtp(route_param('code'))
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ], StatusCode::UNAUTHORIZED);
        }
    }
}