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

use Quantum\Database\Database;
use Quantum\Di\Di;

/**
 * Gets the Database instance from DI
 */
function db(): Database
{
    if (!Di::isRegistered(Database::class)) {
        Di::register(Database::class);
    }

    return Di::get(Database::class);
}
