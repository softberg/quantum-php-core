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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Database\Database;

/**
 * Session Manager class
 *
 * @package Quantum
 * @subpackage Libraries.Session
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
     *
     * @return Session
     * @throws \Exception
     */
    public function getSessionHandler()
    {
        $sessionHandler = null;

        $driver = get_config('session_driver');

        if (!session_id()) {

            if ($driver == $this->databaseDriver) {
                $orm = Database::getORMInstance(null, get_config('session_table', 'sessions'));
                session_set_save_handler(new DbSessionHandler($orm), true);
            }

            @session_start();

        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > get_config('session_timeout', 1800)) {
            @session_unset();
            @session_destroy();
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        return new Session($_SESSION);
    }

}
