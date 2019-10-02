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

namespace Quantum\Libraries\Cookie;

use Quantum\Libraries\Encryption\Cryptor;

/**
 * Cookie Class
 *
 * @package Quantum
 * @subpackage Libraries.Cookie
 * @category Libraries
 */
class Cookie implements CookieStorageInterface
{

    /**
     * Cookie storage
     *
     * @var array $storage
     */
    private $storage = [];


    /**
     * Cookie constructor.
     *
     * @param array $storage
     */
    public function __construct(&$storage = [])
    {
        $this->storage = &$storage;
    }

    /**
     * Gets data by given key
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
        $allCookies = [];

        foreach ($this->storage as $key => $value) {
            $allCookies[$key] = $this->decode($value);
        }

        return $allCookies;
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
     * Sets data by given key
     *
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
     *
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
     *
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
     *
     * @param string $value
     * @return string
     */
    private function encode($value)
    {
        return Cryptor::encrypt($value);
    }

    /**
     * Decodes the cookie data
     *
     * @param string $value
     * @return string
     */
    private function decode($value)
    {
        return Cryptor::decrypt($value);
    }

}
