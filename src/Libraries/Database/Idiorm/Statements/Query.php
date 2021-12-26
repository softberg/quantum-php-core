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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Database\Idiorm\Statements;

use Quantum\Exceptions\DatabaseException;

/**
 * Trait Query
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Query
{

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        return (self::$ormClass)::raw_execute($query, $parameters);
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function query(string $query, array $parameters = []): array
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        return (self::$ormClass)::for_table('dummy')->raw_query($query, $parameters)->find_array();
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function lastQuery(): ?string
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        return (self::$ormClass)::get_last_query();
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function lastStatement(): object
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        return (self::$ormClass)::get_last_statement();
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function queryLog(): array
    {
        if (!self::getConnection()) {
            throw DatabaseException::missingConfig();
        }

        return (self::$ormClass)::get_query_log();
    }

}