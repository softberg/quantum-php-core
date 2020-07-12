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

use Quantum\Contracts\StorageInterface;

/**
 * Interface CookieStorageInterface
 * @package Quantum\Libraries\Cookie
 */
interface CookieStorageInterface extends StorageInterface
{

    /**
     * Should be implemented in derived classes to set storage value with given key
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
     * @param string $key
     * @param string $path
     */
    public function delete($key, $path = '/');
}
