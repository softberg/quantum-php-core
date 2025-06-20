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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;

/**
 * Trait Transaction
 * @package Quantum\Libraries\Database
 */
trait Transaction
{

    /**
     * Begins a transaction
     * @throws DatabaseException
     */
    public static function beginTransaction()
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        (self::$ormClass)::get_db()->beginTransaction();
    }

    /**
     * Commits a transaction
     * @throws DatabaseException
     */
    public static function commit(): void
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        (self::$ormClass)::get_db()->commit();
    }

    /**
     * Rolls back a transaction
     * @throws DatabaseException
     */
    public static function rollback(): void
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        (self::$ormClass)::get_db()->rollBack();
    }
}