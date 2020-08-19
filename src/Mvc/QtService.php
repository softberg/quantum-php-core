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
 * @since 2.0.0
 */

namespace Quantum\Mvc;

use Quantum\Helpers\Helper;
use Quantum\Exceptions\ExceptionMessages;

/**
 * Base Service Class
 *
 * QtService class is a base abstract class that every service should extend,
 *
 * @package Quantum
 * @category MVC
 * @method void __init(...$args)
 */
class QtService
{

    /**
     * Instance of QtService
     * @var QtService
     */
    private static $instance;

    /**
     * Gets the QtService singleton instance
     * @return QtService
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Handles the missing methods of the service
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        throw new \BadMethodCallException(Helper::_message(ExceptionMessages::UNDEFINED_METHOD, $method));
    }

}
