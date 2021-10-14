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
 * @since 2.6.0
 */

namespace Quantum\Exceptions;

/**
 * Class FileSystemException
 * @package Quantum\Exceptions
 */
class FileSystemException extends \Exception
{
    /**
     * Method not supported message
     */
    const NOT_SUPPORTED_METHOD = 'The method `{%1}` is not supported on current `{%2}` adapter';

    /**
     * @param string $methodName
     * @param string $adapterName
     * @return \Quantum\Exceptions\FileSystemException
     */
    public static function methodNotSupported(string $methodName, string $adapterName): FileSystemException
    {
        return new static(_message(self::NOT_SUPPORTED_METHOD, [$methodName, $adapterName]), E_WARNING);
    }
}
