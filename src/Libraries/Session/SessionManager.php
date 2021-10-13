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

namespace Quantum\Libraries\Session;

use Quantum\Exceptions\SessionException;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Libraries\Database\Database;

/**
 * Class SessionManager
 * @package Quantum\Libraries\Session
 */
class SessionManager
{

    /**
     * Session Db driver
     */
    const DRIVER = 'database';

    /**
     * Gets the handler
     * @return \Quantum\Libraries\Session\Session
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\SessionException
     */
    public static function getHandler(): Session
    {
        if (!session_id()) {
            if (self::DRIVER == config()->get('session_driver')) {
                $orm = Database::getInstance()->getOrm(config()->get('session_table', 'sessions'));
                session_set_save_handler(new DbSessionHandler($orm), true);
            }

            if (@session_start() === false) {
                throw SessionException::sessionNotStarted();
            }
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > config()->get('session_timeout', 1800)) {
            if (@session_destroy() === false) {
                throw SessionException::sessionNotDestroyed();
            }
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        return new Session($_SESSION, new Cryptor);
    }

}
