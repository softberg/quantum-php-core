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

namespace Quantum\App\Exceptions;

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
            "The method `$methodName` is not supported for `$className`",
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
            "The adapter `$name` is not supported`",
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
            "The driver `$name` is not supported",
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
            "The file `$name` not found",
            E_ERROR
        );
    }
}