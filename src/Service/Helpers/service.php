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
 * @since 2.9.9
 */

use Quantum\Service\Exceptions\ServiceException;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Service\QtService;

/**
 * Gets or creates service instance
 * @param string $serviceClass
 * @param bool $singleton
 * @return QtService
 * @throws ReflectionException
 * @throws BaseException
 * @throws DiException
 * @throws ServiceException
 */
function service(string $serviceClass, bool $singleton = false): QtService
{
    return $singleton ? ServiceFactory::get($serviceClass) : ServiceFactory::create($serviceClass);
}
