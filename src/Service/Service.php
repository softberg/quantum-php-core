<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Service;

use Quantum\Service\Exceptions\ServiceException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Service
 * @package Quantum\Service
 */
abstract class Service
{
    /**
     * Handles the missing methods of the service
     * @param array<mixed> $arguments
     * @return never
     * @throws BaseException
     */
    public function __call(string $method, array $arguments)
    {
        throw ServiceException::methodNotSupported($method, Service::class);
    }
}
