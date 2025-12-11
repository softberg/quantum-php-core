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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Storage\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Session
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const DIRECTORY_NOT_EXISTS = 'The directory {%1} does not exists.';

    const DIRECTORY_NOT_WRITABLE = 'The directory {%1} is not writable.';

    const FILE_ALREADY_EXISTS = 'The file {%1} already exists.';

    const FILE_TYPE_NOT_ALLOWED = 'The file type `{%1}` is not allowed.';
}