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

namespace Quantum\Libraries\Database;

use Quantum\Mvc\QtModel;

/**
 * Database Abstract Layer interface
 * @package Quantum\Libraries\Database
 */
interface DbalInterface
{

    /**
     * Connects to database
     * @param array $config
     */
    public static function connect(array $config);

    /**
     * Gets the active connection
     * @return array|null
     */
    public static function getConnection(): ?array;

    /**
     * Close the database connection
     */
    public static function disconnect();

    /**
     * Gets the table
     * @return string
     */
    public function getTable(): string;

    /**
     * Selects the values from provided table columns
     * @param mixed $columns
     * @return object
     */
    public function select(...$columns): object;

    /**
     * Gets the ORM model
     * @return object
     */
    public function getOrmModel(): object;

    /**
     * Updates the ORM model
     * @param object $ormModel
     */
    public function updateOrmModel(object $ormModel);

    /**
     * Finds the record by primary key
     * @param int $id
     * @return object
     */
    public function findOne(int $id): object;

    /**
     * Finds the record by given column and value
     * @param string $column
     * @param mixed $value
     * @return object
     */
    public function findOneBy(string $column, $value): object;

    /**
     * Gets the first item
     * @return object
     */
    public function first(): object;

    /**
     * Adds a criteria to query
     * @param string $column
     * @param string $operator
     * @param mixed|null $value
     * @return object
     */
    public function criteria(string $column, string $operator, $value = null): object;

    /**
     * Adds criteria to query
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias): object;

    /**
     * Orders the result by ascending or descending
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy(string $column, string $direction): object;

    /**
     * Groups the result by given column
     * @param string $column
     * @return object
     */
    public function groupBy(string $column): object;

    /**
     * Returns the limited result set
     * @param int $limit
     * @return object
     */
    public function limit(int $limit): object;

    /**
     * Returns the result by given offset (works when limit also applied)
     * @param integer $offset
     * @return object
     */
    public function offset(int $offset): object;

    /**
     * Gets the result set
     * @param int|null $returnType
     * @return mixed
     */
    public function get(?int $returnType);

    /**
     * Returns the count
     * @return int
     */
    public function count(): int;

    /**
     * Returns the result as array
     * @return array
     */
    public function asArray(): array;

    /**
     * Creates new db record
     * @return object
     */
    public function create(): object;

    /**
     * Saves the data into the database
     * @return bool
     */
    public function save(): bool;

    /**
     * Deletes the record from the database
     * @return bool
     */
    public function delete(): bool;

    /**
     * Deletes all records by previously applied criteria
     * @return bool
     */
    public function deleteAll(): bool;

    /**
     * Adds a simple JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return object
     */
    public function join(string $table, array $constraint, ?string $tableAlias = null): object;

    /**
     * Adds an INNER JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return object
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): object;

    /**
     * Adds an LEFT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return object
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): object;

    /**
     * Adds an RIGHT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $tableAlias
     * @return object
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): object;

    /**
     * Joins two models
     * @param QtModel $model
     * @param bool $switch
     * @return object
     */
    public function joinTo(QtModel $model, bool $switch = true): object;

    /**
     * Joins through connector model
     * @param QtModel $model
     * @param bool $switch
     * @return object
     */
    public function joinThrough(QtModel $model, bool $switch = true): object;

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


}
