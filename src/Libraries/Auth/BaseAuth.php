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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Mailer\Mailer;

/**
 * Class BaseAuth
 * @package Quantum\Libraries\Auth
 */
abstract class BaseAuth
{

    /**
     * One time password length
     */
    const OTP_LENGTH = 6;
    
    /**
     * One time password key
     */
    const OTP_KEY = 'otpKey';
    
    /**
     * One time password expiry key
     */
    const OTP_EXPIRY_KEY = 'otpExpiryKey';
    
    /**
     * One time password token key
     */
    const OTP_TOKEN_KEY = 'otpTokenKey';

    /**
     * Username key
     */
    const USERNAME_KEY = 'usernameKey';
    
    /**
     * Password key
     */
    const PASSWORD_KEY = 'passwordKey';

    /**
     * Access token key
     */
    const ACCESS_TOKEN_KEY = 'accessTokenKey';

    /**
     * Refresh token key
     */
    const REFRESH_TOKEN_KEY = 'refreshTokenKey';
    
    /**
     * Activation token key
     */
    const ACTIVATION_TOKEN_KEY = 'activationTokenKey';
    
    /**
     * Reset token key
     */
    const RESET_TOKEN_KEY = 'resetTokenKey';
    
    /**
     * Remember token key
     */
    const REMEMBER_TOKEN_KEY = 'rememberTokenKey';
    
    /**
     * User
     * @return mixed|null
     */
    protected abstract function user();

    /**
     * Check
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Check Verification
     * @return bool
     */
    public function checkVerification()
    {
        if (isset($this->user()->verification_code) && !empty($this->user()->verification_code)) {
            return true;
        }
        return false;
    }

    /**
     * Sign Up
     * @param array $userData
     * @param array|null $customData
     * @return mixed
     */
    public function signup(Mailer $mailer, $userData, $customData = null)
    {
        $activationToken = $this->generateToken();

        $userData[$this->keys[self::PASSWORD_KEY]] = $this->hasher->hash($userData[$this->keys[self::PASSWORD_KEY]]);
        $userData[$this->keys[self::ACTIVATION_TOKEN_KEY]] = $activationToken;

        $user = $this->authService->add($userData);

        $body = [
            'user' => $user,
            'activationToken' => $activationToken
        ];

        if ($customData) {
            $body = array_merge($body, $customData);
        }

        $this->sendMail($mailer, $user, $body);

        return $user;
    }

    /**
     * Activate
     * @param string $token
     */
    public function activate($token)
    {
        $this->authService->update(
                $this->keys[self::ACTIVATION_TOKEN_KEY], 
                $token, 
                [$this->keys[self::ACTIVATION_TOKEN_KEY] => '']
        );
    }

    /**
     * Forget
     * @param Mailer $mailer
     * @param string $username
     * @param string $template
     * @return string
     */
    public function forget(Mailer $mailer, $username)
    {
        $user = $this->authService->get($this->keys[self::USERNAME_KEY], $username);

        $resetToken = $this->generateToken();

        $this->authService->update(
                $this->keys[self::USERNAME_KEY], 
                $username, 
                [$this->keys[self::RESET_TOKEN_KEY] => $resetToken]
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
     * @param string $token
     * @param string $password
     */
    public function reset($token, $password)
    {
        $user = $this->authService->get($this->keys[self::RESET_TOKEN_KEY], $token);

        if (!$this->isActivated($user)) {
            $this->activate($token);
        }

        $this->authService->update(
                $this->keys[self::RESET_TOKEN_KEY], 
                $token, 
                [
                    $this->keys[self::PASSWORD_KEY] => $this->hasher->hash($password), 
                    $this->keys[self::RESET_TOKEN_KEY] => ''
                ]
        );
    }
    
    /**
     * Two Step Verification
     * @param Mailer $mailer
     * @param array $user
     * @return string
     */
    protected function twoStepVerification(Mailer $mailer, $user)
    {
        $otp = random_number(self::OTP_LENGTH);
        
        $otpToken = $this->generateToken($user[$this->keys[self::USERNAME_KEY]]);
        
        $time = new \DateTime();

        $time->add(new \DateInterval('PT' . config()->get('otp_expires') . 'M'));

        $this->authService->update(
                $this->keys[self::USERNAME_KEY], 
                $user[$this->keys[self::USERNAME_KEY]], 
                [
                    $this->keys[self::OTP_KEY] => $otp,
                    $this->keys[self::OTP_EXPIRY_KEY] => $time->format('Y-m-d H:i'),
                    $this->keys[self::OTP_TOKEN_KEY] => $otpToken,
                ]
        );

        $body = [
            'user' => $user,
            'code' => $otp
        ];
        
        $this->sendMail($mailer, $user, $body);

        return $otpToken;
    }

    /**
     * Filter Fields
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
     * @param mixed|null $val
     * @return string
     */
    protected function generateToken($val = null)
    {
        return base64_encode($this->hasher->hash($val ?: env('APP_KEY')));
    }

    /**
     * Is user account activated
     * @param mixed $user
     * @return bool
     */
    protected function isActivated($user)
    {
        return empty($user[$this->keys[self::ACTIVATION_TOKEN_KEY]]) ? true : false;
    }

    /**
     * Send email
     * @param Mailer $mailer
     * @param array $user
     * @param array $body
     */
    protected function sendMail(Mailer $mailer, array $user, array $body)
    {
        $fullName = (isset($user['firstname']) && isset($user['lastname'])) ? $user['firstname'] . ' ' . $user['lastname'] : '';

        $mailer->setFrom(config()->get('app_email'), config()->get('app_name'))
                ->setAddress($user[$this->keys[self::USERNAME_KEY]], $fullName)
                ->setBody($body)
                ->send();
    }

}
