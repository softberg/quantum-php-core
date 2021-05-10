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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Exceptions\AuthException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Mailer\Mailer;
use ReflectionClass;

/**
 * Class BaseAuth
 * @package Quantum\Libraries\Auth
 */
abstract class BaseAuth
{

    /**
     * One time password key
     */
    const OTP_KEY = 'otp';

    /**
     * One time password expiry key
     */
    const OTP_EXPIRY_KEY = 'otpExpiry';

    /**
     * One time password token key
     */
    const OTP_TOKEN_KEY = 'otpToken';

    /**
     * Username key
     */
    const USERNAME_KEY = 'username';

    /**
     * Password key
     */
    const PASSWORD_KEY = 'password';

    /**
     * Access token key
     */
    const ACCESS_TOKEN_KEY = 'accessToken';

    /**
     * Refresh token key
     */
    const REFRESH_TOKEN_KEY = 'refreshToken';

    /**
     * Activation token key
     */
    const ACTIVATION_TOKEN_KEY = 'activationToken';

    /**
     * Reset token key
     */
    const RESET_TOKEN_KEY = 'resetToken';

    /**
     * Remember token key
     */
    const REMEMBER_TOKEN_KEY = 'rememberToken';

    /**
     * @var \Quantum\Libraries\Mailer\Mailer
     */
    protected $mailer;

    /**
     * @var \Quantum\Libraries\Hasher\Hasher
     */
    protected $hasher;

    /**
     * @var \Quantum\Libraries\JWToken\JWToken
     */
    protected $jwt;

    /**
     * @var \Quantum\Libraries\Auth\AuthServiceInterface
     */
    protected $authService;

    /**
     * @var string
     */
    protected $authUserKey = 'auth_user';

    /**
     * @var int
     */
    protected $otpLenght = 6;

    /**
     * @var array
     */
    protected $keyFields = [];

    /**
     * @var array
     */
    protected $visibleFields = [];

    /**
     * User
     * @return \Quantum\Libraries\Auth\User|null
     */
    protected abstract function user(): ?User;

    /**
     * Verify user schema
     * @param array $schema
     * @throws \Quantum\Exceptions\AuthException
     */
    protected function verifySchema(array $schema)
    {
        $reflectionClass = new ReflectionClass($this);
        $constants = $reflectionClass->getConstants();

        foreach ($constants as $constant) {
            if (!in_array($constant, array_keys($schema))) {
                throw new AuthException(AuthException::INCORRECT_USER_SCHEMA);
            }

            if (!isset($schema[$constant]['name'])) {
                throw new AuthException(AuthException::INCORRECT_USER_SCHEMA);
            }

            $this->keyFields[$constant] = $schema[$constant]['name'];
        }

        foreach ($schema as $field) {
            if ($field['visible']) {
                $this->visibleFields[] = $field['name'];
            }
        }
    }

    /**
     * Check
     * @return bool
     */
    public function check(): bool
    {
        return !is_null($this->user());
    }

    /**
     * Sign Up
     * @param array $userData
     * @param array|null $customData
     * @return \Quantum\Libraries\Auth\User
     */
    public function signup(array $userData, array $customData = null): User
    {
        $activationToken = $this->generateToken();

        $userData[$this->keyFields[self::PASSWORD_KEY]] = $this->hasher->hash($userData[$this->keyFields[self::PASSWORD_KEY]]);
        $userData[$this->keyFields[self::ACTIVATION_TOKEN_KEY]] = $activationToken;

        $user = $this->authService->add($userData);

        $body = [
            'user' => $user,
            'activationToken' => $activationToken
        ];

        if ($customData) {
            $body = array_merge($body, $customData);
        }

        $this->mailer->setSubject(t('common.activate_account'));
        $this->mailer->setTemplate(base_dir() . DS . 'base' . DS . 'views' . DS . 'email' . DS . 'activate');

        $this->sendMail($user, $body);

        return $user;
    }

    /**
     * Activate
     * @param string $token
     */
    public function activate(string $token)
    {
        $this->authService->update(
            $this->keyFields[self::ACTIVATION_TOKEN_KEY],
            $token,
            [$this->keyFields[self::ACTIVATION_TOKEN_KEY] => '']
        );
    }

    /**
     * Forget
     * @param string $username
     * @return string
     */
    public function forget(string $username): string
    {
        $user = $this->authService->get($this->keyFields[self::USERNAME_KEY], $username);

        $resetToken = $this->generateToken();

        $this->authService->update(
            $this->keyFields[self::USERNAME_KEY],
            $username,
            [$this->keyFields[self::RESET_TOKEN_KEY] => $resetToken]
        );

        $body = [
            'user' => $user,
            'resetToken' => $resetToken
        ];

        $this->mailer->setSubject(t('common.reset_password'));
        $this->mailer->setTemplate(base_dir() . DS . 'base' . DS . 'views' . DS . 'email' . DS . 'reset');

        $this->sendMail($user, $body);

        return $resetToken;
    }

    /**
     * Reset
     * @param string $token
     * @param string $password
     */
    public function reset(string $token, string $password)
    {
        $user = $this->authService->get($this->keyFields[self::RESET_TOKEN_KEY], $token);

        if (!$this->isActivated($user)) {
            $this->activate($token);
        }

        $this->authService->update(
            $this->keyFields[self::RESET_TOKEN_KEY],
            $token,
            [
                $this->keyFields[self::PASSWORD_KEY] => $this->hasher->hash($password),
                $this->keyFields[self::RESET_TOKEN_KEY] => ''
            ]
        );
    }

    /**
     * Resend OTP
     * @param string $otpToken
     * @return string
     * @throws \Quantum\Exceptions\AuthException
     */
    public function resendOtp(string $otpToken): string
    {
        $user = $this->authService->get($this->keyFields[self::OTP_TOKEN_KEY], $otpToken);

        if (!$user) {
            throw new AuthException(AuthException::INCORRECT_AUTH_CREDENTIALS);
        }

        return $this->twoStepVerification($user);

    }

    /**
     * Two Step Verification
     * @param \Quantum\Libraries\Auth\User $user
     * @return string
     * @throws \Exception
     */
    protected function twoStepVerification(User $user): string
    {
        $otp = random_number($this->otpLenght);

        $otpToken = $this->generateToken($user->getFieldValue($this->keyFields[self::USERNAME_KEY]));

        $time = new \DateTime();

        $time->add(new \DateInterval('PT' . config()->get('otp_expires') . 'M'));

        $this->authService->update(
            $this->keyFields[self::USERNAME_KEY],
            $user->getFieldValue($this->keyFields[self::USERNAME_KEY]),
            [
                $this->keyFields[self::OTP_KEY] => $otp,
                $this->keyFields[self::OTP_EXPIRY_KEY] => $time->format('Y-m-d H:i'),
                $this->keyFields[self::OTP_TOKEN_KEY] => $otpToken,
            ]
        );

        $body = [
            'user' => $user,
            'code' => $otp
        ];

        $this->mailer->setSubject(t('common.otp'));
        $this->mailer->setTemplate(base_dir() . DS . 'base' . DS . 'views' . DS . 'email' . DS . 'verification');

        $this->sendMail($user, $body);

        return $otpToken;
    }

    /**
     * Verify and update OTP
     * @param int $otp
     * @param string $otpToken
     * @return \Quantum\Libraries\Auth\User
     * @throws \Quantum\Exceptions\AuthException
     */
    protected function verifyAndUpdateOtp(int $otp, string $otpToken): User
    {
        $user = $this->authService->get($this->keyFields[self::OTP_TOKEN_KEY], $otpToken);

        if (!$user || $otp != $user->getFieldValue($this->keyFields[self::OTP_KEY])) {
            throw new AuthException(AuthException::INCORRECT_VERIFICATION_CODE);
        }

        if (new \DateTime() >= new \DateTime($user->getFieldValue($this->keyFields[self::OTP_EXPIRY_KEY]))) {
            throw new AuthException(AuthException::VERIFICATION_CODE_EXPIRED);
        }

        $this->authService->update(
            $this->keyFields[self::USERNAME_KEY],
            $user->getFieldValue($this->keyFields[self::USERNAME_KEY]),
            [
                $this->keyFields[self::OTP_KEY] => null,
                $this->keyFields[self::OTP_EXPIRY_KEY] => null,
                $this->keyFields[self::OTP_TOKEN_KEY] => null,
            ]
        );

        return $user;
    }

    /**
     * Filters and gets the visible fields
     * @param \Quantum\Libraries\Auth\User $user
     * @return array
     */
    protected function getVisibleFields(User $user): array
    {
        $userData = $user->getData();

        if (count($this->visibleFields)) {
            foreach ($userData as $field => $value) {
                if (!in_array($field, $this->visibleFields)) {
                    unset($userData[$field]);
                }
            }
        }

        return $userData;
    }

    /**
     * Is user account activated
     * @param \Quantum\Libraries\Auth\User $user
     * @return bool
     */
    protected function isActivated(User $user): bool
    {
        return empty($user->getFieldValue($this->keyFields[self::ACTIVATION_TOKEN_KEY])) ? true : false;
    }

    /**
     * Generate Token
     * @param string|null $val
     * @return string
     */
    protected function generateToken(string $val = null): string
    {
        return base64_encode($this->hasher->hash($val ?: env('APP_KEY')));
    }

    /**
     * Send email
     * @param \Quantum\Libraries\Auth\User $user
     * @param array $body
     */
    protected function sendMail(User $user, array $body)
    {
        $fullName = ($user->hasField('firstname') && $user->hasField('lastname')) ? $user->getFieldValue('firstname') . ' ' . $user->getFieldValue('lastname') : '';

        $this->mailer->setFrom(config()->get('app_email'), config()->get('app_name'))
            ->setAddress($user->getFieldValue($this->keyFields[self::USERNAME_KEY]), $fullName)
            ->setBody($body)
            ->send();
    }

}
