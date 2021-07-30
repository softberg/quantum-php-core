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
 * @since 2.5.0
 */

namespace Quantum\Exceptions;

/**
 * Class AuthException
 * @package Quantum\Exceptions
 */
class AuthException extends \Exception
{
    /**
     * Incorrect auth credentials  message
     */
    const INCORRECT_AUTH_CREDENTIALS = 'Incorrect credentials';

    /**
     * Incorrect auth credentials  message
     */
    const INACTIVE_ACCOUNT = 'The account is not activated';

    /**
     * Incorrect verification code
     */
    const INCORRECT_VERIFICATION_CODE = 'Incorrect verification code.';

    /**
     * Verification code expiry in
     */
    const VERIFICATION_CODE_EXPIRED = 'Verification code expired';

    /**
     * Misconfigured session handler  message
     */
    const MISCONFIGURED_AUTH_CONFIG = 'Auth config is not properly configured';

    /**
     * Incorrect user schema
     */
    const INCORRECT_USER_SCHEMA = 'User schema does not contains all key fields';

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function incorrectCredentials(): AuthException
    {
        return new static(self::INCORRECT_AUTH_CREDENTIALS);
    }

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function inactiveAccount(): AuthException
    {
        return new static(self::INACTIVE_ACCOUNT);
    }

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function incorrectVerificationCode(): AuthException
    {
        return new static(self::INCORRECT_VERIFICATION_CODE);
    }

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function verificationCodeExpired(): AuthException
    {
        return new static(self::VERIFICATION_CODE_EXPIRED);
    }

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function misconfiguredAuthConfig(): AuthException
    {
        return new static(self::MISCONFIGURED_AUTH_CONFIG);
    }

    /**
     * @return \Quantum\Exceptions\AuthException
     */
    public static function incorrectUserSchema(): AuthException
    {
        return new static(self::INCORRECT_USER_SCHEMA);
    }
}
