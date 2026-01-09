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

namespace Quantum\Libraries\Auth\Contracts;

use Quantum\Libraries\Auth\User;

/**
 * Interface AuthenticatableInterface
 * @package Quantum\Libraries\Auth
 */
interface AuthenticatableInterface
{
    /**
     * Auth user key
     */
    public const AUTH_USER = 'auth_user';

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function signin(string $username, string $password);

    /**
     * Sign Out
     * @return bool
     */
    public function signout(): bool;

    /**
     * Check
     * @return bool
     */
    public function check(): bool;

    /**
     * User
     * @return User|null
     */
    public function user(): ?User;
}
