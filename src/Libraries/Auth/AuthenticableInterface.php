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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Auth;

/**
 * Interface AuthenticableInterface
 * @package Quantum\Libraries\Auth
 */
interface AuthenticableInterface
{

    /**
     * Sign In
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function signin(string $username, string $password);

    /**
     * Sign Out
     * @return mixed
     */
    public function signout();

    /**
     * Check
     * @return bool
     */
    public function check(): bool;

    /**
     * User
     * @return \Quantum\Libraries\Auth\User|null
     */
    public function user(): ?User;
}
