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

namespace Quantum\Libraries\Session\Contracts;

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
     * @return mixed|string|null
     */
    public function getFlash(string $key);

    /**
     * Sets flash value by given key
     * @param string $key
     * @param mixed $value
     */
    public function setFlash(string $key, $value);

    /**
     * Gets the session ID
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Update the current session id with a newly generated one
     * @return bool
     */
    public function regenerateId(): bool;
}
