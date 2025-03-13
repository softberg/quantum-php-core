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

namespace Quantum\Libraries\Logger\Factories;

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Adapters\MessageAdapter;
use Quantum\Libraries\Logger\Adapters\SingleAdapter;
use Quantum\Libraries\Logger\Adapters\DailyAdapter;
use Quantum\Libraries\Logger\LoggerConfig;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
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
    ];

    /**
     * @var Logger|null
     */
    private static $instance = null;

    /**
     * @return Logger
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(): Logger
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
     * @return Logger
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function createInstance(): Logger
    {
        if (is_debug_mode()) {
            return new Logger(new MessageAdapter());
        }

        if (!config()->has('logging')) {
            config()->import(new Setup('config', 'logging'));
        }

        $adapter = config()->get('logging.default');

        $adapterClass = self::getAdapterClass($adapter);

        $logLevel = config()->get('logging.level', 'error');
        LoggerConfig::setAppLogLevel($logLevel);

        return new Logger(new $adapterClass(config()->get('logging.' . $adapter)));
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