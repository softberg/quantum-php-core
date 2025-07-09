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
 * @since 2.9.8
 */

namespace Quantum\Service;

use Quantum\Service\Exceptions\ServiceException;

/**
 * Class QtService
 * @package Quantum\Service
 */
class QtService
{

    /**
     * Handles the missing methods of the service
     * @param string $method
     * @param array $arguments
     * @throws ServiceException
     */
    public function __call(string $method, array $arguments)
    {
        throw ServiceException::undefinedMethod($method);
    }
}