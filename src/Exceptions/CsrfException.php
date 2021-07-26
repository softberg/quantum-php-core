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
 * Class CsrfException
 * @package Quantum\Exceptions
 */
class CsrfException extends \Exception
{
    /**
     * CSRF token not found message
     */
    const CSRF_TOKEN_NOT_FOUND = 'CSRF Token is missing';

    /**
     * CSRF token not matched message
     */
    const CSRF_TOKEN_NOT_MATCHED = 'CSRF Token does not matched';

    /**
     * @return \Quantum\Exceptions\CsrfException
     */
    public static function tokenNotFound(): CsrfException
    {
        return new static(self::CSRF_TOKEN_NOT_FOUND, E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\CsrfException
     */
    public static function tokenNotMatched(): CsrfException
    {
        return new static(self::CSRF_TOKEN_NOT_MATCHED, E_WARNING);
    }
}
