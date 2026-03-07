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
 * @since 3.0.0
 */

namespace Quantum\Session\Adapters\Database;

use Quantum\Session\Contracts\SessionStorageInterface;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Session\Traits\SessionTrait;
use Quantum\Model\Factories\ModelFactory;

/**
 * Class Session
 * @package Quantum\Session
 */
class DatabaseSessionAdapter implements SessionStorageInterface
{
    use SessionTrait;

    /**
     * Session default table
     */
    public const SESSION_TABLE = 'sessions';

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

        $sessionModel = ModelFactory::createDynamicModel($sessionTable);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_save_handler(new DatabaseHandler($sessionModel->getOrmInstance()), true);

            if (@session_start() === false) {
                throw SessionException::sessionNotStarted();
            }
        }

        self::$params = $params;
        self::$storage = &$_SESSION;
    }
}
