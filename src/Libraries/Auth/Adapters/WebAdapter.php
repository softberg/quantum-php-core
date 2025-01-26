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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Auth\Adapters;

use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Auth\Contracts\AuthServiceInterface;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Constants\AuthKeys;
use Quantum\Libraries\Auth\Traits\AuthTrait;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Auth\User;
use ReflectionException;
use Exception;

/**
 * Class WebAuth
 * @package Quantum\Libraries\Auth
 */
class WebAdapter implements AuthenticatableInterface
{

    use AuthTrait;

    /**
     * @param AuthServiceInterface $authService
     * @param Mailer $mailer
     * @param Hasher $hasher
     * @throws AuthException
     */
    public function __construct(AuthServiceInterface $authService, Mailer $mailer, Hasher $hasher)
    {
        $this->authService = $authService;
        $this->mailer = $mailer;
        $this->hasher = $hasher;

        $this->verifySchema($this->authService->userSchema());
    }

    /**
     * @inheritDoc
     * @throws AuthException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
     * @throws Exception
     */
    public function signin(string $username, string $password, bool $remember = false)
    {
        $user = $this->getUser($username, $password);

        if ($remember) {
            $this->setRememberToken($user);
        }

        if ($this->isTwoFactorEnabled()) {
            return $this->twoStepVerification($user);
        }

        session()->regenerateId();
        session()->set(self::AUTH_USER, $this->getVisibleFields($user));

        return true;
    }

    /**
     * @inheritDoc
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws BaseException
     */
    public function signout(): bool
    {
        if (session()->has(self::AUTH_USER)) {
            session()->delete(self::AUTH_USER);
            session()->regenerateId();
            $this->removeRememberToken();

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws ConfigException
     * @throws CryptorException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
     */
    public function user(): ?User
    {
        if (session()->has(self::AUTH_USER) && is_array(session()->get(self::AUTH_USER))) {
            return (new User())->setData(session()->get(self::AUTH_USER));
        }

        if (cookie()->has($this->keyFields[AuthKeys::REMEMBER_TOKEN])) {
            $user = $this->checkRememberToken();

            if ($user) {
                session()->set(self::AUTH_USER, $this->getVisibleFields($user));
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
     * @throws AuthException
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function verifyOtp(int $otp, string $otpToken): bool
    {
        $user = $this->verifyAndUpdateOtp($otp, $otpToken);

        session()->set(self::AUTH_USER, $this->getVisibleFields($user));

        return true;
    }

    /**
     * Check Remember Token
     * @return User|false
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get(
            $this->keyFields[AuthKeys::REMEMBER_TOKEN],
            cookie()->get($this->keyFields[AuthKeys::REMEMBER_TOKEN])
        );

        if (!$user) {
            return false;
        }

        if ($this->isTwoFactorEnabled() && !empty($user->getFieldValue($this->keyFields[AuthKeys::OTP_TOKEN]))) {
            return false;
        }

        return $user;
    }

    /**
     * Set Remember Token
     * @param User $user
     */
    private function setRememberToken(User $user)
    {
        $rememberToken = $this->generateToken();

        $this->authService->update(
            $this->keyFields[AuthKeys::USERNAME],
            $user->getFieldValue($this->keyFields[AuthKeys::USERNAME]),
            [$this->keyFields[AuthKeys::REMEMBER_TOKEN] => $rememberToken]
        );

        cookie()->set($this->keyFields[AuthKeys::REMEMBER_TOKEN], $rememberToken);
    }

    /**
     * Remove Remember token
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keyFields[AuthKeys::REMEMBER_TOKEN])) {
            $user = $this->authService->get(
                $this->keyFields[AuthKeys::REMEMBER_TOKEN],
                cookie()->get($this->keyFields[AuthKeys::REMEMBER_TOKEN])
            );

            if ($user) {
                $this->authService->update(
                    $this->keyFields[AuthKeys::REMEMBER_TOKEN],
                    $user->getFieldValue($this->keyFields[AuthKeys::REMEMBER_TOKEN]),
                    [$this->keyFields[AuthKeys::REMEMBER_TOKEN] => '']
                );
            }

            cookie()->delete($this->keyFields[AuthKeys::REMEMBER_TOKEN]);
        }
    }
}