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
 * @since 2.4.0
 */
namespace Quantum\Libraries\Database\Statements;

/**
 * Trait Query
 * @package Quantum\Libraries\Database\Statements
 */
trait Query
{
    /**
     * Raw execute
     * @inheritDoc
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        return (self::$ormClass)::raw_execute($query, $parameters);
    }

    /**
     * Raw query
     * @inheritDoc
     */
    public static function query(string $query, array $parameters = []): array
    {
        return (self::$ormClass)::for_table('dummy')->raw_query($query, $parameters)->find_array();
    }

    /**
     * Gets the last query executed
     * @inheritDoc
     */
    public static function lastQuery(): string
    {
        return (self::$ormClass)::get_last_query();
    }

    /**
     * Returns the PDOStatement instance last used
     * @inheritDoc
     */
    public static function lastStatement(): object
    {
        return (self::$ormClass)::get_last_statement();
    }

    /**
     * Get an array containing all the queries
     * run on a specified connection up to now.
     *
     * @return array
     */
    public static function queryLog(): array
    {

        return (self::$ormClass)::get_query_log();
    }

}