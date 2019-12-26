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
 * @since 1.9.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Mailer\Mailer;

/**
 * Trait AuthTools
 *
 * @package Quantum\Libraries\Auth
 */
class BaseAuth
{
    /**
     * Check
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Sign Up
     *
     * @param array $user
     * @return mixed
     */
    public function signup($user)
    {
        $user[$this->keys['passwordKey']] = $this->hasher->hash($user[$this->keys['passwordKey']]);
        return $this->authService->add($user);
    }

    /**
     * Forget
     *
     * @param Mailer $mailer
     * @param string $email
     * @param string $template
     * @return string
     */
    public function forget(Mailer $mailer, $email, $template)
    {
        $user = $this->authService->get($this->keys['usernameKey'], $email);

        $resetToken = $this->generateToken();

        $this->authService->update($email, [
            $this->keys['resetTokenKey'] => $resetToken
        ]);

        $mailer
            ->createFrom(['email' => get_config('app_email'), 'name' => get_config('app_name')])
            ->createAddresses(['email' => $user['username'], 'name' => $user['firstname'] . ' ' . $user['lastname']])
            ->createBody([
                'user' => $user,
                'resetToken' => $resetToken
            ], ['template' => $template])
            ->send();

        return $resetToken;
    }

    /**
     * Reset
     *
     * @param string $token
     * @param string $password
     */
    public function reset($token, $password)
    {
        $this->authService->update($token, [
            $this->keys['passwordKey'] => $this->hasher->hash($password),
            $this->keys['resetTokenKey'] => ''
        ]);
    }

    /**
     * Filter Fields
     *
     * @param array $user
     * @return mixed
     */
    protected function filterFields(array $user)
    {
        if (count($this->authService->getVisibleFields()) > 0) {
            foreach ($user as $key => $value) {
                if (!in_array($key, $this->authService->getVisibleFields())) {
                    unset($user[$key]);
                }
            }
        }

        return $user;
    }

    /**
     * Generate Token
     *
     * @return string
     */
    protected function generateToken()
    {
        return base64_encode($this->hasher->hash(env('APP_KEY')));
    }
}