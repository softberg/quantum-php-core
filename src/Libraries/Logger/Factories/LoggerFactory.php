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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Logger\Factories;

use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Adapters\MessageAdapter;
use Quantum\Libraries\Logger\Adapters\SingleAdapter;
use Quantum\Libraries\Logger\Adapters\DailyAdapter;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Libraries\Logger\LoggerConfig;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Logger\Logger;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class LoggerFactory
 * @package Quantum\Logger
 */
class LoggerFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Logger::SINGLE => SingleAdapter::class,
        Logger::DAILY => DailyAdapter::class,
        Logger::MESSAGE => MessageAdapter::class,
    ];

    /**
     * @var Logger|null
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return Logger
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Logger
    {
        if (!config()->has('logging')) {
            config()->import(new Setup('config', 'logging'));
        }

        $isDebug = is_debug_mode();

        if (!$isDebug && $adapter === Logger::MESSAGE) {
            throw LoggerException::adapterNotAllowed(Logger::MESSAGE);
        }

        $adapter = $isDebug ? Logger::MESSAGE : ($adapter ?? config()->get('logging.default'));

        $adapterClass = self::getAdapterClass($adapter);

        $logLevel = config()->get('logging.' . $adapter . '.level', 'error');

        LoggerConfig::setAppLogLevel($logLevel);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return Logger
     */
    private static function createInstance(string $adapterClass, string $adapter): Logger
    {
        return $adapter === Logger::MESSAGE
            ? new Logger(new MessageAdapter())
            : new Logger(new $adapterClass(config()->get('logging.' . $adapter)));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw LoggerException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}