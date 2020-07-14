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
use Quantum\Routes\RouteController;
use Quantum\Exceptions\ExceptionMessages;
use BadMethodCallException;


/**
 * Base Controller Class
 *
 * QtController class is a base class that every controller should extend
 *
 * @package Quantum
 * @category MVC
 */
class QtController extends RouteController
{

    /**
     * Instance of QtController
     * @var QtController
     */
    private static $instance;

    /**
     * Gets the QtController singleton instance
     * @return QtController
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Handles the missing methods of the controller
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        throw new BadMethodCallException(Helper::_message(ExceptionMessages::UNDEFINED_METHOD, $method));
    }

}
