<?php

declare(strict_types=1);

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

namespace Quantum\Cookie\Contracts;

use Quantum\Contracts\StorageInterface;

/**
 * Interface CookieStorageInterface
 * @package Quantum\Cookie
 */
interface CookieStorageInterface extends StorageInterface
{
    /**
     * Sets data by given key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value = '', int $time = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): void;

    /**
     * Deletes data by given key
     * @return void
     */
    public function delete(string $key, string $path = '/'): void;
}
