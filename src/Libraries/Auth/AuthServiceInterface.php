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
 * @since 1.9.0
 */

namespace Quantum\Libraries\Auth;

/**
 * Interface AuthServiceInterface
 *
 * @package Quantum\Libraries\Auth
 */
interface AuthServiceInterface
{

    /**
     * Get
     *
     * @param string $username
     * @return mixed
     */
    public function get($username);

    /**
     * Add
     *
     * @param array $user
     * @return mixed
     */
    public function add($user);

    /**
     * Update
     *
     * @param mixed $username
     * @param array $data
     * @return mixed
     */
    public function update($field, $data);

    /**
     * Get Visible Fields
     *
     * @return mixed
     */
    public function getVisibleFields();

    /**
     * Get Defined Keys
     *
     * @return array
     */
    public function getDefinedKeys();
}
