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

use Quantum\Storage\StorageInterface;

/**
 * Interface SessionStorageInterface
 *
 * @package Quantum\Libraries\Session
 */
interface SessionStorageInterface extends StorageInterface
{

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
     * Should be implemented in derived classes to get session ID
     */
    public function getSessionId();


}
