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
 * @since 2.4.0
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
     * @return \Quantum\Libraries\Auth\ApiAuth|\Quantum\Libraries\Auth\WebAuth
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\LoaderException
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
            throw new AuthException(AuthException::MISCONFIGURED_AUTH_CONFIG);
        }
    }

    /**
     * Auth Service
     * @param \Quantum\Loader\Loader $loader
     * @return \Quantum\Libraries\Auth\AuthServiceInterface
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\LoaderException
     */
    public static function authService(Loader $loader): AuthServiceInterface
    {
        if (!config()->has('auth')) {

            $loader->setup(new Setup('config', 'auth'));

            config()->import($loader, 'auth');
        }

        self::$authType = config()->get('auth.type');

        return Di::get(ServiceFactory::class)->create(config()->get('auth.service'));
    }

}
