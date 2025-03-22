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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Session\Adapters\Database;

use Quantum\Libraries\Session\Contracts\SessionStorageInterface;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Session\Traits\SessionTrait;
use Quantum\Factory\ModelFactory;

/**
 * Class Session
 * @package Quantum\Libraries\Session
 */
class DatabaseSessionAdapter implements SessionStorageInterface
{

    use SessionTrait;

    /**
     * Session default table
     */
    const SESSION_TABLE = 'sessions';

    /**
     * Session params
     * @var array
     */
    private static $params = [];

    /**
     * Session storage
     * @var array $storage
     */
    private static $storage = [];

    /**
     * @param array|null $params
     * @throws SessionException
     */
    public function __construct(?array $params = null)
    {
        $this->initializeSession($params);
    }

    /**
     * @param array|null $params
     * @return void
     * @throws SessionException
     */
    protected function initializeSession(?array $params = null): void
    {
        $sessionTable = $params['table'] ?? self::SESSION_TABLE;

        $sessionModel = ModelFactory::create($sessionTable);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_save_handler(new DatabaseHandler($sessionModel), true);

            if (@session_start() === false) {
                throw SessionException::sessionNotStarted();
            }
        }

        self::$params = $params;
        self::$storage = &$_SESSION;
    }
}