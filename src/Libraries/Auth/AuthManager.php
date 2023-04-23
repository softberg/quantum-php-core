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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Mailer\MailerManager;
use Quantum\Exceptions\ServiceException;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\MailerException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\AuthException;
use Quantum\Exceptions\LangException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Exceptions\DiException;
use Quantum\Factory\ServiceFactory;
use Quantum\Mvc\QtService;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Auth
 */
class AuthManager
{

    /**
     * Get Handler
     * @return ApiAuth|WebAuth|void
     * @throws AuthException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     * @throws LangException
     * @throws MailerException
     */
    public static function getHandler()
    {
        self::loadConfigs();

        if (!config()->has('mailer') || !config()->has('mailer.current')) {
            config()->import(new Setup('config', 'mailer'));
        }

        switch (config()->get('auth.type')) {
            case 'web':
                return WebAuth::getInstance(self::getAuthService(), MailerManager::getHandler(), new Hasher);
            case 'api':
                $jwt = (new JWToken())->setLeeway(1)->setClaims((array)config()->get('auth.claims'));
                return ApiAuth::getInstance(self::getAuthService(), MailerManager::getHandler(), new Hasher, $jwt);
            default:
                AuthException::undefinedAuthType();
        }
    }

    /**
     * Gets the auth service
     * @return QtService
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    public static function getAuthService(): QtService
    {
        return ServiceFactory::create(config()->get('auth.service'));
    }

    /**
     * @throws ConfigException
     * @throws ReflectionException
     * @throws DiException
     * @throws AuthException
     */
    private static function loadConfigs()
    {
        if (!config()->has('auth')) {
            config()->import(new Setup('Config', 'auth'));
        }

        if (!config()->has('auth.type') && !config()->has('auth.type')) {
            throw AuthException::misconfiguredAuthConfig();
        }
    }
}
