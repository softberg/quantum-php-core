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
use Quantum\Service\Factories\ServiceFactory;
use {{MODULE_NAMESPACE}}\Services\AuthService;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class AccountController
 * @package Modules\{{MODULE_NAME}}
 */
class AccountController extends BaseController
{
    /**
     * Auth service
     * @var AuthService
     */
    public $authService;
    
    /**
     * Works before an action
     */
    public function __before()
    {
        $this->authService = ServiceFactory::create(AuthService::class);
    }

    /**
     * Action - update user info
     * @param Request $request
     * @param Response $response
     */
    public function update(Request $request, Response $response)
    {
        try {
            $firstname = $request->get('firstname');
            $lastname = $request->get('lastname');

            $this->authService->update('uuid', auth()->user()->uuid, [
                'firstname' => $firstname,
                'lastname' => $lastname
            ]);

            auth()->refreshUser(auth()->user()->uuid);

            $response->json([
                'status' => self::STATUS_SUCCESS,
                'message' => t('common.updated_successfully')
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Action - update password
     * @param Request $request
     * @param Response $response
     */
    public function updatePassword(Request $request, Response $response)
    {
        try {
            $hasher = new Hasher();
    
            $newPassword = $request->get('new_password');
    
            $this->authService->update('uuid', auth()->user()->uuid, [
                'password' => $hasher->hash($newPassword)
            ]);

            $response->json([
                'status' => self::STATUS_SUCCESS,
                'message' => t('common.updated_successfully')
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
}