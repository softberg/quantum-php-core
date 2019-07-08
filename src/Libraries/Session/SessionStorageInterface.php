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
 * @since 1.5.0
 */

namespace Quantum\Libraries\Session;

/**
 * Session Storage interface
 *
 * The common interface, which should implemented by Session storage classes
 *
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
interface SessionStorageInterface
{

    /**
     * Should be implemented in derived classes to get value from storage by key
     *
     * @param string $key
     */
    public function get($key);

    /**
     * Should be implemented in derived classes to get whole storage data
     */
    public function all();

    /**
     * Should be implemented in derived classes to check if storage contains a key
     *
     * @param string $key
     */
    public function has($key);

    /**
     * Should be implemented in derived classes to set storage value with given key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * Should be implemented in derived classes to get flash values by given key
     *
     * @param string $key
     */
    public function getFlash($key);

    /**
     * Should be implemented in derived classes to set flash values with given key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setFlash($key, $value);

    /**
     * Should be implemented in derived classes to delete data with given key from session
     *
     * @param string $key
     */
    public function delete($key);

    /**
     * Should be implemented in derived classes to delete whole storage data
     */
    public function flush();

    /**
     * Should be implemented in derived classes to get session ID
     */
    public function getSessionId();


}
