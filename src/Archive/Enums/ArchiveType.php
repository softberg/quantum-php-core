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

namespace Quantum\Archive\Enums;

/**
 * Class ArchiveType
 * @package Quantum\Archive
 * @codeCoverageIgnore
 */
final class ArchiveType
{
    public const PHAR = 'phar';

    public const ZIP = 'zip';

    private function __construct()
    {
    }
}
