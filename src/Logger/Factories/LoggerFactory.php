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

namespace Quantum\Logger\Factories;

use Quantum\Logger\Contracts\ReportableInterface;
use Quantum\Logger\Exceptions\LoggerException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Logger\Adapters\SingleAdapter;
use Quantum\Logger\Adapters\DailyAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Logger\Enums\LoggerType;
use Quantum\Logger\LoggerConfig;
use Quantum\Logger\Logger;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class LoggerFactory
 * @package Quantum\Logger
 */
class LoggerFactory
{
    public const ADAPTERS = [
        LoggerType::SINGLE => SingleAdapter::class,
        LoggerType::DAILY => DailyAdapter::class,
        LoggerType::MESSAGE => MessageAdapter::class,
    ];

    /**
     * @var array<string, Logger>
     */
    private array $instances = [];

    /**
     * @throws ConfigException|DiException|BaseException|ReflectionException
     */
    public static function get(?string $adapter = null): Logger
    {
        return Di::get(self::class)->resolve($adapter);
    }

    /**
     * @throws ConfigException|DiException|BaseException|ReflectionException
     */
    public function resolve(?string $adapter = null): Logger
    {
        if (!config()->has('logging')) {
            config()->import(new Setup('config', 'logging'));
        }

        $isDebug = is_debug_mode();

        if (!$isDebug && $adapter === LoggerType::MESSAGE) {
            throw LoggerException::adapterNotSupported(LoggerType::MESSAGE);
        }

        $adapter = $isDebug ? LoggerType::MESSAGE : ($adapter ?? config()->get('logging.default'));

        $adapterClass = $this->getAdapterClass($adapter);

        $logLevel = config()->get('logging.' . $adapter . '.level', 'error');

        LoggerConfig::setAppLogLevel($logLevel);

        if (!isset($this->instances[$adapter])) {
            $this->instances[$adapter] = $this->createInstance($adapterClass, $adapter);
        }

        return $this->instances[$adapter];
    }

    /**
     * @throws DiException|BaseException|ReflectionException
     */
    private function createInstance(string $adapterClass, string $adapter): Logger
    {
        if ($adapter === LoggerType::MESSAGE) {
            return new Logger(new MessageAdapter());
        }

        $adapterInstance = new $adapterClass(config()->get('logging.' . $adapter));

        if (!$adapterInstance instanceof ReportableInterface) {
            throw LoggerException::adapterNotSupported($adapter);
        }

        return new Logger($adapterInstance);
    }

    /**
     * @throws BaseException
     */
    private function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw LoggerException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}
