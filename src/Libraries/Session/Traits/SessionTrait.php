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

namespace Quantum\Libraries\Session\Traits;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;

/**
 * Traits SessionTrait
 * @package Quantum\Libraries\Session
 */
trait SessionTrait
{

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $allSessions = [];

        foreach (self::$storage as $key => $value) {
            $allSessions[$key] = is_string($value) ? crypto_decode($value) : $value;
        }

        return $allSessions;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset(self::$storage[$key]) && !empty(self::$storage[$key]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->has($key) ? crypto_decode(self::$storage[$key]) : null;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value)
    {
        self::$storage[$key] = crypto_encode($value);
    }

    /**
     * @inheritDoc
     */
    public function getFlash(string $key)
    {
        $flashData = null;

        if ($this->has($key)) {
            $flashData = $this->get($key);
            $this->delete($key);
        }

        return $flashData;
    }

    /**
     * @inheritDoc
     */
    public function setFlash(string $key, $value)
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key)
    {
        if ($this->has($key)) {
            unset(self::$storage[$key]);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        self::$storage = [];
        session_destroy();
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return session_id() ?? null;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws SessionException
     */
    public function regenerateId(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $result = session_regenerate_id(true);

        if ($result) {
            session_write_close();
        }

        $this->initializeSession(self::$params);

        return $result;
    }
}