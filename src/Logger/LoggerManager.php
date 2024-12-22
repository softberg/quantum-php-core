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

namespace Quantum\Logger;

use Quantum\Libraries\Config\ConfigException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class LoggerManager
 * @package Quantum\Logger
 */
class LoggerManager
{
    const ADAPTERS = [
        'single',
        'daily',
    ];

    /**
     * @return Logger
     * @throws ConfigException
     * @throws DiException
     * @throws LoggerException
     * @throws ReflectionException
     */
    public static function getHandler(): Logger
    {
        if(is_debug_mode()) {
            return LoggerFactory::createLogger();
        }

        if (!config()->has('logging')) {
            config()->import(new Setup('config', 'logging'));
        }

        $logAdapter = config()->get('logging.current');

        if (!in_array($logAdapter, self::ADAPTERS)) {
            throw LoggerException::unsupportedAdapter($logAdapter);
        }

        $logLevel = config()->get('logging.level', 'error');
        LoggerConfig::setAppLogLevel($logLevel);

        $logAdapterClass = __NAMESPACE__ . '\\Adapters\\' . ucfirst($logAdapter) . 'Adapter';

        $logAdapter = new $logAdapterClass(config()->get('logging.' . $logAdapter));

        return LoggerFactory::createLogger($logAdapter);
    }

}