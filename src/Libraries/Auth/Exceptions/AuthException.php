<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
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
    public static function incorrectCredentials(): self
    {
        return new self(
            ExceptionMessages::INCORRECT_CREDENTIALS,
            E_ERROR
        );
    }

    /**
     * @return AuthException
     */
    public static function inactiveAccount(): self
    {
        return new self(
            ExceptionMessages::INACTIVE_ACCOUNT,
            E_ERROR
        );
    }

    /**
     * @return AuthException
     */
    public static function incorrectVerificationCode(): self
    {
        return new self(
            ExceptionMessages::INCORRECT_VERIFICATION_CODE,
            E_ERROR
        );
    }

    /**
     * @return AuthException
     */
    public static function verificationCodeExpired(): self
    {
        return new self(
            ExceptionMessages::VERIFICATION_CODE_EXPIRED,
            E_ERROR
        );
    }

    /**
     * @return AuthException
     */
    public static function incorrectUserSchema(): self
    {
        return new self(
            ExceptionMessages::INCORRECT_USER_SCHEMA,
            E_ERROR
        );
    }
}
