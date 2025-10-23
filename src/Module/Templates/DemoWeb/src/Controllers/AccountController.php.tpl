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
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Account service
     * @var AuthService
     */
    public $authService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->authService = ServiceFactory::create(AuthService::class);

        parent::__before();
    }

    /**
     * Action - show user info
     * @param Response $response
     */
    public function form(Response $response)
    {
        $this->view->setParams([
            'title' => t('common.account_settings') . ' | ' . config()->get('app.name'),
        ]);

        $response->html($this->view->render('account/form'));
    }

    /**
     * Action - update user info
     * @param Request $request 
     */
    public function update(Request $request)
    {
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');

        $user = $this->authService->update('uuid', auth()->user()->uuid, [
            'firstname' => $firstname,
            'lastname' => $lastname
        ]);

        auth()->refreshUser(auth()->user()->uuid);

        redirect(base_url(true) . '/' . current_lang() . '/account-settings#account_profile');
    }

    /**
     * Action - update password
     * @param Request $request 
     */
    public function updatePassword(Request $request)
    {
        $hasher = new Hasher();

        $newPassword = $request->get('new_password', null);
        
        $this->authService->update('uuid', auth()->user()->uuid, [
            'password' => $hasher->hash($newPassword)
        ]);

        redirect(base_url(true) . '/' . current_lang() . '/account-settings#account_password');
    }
}