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

namespace Quantum\Hook\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Hook
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const DUPLICATE_HOOK_NAME = 'The Hook `{%1}` already registered.';

    const UNREGISTERED_HOOK_NAME = 'The Hook `{%1}` was not registered.';
}