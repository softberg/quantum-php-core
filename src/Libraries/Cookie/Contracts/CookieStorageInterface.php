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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Cookie\Contracts;

use Quantum\Contracts\StorageInterface;

/**
 * Interface CookieStorageInterface
 * @package Quantum\Libraries\Cookie
 */
interface CookieStorageInterface extends StorageInterface
{
    /**
     * Sets data by given key
     * @param string $key
     * @param mixed $value
     * @param int $time
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function set(string $key, $value = '', int $time = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false);

    /**
     * Deletes data by given key
     * @param string $key
     * @param string $path
     */
    public function delete(string $key, string $path = '/');
}
