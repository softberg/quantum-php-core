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

namespace Quantum\Libraries\Session;

use Quantum\Exceptions\AppException;

/**
 * Class SessionException
 * @package Quantum\Libraries\Session
 */
class SessionException extends AppException
{
    /**
     * @return SessionException
     */
    public static function sessionNotStarted(): SessionException
    {
        return new static(t('exception.session_not_started'), E_WARNING);
    }

    /**
     * @return SessionException
     */
    public static function sessionNotDestroyed(): SessionException
    {
        return new static(t('exception.session_not_destroyed'), E_WARNING);
    }

    /**
     * @return SessionException
     */
    public static function sessionTableNotProvided(): SessionException
    {
        return new static(t('exception.session_table_not_provided'));
    }
}
