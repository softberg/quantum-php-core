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

namespace Quantum\Contracts;

/**
 * Interface StorageInterface
 * @package Quantum\Contracts
 */
interface StorageInterface
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
     * @return void
     */
    public function set(string $key, $value);

    /**
     * Deletes the data from the storage by given key
     * @return void
     */
    public function delete(string $key);

    /**
     * Deletes whole storage data
     * @return void
     */
    public function flush();
}
