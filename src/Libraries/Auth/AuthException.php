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

/**
 * Class AuthException
 * @package Quantum\Exceptions
 */
class AuthException extends \Exception
{
    /**
     * @return AuthException
     */
    public static function incorrectCredentials(): AuthException
    {
        return new static(t('exception.incorrect_auth_credentials'));
    }

    /**
     * @return AuthException
     */
    public static function inactiveAccount(): AuthException
    {
        return new static(t('exception.inactive_account'));
    }

    /**
     * @return AuthException
     */
    public static function incorrectVerificationCode(): AuthException
    {
        return new static(t('exception.incorrect_verification_code'));
    }

    /**
     * @return AuthException
     */
    public static function verificationCodeExpired(): AuthException
    {
        return new static(t('exception.verification_code_expired'));
    }

    /**
     * @return AuthException
     */
    public static function misconfiguredAuthConfig(): AuthException
    {
        return new static(t('exception.misconfigured_auth_config'));
    }

    /**
     * @param string $name
     * @return AuthException
     */
    public static function undefinedAuthType(string $name): AuthException
    {
        return new static(t('exception.undefined_auth_type', $name));
    }

    /**
     * @return AuthException
     */
    public static function incorrectUserSchema(): AuthException
    {
        return new static(t('exception.incorrect_user_schema'));
    }

    /**
     * @return AuthException
     */
    public static function incorrectAuthService(): AuthException
    {
        return new static(t('exception.incorrect_auth_service'));
    }
}
