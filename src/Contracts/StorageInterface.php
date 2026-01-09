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

namespace Quantum\Contracts;

/**
 * Interface StorageInterface
 * @package Quantum\Contracts
 */
interface StorageInterface
{
    /**
     * Gets whole storage data
     */
    public function all();

    /**
     * Checks if the storage contains a key
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Gets the value from the storage by given key
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key);

    /**
     * Sets storage value with the given key
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value);

    /**
     * Deletes the data from the storage by given key
     * @param string $key
     */
    public function delete(string $key);

    /**
     * Deletes whole storage data
     */
    public function flush();
}
