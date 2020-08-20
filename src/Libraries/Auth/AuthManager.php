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

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\AuthException;
use Quantum\Libraries\JWToken\JWToken;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Factory\ServiceFactory;
use Quantum\Loader\Loader;
use stdClass;

/**
 * Class AuthManager
 *
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
    private $authType = null;

    /**
     * Get
     * @return WebAuth|ApiAuth|AuthenticableInterface
     * @throws AuthException
     */
    public function get()
    {
        return self::$authInstance;
    }

    /**
     * AuthManager constructor.
     * @throws AuthException
     */
    public function __construct()
    {
        $authService = $this->authService();

        if ($this->authType && $authService) {
            switch ($this->authType) {
                case 'web':
                    self::$authInstance = new WebAuth(/** @scrutinizer ignore-type */ $authService, new Hasher);
                    break;
                case 'api':
                    $jwt = (new JWToken())->setLeeway(1)->setClaims((array) config()->get('auth.claims'));
                    self::$authInstance = new ApiAuth(/** @scrutinizer ignore-type */ $authService, new Hasher, $jwt);
                    break;
            }
        } else {
            throw new AuthException(ExceptionMessages::MISCONFIGURED_AUTH_CONFIG);
        }
    }

    /**
     * Auth Service
     * @return \Quantum\Mvc\QtService
     * @throws \Exception
     */
    public function authService()
    {
        if (!config()->has('auth')) {

            $loaderSetup = new stdClass();
            $loaderSetup->module = current_module();
            $loaderSetup->env = 'config';
            $loaderSetup->fileName = 'auth';
            $loaderSetup->exceptionMessage = ExceptionMessages::CONFIG_FILE_NOT_FOUND;

            $loader = (new Loader())->setup($loaderSetup);

            config()->import($loader, 'auth');
        }

        $this->authType = config()->get('auth.type');

        return (new ServiceFactory)->create(config()->get('auth.service'));
    }

}
