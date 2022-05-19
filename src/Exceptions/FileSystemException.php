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
     * Directory does not exists message
     */
    const DIRECTORY_NOT_EXIST = 'Directory `{%1}` does not exists';

    /**
     * Directory is not writable message
     */
    const DIRECTORY_NOT_WRITABLE = 'Directory `{%1}` not writable';

    /**
     * Directory can not be created message
     */
    const DIRECTORY_CANT_BE_CREATED = 'Directory `{%1}` could not be created';

    /**
     * File already exists message
     */
    const FILE_ALREADY_EXISTS = 'File already exists';

    /**
     * @param string $methodName
     * @param string $adapterName
     * @return \Quantum\Exceptions\FileSystemException
     */
    public static function methodNotSupported(string $methodName, string $adapterName): FileSystemException
    {
        return new static(_message(self::NOT_SUPPORTED_METHOD, [$methodName, $adapterName]), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileSystemException
     */
    public static function directoryNotExists(string $name): FileSystemException
    {
        return new static(_message(self::DIRECTORY_NOT_EXIST, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileSystemException
     */
    public static function directoryNotWritable(string $name): FileSystemException
    {
        return new static(_message(self::DIRECTORY_NOT_WRITABLE, $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\FileSystemException
     */
    public static function fileAlreadyExists(): FileSystemException
    {
        return new static(self::FILE_ALREADY_EXISTS, E_WARNING);
    }

}
