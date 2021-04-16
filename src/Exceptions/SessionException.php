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
 * @since 1.9.5
 */

namespace Quantum\Exceptions;

/**
 * ServiceException class
 *
 * @package Quantum
 * @category Exceptions
 */
class SessionException extends \Exception
{
    /**
     * Session start error message
     */
    const RUNTIME_SESSION_START = 'Can not start the session';

    /**
     * Session destroy error  message
     */
    const RUNTIME_SESSION_DESTROY = 'Can not destroy the session';
}
