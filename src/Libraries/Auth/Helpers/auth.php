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
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Auth\Factories\AuthFactory;
use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Auth\Auth;

/**
 * Gets the Auth handler
 * @param string|null $adapter
 * @return Auth
 * @throws AuthException
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 * @throws ServiceException
 */
function auth(?string $adapter = null): Auth
{
    return AuthFactory::get($adapter);
}