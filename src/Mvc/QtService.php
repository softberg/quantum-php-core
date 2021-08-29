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
 * @since 2.5.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ServiceException;

/**
 * Class QtService
 * @package Quantum\Mvc
 * @method void __init()
 */
class QtService
{

    /**
     * Handles the missing methods of the service
     * @param string $method
     * @param array $arguments
     * @throws \Quantum\Exceptions\ServiceException
     */
    public function __call(string $method, array $arguments)
    {
        throw ServiceException::undefinedMethod($method);
    }

}
