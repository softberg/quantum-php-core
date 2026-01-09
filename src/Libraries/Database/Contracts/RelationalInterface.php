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

namespace Quantum\Libraries\Database\Contracts;

/**
 * Database Abstract Layer interface
 * @package Quantum\Libraries\Database
 */
interface RelationalInterface
{
    /**
     * Raw execute
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    public static function execute(string $query, array $parameters = []): bool;

    /**
     * Raw query
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public static function query(string $query, array $parameters = []): array;

    /**
     * Gets the last query executed
     * @return string|null
     */
    public static function lastQuery(): ?string;

    /**
     * Returns the PDOStatement instance last used
     * @return object
     */
    public static function lastStatement(): object;

    /**
     * Get an array containing all the queries
     * run on a specified connection up to now.
     * @return array
     */
    public static function queryLog(): array;

    /**
     * Adds a simple JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return DbalInterface
     */
    public function join(string $table, array $constraint, ?string $tableAlias = null): DbalInterface;

    /**
     * Adds an INNER JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return DbalInterface
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface;

    /**
     * Adds an LEFT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return DbalInterface
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface;

    /**
     * Adds an RIGHT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return DbalInterface
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface;

}
