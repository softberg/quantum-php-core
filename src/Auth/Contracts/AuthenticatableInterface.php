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
 * Interface AuthenticatableInterface
 * @package Quantum\Auth
 */
interface AuthenticatableInterface
{
    /**
     * Auth user key
     */
    public const AUTH_USER = 'auth_user';

    /**
     * Sign In
     * @return mixed
     */
    public function signin(string $username, string $password);

    /**
     * Sign Out
     */
    public function signout(): bool;

    /**
     * Check
     */
    public function check(): bool;

    /**
     * User
     */
    public function user(): ?User;
}
