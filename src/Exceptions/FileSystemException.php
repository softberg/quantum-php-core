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
 * @since 2.9.0
 */

namespace Quantum\Exceptions;

/**
 * Class FileSystemException
 * @package Quantum\Exceptions
 */
class FileSystemException extends AppException
{

    /**
     * @param string $name
     * @return FileSystemException
     * @throws LangException
     */
    public static function directoryNotExists(string $name): FileSystemException
    {
        return new static(t('exception.directory_not_exist', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return FileSystemException
     * @throws LangException
     */
    public static function directoryNotWritable(string $name): FileSystemException
    {
        return new static(t('exception.directory_not_writable', $name), E_WARNING);
    }

    /**
     * @return FileSystemException
     * @throws LangException
     */
    public static function fileAlreadyExists(): FileSystemException
    {
        return new static(t('exception.file_already_exists'), E_WARNING);
    }

}
