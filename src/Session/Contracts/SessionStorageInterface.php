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

namespace Quantum\Session\Contracts;

use Quantum\Contracts\StorageInterface;

/**
 * Interface SessionStorageInterface
 * @package Quantum\Session
 */
interface SessionStorageInterface extends StorageInterface
{
    /**
     * Gets flash value by given key
     * @return mixed|string|null
     */
    public function getFlash(string $key);

    /**
     * Sets the flash message
     * @param mixed $value
     * @return void
     */
    public function setFlash(string $key, $value);

    /**
     * Gets the session ID
     */
    public function getId(): ?string;

    /**
     * Update the current session id with a newly generated one
     */
    public function regenerateId(): bool;
}
