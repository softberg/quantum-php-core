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

namespace Quantum\Libraries\Storage\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class FileUploadException
 * @package Quantum\Libraries\Storage
 */
class FileUploadException extends BaseException
{

    /**
     * @param string $name
     * @return FileUploadException
     */
    public static function fileTypeNotAllowed(string $name): FileUploadException
    {
        return new static(t('exception.file_type_not_allowed', $name), E_WARNING);
    }
}