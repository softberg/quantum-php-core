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
 * @since 2.9.6
 */

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Session\Factories\SessionFactory;
use Quantum\Libraries\Session\Session;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;

/**
 * @param string|null $adapter
 * @return Session
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function session(?string $adapter = null): Session
{
    return SessionFactory::get($adapter);
}