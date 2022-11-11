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

/**
 * Class Session
 * @package Quantum\Libraries\Session
 */
class Session implements SessionStorageInterface
{

    /**
     * Session storage
     * @var array $storage
     */
    private static $storage = [];

    /**
     * Session instance
     * @var Session|null
     */
    private static $instance = null;

    /**
     * Session constructor.
     */
    private function __construct()
    {
        // Preventing to create new object through constructor
    }

    /**
     * Gets the session instance
     * @param array $storage
     * @return Session|null
     */
    public static function getInstance(array &$storage): ?Session
    {
        self::$storage = &$storage;

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     * @throws CryptorException
     */
    public function all(): array
    {
        $allSessions = [];

        foreach (self::$storage as $key => $value) {
            $allSessions[$key] = crypto_decode($value);
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
     * @throws CryptorException
     */
    public function get(string $key)
    {
        return $this->has($key) ? crypto_decode(self::$storage[$key]) : null;
    }

    /**
     * @inheritDoc
     * @throws CryptorException
     */
    public function set(string $key, $value)
    {
        self::$storage[$key] = crypto_encode($value);
    }

    /**
     * @inheritDoc
     * @throws CryptorException
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
     * @throws CryptorException
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
     */
    public function regenerateId(): bool
    {
        return session_regenerate_id(true);
    }
}
