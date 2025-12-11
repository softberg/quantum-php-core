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

namespace Quantum\Libraries\Database\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Database
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const INCORRECT_CONFIG = 'The structure of config is not correct';

    const NOT_SUPPORTED_OPERATOR = 'The operator `{%1}` is not supported';

    const TABLE_ALREADY_EXISTS = 'The table `{%1}` is already exists';

    const TABLE_NOT_EXISTS = 'The table `{%1}` does not exists';
}