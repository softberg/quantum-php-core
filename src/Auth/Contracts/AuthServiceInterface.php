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

namespace Quantum\Auth\Contracts;

use Quantum\Auth\User;

/**
 * Interface AuthServiceInterface
 * @package Quantum\Auth
 */
interface AuthServiceInterface
{
    /**
     * Get
     */
    public function get(string $field, ?string $value): ?User;

    /**
     * Add
     */
    public function add(array $data): User;

    /**
     * Update
     */
    public function update(string $field, ?string $value, array $data): ?User;

    /**
     * User Schema
     */
    public function userSchema(): array;
}
