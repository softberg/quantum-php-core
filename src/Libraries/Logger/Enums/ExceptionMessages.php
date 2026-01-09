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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Logger\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Logger
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const LOG_PATH_NOT_DIRECTORY = 'Log path is not point to a directory.';

    public const LOG_PATH_NOT_FILE = 'Log path is not point to a file.';
}
