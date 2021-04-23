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

use Quantum\Exceptions\AuthException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Mailer\Mailer;

/**
 * Class WebAuth
 * @package Quantum\Libraries\Auth
 */
class WebAuth extends BaseAuth implements AuthenticableInterface
{

    /**
     * @var Mailer
     */
    protected $mailer;
    
    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var string
     */
    protected $authUserKey = 'auth_user';

    /**
     * WebAuth constructor.
     * @param AuthServiceInterface $authService
     * @param Mailer $mailer
     * @param Hasher $hasher
     */
    public function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher)
    {
        $this->mailer = $mailer;
        $this->hasher = $hasher;
        $this->authService = $authService;
        $this->keys = $this->authService->getDefinedKeys();
    }

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @param boolean $remember
     * @return string|boolean
     * @throws AuthException
     */
    public function signin($username, $password, $remember = false)
    {
        $user = $this->authService->get($this->keys[self::USERNAME_KEY], $username);
        if (empty($user)) {
            throw new AuthException(AuthException::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->hasher->check($password, $user[$this->keys[self::PASSWORD_KEY]])) {
            throw new AuthException(AuthException::INCORRECT_AUTH_CREDENTIALS);
        }

        if (!$this->isActivated($user)) {
            throw new AuthException(AuthException::INACTIVE_ACCOUNT);
        }

        if ($remember) {
            $this->setRememberToken($user);
        }

        if (filter_var(config()->get('2SV'), FILTER_VALIDATE_BOOLEAN)) {
            $otpToken = $this->twoStepVerification($user);
            return $otpToken;

        } else {
            session()->set($this->authUserKey, $this->filterFields($user));
            return true;
        }
    }

    /**
     * Sign Out
     * @throws \Exception
     */
    public function signout()
    {
        if (session()->has($this->authUserKey)) {
            session()->delete($this->authUserKey);
            $this->removeRememberToken();
        }
    }

    /**
     * User
     * @return object|null
     * @throws \Exception
     */
    public function user()
    {
        if (session()->has($this->authUserKey)) {
            return (object) session()->get($this->authUserKey);
        } else if (cookie()->has($this->keys[self::REMEMBER_TOKEN_KEY])) {
            $user = $this->checkRememberToken();
            
            if ($user) {
                $this->setRememberToken($user);
                return $this->user();
            }
        }
        
        return null;
    }

    /**
     * Verify OTP
     * @param integer $otp
     * @param string $otpToken
     * @return bool
     * @throws AuthException
     */
    public function verifyOtp($otp, $otpToken)
    {
        $user = $this->authService->get($this->keys[self::OTP_TOKEN_KEY], $otpToken);

        if (empty($user) || $otp != $user[$this->keys[self::OTP_KEY]]) {
            throw new AuthException(AuthException::INCORRECT_VERIFICATION_CODE);
        }
 
        if (new \DateTime() >= new \DateTime($user[$this->keys[self::OTP_EXPIRY_KEY]])){
            throw new AuthException(AuthException::VERIFICATION_CODE_EXPIRED);
        }

        $this->authService->update(
                $this->keys[self::USERNAME_KEY], 
                $user[$this->keys[self::USERNAME_KEY]], 
                [
                    $this->keys[self::OTP_KEY] => null,
                    $this->keys[self::OTP_EXPIRY_KEY] => null,
                    $this->keys[self::OTP_TOKEN_KEY] => null,
                ]
        );

        session()->set($this->authUserKey, $this->filterFields($user));

        return true;
    }

    /**
     * Resend OTP
     * @param string $otpToken
     * @return string
     * @throws \Exception
     */
    public function resendOtp($otpToken)
    {
        $user = $this->authService->get($this->keys[self::OTP_TOKEN_KEY], $otpToken);

        if (empty($user)) {
            throw new AuthException(AuthException::INCORRECT_AUTH_CREDENTIALS);
        }

        return $this->twoStepVerification($user);

    }

    /**
     * Check Remember Token
     * @return bool|mixed
     * @throws \Exception
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get($this->keys[self::REMEMBER_TOKEN_KEY], cookie()->get($this->keys[self::REMEMBER_TOKEN_KEY]));
        
        if (!empty($user)) {
            return $user;
        }
        
        return false;
    }

    /**
     * Set Remember Token
     * @param array $user
     * @throws \Exception
     */
    private function setRememberToken(array $user)
    {
        $rememberToken = $this->generateToken();

        $this->authService->update($this->keys[self::USERNAME_KEY], $user[$this->keys[self::USERNAME_KEY]], [
            $this->keys[self::REMEMBER_TOKEN_KEY] => $rememberToken
        ]);

        session()->set($this->authUserKey, $this->filterFields($user));
        cookie()->set($this->keys[self::REMEMBER_TOKEN_KEY], $rememberToken);
    }

    /**
     * Remove Remember Token
     * @throws \Exception
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keys[self::REMEMBER_TOKEN_KEY])) {
            $user = $this->authService->get($this->keys[self::REMEMBER_TOKEN_KEY], cookie()->get($this->keys[self::REMEMBER_TOKEN_KEY]));

            if (!empty($user)) {
                $this->authService->update(
                        $this->keys[self::REMEMBER_TOKEN_KEY], 
                        $user[$this->keys[self::REMEMBER_TOKEN_KEY]], 
                        [$this->keys[self::REMEMBER_TOKEN_KEY] => '']
                );
            }

            cookie()->delete($this->keys[self::REMEMBER_TOKEN_KEY]);
        }
    }
}
