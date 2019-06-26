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

/**
 * Cookie Class
 *
 * @package Quantum
 * @subpackage Libraries.Cookie
 * @category Libraries
 */
class CookieAdapter
{

    /**
     * Gets data from cookie by given key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->has($key) ? $this->decode($_COOKIE[$key]) : null;
    }

    /*
     * Gets whole cookie data
     */
    public function all()
    {
        $allCookies = [];
        if (isset($_COOKIE) && count($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                $allCookies[$key] = $this->decode($value);
            }
        }
        return $allCookies;
    }

    /**
     * Check if cookie contains a data by given key
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($_COOKIE[$key]) ? true : false;
    }

    /**
     * Sets cookie data by given key
     *
     * @param string $key
     * @param string $value
     * @param integer $time
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     * @return bool
     */
    public function set($key, $value = '', $time = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        return setcookie($key, $this->encode($value), time() + $time, $path, $domain, $secure, $httponly);
    }

    /**
     * Delete cookie data by given key
     *
     * @param string $key
     * @param string $path
     * @return bool
     */
    public function delete($key, $path = '/')
    {
        if ($this->has($key)) {
            return setcookie($key, '', time() - 3600, $path);
        }

        return false;
    }

    /**
     * Deletes whole cookie data
     */
    public function flush()
    {
        if (count($_COOKIE) > 0) {
            foreach ($_COOKIE as $key => $value) {
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
        return base64_encode($value);
    }

    /**
     * Decodes the cookie data
     *
     * @param string $value
     * @return string
     */
    private function decode($value)
    {
        return base64_decode($value);
    }

}
