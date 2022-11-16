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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Mailer\Mailer;
use ReflectionException;

/**
 * Class WebAuth
 * @package Quantum\Libraries\Auth
 */
class WebAuth extends BaseAuth implements AuthenticableInterface
{

    /**
     * Instance of WebAuth
     * @var WebAuth
     */
    private static $instance;

    /**
     * WebAuth constructor.
     * @param AuthServiceInterface $authService
     * @param Mailer $mailer
     * @param Hasher $hasher
     * @throws \Quantum\Exceptions\AuthException
     */
    private function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher)
    {
        $this->mailer = $mailer;
        $this->hasher = $hasher;
        $this->authService = $authService;

        $userSchema = $this->authService->userSchema();

        $this->verifySchema($userSchema);
    }

    /**
     * Get Instance
     * @param AuthServiceInterface $authService
     * @param Mailer $mailer
     * @param Hasher $hasher
     * @return WebAuth
     * @throws \Quantum\Exceptions\AuthException
     */
    public static function getInstance(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher): WebAuth
    {
        if (self::$instance === null) {
            self::$instance = new self($authService, $mailer, $hasher);
        }

        return self::$instance;
    }

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return bool|string
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\CryptorException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\SessionException
     * @throws ReflectionException
     */
    public function signin(string $username, string $password, bool $remember = false)
    {
        $user = $this->getUser($username, $password);

        if ($remember) {
            $this->setRememberToken($user);
        }

        if (filter_var(config()->get('2FA'), FILTER_VALIDATE_BOOLEAN)) {
            return $this->twoStepVerification($user);
        } else {
            session()->set($this->authUserKey, $this->getVisibleFields($user));
            return true;
        }
    }

    /**
     * Sign Out
     * @return bool
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\CryptorException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\SessionException
     * @throws ReflectionException
     */
    public function signout(): bool
    {
        if (session()->has($this->authUserKey)) {
            session()->delete($this->authUserKey);
            $this->removeRememberToken();

            return true;
        }

        return false;
    }

    /**
     * Gets the user object
     * @return User|null
     * @throws ReflectionException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\CryptorException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\SessionException
     */
    public function user(): ?User
    {
        if (session()->has($this->authUserKey) && is_array(session()->get($this->authUserKey))) {
            return (new User())->setData(session()->get($this->authUserKey));
        } else if (cookie()->has($this->keyFields[self::REMEMBER_TOKEN_KEY])) {
            $user = $this->checkRememberToken();

            if ($user) {
                session()->set($this->authUserKey, $this->getVisibleFields($user));
                return $this->user();
            }
        }

        return null;
    }

    /**
     * Verify OTP
     * @param int $otp
     * @param string $otpToken
     * @return bool
     * @throws ReflectionException
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\CryptorException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\SessionException
     */
    public function verifyOtp(int $otp, string $otpToken): bool
    {
        $user = $this->verifyAndUpdateOtp($otp, $otpToken);

        session()->set($this->authUserKey, $this->getVisibleFields($user));

        return true;
    }

    /**
     * Check Remember Token
     * @return User|false
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get($this->keyFields[self::REMEMBER_TOKEN_KEY], cookie()->get($this->keyFields[self::REMEMBER_TOKEN_KEY]));

        if (!$user) {
            return false;
        }

        if (filter_var(config()->get('2FA'), FILTER_VALIDATE_BOOLEAN) && !empty($user->getFieldValue($this->keyFields[self::OTP_TOKEN_KEY]))) {
            return false;
        }

        return $user;
    }

    /**
     * Set Remember Token
     * @param User $user
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function setRememberToken(User $user)
    {
        $rememberToken = $this->generateToken();

        $this->authService->update(
            $this->keyFields[self::USERNAME_KEY],
            $user->getFieldValue($this->keyFields[self::USERNAME_KEY]),
            [$this->keyFields[self::REMEMBER_TOKEN_KEY] => $rememberToken]
        );

        cookie()->set($this->keyFields[self::REMEMBER_TOKEN_KEY], $rememberToken);
    }

    /**
     * Remove Remember token
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keyFields[self::REMEMBER_TOKEN_KEY])) {
            $user = $this->authService->get($this->keyFields[self::REMEMBER_TOKEN_KEY], cookie()->get($this->keyFields[self::REMEMBER_TOKEN_KEY]));

            if ($user) {
                $this->authService->update(
                    $this->keyFields[self::REMEMBER_TOKEN_KEY],
                    $user->getFieldValue($this->keyFields[self::REMEMBER_TOKEN_KEY]),
                    [$this->keyFields[self::REMEMBER_TOKEN_KEY] => '']
                );
            }

            cookie()->delete($this->keyFields[self::REMEMBER_TOKEN_KEY]);
        }
    }
}
