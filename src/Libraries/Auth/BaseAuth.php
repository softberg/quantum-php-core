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

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\LangException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Exceptions\DiException;
use PHPMailer\PHPMailer\Exception;
use ReflectionException;
use ReflectionClass;
use DateInterval;
use DateTime;

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
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var JWToken
     */
    protected $jwt;

    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @var string
     */
    protected $authUserKey = 'auth_user';

    /**
     * @var int
     */
    protected $otpLength = 6;

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
     * @return User|null
     */
    protected abstract function user(): ?User;

    /**
     * Verify user schema
     * @param array $schema
     * @throws AuthException
     * @throws LangException
     */
    protected function verifySchema(array $schema)
    {
        $reflectionClass = new ReflectionClass($this);
        $constants = $reflectionClass->getConstants();

        foreach ($constants as $constant) {
            if (!in_array($constant, array_keys($schema))) {
                throw AuthException::incorrectUserSchema();
            }

            if (!isset($schema[$constant]['name'])) {
                throw AuthException::incorrectUserSchema();
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
     * @return User
     * @throws LangException
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
        $this->mailer->setTemplate(base_dir() . DS . 'shared' . DS . 'views' . DS . 'email' . DS . 'activate');

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
     * @return string|null
     * @throws LangException
     */
    public function forget(string $username): ?string
    {
        $user = $this->authService->get($this->keyFields[self::USERNAME_KEY], $username);

        if ($user) {
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
            $this->mailer->setTemplate(base_dir() . DS . 'shared' . DS . 'views' . DS . 'email' . DS . 'reset');

            $this->sendMail($user, $body);

            return $resetToken;
        }
    }

    /**
     * Reset
     * @param string $token
     * @param string $password
     */
    public function reset(string $token, string $password)
    {
        $user = $this->authService->get($this->keyFields[self::RESET_TOKEN_KEY], $token);

        if ($user) {
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
    }

    /**
     * Resend OTP
     * @param string $otpToken
     * @return string
     * @throws AuthException
     * @throws \Exception
     */
    public function resendOtp(string $otpToken): string
    {
        $user = $this->authService->get($this->keyFields[self::OTP_TOKEN_KEY], $otpToken);

        if (!$user) {
            throw AuthException::incorrectCredentials();
        }

        return $this->twoStepVerification($user);

    }

    /**
     * Gets the user by username and password
     * @param string $username
     * @param string $password
     * @return User
     * @throws AuthException
     * @throws LangException
     */
    protected function getUser(string $username, string $password): User
    {
        $user = $this->authService->get($this->keyFields[self::USERNAME_KEY], $username);

        if (!$user) {
            throw AuthException::incorrectCredentials();
        }

        if (!$this->hasher->check($password, $user->getFieldValue($this->keyFields[self::PASSWORD_KEY]))) {
            throw AuthException::incorrectCredentials();
        }

        if (!$this->isActivated($user)) {
            throw AuthException::inactiveAccount();
        }

        return $user;
    }

    /**
     * Two-Step Verification
     * @param User $user
     * @return string
     * @throws \Exception
     */
    protected function twoStepVerification(User $user): string
    {
        $otp = random_number($this->otpLength);

        $otpToken = $this->generateToken($user->getFieldValue($this->keyFields[self::USERNAME_KEY]));

        $time = new DateTime();

        $time->add(new DateInterval('PT' . config()->get('otp_expires') . 'M'));

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
        $this->mailer->setTemplate(base_dir() . DS . 'shared' . DS . 'views' . DS . 'email' . DS . 'verification');

        $this->sendMail($user, $body);

        return $otpToken;
    }

    /**
     * Verify and update OTP
     * @param int $otp
     * @param string $otpToken
     * @return User
     * @throws AuthException
     * @throws \Exception
     */
    protected function verifyAndUpdateOtp(int $otp, string $otpToken): User
    {
        $user = $this->authService->get($this->keyFields[self::OTP_TOKEN_KEY], $otpToken);

        if (!$user || $otp != $user->getFieldValue($this->keyFields[self::OTP_KEY])) {
            throw AuthException::incorrectVerificationCode();
        }

        if (new DateTime() >= new DateTime($user->getFieldValue($this->keyFields[self::OTP_EXPIRY_KEY]))) {
            throw AuthException::verificationCodeExpired();
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
     * @param User $user
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
     * @param User $user
     * @return bool
     */
    protected function isActivated(User $user): bool
    {
        return empty($user->getFieldValue($this->keyFields[self::ACTIVATION_TOKEN_KEY]));
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
     * @param User $user
     * @param array $body
     * @throws Exception
     * @throws DiException
     * @throws ReflectionException
     */

    /**
     * Send email
     * @param User $user
     * @param array $body
     */
    protected function sendMail(User $user, array $body)
    {
        $fullName = ($user->hasField('firstname') && $user->hasField('lastname')) ? $user->getFieldValue('firstname') . ' ' . $user->getFieldValue('lastname') : '';

        $appEmail = config()->get('app_email') ?: '';
        $appName = config()->get('app_name') ?: '';

        $this->mailer->setFrom($appEmail, $appName)
            ->setAddress($user->getFieldValue($this->keyFields[self::USERNAME_KEY]), $fullName)
            ->setBody($body)
            ->send();
    }

}
