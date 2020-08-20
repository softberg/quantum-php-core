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

namespace Quantum\Libraries\Cookie;

use Quantum\Libraries\Encryption\Cryptor;

/**
 * Cookie Class
 * @package Quantum
 * @category Libraries
 */
class Cookie implements CookieStorageInterface
{

    /**
     * Cookie storage
     * @var array $storage
     */
    private $storage = [];
    
    /**
     * Cryptor instance
     * @var Cryptor
     */
    private $cryptor;

    /**
     * Cookie constructor.
     * @param array $storage
     */
    public function __construct(&$storage, Cryptor $cryptor)
    {
        $this->storage = &$storage;
        $this->cryptor = $cryptor;
    }

    /**
     * Gets data by given key
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
        $allCookies = [];

        foreach ($this->storage as $key => $value) {
            $allCookies[$key] = $this->decode($value);
        }

        return $allCookies;
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
     * Sets data by given key
     * @param string $key
     * @param string $value
     * @param integer $time
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     * @return void
     */
    public function set($key, $value = '', $time = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
        $this->storage[$key] = $this->encode($value);
        setcookie($key, $this->encode($value), $time ? time() + $time : $time, $path, $domain, $secure, $httponly);
    }

    /**
     * Deletes data by given key
     * @param string $key
     * @param string $path
     * @return void
     */
    public function delete($key, $path = '/')
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
            setcookie($key, '', time() - 3600, $path);
        }
    }

    /**
     * Deletes whole cookie data
     * @return void
     */
    public function flush()
    {
        if (count($this->storage) > 0) {
            foreach ($this->storage as $key => $value) {
                $this->delete($key, '/');
            }
        }
    }

    /**
     * Encodes the cookie data
     * @param string $value
     * @return string
     */
    private function encode($value)
    {
        $value = (is_array($value) || is_object($value)) ? serialize($value) : $value;
        return $this->cryptor->encrypt($value);
    }

    /**
     * Decodes the cookie data
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
