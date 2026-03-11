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

namespace Quantum\Session\Adapters\Native;

use Quantum\Session\Contracts\SessionStorageInterface;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Session\Traits\SessionTrait;

/**
 * Class Session
 * @package Quantum\Session
 */
class NativeSessionAdapter implements SessionStorageInterface
{
    use SessionTrait;

    /**
     * Session default timeout
     */
    public const SESSION_TIMEOUT = 30 * 60;

    /**
     * Session params
     */
    private static ?array $params = [];

    /**
     * Session storage
     */
    private static array $storage = [];

    /**
     * @throws SessionException
     */
    public function __construct(?array $params = null)
    {
        $this->initializeSession($params);
    }

    /**
     * @throws SessionException
     */
    protected function initializeSession(?array $params = null): void
    {
        $timeout = $params['timeout'] ?? self::SESSION_TIMEOUT;

        if (session_status() !== PHP_SESSION_ACTIVE && @session_start() === false) {
            throw SessionException::sessionNotStarted();
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $timeout && @session_destroy() === false) {
            throw SessionException::sessionNotDestroyed();
        }

        $_SESSION['LAST_ACTIVITY'] = time();

        self::$params = $params;
        self::$storage = &$_SESSION;
    }
}
