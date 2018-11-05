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
 * Session class
 * 
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
class Session implements SessionInterface {

    /**
     * Class constructor
     * 
     * @param integer $timeout_duration
     * @return void
     */
    public function __construct($timeout_duration) {

        if (!session_id()) {
            @session_start();
        }
        
        $timeout_duration = $timeout_duration ? $timeout_duration : 1800;

        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration) {
            @session_unset();
            @session_destroy();
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    /**
     * Gets value from session by key
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->has($key) ? $_SESSION[$key] : NULL;
    }

    /*
     * Gets whole session data
     */
    public function all() {
        return $_SESSION;
    }

    /**
     * Check if session contains a key
     * 
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return isset($_SESSION[$key]) ? true : false;
    }

    /**
     * Sets session value with given key
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets flash values by given key
     * 
     * @param string $key
     * @return mixed
     */
    public function getFlash($key) {
        if ($this->has($key)) {
            $flashData = $_SESSION[$key];
            $this->delete($key);
            return $flashData;
        }
    }

    /**
     * Sets flash values with given key
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setFlash($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Delete data with given key from session
     * 
     * @param string $key
     */
    public function delete($key) {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Deletes whole session data
     */
    public function flush() {
        @session_destroy();
    }

}
