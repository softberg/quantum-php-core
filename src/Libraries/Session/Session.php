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

use Quantum\Libraries\Encryption\Cryptor;

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
    private $storage = [];

    /**
     * Cryptor instance
     * @var \Quantum\Libraries\Encryption\Cryptor
     */
    private $cryptor;

    /**
     * Session constructor.
     * @param array $storage
     * @param \Quantum\Libraries\Encryption\Cryptor $cryptor
     */
    public function __construct(array &$storage, Cryptor $cryptor)
    {
        $this->storage = &$storage;
        $this->cryptor = $cryptor;
    }

    /**
     * Gets all data
     * @return array
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function all(): array
    {
        $allSessions = [];

        foreach ($this->storage as $key => $value) {
            $allSessions[$key] = $this->decode($value);
        }

        return $allSessions;
    }

    /**
     * Check if storage contains a data by given key
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->storage[$key]) && !empty($this->storage[$key]);
    }

    /**
     * Gets value by given key
     * @param string $key
     * @return mixed|null
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function get(string $key)
    {
        return $this->has($key) ? $this->decode($this->storage[$key]) : null;
    }

    /**
     * Sets value by given key
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function set(string $key, $value): self
    {
        $this->storage[$key] = $this->encode($value);
        return $this;
    }

    /**
     * Gets flash value by given key
     * @param string $key
     * @return mixed|string|null
     * @throws \Quantum\Exceptions\CryptorException
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
     * Sets flash value by given key
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws \Quantum\Exceptions\CryptorException
     */
    public function setFlash(string $key, $value): self
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * Deletes data from storage by given key
     * @param string $key
     */
    public function delete(string $key)
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
        }
    }

    /**
     * Destroys whole storage data
     */
    public function flush()
    {
        $this->storage = [];
        session_destroy();
    }

    /**
     * Gets the session Id
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return session_id() ?? null;
    }

    /**
     * Encodes the session data
     * @param mixed $value
     * @return string
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function encode($value): string
    {
        $value = (is_array($value) || is_object($value)) ? serialize($value) : $value;
        return $this->cryptor->encrypt($value);
    }

    /**
     * Decodes the session data
     * @param string $value
     * @return mixed|string
     * @throws \Quantum\Exceptions\CryptorException
     */
    private function decode(string $value)
    {
        if (empty($value)) {
            return $value;
        }

        $decrypted = $this->cryptor->decrypt($value);

        if ($data = @unserialize($decrypted)) {
            $decrypted = $data;
        }

        return $decrypted;
    }

}
