<?php

declare(strict_types=1);

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

use Quantum\Model\Exceptions\ModelException;
use Quantum\Session\Contracts\SessionStorageInterface;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Session\Traits\SessionTrait;

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
     */
    private static ?array $params = [];

    /**
     * Session storage
     */
    private static array $storage = [];

    /**
     * @throws SessionException|ModelException
     */
    public function __construct(?array $params = null)
    {
        $this->initializeSession($params);
    }

    /**
     * @throws SessionException|ModelException
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
