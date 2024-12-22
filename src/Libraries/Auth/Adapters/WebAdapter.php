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

use Quantum\Libraries\Auth\AuthenticatableInterface;
use Quantum\Libraries\Encryption\CryptorException;
use Quantum\Libraries\Database\DatabaseException;
use Quantum\Libraries\Auth\AuthServiceInterface;
use Quantum\Libraries\Session\SessionException;
use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Auth\AuthException;
use Quantum\Libraries\Lang\LangException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Auth\BaseAuth;
use Quantum\Exceptions\DiException;
use Quantum\Libraries\Auth\User;
use ReflectionException;
use Exception;

/**
 * Class WebAuth
 * @package Quantum\Libraries\Auth
 */
class WebAdapter extends BaseAuth implements AuthenticatableInterface
{

    /**
     * @var WebAdapter
     */
    private static $instance;

    /**
     * @param AuthServiceInterface $authService
     * @param MailerInterface $mailer
     * @param Hasher $hasher
     * @throws AuthException
     */
    private function __construct(AuthServiceInterface $authService, MailerInterface $mailer, Hasher $hasher)
    {
        $this->authService = $authService;
        $this->mailer = $mailer;
        $this->hasher = $hasher;

        $userSchema = $this->authService->userSchema();

        $this->verifySchema($userSchema);
    }

    /**
     * @param AuthServiceInterface $authService
     * @param MailerInterface $mailer
     * @param Hasher $hasher
     * @return self
     * @throws AuthException
     */
    public static function getInstance(AuthServiceInterface $authService, MailerInterface $mailer, Hasher $hasher): self
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
     * @return string|true
     * @throws AuthException
     * @throws ConfigException
     * @throws CryptorException
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

        if (filter_var(config()->get('2FA'), FILTER_VALIDATE_BOOLEAN)) {
            return $this->twoStepVerification($user);
        } else {
            session()->regenerateId();
            session()->set($this->authUserKey, $this->getVisibleFields($user));
            return true;
        }
    }

    /**
     * Sign Out
     * @return bool
     * @throws ConfigException
     * @throws CryptorException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
     */
    public function signout(): bool
    {
        if (session()->has($this->authUserKey)) {
            session()->delete($this->authUserKey);
            session()->regenerateId();
            $this->removeRememberToken();

            return true;
        }

        return false;
    }

    /**
     * Gets the user object
     * @return User|null
     * @throws ConfigException
     * @throws CryptorException
     * @throws DatabaseException
     * @throws DiException
     * @throws LangException
     * @throws ReflectionException
     * @throws SessionException
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
     * @throws AuthException
     * @throws ConfigException
     * @throws CryptorException
     * @throws DatabaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws SessionException
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
     * @throws CryptorException
     */
    private function checkRememberToken()
    {
        $user = $this->authService->get(
            $this->keyFields[self::REMEMBER_TOKEN_KEY],
            cookie()->get($this->keyFields[self::REMEMBER_TOKEN_KEY])
        );

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
     * @throws CryptorException
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
     * @throws CryptorException
     */
    private function removeRememberToken()
    {
        if (cookie()->has($this->keyFields[self::REMEMBER_TOKEN_KEY])) {
            $user = $this->authService->get(
                $this->keyFields[self::REMEMBER_TOKEN_KEY],
                cookie()->get($this->keyFields[self::REMEMBER_TOKEN_KEY])
            );

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
