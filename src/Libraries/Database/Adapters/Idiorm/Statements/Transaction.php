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

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\App\Exceptions\BaseException;

/**
 * Trait Transaction
 * @package Quantum\Libraries\Database
 */
trait Transaction
{

    /**
     * Begins a transaction
     * @throws BaseException
     */
    public static function beginTransaction()
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        (self::$ormClass)::get_db()->beginTransaction();
    }

    /**
     * Commits a transaction
     * @throws BaseException
     */
    public static function commit(): void
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        (self::$ormClass)::get_db()->commit();
    }

    /**
     * Rolls back a transaction
     * @throws BaseException
     */
    public static function rollback(): void
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        (self::$ormClass)::get_db()->rollBack();
    }
}