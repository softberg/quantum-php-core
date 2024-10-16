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
 * @since 2.9.0
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
     * Type array
     */
    const TYPE_ARRAY = 1;

    /**
     * Type object
     */
    const TYPE_OBJECT = 2;

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
     * @return DbalInterface
     */
    public function select(...$columns): DbalInterface;

    /**
     * Finds the record by primary key
     * @param int $id
     * @return DbalInterface
     */
    public function findOne(int $id): DbalInterface;

    /**
     * Finds the record by given column and value
     * @param string $column
     * @param mixed $value
     * @return DbalInterface
     */
    public function findOneBy(string $column, $value): DbalInterface;

    /**
     * Gets the first item
     * @return DbalInterface
     */
    public function first(): DbalInterface;

    /**
     * Adds a criteria to query
     * @param string $column
     * @param string $operator
     * @param mixed|null $value
     * @return DbalInterface
     */
    public function criteria(string $column, string $operator, $value = null): DbalInterface;

    /**
     * Adds criteria to query
     * @param array ...$criterias
     * @return DbalInterface
     */
    public function criterias(...$criterias): DbalInterface;

    /**
     * Adds having criteria to query
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * @return DbalInterface
     */
    public function having(string $column, string $operator, string $value = null): DbalInterface;

    /**
     * Groups the result by given column
     * @param string $column
     * @return DbalInterface
     */
    public function groupBy(string $column): DbalInterface;

    /**
     * Orders the result by ascending or descending
     * @param string $column
     * @param string $direction
     * @return DbalInterface
     */
    public function orderBy(string $column, string $direction): DbalInterface;

    /**
     * Returns the result by given offset (works when limit also applied)
     * @param integer $offset
     * @return DbalInterface
     */
    public function offset(int $offset): DbalInterface;

    /**
     * Returns the limited result set
     * @param int $limit
     * @return DbalInterface
     */
    public function limit(int $limit): DbalInterface;

    /**
     * Gets the result set
     * @return mixed
     */
    public function get();

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return PaginatorInterface
     */
    public function paginate(int $perPage, int $currentPage = 1): PaginatorInterface;

    /**
     * Returns the count
     * @return int
     */
    public function count(): int;

    /**
     * Returns the data as array
     * @return array
     */
    public function asArray(): array;

    /**
     * Sets or gets the model property
     * @param string $key
     * @param mixed|null $value
     * @return void|mixed|null
     */
    public function prop(string $key, $value = null);

    /**
     * Creates new db record
     * @return DbalInterface
     */
    public function create(): DbalInterface;

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
     * Deletes many records by previously applied criteria
     * @return bool
     */
    public function deleteMany(): bool;

    /**
     * Joins two models
     * @param QtModel $model
     * @param bool $switch
     * @return DbalInterface
     */
    public function joinTo(QtModel $model, bool $switch = true): DbalInterface;

    /**
     * Joins through connector model
     * @param QtModel $model
     * @param bool $switch
     * @return DbalInterface
     */
    public function joinThrough(QtModel $model, bool $switch = true): DbalInterface;
}
