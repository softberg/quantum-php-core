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

namespace Quantum\Session\Factories;

use Quantum\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Session\Enums\SessionType;
use Quantum\Session\Session;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class SessionFactory
 * @package Quantum\Session
 */
class SessionFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        SessionType::NATIVE => NativeSessionAdapter::class,
        SessionType::DATABASE => DatabaseSessionAdapter::class,
    ];

    /**
     * @var array<string, Session>
     */
    private static array $instances = [];

    /**
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Session
    {
        if (!config()->has('session')) {
            config()->import(new Setup('config', 'session'));
        }

        $adapter ??= config()->get('session.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    private static function createInstance(string $adapterClass, string $adapter): Session
    {
        return new Session(new $adapterClass(config()->get('session.' . $adapter)));
    }

    /**
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw SessionException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}
