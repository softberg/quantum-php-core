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

namespace Quantum\Libraries\Hasher\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Libraries\Hasher
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const ALGORITHM_NOT_SUPPORTED = 'The algorithm {%1} not supported.';

    public const INVALID_BCRYPT_COST = 'Provided bcrypt cost is invalid.';
}
