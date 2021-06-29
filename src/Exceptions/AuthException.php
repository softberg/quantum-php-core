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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * AuthException class
 *
 * @package Quantum
 * @category Exceptions
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
}
