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

use Quantum\Libraries\Session\SessionManager;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Auth\AuthManager;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Di\Di;

/**
 * Gets the session handler
 * @return \Quantum\Libraries\Session\Session
 * @throws \Quantum\Exceptions\DatabaseException
 * @throws \Quantum\Exceptions\SessionException
 */
function session(): Session
{
    return SessionManager::getHandler();
}

/**
 * Gets cookie handler
 * @return Quantum\Libraries\Cookie\Cookie
 */
function cookie(): Cookie
{
    return new Cookie($_COOKIE, new Cryptor);
}

/**
 * Gets the Auth handler
 * @return \Quantum\Libraries\Auth\ApiAuth|\Quantum\Libraries\Auth\WebAuth
 * @throws \Quantum\Exceptions\AuthException
 * @throws \Quantum\Exceptions\ConfigException
 * @throws \Quantum\Exceptions\DiException
 * @throws \ReflectionException
 */
function auth()
{
    return AuthManager::getHandler();
}

/**
 * Gets the Mail instance
 * @return \Quantum\Libraries\Mailer\Mailer
 * @throws \Quantum\Exceptions\DiException
 * @throws \ReflectionException
 */
function mailer(): Mailer
{
    return Di::get(Mailer::class);
}

/**
 * Gets the AssetManager instance
 * @return \Quantum\Libraries\Asset\AssetManager|null
 */
function asset(): ?AssetManager
{
    return AssetManager::getInstance();
}
