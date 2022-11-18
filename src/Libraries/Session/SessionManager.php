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

namespace Quantum\Libraries\Session;

use Quantum\Exceptions\CryptorException;
use Quantum\Exceptions\LangException;
use Quantum\Libraries\Session\Handlers\DatabaseHandler;
use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\SessionException;
use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class SessionManager
 * @package Quantum\Libraries\Session
 */
class SessionManager
{

    /**
     * Session Db driver
     */
    const DB_DRIVER = 'database';

    /**
     * Default table for sessions
     */
    const SESSION_TABLE = 'sessions';

    /**
     * Default session timeout
     */
    const SESSION_TIMEOUT = 30 * 60;

    /**
     * Get handler
     * @return Session
     * @throws ReflectionException
     * @throws DatabaseException
     * @throws SessionException
     * @throws ConfigException
     * @throws LangException
     * @throws DiException
     */
    public static function getHandler(): Session
    {
        if (!config()->has('session')) {
            config()->import(new Setup('Config', 'session'));
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (config()->get('session.driver') == self::DB_DRIVER) {
                $sessionTable = config()->get('session.table', self::SESSION_TABLE);

                $sessionModel = Database::getInstance()->getOrm($sessionTable);
                session_set_save_handler(new DatabaseHandler($sessionModel), true);
            }

            if (@session_start() === false) {
                throw SessionException::sessionNotStarted();
            }

            @session_gc();
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > config()->get('session.timeout', self::SESSION_TIMEOUT)) {
            if (@session_destroy() === false) {
                throw SessionException::sessionNotDestroyed();
            }
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        return Session::getInstance($_SESSION);
    }

}
