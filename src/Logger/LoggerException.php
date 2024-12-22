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

use Exception;

/**
 * Class LoggerException
 * @package Quantum\Logger
 */
class LoggerException extends Exception
{
    /**
     * @param string $name
     * @return LoggerException
     */
    public static function unsupportedAdapter(string $name): LoggerException
    {
        return new static(t('exception.not_supported_adapter', $name));
    }

    /**
     * @param string $name
     * @return LoggerException
     */
    public static function logPathIsNotDirectory(string $name): LoggerException
    {
        return new static(t('exception.log_path_is_not_directory', $name));
    }

    /**
     * @param string $name
     * @return LoggerException
     */
    public static function logPathIsNotFile(string $name): LoggerException
    {
        return new static(t('exception.log_path_is_not_file', $name));
    }
}