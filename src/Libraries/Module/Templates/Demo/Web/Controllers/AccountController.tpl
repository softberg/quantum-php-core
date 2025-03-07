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

use Shared\Transformers\PostTransformer;
use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ViewFactory;
use Shared\Services\AccountService;
use Shared\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Factory\ModelFactory;
use Shared\Models\User;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;

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
     * Account profile
     */
    const ACCOUNT_PROFILE = '#account_profile';

    /**
     * Account password
     */
    const ACCOUNT_PASSWORD = '#account_password';

    /**
     * Account service
     * @var AccountService
     */
    public $accountService;

    /**
     * Action - show user info
     */
    public function form(Response $response, ViewFactory $view)
    {
        $view->setParams([
            'title' => t('common.account_settings') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs')
        ]);

        $response->html($view->render('account/form'));
    }

    /**
     * Action - update user info
     * @param Request $request 
     * @param Response $response
     * @param string|null $lang
     */
    public function update(Request $request, ViewFactory $view)
    {
        $this->accountService = ServiceFactory::get(AccountService::class);

        $firstname = $request->get('firstname', null);
        $lastname = $request->get('lastname', null);

        $view->setParams([
            'title' => t('common.account_settings') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
        ]);

        $user = $this->accountService->update($request->get('uuid', null), [
            'firstname' => $firstname,
            'lastname' => $lastname
        ]);

        $userData = session()->get(AuthenticatableInterface::AUTH_USER);

        $userData['firstname'] = $user->firstname;
        $userData['lastname'] = $user->lastname;
             
        session()->set(AuthenticatableInterface::AUTH_USER, $userData);

        redirect(base_url(true) . '/' . current_lang() . '/account-settings' . self::ACCOUNT_PROFILE);
    }

    /**
     * Action - update password
     * @param Request $request 
     * @param Response $response
     * @param string|null $lang
     */
    public function updatePassword(Request $request, ViewFactory $view)
    {
        $this->accountService = ServiceFactory::get(AccountService::class);

        $hasher = new Hasher();
        $hasher->setAlgorithm(PASSWORD_BCRYPT);

        $currentPassword = $request->get('current_password', null);
        $newPassword = $request->get('new_password', null);
        $confirmPassword = $request->get('confirm_password', null);
        $uuid = $request->get('uuid', null);
        
        $view->setParams([
            'title' => t('common.account_settings') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs')
        ]);

        $userData = $this->accountService->update($uuid, [
            'password' => $hasher->hash($confirmPassword)
        ]);

        redirect(base_url(true) . '/' . current_lang() . '/account-settings' . self::ACCOUNT_PASSWORD);
    }
}