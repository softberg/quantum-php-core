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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * FileUploadException class
 *
 * @package Quantum
 * @category Exceptions
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
}
