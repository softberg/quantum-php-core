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
use Quantum\Factory\ViewFactory;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class AccountController
 * @package Modules\Web\Controllers
 */
class AccountController extends BaseController
{
    /**
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Account service
     * @var AccountService
     */
    public $accountService;

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before(ViewFactory $view)
    {
        $this->accountService = ServiceFactory::get(AccountService::class);

        parent::__before($view);
    }

    /**
     * Action - update user info
     * @param Request $request
     * @param Response $response
     */
    public function update(Request $request, Response $response)
    {
        try {
            $firstname = $request->get('firstname', null);
            $lastname = $request->get('lastname', null);
            $uuid = $request->get('uuid', null);
    
            $this->accountService->update($uuid, [
                'firstname' => $firstname,
                'lastname' => $lastname
            ]);
    
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
     * Action - update password
     * @param Request $request
     * @param Response $response
     */
    public function updatePassword(Request $request, Response $response)
    {
        try {
            $hasher = new Hasher();
            $hasher->setAlgorithm(PASSWORD_BCRYPT);

            $newPassword = $request->get('new_password', null);
            $uuid = $request->get('uuid', null);

            $this->accountService->update($uuid, [
                'password' => $hasher->hash($newPassword)
            ]);

        } catch (AuthException $e) {
            $response->json([
                'status' => self::STATUS_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
}