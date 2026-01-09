<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Libraries\Storage\Exceptions;

use Quantum\App\Enums\ExceptionMessages as ExceptionMessagesAlias;
use Quantum\Libraries\Storage\Enums\ExceptionMessages;
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
    public static function fileTypeNotAllowed(string $name): self
    {
        return new self(
            _message(ExceptionMessages::FILE_TYPE_NOT_ALLOWED, [$name]),
            E_WARNING
        );
    }

    /**
     * @param string $name
     * @return FileUploadException
     */
    public static function incorrectMimeTypesConfig(string $name): self
    {
        return new self(
            _message(ExceptionMessagesAlias::MISSING_CONFIG, $name),
            E_ERROR
        );
    }
}
