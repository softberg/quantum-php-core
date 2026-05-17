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

namespace Quantum\Storage\Enums;

/**
 * Class FileSystemType
 * @package Quantum\Storage
 * @codeCoverageIgnore
 */
final class FileSystemType
{
    public const LOCAL = 'local';

    public const DROPBOX = 'dropbox';

    public const GDRIVE = 'gdrive';

    private function __construct()
    {
    }
}
