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

namespace Quantum\Storage\Exceptions;

use Quantum\Storage\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class FileSystemException
 * @package Quantum\Storage
 */
class FileSystemException extends BaseException
{
    public static function directoryNotExists(string $name): self
    {
        return new self(
            _message(ExceptionMessages::DIRECTORY_NOT_EXISTS, [$name]),
            E_WARNING
        );
    }

    public static function directoryNotWritable(string $name): self
    {
        return new self(
            _message(ExceptionMessages::DIRECTORY_NOT_WRITABLE, [$name]),
            E_WARNING
        );
    }

    public static function fileAlreadyExists(string $path): self
    {
        return new self(
            _message(ExceptionMessages::FILE_ALREADY_EXISTS, [$path]),
            E_WARNING
        );
    }
}
