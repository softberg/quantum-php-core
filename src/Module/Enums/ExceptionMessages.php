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

namespace Quantum\Module\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Module
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const MODULE_ROUTES_NOT_FOUND = 'Routes not found for module `{%1}`';

    public const MODULE_CONFIG_NOT_FOUND = 'Config not found for a module';

    public const MODULE_CREATION_INCOMPLETE = 'Module creation incomplete: missing files.';

    public const MISSING_MODULE_TEMPLATE = 'Template `{%1}` does not exist';

    public const MISSING_MODULE_DIRECTORY = 'Module directory does not exist, skipping config update.';

    public const MODULE_ALREADY_EXISTS = 'A module or prefix named `{%1}` already exists';
}
