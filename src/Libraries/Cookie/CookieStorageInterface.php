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
 * Cookie Storage interface
 *
 * The common interface, which should implemented by Cookie storage classes
 *
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
interface CookieStorageInterface
{

    /**
     * Should be implemented in derived classes to get value from storage by key
     *
     * @param string $key
     */
    public function get($key);

    /**
     * Should be implemented in derived classes to get whole storage data
     */
    public function all();

    /**
     * Should be implemented in derived classes to check if storage contains a key
     *
     * @param string $key
     */
    public function has($key);

    /**
     * Should be implemented in derived classes to set storage value with given key
     *
     * @param string $key
     * @param string $value
     * @param int $time
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function set($key, $value = '', $time = 0, $path = '/', $domain = '', $secure = false, $httponly = false);

    /**
     * Should be implemented in derived classes to delete data with given key from session
     *
     * @param string $key
     * @param string $path
     */
    public function delete($key, $path = '/');

    /**
     * Should be implemented in derived classes to delete whole storage data
     */
    public function flush();

}
