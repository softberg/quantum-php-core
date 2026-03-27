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

use Quantum\Auth\Contracts\AuthenticatableInterface;
use Quantum\Auth\Contracts\AuthServiceInterface;
use Quantum\Service\Exceptions\ServiceException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Auth\Adapters\SessionAuthAdapter;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Auth\Adapters\JwtAuthAdapter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Auth\Enums\AuthType;
use Quantum\Service\QtService;
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
        AuthType::SESSION => SessionAuthAdapter::class,
        AuthType::JWT => JwtAuthAdapter::class,
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
        $authService = self::createAuthService($adapter);

        $adapterInstance = $adapter === AuthType::JWT
            ? new $adapterClass($authService, mailer(), new Hasher(), self::createJwtInstance())
            : new $adapterClass($authService, mailer(), new Hasher());

        if (!$adapterInstance instanceof AuthenticatableInterface) {
            throw AuthException::adapterNotSupported($adapter);
        }

        return new Auth($adapterInstance);
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

        /** @var class-string<QtService> $authServiceClass */
        $authService = ServiceFactory::create($authServiceClass);

        if (!$authService instanceof AuthServiceInterface) {
            throw AuthException::notInstanceOf($authServiceClass, AuthServiceInterface::class);
        }

        return $authService;
    }

    private static function createJwtInstance(): JwtToken
    {
        return (new JwtToken())
            ->setLeeway(1)
            ->setClaims((array) config()->get('auth.claims'));
    }
}
