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

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Factory\ServiceFactory;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Auth
 */
class AuthManager
{

    /**
     * @var string|null
     */
    private static $authType = null;

    /**
     * Get Handler
     * @param \Quantum\Loader\Loader $loader
     * @return \Quantum\Libraries\Auth\ApiAuth|\Quantum\Libraries\Auth\WebAuth|void
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function getHandler(Loader $loader)
    {
        $authService = self::authService($loader);

        if (self::$authType && $authService) {
            switch (self::$authType) {
                case 'web':
                    return WebAuth::getInstance($authService, new Mailer, new Hasher);
                case 'api':
                    $jwt = (new JWToken())->setLeeway(1)->setClaims((array)config()->get('auth.claims'));
                    return ApiAuth::getInstance($authService, new Mailer, new Hasher, $jwt);
            }
        } else {
            throw AuthException::misconfiguredAuthConfig();
        }
    }

    /**
     * Gets the auth service
     * @param \Quantum\Loader\Loader $loader
     * @return \Quantum\Libraries\Auth\AuthServiceInterface
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function authService(Loader $loader): AuthServiceInterface
    {
        if (!config()->has('auth')) {
            config()->import(new Setup('config', 'auth'), 'auth');
        }

        self::$authType = config()->get('auth.type');

        return Di::get(ServiceFactory::class)->create(config()->get('auth.service'));
    }

}
