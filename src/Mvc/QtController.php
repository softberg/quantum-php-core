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
 * @since 2.6.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ControllerException;
use Quantum\Router\RouteController;

/**
 * Class QtController
 * @package Quantum\Mvc
 */
class QtController extends RouteController
{

    /**
     * @var bool
     */
    public $csrfVerification = true;

    /**
     * Handles the missing methods of the controller
     * @param string $method
     * @param array $arguments
     * @throws ControllerException
     */
    public function __call(string $method, array $arguments)
    {
        throw ControllerException::undefinedMethod($method);
    }
}