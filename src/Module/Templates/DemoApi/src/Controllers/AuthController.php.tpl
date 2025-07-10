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

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class AuthController
 * @package Modules\Api
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

            if (filter_var(config()->get('TWO_FA'), FILTER_VALIDATE_BOOLEAN)) {
                $response->set('code', $code);
            }

            $response->json([
                'status' => self::STATUS_SUCCESS
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Action - me
     * @param Response $response
     * @throws AuthException
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
     * @throws AuthException
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
            ]);
        }
    }

    /**
     *  Action - sign up
     * @param Request $request
     * @param Response $response
     * @throws AuthException
     */
    public function signup(Request $request, Response $response)
    {
        auth()->signup($request->all());

        $response->json([
            'status' => self::STATUS_SUCCESS,
            'message' => t('common.successfully_signed_up')
        ]);
    }

    /**
     * Action - activate
     * @param Request $request
     * @param Response $response
     * @throws AuthException
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
     * @throws AuthException
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
     * @throws AuthException
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
            ]);
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
            ]);
        }
    }
}