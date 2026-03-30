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

namespace Quantum\Session\Traits;

use Quantum\Session\Exceptions\SessionException;
use Quantum\Model\Exceptions\ModelException;

/**
 * Traits SessionTrait
 * @package Quantum\Session
 */
trait SessionTrait
{
    /**
     * @inheritDoc
     * @return array<string, mixed>
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
    public function set(string $key, $value): void
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
     * @return $this
     */
    public function setFlash(string $key, $value)
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): void
    {
        if ($this->has($key)) {
            unset(self::$storage[$key]);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        self::$storage = [];
        session_destroy();
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        $id = session_id();

        return $id !== false ? $id : null;
    }

    /**
     * @inheritDoc
     * @throws SessionException|ModelException
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
