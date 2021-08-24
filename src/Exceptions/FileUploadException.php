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
 * @since 2.5.0
 */

namespace Quantum\Exceptions;

/**
 * Class FileUploadException
 * @package Quantum\Exceptions
 */
class FileUploadException extends \Exception
{
    /**
     * File already exists message
     */
    const FILE_ALREADY_EXISTS = 'File already exists';

    /**
     * File was not sent with a POST request message
     */
    const FILE_NOT_UPLOADED = 'The uploaded file was not sent with a POST request';

    /**
     * Directory does not exists message
     */
    const DIRECTORY_NOT_EXIST = 'Directory `{%1}` does not exists';

    /**
     * Directory is not writable message
     */
    const DIRECTORY_NOT_WRITABLE = 'Directory `{%1}` not writable';

    /**
     * Upload file not found message
     */
    const UPLOADED_FILE_NOT_FOUND = 'Cannot find uploaded file identified by key `{%1}`';

    /**
     * Directory can not be created message
     */
    const DIRECTORY_CANT_BE_CREATED = 'Directory `{%1}` could not be created';

    /**
     * File type not allwed message
     */
    const FILE_TYPE_NOT_ALLOWED = 'The file type `{%1}` is not allowed';

    /**
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileAlreadyExists(): FileUploadException
    {
        return new static(self::FILE_ALREADY_EXISTS, E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileNotUploaded(): FileUploadException
    {
        return new static(self::FILE_NOT_UPLOADED, E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function directoryNotExists(string $name): FileUploadException
    {
        return new static(_message(self::DIRECTORY_NOT_EXIST, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function directoryNotWritable(string $name): FileUploadException
    {
        return new static(_message(self::DIRECTORY_NOT_WRITABLE, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileNotFound(string $name): FileUploadException
    {
        return new static(_message(self::UPLOADED_FILE_NOT_FOUND, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileTypeNotAllowed(string $name): FileUploadException
    {
        return new static(_message(self::FILE_TYPE_NOT_ALLOWED, $name), E_WARNING);
    }
}
