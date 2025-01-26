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

namespace Quantum\Libraries\Auth\Factories;

use Quantum\Libraries\Auth\Contracts\AuthServiceInterface;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Auth\Adapters\WebAdapter;
use Quantum\Libraries\Auth\Adapters\ApiAdapter;
use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Libraries\Jwt\JwtToken;
use Quantum\Factory\ServiceFactory;
use Quantum\Libraries\Auth\Auth;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class AuthFactory
 * @package Quantum\Libraries\Auth
 */
class AuthFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Auth::WEB => WebAdapter::class,
        Auth::API => ApiAdapter::class,
    ];

    /**
     * @var Auth|null
     */
    private static $instance = null;

    /**
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
    public static function get(): Auth
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
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
    private static function createInstance(): Auth
    {
        if (!config()->has('auth')) {
            config()->import(new Setup('Config', 'auth'));
        }

        $adapter = config()->get('auth.type');
        $adapterClass = self::getAdapterClass($adapter);

        $authService = self::createAuthService();
        $mailer = mailer();
        $hasher = new Hasher();
        $jwt = null;

        if ($adapter == Auth::API) {
            $jwt = (new JwtToken())
                ->setLeeway(1)
                ->setClaims((array)config()->get('auth.claims'));
        }

        return new Auth(new $adapterClass($authService, $mailer, $hasher, $jwt));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw AuthException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }

    /**
     * @return AuthServiceInterface
     * @throws AuthException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createAuthService(): AuthServiceInterface
    {
        $authService = ServiceFactory::create(config()->get('auth.service'));

        if (!$authService instanceof AuthServiceInterface) {
            throw AuthException::incorrectAuthService();
        }

        return $authService;
    }
}