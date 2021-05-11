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

use Quantum\Contracts\StorageInterface;

/**
 * Interface SessionStorageInterface
 * @package Quantum\Libraries\Session
 */
interface SessionStorageInterface extends StorageInterface
{

    /**
     * Gets flash value by given key
     * @param string $key
     */
    public function getFlash(string $key);

    /**
     * Sets flash value by given key
     * @param string $key
     * @param mixed $value
     */
    public function setFlash(string $key, $value);

    /**
     * Gets the session Id
     * @return string|null
     */
    public function getSessionId(): ?string;
}
