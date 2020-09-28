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
 * Session class
 * @package Quantum
 * @category Libraries
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
     * @var Cryptor
     */
    private $cryptor;

    /**
     * Session constructor.
     * @param array $storage
     */
    public function __construct(&$storage, Cryptor $cryptor)
    {
        $this->storage = &$storage;
        $this->cryptor = $cryptor;
    }

    /**
     * Gets value by given key
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->has($key) ? $this->decode($this->storage[$key]) : null;
    }

    /*
     * Gets whole data
     * @return array
     */

    public function all()
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
    public function has($key)
    {
        return isset($this->storage[$key]) ? true : false;
    }

    /**
     * Sets value by given key
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $this->encode($value);
    }

    /**
     * Gets flash values by given key
     * @param string $key
     * @return mixed|null
     */
    public function getFlash($key)
    {
        $flashData = null;

        if ($this->has($key)) {
            $flashData = $this->get($key);
            $this->delete($key);
        }

        return $flashData;
    }

    /**
     * Sets flash values by given key
     * @param string $key
     * @param mixed $value
     */
    public function setFlash($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Deletes data from storage by given key
     * @param string $key
     */
    public function delete($key)
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
     * @return null|string
     */
    public function getSessionId()
    {
        return session_id() ?? null;
    }

    /**
     * Encodes the session data
     * @param mixed $value
     * @return string
     */
    private function encode($value)
    {
        $value = (is_array($value) || is_object($value)) ? serialize($value) : $value;
        return $this->cryptor->encrypt($value);
    }

    /**
     * Decodes the session data
     * @param string $value
     * @return string
     */
    private function decode($value)
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
