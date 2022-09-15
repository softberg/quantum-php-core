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
 * Class FileUploadException
 * @package Quantum\Exceptions
 */
class FileUploadException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileNotUploaded(): FileUploadException
    {
        return new static(t('exception.file_not_uploaded'), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileNotFound(string $name): FileUploadException
    {
        return new static(t('exception.uploaded_file_not_found', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\FileUploadException
     */
    public static function fileTypeNotAllowed(string $name): FileUploadException
    {
        return new static(t('exception.file_type_not_allowed', $name), E_WARNING);
    }

}
