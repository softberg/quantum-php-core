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

/**
 * Class LoggerConfig
 * @package Quantum\Logger
 */
class LoggerConfig
{
    /**
     * Log levels mapped to integer values.
     */
    const LOG_LEVELS = [
        'debug' => 100,
        'info' => 200,
        'notice' => 250,
        'warning' => 300,
        'error' => 400,
        'critical' => 500,
        'alert' => 550,
        'emergency' => 600,
    ];

    /**
     * Default log level.
     */
    const DEFAULT_LOG_LEVEL = 'error';

    /**
     * @var string
     */
    private static $logLevel = self::DEFAULT_LOG_LEVEL;

    /**
     * Set the application's log level.
     * @param string $level
     * @return void
     */
    public static function setAppLogLevel(string $level): void
    {
        if (isset(self::LOG_LEVELS[$level])) {
            self::$logLevel = $level;
        }
    }

    /**
     * Get the integer value of the application's log level.
     * @return int
     */
    public static function getAppLogLevel(): int
    {
        return self::LOG_LEVELS[self::$logLevel] ?? self::LOG_LEVELS[self::DEFAULT_LOG_LEVEL];
    }

    /**
     * Get the integer log level for a given error type.
     * @param string $errorType
     * @return int
     */
    public static function getLogLevel(string $errorType): int
    {
        return self::LOG_LEVELS[$errorType] ?? self::LOG_LEVELS[self::DEFAULT_LOG_LEVEL];
    }
}
