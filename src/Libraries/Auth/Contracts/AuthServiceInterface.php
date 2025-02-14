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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Auth\Contracts;

use Quantum\Libraries\Auth\User;

/**
 * Interface AuthServiceInterface
 * @package Quantum\Libraries\Auth
 */
interface AuthServiceInterface
{

    /**
     * Get
     * @param string $field
     * @param string|null $value
     * @return User|null
     */
    public function get(string $field, ?string $value): ?User;

     /**
     * Add
     * @param array $data
     * @return User
     */
    public function add(array $data): User;

    /**
     * Update
     * @param string $field
     * @param string|null $value
     * @param array $data
     * @return User|null
     */
    public function update(string $field, ?string $value, array $data): ?User;

    /**
     * User Schema
     * @return array
     */
    public function userSchema(): array;
}