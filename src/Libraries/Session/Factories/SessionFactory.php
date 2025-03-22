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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Session\Factories;

use Quantum\Libraries\Session\Adapters\Database\DatabaseSessionAdapter;
use Quantum\Libraries\Session\Adapters\Native\NativeSessionAdapter;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Session\Session;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class SessionFactory
 * @package Quantum\Libraries\Session
 */
class SessionFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Session::NATIVE => NativeSessionAdapter::class,
        Session::DATABASE => DatabaseSessionAdapter::class,
    ];

    /**
     * @var Session|null
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return Session
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Session
    {
        if (!config()->has('session')) {
            config()->import(new Setup('Config', 'session'));
        }

        $adapter = $adapter ?? config()->get('session.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return Session
     */
    private static function createInstance(string $adapterClass, string $adapter): Session
    {
        return new Session(new $adapterClass(config()->get('session.' . $adapter)));
    }

    /**
     * @param string $adapter
     * @return string
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