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
 * @since 2.9.0
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Libraries\Hasher\Hasher;
use Quantum\Factory\ServiceFactory;
use Shared\Services\AccountService;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class AccountController
 * @package Modules\Api\Controllers
 */
class AccountController extends BaseController
{
    /**
     * Account service
     * @var AccountService
     */
    public $accountService;
    
    /**
     * Works before an action
     */
    public function __before()
    {
        $this->accountService = ServiceFactory::get(AccountService::class);
    }

    /**
     * Action - update user info
     * @param Request $request
     * @param Response $response
     * @throws AuthException
     */
    public function update(Request $request, Response $response)
    {
        try {
            $firstname = $request->get('firstname', null);
            $lastname = $request->get('lastname', null);
    
            $this->accountService->update(auth()->user()->uuid, [
                'firstname' => $firstname,
                'lastname' => $lastname
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => t('exception.execution_terminated')
            ]);
        }
    }

    /**
     * Action - update password
     * @param Request $request
     * @param Response $response
     * @throws AuthException
     */
    public function updatePassword(Request $request, Response $response)
    {
        try {
            $hasher = new Hasher();
            $hasher->setAlgorithm(PASSWORD_BCRYPT);
    
            $newPassword = $request->get('new_password', null);
    
            $this->accountService->update(auth()->user()->uuid, [
                'password' => $hasher->hash($newPassword)
            ]);
        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => t('exception.execution_terminated')
            ]);
        }
    }
}