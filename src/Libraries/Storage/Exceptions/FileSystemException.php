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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Storage\Exceptions;

use Quantum\Libraries\Storage\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class FileSystemException
 * @package Quantum\Libraries\Storage
 */
class FileSystemException extends BaseException
{

    /**
     * @param string $name
     * @return FileSystemException
     */
    public static function directoryNotExists(string $name): FileSystemException
    {
        return new static(_message(ExceptionMessages::DIRECTORY_NOT_EXISTS, [$name]), E_WARNING);
    }

    /**
     * @param string $name
     * @return FileSystemException
     */
    public static function directoryNotWritable(string $name): FileSystemException
    {
        return new static(_message(ExceptionMessages::DIRECTORY_NOT_WRITABLE, [$name]), E_WARNING);
    }

    /**
     * @param string $path
     * @return FileSystemException
     */
    public static function fileAlreadyExists(string $path): FileSystemException
    {
        return new static(_message(ExceptionMessages::FILE_ALREADY_EXISTS, [$path]), E_WARNING);
    }
}