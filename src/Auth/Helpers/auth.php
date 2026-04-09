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
 * @since 3.0.0
 */

use Quantum\Service\Exceptions\ServiceException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Auth\Factories\AuthFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\Auth\Auth;

/**
 * Gets the Auth handler
 * @throws AuthException|ConfigException|ServiceException|BaseException|DiException|ReflectionException
 */
function auth(?string $adapter = null): Auth
{
    return AuthFactory::get($adapter);
}
