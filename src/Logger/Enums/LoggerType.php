<?php

declare(strict_types=1);

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

namespace Quantum\Logger\Enums;

/**
 * Class LoggerType
 * @package Quantum\Logger
 */
final class LoggerType
{
    public const SINGLE = 'single';

    public const DAILY = 'daily';

    public const MESSAGE = 'message';

    private function __construct()
    {
    }
}
