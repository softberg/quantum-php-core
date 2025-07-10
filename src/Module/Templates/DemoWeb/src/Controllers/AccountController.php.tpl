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

use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Libraries\Hasher\Hasher;
use Shared\Services\AuthService;
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
     * @var AuthService
     */
    public $authService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->authService = ServiceFactory::get(AuthService::class);

        parent::__before();
    }

    /**
     * Action - show user info
     * @param Response $response
     */
    public function form(Response $response)
    {
        $this->view->setParams([
            'title' => t('common.account_settings') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs')
        ]);

        $response->html($this->view->render('account/form'));
    }

    /**
     * Action - update user info
     * @param Request $request 
     */
    public function update(Request $request)
    {
        $firstname = $request->get('firstname', null);
        $lastname = $request->get('lastname', null);

        $user = $this->authService->update('uuid', auth()->user()->uuid, [
            'firstname' => $firstname,
            'lastname' => $lastname
        ]);

        $userData = session()->get(AuthenticatableInterface::AUTH_USER);

        $userData['firstname'] = $user->firstname;
        $userData['lastname'] = $user->lastname;
             
        session()->set(AuthenticatableInterface::AUTH_USER, $userData);

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