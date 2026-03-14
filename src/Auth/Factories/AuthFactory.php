<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Auth\Factories;

use Quantum\Auth\Contracts\AuthServiceInterface;
use Quantum\Service\Exceptions\ServiceException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Hasher\Hasher;
use Quantum\Jwt\JwtToken;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Auth\Auth;

/**
 * Class AuthFactory
 * @package Quantum\Auth
 */
class AuthFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        Auth::SESSION => SessionAuthAdapter::class,
        Auth::JWT => JwtAuthAdapter::class,
    ];

    /**
     * @var array<string, Auth>
     */
    private static array $instances = [];

    /**
     * @throws AuthException
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    public static function get(?string $adapter = null): Auth
    {
        if (!config()->has('auth')) {
            config()->import(new Setup('config', 'auth'));
        }

        $adapter ??= config()->get('auth.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @throws AuthException
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createInstance(string $adapterClass, string $adapter): Auth
    {
        return new Auth(new $adapterClass(
            self::createAuthService($adapter),
            mailer(),
            new Hasher(),
            self::createJwtInstance($adapter)
        ));
    }

    /**
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
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    private static function createAuthService(string $adapter): AuthServiceInterface
    {
        $authServiceClass = config()->get('auth.' . $adapter . '.service');

        $authService = ServiceFactory::create($authServiceClass);

        if (!$authService instanceof AuthServiceInterface) {
            throw AuthException::notInstanceOf($authServiceClass, AuthServiceInterface::class);
        }

        return $authService;
    }

    private static function createJwtInstance(string $adapter): ?JwtToken
    {
        return $adapter === Auth::JWT ? (new JwtToken())->setLeeway(1)->setClaims((array) config()->get('auth.claims')) : null;
    }
}
