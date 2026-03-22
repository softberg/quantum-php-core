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

/**
 * Interface SessionStorageInterface
 * @package Quantum\Session
 */
interface SessionStorageInterface
{
    /**
     * Gets whole storage data
     * @return mixed
     */
    public function all();

    /**
     * Checks if the storage contains a key
     */
    public function has(string $key): bool;

    /**
     * Gets the value from the storage by given key
     * @return mixed|null
     */
    public function get(string $key);

    /**
     * Sets storage value with the given key
     * @param mixed $value
     */
    public function set(string $key, $value): void;

    /**
     * Deletes the data from the storage by given key
     */
    public function delete(string $key): void;

    /**
     * Deletes whole storage data
     */
    public function flush(): void;

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
