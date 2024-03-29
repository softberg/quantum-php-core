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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class AppException
 * @package Quantum\Exceptions
 */
class AppException extends \Exception
{

    /**
     * @return \Quantum\Exceptions\AppException
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

}
