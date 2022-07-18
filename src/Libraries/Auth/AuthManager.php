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

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Factory\ServiceFactory;
use Quantum\Mvc\QtService;
use Quantum\Loader\Setup;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Auth
 */
class AuthManager
{

    /**
     * Get Handler
     * @return \Quantum\Libraries\Auth\ApiAuth|\Quantum\Libraries\Auth\WebAuth
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function getHandler()
    {
        self::loadConfigs();
            
        switch (config()->has('auth.type')) {
            case 'web':
                return WebAuth::getInstance(self::getAuthService(), new Mailer, new Hasher);
            case 'api':
                $jwt = (new JWToken())->setLeeway(1)->setClaims((array) config()->get('auth.claims'));
                return ApiAuth::getInstance(self::getAuthService(), new Mailer, new Hasher, $jwt);
            default :
                AuthException::undefinedAuthType();
        }
    }

    /**
     * Gets the auth service
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\ConfigException
     */
    public static function getAuthService(): QtService
    {
        return ServiceFactory::create(config()->get('auth.service'));
    }

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
