<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Environment\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Environment
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const IMMUTABLE_ENVIRONMENT = 'The environment is immutable. Modifications are not allowed.';

    public const ENVIRONMENT_NOT_LOADED = 'Environment not loaded. Call `load()` method first.';
}
