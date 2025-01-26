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
 * @since 2.9.5
 */

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Auth\Factories\AuthFactory;
use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Auth\Auth;

/**
 * Gets the Auth handler
 * @return Auth
 * @throws BaseException
 * @throws AuthException
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws MailerException
 * @throws ReflectionException
 * @throws ServiceException
 */
function auth(): Auth
{
    return AuthFactory::get();
}