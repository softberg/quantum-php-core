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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Auth\Traits;

use Quantum\Libraries\Auth\Contracts\AuthServiceInterface;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Jwt\Exceptions\JwtException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Libraries\Auth\Constants\AuthKeys;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Libraries\Auth\User;
use ReflectionException;
use ReflectionClass;
use DateInterval;
use Exception;
use DateTime;

/**
 * Trait AuthTrait
 * @package Quantum\Libraries\Auth
 */
trait AuthTrait
{

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var JwtToken
     */
    protected $jwt;

    /**
     * @var AuthServiceInterface
     */
    protected $authService;

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
     * @inheritDoc
     * @throws ConfigException
     * @throws DiException
     * @throws JwtException
     * @throws ReflectionException
     * @throws BaseException
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
     */
    public function signup(array $userData, array $customData = null): User
    {
        $activationToken = $this->generateToken();

        $userData[$this->keyFields[AuthKeys::PASSWORD]] = $this->hasher->hash($userData[$this->keyFields[AuthKeys::PASSWORD]]);
        $userData[$this->keyFields[AuthKeys::ACTIVATION_TOKEN]] = $activationToken;

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
            $this->keyFields[AuthKeys::ACTIVATION_TOKEN],
            $token,
            [$this->keyFields[AuthKeys::ACTIVATION_TOKEN] => '']
        );
    }

    /**
     * Forget
     * @param string $username
     * @return string|null
     */
    public function forget(string $username): ?string
    {
        $user = $this->authService->get($this->keyFields[AuthKeys::USERNAME], $username);

        if (!$user) {
            return null;
        }

        $resetToken = $this->generateToken();

        $this->authService->update(
            $this->keyFields[AuthKeys::USERNAME],
            $username,
            [$this->keyFields[AuthKeys::RESET_TOKEN] => $resetToken]
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

    /**
     * Reset
     * @param string $token
     * @param string $password
     */
    public function reset(string $token, string $password)
    {
        $user = $this->authService->get($this->keyFields[AuthKeys::RESET_TOKEN], $token);

        if ($user) {
            if (!$this->isActivated($user)) {
                $this->activate($token);
            }

            $this->authService->update(
                $this->keyFields[AuthKeys::RESET_TOKEN],
                $token,
                [
                    $this->keyFields[AuthKeys::PASSWORD] => $this->hasher->hash($password),
                    $this->keyFields[AuthKeys::RESET_TOKEN] => ''
                ]
            );
        }
    }

    /**
     * Resend OTP
     * @param string $otpToken
     * @return string
     * @throws AuthException
     * @throws Exception
     */
    public function resendOtp(string $otpToken): string
    {
        $user = $this->authService->get($this->keyFields[AuthKeys::OTP_TOKEN], $otpToken);

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
     */
    protected function getUser(string $username, string $password): User
    {
        $user = $this->authService->get($this->keyFields[AuthKeys::USERNAME], $username);

        if (!$user) {
            throw AuthException::incorrectCredentials();
        }

        if (!$this->hasher->check($password, $user->getFieldValue($this->keyFields[AuthKeys::PASSWORD]))) {
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
     * @throws Exception
     */
    protected function twoStepVerification(User $user): string
    {
        $otp = random_number($this->otpLength);

        $otpToken = $this->generateToken($user->getFieldValue($this->keyFields[AuthKeys::USERNAME]));

        $time = new DateTime();

        $time->add(new DateInterval('PT' . config()->get('auth.otp_expires') . 'M'));

        $this->authService->update(
            $this->keyFields[AuthKeys::USERNAME],
            $user->getFieldValue($this->keyFields[AuthKeys::USERNAME]),
            [
                $this->keyFields[AuthKeys::OTP] => $otp,
                $this->keyFields[AuthKeys::OTP_EXPIRY] => $time->format('Y-m-d H:i'),
                $this->keyFields[AuthKeys::OTP_TOKEN] => $otpToken,
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
     * @throws Exception
     */
    protected function verifyAndUpdateOtp(int $otp, string $otpToken): User
    {
        $user = $this->authService->get($this->keyFields[AuthKeys::OTP_TOKEN], $otpToken);

        if (!$user || $otp != $user->getFieldValue($this->keyFields[AuthKeys::OTP])) {
            throw AuthException::incorrectVerificationCode();
        }

        if (new DateTime() >= new DateTime($user->getFieldValue($this->keyFields[AuthKeys::OTP_EXPIRY]))) {
            throw AuthException::verificationCodeExpired();
        }

        $this->authService->update(
            $this->keyFields[AuthKeys::USERNAME],
            $user->getFieldValue($this->keyFields[AuthKeys::USERNAME]),
            [
                $this->keyFields[AuthKeys::OTP] => null,
                $this->keyFields[AuthKeys::OTP_EXPIRY] => null,
                $this->keyFields[AuthKeys::OTP_TOKEN] => null,
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
        return empty($user->getFieldValue($this->keyFields[AuthKeys::ACTIVATION_TOKEN]));
    }

    /**
     * Generate Token
     * @param string|null $val
     * @return string
     */
    protected function generateToken(string $val = null): string
    {
        return base64_encode($this->hasher->hash($val ?: config()->get('app.key')));
    }

    /**
     * Send email
     * @param User $user
     * @param array $body
     */
    protected function sendMail(User $user, array $body)
    {
        $fullName = ($user->hasField('firstname') && $user->hasField('lastname')) ? $user->getFieldValue('firstname') . ' ' . $user->getFieldValue('lastname') : '';

        $appEmail = config()->get('app.email') ?: '';
        $appName = config()->get('app.name') ?: '';

        $this->mailer->setFrom($appEmail, $appName)
            ->setAddress($user->getFieldValue($this->keyFields[AuthKeys::USERNAME]), $fullName)
            ->setBody($body)
            ->send();
    }

    /**
     * Verify user schema
     * @param array $schema
     * @throws AuthException
     */
    protected function verifySchema(array $schema)
    {
        $constants = (new ReflectionClass(AuthKeys::class))->getConstants();

        foreach ($constants as $constant) {
            if (!isset($schema[$constant]) || !isset($schema[$constant]['name'])) {
                throw AuthException::incorrectUserSchema();
            }

            $this->keyFields[$constant] = $schema[$constant]['name'];
        }

        foreach ($schema as $field) {
            if (isset($field['visible']) && $field['visible']) {
                $this->visibleFields[] = $field['name'];
            }
        }
    }

    /**
     * @return bool
     */
    protected function isTwoFactorEnabled(): bool
    {
        return filter_var(config()->get('auth.two_fa'), FILTER_VALIDATE_BOOLEAN);
    }
}