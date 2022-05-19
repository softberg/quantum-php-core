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
     * File was not sent with a POST request message
     */
    const FILE_NOT_UPLOADED = 'The uploaded file was not sent with a POST request';

    /**
     * Upload file not found message
     */
    const UPLOADED_FILE_NOT_FOUND = 'Cannot find uploaded file identified by key `{%1}`';

    /**
     * File type not allowed message
     */
    const FILE_TYPE_NOT_ALLOWED = 'The file type `{%1}` is not allowed';

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
