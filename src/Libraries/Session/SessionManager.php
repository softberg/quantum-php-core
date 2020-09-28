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

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Database\Database;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Loader\Loader;

/**
 * Session Manager class
 *
 * @package Quantum
 * @category Libraries
 */
class SessionManager
{

    /**
     * @var string
     */
    private $databaseDriver = 'database';

    /**
     * Get session handler
     * @return Session
     * @throws \RuntimeException
     */
    public function getSessionHandler()
    {
        $driver = config()->get('session_driver');

        if (!session_id()) {

            if ($driver == $this->databaseDriver) {
                $orm = (new Database(new Loader()))->getORM(config()->get('session_table', 'sessions'));
                session_set_save_handler(new DbSessionHandler($orm), true);
            }

            if (@session_start() === false) {
                throw new \RuntimeException(ExceptionMessages::RUNTIME_SESSION_START);
            }
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > config()->get('session_timeout', 1800)) {
            if (@session_destroy() === false) {
                throw new \RuntimeException(ExceptionMessages::RUNTIME_SESSION_DESTROY);
            }
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        return new Session($_SESSION, new Cryptor);
    }

}
