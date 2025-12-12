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

namespace Quantum\Module\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Module
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const MODULE_ROUTES_NOT_FOUND = 'Routes not found for module `{%1}`';

    const MODULE_CONFIG_NOT_FOUND = 'Config not found for a module';

    const MODULE_CREATION_INCOMPLETE = 'Module creation incomplete: missing files.';

    const MISSING_MODULE_TEMPLATE = 'Template `{%1}` does not exist';

    const MISSING_MODULE_DIRECTORY = 'Module directory does not exist, skipping config update.';

    const MODULE_ALREADY_EXISTS = 'A module or prefix named `{%1}` already exists';
}