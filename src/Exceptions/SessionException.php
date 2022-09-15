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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class SessionException
 * @package Quantum\Exceptions
 */
class SessionException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionNotStarted(): SessionException
    {
        return new static(t('exception.session_not_started'), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionNotDestroyed(): SessionException
    {
        return new static(t('exception.session_not_destroyed'), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\SessionException
     */
    public static function sessionTableNotProvided(): SessionException
    {
        return new static(t('exception.session_table_not_provided'));
    }
}
