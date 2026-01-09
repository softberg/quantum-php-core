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

namespace Quantum\Migration\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Migration
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const WRONG_MIGRATION_DIRECTION = 'Migration direction can only be [up] or [down]';

    public const NOT_SUPPORTED_ACTION = 'The action `{%1}`, is not supported';

    public const NOTHING_TO_MIGRATE = 'Nothing to migrate';
}
