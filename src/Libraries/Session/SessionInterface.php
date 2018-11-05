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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Session;

/**
 * Session interface
 * 
 * The common interface, which should implemented by session classes
 * 
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
interface SessionInterface {
    
    /**
     * Should be implemented in classes to get value from session by key
     * 
     * @param string $key
     */
    public function get($key);
    
    /**
     * Should be implemented in classes to get whole session data
     */
    public function all();
    
    /**
     * Should be implemented in classes to check if session contains a key
     * 
     * @param string $key
     */
    public function has($key);

    /**
     * Should be implemented in classes to set session value with given key
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);
    
    /**
     * Should be implemented in classes to get flash values by given key
     * 
     * @param string $key
     */
    public function getFlash($key);
    
    /**
     * Should be implemented in classes to set flash values with given key
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setFlash($key, $value);

    /**
     * Should be implemented in classes to delete data with given key from session
     * 
     * @param string $key
     */
    public function delete($key);
    
    /**
     * Should be implemented in classes to delete whole session data
     */
    public function flush();

    
}
