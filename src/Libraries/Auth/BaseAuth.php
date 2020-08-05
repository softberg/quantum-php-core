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
     * @param array $userData
     * @param array|null $customData
     * @return mixed
     */
     public function signup(Mailer $mailer, $userData, $customData = null)
    {
        $activationToken = $this->generateToken();

        $userData[$this->keys['passwordKey']] = $this->hasher->hash($userData[$this->keys['passwordKey']]);
        $userData[$this->keys['activationTokenKey']] = $activationToken;

        $user = $this->authService->add($userData);

        $body = [
            'user' => $user,
            'activationToken' => $activationToken
        ];

        if($customData) {
            $body = array_merge($body, $customData);
        }

        $this->sendMail($mailer, $user, $body);

        return $user;
    }

    /**
     * Activate
     * 
     * @param string $token
     */
    public function activate($token)
    {
        $this->authService->update(
                $this->keys['activationTokenKey'],
                $token, [$this->keys['activationTokenKey'] => '']
        );
    }

    /**
     * Forget
     *
     * @param Mailer $mailer
     * @param string $email
     * @param string $template
     * @return string
     */
    public function forget(Mailer $mailer, $email)
    {
        $user = $this->authService->get($this->keys['usernameKey'], $email);

        $resetToken = $this->generateToken();

        $this->authService->update(
                $this->keys['usernameKey'],
                $email,
                [$this->keys['resetTokenKey'] => $resetToken]
        );

        $body = [
            'user' => $user,
            'resetToken' => $resetToken
        ];

        $this->sendMail($mailer, $user, $body);

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
        $user = $this->authService->get($this->keys['resetTokenKey'], $token);
        
        if(!$this->isActivated($user)) {
            $this->activate($token);
        }
        
        $this->authService->update(
                $this->keys['resetTokenKey'],
                $token,
                [$this->keys['passwordKey'] => $this->hasher->hash($password), $this->keys['resetTokenKey'] => '']
        );
    }

    /**
     * Filter Fields
     *
     * @param array $user
     * @return mixed
     */
    protected function filterFields(array $user)
    {
        if (count($this->authService->getVisibleFields())) {
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

    protected function isActivated($user)
    {
        return empty($user[$this->keys['activationTokenKey']]) ? true : false;
    }

    protected function sendMail(Mailer $mailer, array $user, array $body)
    {
        $fullName = (isset($user['firstname']) && isset($user['lastname'])) ? $user['firstname'] . ' ' . $user['lastname'] : '';

        $mailer->setFrom(config()->get('app_email'), config()->get('app_name'))
                ->setAddress($user[$this->keys['usernameKey']], $fullName)
                ->setBody($body)
                ->send();
    }

}
