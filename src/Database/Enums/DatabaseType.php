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

namespace Quantum\Database\Enums;

/**
 * Class DatabaseType
 * @package Quantum\Database
 */
final class DatabaseType
{
    public const SLEEKDB = 'sleekdb';

    public const MYSQL = 'mysql';

    public const SQLITE = 'sqlite';

    public const PGSQL = 'pgsql';

    private function __construct()
    {
    }
}
