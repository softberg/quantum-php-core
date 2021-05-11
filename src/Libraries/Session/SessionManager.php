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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Session;

use Quantum\Exceptions\SessionException;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Libraries\Database\Database;
use Quantum\Loader\Loader;

/**
 * Class SessionManager
 * @package Quantum\Libraries\Session
 */
class SessionManager
{

    /**
     * @var string
     */
    private static $databaseDriver = 'database';

    /**
     * GetHandler
     * @param \Quantum\Loader\Loader $loader
     * @return \Quantum\Libraries\Session\Session
     * @throws \Quantum\Exceptions\ModelException
     * @throws \RuntimeException
     */
    public static function getHandler(Loader $loader): Session
    {
        if (!session_id()) {

            if (self::$databaseDriver == config()->get('session_driver')) {
                $orm = (new Database($loader))->getORM(config()->get('session_table', 'sessions'));
                session_set_save_handler(new DbSessionHandler($orm, $loader), true);
            }

            if (@session_start() === false) {
                throw new \RuntimeException(SessionException::RUNTIME_SESSION_START);
            }
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > config()->get('session_timeout', 1800)) {
            if (@session_destroy() === false) {
                throw new \RuntimeException(SessionException::RUNTIME_SESSION_DESTROY);
            }
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        return new Session($_SESSION, new Cryptor);
    }

}
