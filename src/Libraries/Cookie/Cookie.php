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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Cookie;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Routes\RouteController;

/**
 * Cookie Class
 * 
 * @package Quantum
 * @subpackage Libraries.Cookie
 * @category Libraries
 */
class Cookie {

    /**
     * Gets data from cookie by given key
     * 
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return self::has($key) ? self::decode($_COOKIE[$key]) : NULL;
    }

    /*
     * Gets whole cookie data
     */
    public static function all() {
        $allCookies = array();
        if (isset($_COOKIE) && count($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                $allCookies[$key] = self::decode($value);
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
    public static function has($key) {
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
    public static function set($key, $value = '', $time = 0, $path = '/', $domain = '', $secure = FALSE, $httponly = FALSE) {
        return setcookie($key, self::encode($value), time() + $time, $path, $domain, $secure, $httponly);
    }

    /**
     * Delete cookie data by given key
     * 
     * @param string $key
     * @param string $path
     */
    public static function delete($key, $path = '/') {
        if (self::has($key)) {
            setcookie($key, '', time() - 3600, $path);
        }
    }

    /**
     * Deletes whole cookie data
     */
    public static function flush() {
        if (count($_COOKIE) > 0) {
            foreach ($_COOKIE as $key => $value) {
                self::delete($key, '/');
            }
        }
    }

    /**
     * Encodes the cookie data
     * 
     * @param string $value
     * @return string
     */
    private static function encode($value) {
        return base64_encode($value);
    }

    /**
     * Decodes the cookie data
     * 
     * @param string $value
     * @return string
     */
    private static function decode($value) {
        return base64_decode($value);
    }

}
