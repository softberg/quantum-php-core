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

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use PDOException;

/**
 * Trait Query
 * @package Quantum\Libraries\Database
 */
trait Query
{

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        try {
            return (self::$ormClass)::raw_execute($query, $parameters);
        } catch (PDOException $e) {
            throw new DatabaseException(
                $e->getMessage(),
                (int) $e->getCode(),
            );
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public static function query(string $query, array $parameters = []): array
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        try {
            return (self::$ormClass)::for_table('dummy')
                ->raw_query($query, $parameters)
                ->find_array();
        } catch (PDOException $e) {
            throw new DatabaseException(
                $e->getMessage(),
                (int) $e->getCode(),
            );
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public static function lastQuery(): ?string
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        return (self::$ormClass)::get_last_query();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public static function lastStatement(): object
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        return (self::$ormClass)::get_last_statement();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public static function queryLog(): array
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig('database');
        }

        return (self::$ormClass)::get_query_log();
    }

    /**
     * Fetches columns of the table
     * @param string $table
     * @return array
     * @throws DatabaseException
     */
    public static function fetchColumns(string $table): array
    {
        $columns = [];

        self::query('SELECT * FROM ' . $table);
        $statement = self::lastStatement();

        for ($i = 0; $i < $statement->columnCount(); $i++) {
            $columns[] = $statement->getColumnMeta($i)['name'];
        }

        return $columns;
    }
}