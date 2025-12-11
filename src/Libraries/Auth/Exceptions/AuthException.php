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

namespace Quantum\Libraries\Auth\Exceptions;

use Quantum\Libraries\Auth\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class AuthException
 * @package Quantum\Exceptions
 */
class AuthException extends BaseException
{
    /**
     * @return AuthException
     */
    public static function incorrectCredentials(): AuthException
    {
        return new static(ExceptionMessages::INCORRECT_CREDENTIALS, E_ERROR);
    }

    /**
     * @return AuthException
     */
    public static function inactiveAccount(): AuthException
    {
        return new static(ExceptionMessages::INACTIVE_ACCOUNT);
    }

    /**
     * @return AuthException
     */
    public static function incorrectVerificationCode(): AuthException
    {
        return new static(ExceptionMessages::INCORRECT_VERIFICATION_CODE, E_ERROR);
    }

    /**
     * @return AuthException
     */
    public static function verificationCodeExpired(): AuthException
    {
        return new static(ExceptionMessages::VERIFICATION_CODE_EXPIRED, E_ERROR);
    }

    /**
     * @return AuthException
     */
    public static function incorrectUserSchema(): AuthException
    {
        return new static(ExceptionMessages::INCORRECT_USER_SCHEMA, E_ERROR);
    }
}