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
 * @since 2.8.0
 */

use Quantum\Libraries\Session\SessionManager;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Cache\CacheManager;
use Quantum\Libraries\Auth\AuthManager;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Auth\ApiAuth;
use Quantum\Libraries\Auth\WebAuth;
use Quantum\Libraries\Cache\Cache;
use Quantum\Hooks\HookManager;
use Quantum\Di\Di;

/**
 * Gets the session handler
 * @return Session
 * @throws ReflectionException
 * @throws \Quantum\Exceptions\ConfigException
 * @throws \Quantum\Exceptions\DatabaseException
 * @throws \Quantum\Exceptions\DiException
 * @throws \Quantum\Exceptions\SessionException
 */
function session(): Session
{
    return SessionManager::getHandler();
}

/**
 * Gets cookie handler
 * @return Cookie
 */
function cookie(): Cookie
{
    return Cookie::getInstance($_COOKIE);
}

/**
 * Gets the Auth handler
 * @return ApiAuth|WebAuth
 * @throws ReflectionException
 * @throws \Quantum\Exceptions\AuthException
 * @throws \Quantum\Exceptions\ConfigException
 * @throws \Quantum\Exceptions\DiException
 */
function auth()
{
    return AuthManager::getHandler();
}

/**
 * Gets the Mail instance
 * @return Mailer
 * @throws ReflectionException
 * @throws \Quantum\Exceptions\DiException
 */
function mailer(): Mailer
{
    return Di::get(Mailer::class);
}

/**
 * Gets the AssetManager instance
 * @return AssetManager|null
 */
function asset(): ?AssetManager
{
    return AssetManager::getInstance();
}

/**
 * Gets the HookManager instance
 * @return HookManager
 */
function hook(): HookManager
{
    return HookManager::getInstance();
}

/**
 * Gets the Cache handler
 * @return Cache
 * @throws ReflectionException
 * @throws \Quantum\Exceptions\AppException
 * @throws \Quantum\Exceptions\ConfigException
 * @throws \Quantum\Exceptions\DiException
 */
function cache(): Cache
{
    return CacheManager::getHandler();
}
