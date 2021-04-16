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
 * CsrfException class
 *
 * @package Quantum
 * @category Exceptions
 */
class CsrfException extends \Exception
{
    /**
     * CSFT token not found message
     */
    const CSRF_TOKEN_NOT_FOUND = 'CSRF Token is missing';

    /**
     * CSFT token not matched message
     */
    const CSRF_TOKEN_NOT_MATCHED = 'CSRF Token does not matched';
}
