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

namespace Quantum\Http\Response;

/**
 * Trait Body
 * @package Quantum\Http\Response
 */
trait Body
{

    /**
     * Response
     * @var array
     */
    private static $__response = [];
    
    /**
     * Checks if response contains a data by given key
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset(self::$__response[$key]);
    }

    /**
     * Gets the data from response by given key
     * @param string $key
     * @param string|null $default
     * @return mixed
     */
    public static function get(string $key, string $default = null)
    {
        return self::has($key) ? self::$__response[$key] : $default;
    }

    /**
     * Sets new key/value pair into response
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, $value)
    {
        self::$__response[$key] = $value;
    }

    /**
     * Gets all response parameters
     * @return array
     */
    public static function all(): array
    {
        return self::$__response;
    }

    /**
     * Deletes the element from response by given key
     * @param string $key
     */
    public static function delete(string $key)
    {
        if (self::has($key)) {
            unset(self::$__response[$key]);
        }
    }

}