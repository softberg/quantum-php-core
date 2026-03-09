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

namespace Quantum\App\Exceptions;

use Quantum\App\Enums\ExceptionMessages;
use Exception;

/**
 * Class BaseException
 * @package Quantum\App
 */
abstract class BaseException extends Exception
{
    final public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return static
     */
    public static function methodNotSupported(string $methodName, string $className)
    {
        return new static(
            _message(ExceptionMessages::METHOD_NOT_SUPPORTED, [$methodName, $className]),
            E_WARNING
        );
    }

    /**
     * @return static
     */
    public static function adapterNotSupported(string $name)
    {
        return new static(
            _message(ExceptionMessages::ADAPTER_NOT_SUPPORTED, [$name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function driverNotSupported(string $name)
    {
        return new static(
            _message(ExceptionMessages::DRIVER_NOT_SUPPORTED, [$name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function fileNotFound(string $name)
    {
        return new static(
            _message(ExceptionMessages::FILE_NOT_FOUND, [$name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function notFound(string $subject, string $name)
    {
        return new static(
            _message(ExceptionMessages::NOT_FOUND, [$subject, $name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function notInstanceOf(string $instance, string $name)
    {
        return new static(
            _message(ExceptionMessages::NOT_INSTANCE_OF, [$instance, $name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function cantConnect(string $name)
    {
        return new static(
            _message(ExceptionMessages::CANT_CONNECT, [$name]),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function missingConfig(string $name)
    {
        return new static(
            _message(ExceptionMessages::MISSING_CONFIG, $name),
            E_ERROR
        );
    }

    /**
     * @return static
     */
    public static function requestMethodNotAvailable(string $name)
    {
        return new static(
            _message(ExceptionMessages::UNAVAILABLE_REQUEST_METHOD, [$name]),
            E_WARNING
        );
    }
}
