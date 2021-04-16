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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Exceptions\ConfigException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Factory\ServiceFactory;
use Quantum\Loader\Loader;
use Quantum\Di\Di;
use stdClass;

/**
 * Class AuthManager
 * @package Quantum\Libraries\Auth
 */
class AuthManager
{

    /**
     * @var AuthenticableInterface
     */
    private static $authInstance = null;

    /**
     * @var string
     */
    private static $authType = null;

    /**
     * GetHandler
     * @return WebAuth|ApiAuth
     * @throws AuthException
     */
    public static function getHandler(Loader $loader)
    {
        $authService = self::authService($loader);

        if (self::$authType && $authService) {
            switch (self::$authType) {
                case 'web':
                    self::$authInstance = new WebAuth($authService, new Mailer, new Hasher);
                    break;
                case 'api':
                    $jwt = (new JWToken())->setLeeway(1)->setClaims((array) config()->get('auth.claims'));
                    self::$authInstance = new ApiAuth($authService, new Mailer, new Hasher, $jwt);
                    break;
            }
        } else {
            throw new AuthException(AuthException::MISCONFIGURED_AUTH_CONFIG);
        }

        return self::$authInstance;
    }

    /**
     * Auth Service
     * @param Loader $loader
     * @return AuthServiceInterface
     * @throws \Exception
     */
    public static function authService(Loader $loader): AuthServiceInterface
    {
        if (!config()->has('auth')) {

            $loaderSetup = new stdClass();
            $loaderSetup->module = current_module();
            $loaderSetup->env = 'config';
            $loaderSetup->fileName = 'auth';
            $loaderSetup->exceptionMessage = ConfigException::CONFIG_FILE_NOT_FOUND;

            $loader->setup($loaderSetup);

            config()->import($loader, 'auth');
        }

        self::$authType = config()->get('auth.type');

        return Di::get(ServiceFactory::class)->create(config()->get('auth.service'));
    }

}
