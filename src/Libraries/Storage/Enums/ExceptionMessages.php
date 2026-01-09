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

namespace Quantum\Libraries\Storage\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Session
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const DIRECTORY_NOT_EXISTS = 'The directory {%1} does not exists.';

    public const DIRECTORY_NOT_WRITABLE = 'The directory {%1} is not writable.';

    public const FILE_ALREADY_EXISTS = 'The file {%1} already exists.';

    public const FILE_TYPE_NOT_ALLOWED = 'The file type `{%1}` is not allowed.';
}
