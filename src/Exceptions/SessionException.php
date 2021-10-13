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
 * @since 2.6.0
 */

namespace Quantum\Exceptions;

/**
 * Class SessionException
 * @package Quantum\Exceptions
 */
class SessionException extends \Exception
{
    /**
     * Session start error message
     */
    const SESSION_NOT_STARTED = 'Can not start the session';

    /**
     * Session destroy error message
     */
    const SESSION_NOT_DESTROYED = 'Can not destroy the session';

    /**
     * Session table not provided message
     */
    const SESSION_TABLE_NOT_PROVIDED = 'Session table not provided';

    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionNotStarted(): SessionException
    {
        return new static(self::SESSION_NOT_STARTED, E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionNotDestroyed(): SessionException
    {
        return new static(self::SESSION_NOT_DESTROYED, E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionTableNotProvided(): SessionException
    {
        return new static(self::SESSION_TABLE_NOT_PROVIDED);
    }
}
