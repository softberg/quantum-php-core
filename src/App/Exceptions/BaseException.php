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
 * @since 2.9.9
 */

namespace Quantum\App\Exceptions;

use Quantum\App\Enums\ExceptionMessages;
use Exception;

/**
 * Class BaseException
 * @package Quantum\App
 */
class BaseException extends Exception
{

    /**
     * @param string $methodName
     * @param string $className
     * @return self
     */
    public static function methodNotSupported(string $methodName, string $className): self
    {
        return new static(
            _message(ExceptionMessages::METHOD_NOT_SUPPORTED, [$methodName, $className]),
            E_WARNING
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function adapterNotSupported(string $name): self
    {
        return new static(
            _message(ExceptionMessages::ADAPTER_NOT_SUPPORTED, [$name]),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function driverNotSupported(string $name): self
    {
        return new static(
            _message(ExceptionMessages::DRIVER_NOT_SUPPORTED, [$name]),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function fileNotFound(string $name): self
    {
        return new static(
            _message(ExceptionMessages::FILE_NOT_FOUND, [$name]),
            E_ERROR
        );
    }

    /**
     * @param string $subject
     * @param string $name
     * @return self
     */
    public static function notFound(string $subject, string $name): self
    {
        return new static(
            _message(ExceptionMessages::NOT_FOUND, [$subject, $name]),
            E_ERROR
        );
    }

    /**
     * @param string $instance
     * @param string $name
     * @return self
     */
    public static function notInstanceOf(string $instance, string $name): self
    {
        return new static(
            _message(ExceptionMessages::NOT_INSTANCE_OF, [$instance, $name]),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function cantConnect(string $name): self
    {
        return new static(
            _message(ExceptionMessages::CANT_CONNECT, [$name]),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return self
     */

    public static function missingConfig(string $name): self
    {
        return new static(
            _message(ExceptionMessages::MISSING_CONFIG, $name),
            E_ERROR
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function requestMethodNotAvailable(string $name): self
    {
        return new static(
            _message(ExceptionMessages::UNAVAILABLE_REQUEST_METHOD, [$name]),
            E_WARNING
        );
    }
}