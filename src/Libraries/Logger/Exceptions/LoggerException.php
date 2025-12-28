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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Logger\Exceptions;

use Quantum\Libraries\Logger\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class LoggerException
 * @package Quantum\Logger
 */
class LoggerException extends BaseException
{

    /**
     * @param string $name
     * @return LoggerException
     */
    public static function logPathIsNotDirectory(string $name): LoggerException
    {
        return new static(_message(ExceptionMessages::LOG_PATH_NOT_DIRECTORY, [$name]), E_ERROR);
    }

    /**
     * @param string $name
     * @return LoggerException
     */
    public static function logPathIsNotFile(string $name): LoggerException
    {
        return new static(_message(ExceptionMessages::LOG_PATH_NOT_FILE, [$name]), E_ERROR);
    }
}