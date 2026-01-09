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

namespace Quantum\Config\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Config
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const CONFIG_COLLISION = 'Config key `{%1}` is already in use';
}
