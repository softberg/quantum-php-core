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

/**
 * Trait Query
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Query
{

    /**
     * @inheritDoc
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        return (self::$ormClass)::raw_execute($query, $parameters);
    }

    /**
     * @inheritDoc
     */
    public static function query(string $query, array $parameters = []): array
    {
        return (self::$ormClass)::for_table('dummy')->raw_query($query, $parameters)->find_array();
    }

    /**
     * @inheritDoc
     */
    public static function lastQuery(): ?string
    {
        return (self::$ormClass)::get_last_query();
    }

    /**
     * @inheritDoc
     */
    public static function lastStatement(): object
    {
        return (self::$ormClass)::get_last_statement();
    }

    /**
     * @inheritDoc
     */
    public static function queryLog(): array
    {
        return (self::$ormClass)::get_query_log();
    }

}