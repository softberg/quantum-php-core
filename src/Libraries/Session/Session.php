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
 * @since 1.5.0
 */

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Encryption\Cryptor;

/**
 * Session class
 *
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
class Session implements SessionStorageInterface
{

    /**
     * Session storage
     *
     * @var array $storage
     */
    private $storage = [];

    private $cryptor;


    /**
     * Session constructor.
     *
     * @param array $storage
     */
    public function __construct(&$storage = [])
    {
        $this->storage = &$storage;
        $this->cryptor = new Cryptor();
    }

    /**
     * Gets value by given key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->has($key) ? $this->decode($this->storage[$key]) : null;
    }

    /*
     * Gets whole data
     *
     * @return array
     */
    public function all()
    {
        foreach ($this->storage as $key => $value) {
            $this->storage[$key] = $this->decode($value);
        }
        return $this->storage;
    }

    /**
     * Check if storage contains a data by given key
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->storage[$key]) ? true : false;
    }

    /**
     * Sets value by given key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $this->encode($value);
    }

    /**
     * Gets flash values by given key
     *
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
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setFlash($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Deletes data from storage by given key
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
        }
    }

    /**
     * Deletes whole storage data
     *
     * @return void
     */
    public function flush()
    {
        $this->storage = [];
        @session_destroy();
    }

    /**
     * Gets session Id
     *
     * @return null|string
     */
    public function getSessionId()
    {
        return session_id() ?? null;
    }

    /**
     * Encodes the session data
     *
     * @param mixed $value
     * @return string
     */
    private function encode($value)
    {
        $value = is_array($value) ? implode('::', $value) : is_object($value) ? serialize($value) : $value;
        return $this->cryptor->encrypt($value);
    }

    /**
     * Decodes the session data
     *
     * @param string $value
     * @return string
     */
    private function decode($value)
    {
        $decrypted = $this->cryptor->decrypt($value);

        if($obj_data = @unserialize($decrypted)) {
            $decrypted = $obj_data;
        } elseif (preg_match('/::/', $decrypted, $matches)) {
            $decrypted = explode('::', $decrypted);
        }

        return $decrypted;
    }
}