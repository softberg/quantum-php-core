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

namespace Quantum\Exceptions;

use Exception;

/**
 * Class AppException
 * @package Quantum\Exceptions
 */
class AppException extends Exception
{

    /**
     * @return AppException
     */
    public static function missingAppKey(): AppException
    {
        return new static(t('exception.app_key_missing'), E_ERROR);
    }

    /**
     * 
     * @param string $methodName
     * @param string $className
     * @return self
     */
    public static function methodNotSupported(string $methodName, string $className): self
    {
        return new static(t('exception.not_supported_method', [$methodName, $className]), E_WARNING);
    }

    /**
     * @param string $driver
     * @return self
     */
    public static function unsupportedDriver(string $driver): self
    {
        return new static(t('exception.not_supported_driver', $driver), E_ERROR);
    }

    /**
     * @param string $name
     * @return self
     */
    public static function fileNotFound(string $name): self
    {
        return new static(t('exception.file_not_found', $name), E_ERROR);
    }

}
