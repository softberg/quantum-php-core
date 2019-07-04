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
 * Session class
 *
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
class Session
{

    /**
     * @var SessionStorage $sessionStorage
     */
    private $sessionStorage;

    /**
     * NativeSession constructor.
     *
     * @param SessionInterface $session
     * @return void
     */
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * Gets value from session by given key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->has($key) ? $this->sessionStorage->get($key) : null;
    }

    /*
     * Gets whole session data
     *
     * @return array
     */
    public function all()
    {
        return $this->sessionStorage->all();
    }

    /**
     * Check if session contains a data by given key
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->sessionStorage->has($key);
    }

    /**
     * Sets session value by given key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->sessionStorage->set($key, $value);
    }

    /**
     * Gets flash values by given key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getFlash($key)
    {
        $flashData = null;

        if ($this->has($key)) {
            $flashData = $this->get($key);
            $this->delete($key);
        }

        return $flashData;
    }

    /**
     * Sets flash values by given key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setFlash($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Delete data from session by given key
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            $this->sessionStorage->delete($key);
        }
    }

    /**
     * Deletes whole session data
     *
     * @return void
     */
    public function flush()
    {
        $this->sessionStorage->flush();
    }

    /**
     * Gets session Id
     *
     * @return null|string
     */
    public function getSessionId()
    {
        $this->sessionStorage->getSessionId();
    }
}
