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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Database;

use Quantum\Mvc\QtModel;

/**
 * Database Abstract Layer interface
 *
 * The common interface for DBAL, which should implemented by all DBAL classes
 *
 * @package Quantum
 * @category Libraries
 */
interface DbalInterface
{

    /**
     * Get table
     * @return string
     */
    public function getTable();

    /**
     * DB Connect
     * @param array $connectionDetails
     * @return array
     */
    public static function dbConnect($connectionDetails);

    /**
     * Select
     * @param mixed $columns
     * @return array
     */
    public function select(...$columns);

    /**
     * Find One
     * @param int $id
     * @return object
     */
    public function findOne($id);

    /**
     * FindOneBy
     * @param string $column
     * @param mixed $value
     * @return object
     */
    public function findOneBy($column, $value);

    /**
     * First
     * @return object
     */
    public function first();

    /**
     * Criterias
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias);

    /**
     * Order By
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy($column, $direction);

    /**
     * Group By
     * @param string $column
     * @return object
     */
    public function groupBy($column);

    /**
     * Limit
     * @param integer $params
     * @return object
     */
    public function limit($limit);

    /**
     * Offset
     * @param integer $offset
     * @return object
     */
    public function offset($offset);

    /**
     * Get
     * @param null|string $returnType
     * @return mixed
     */
    public function get($returnType = null);

    /**
     * Count
     * @return integer
     */
    public function count();

    /**
     * asArray
     * @return array
     */
    public function asArray();

    /**
     * Create
     * @return object
     */
    public function create();

    /**
     * Save
     * @return bool
     */
    public function save();

    /**
     * Delete
     * @return bool
     */
    public function delete();

    /**
     * Delete All
     * @return bool
     */
    public function deleteAll();

    /**
     * Join
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function join($table, $constraint, $tableAlias = null);

    /**
     * Inner Join
     * Add an INNER JOIN souce to the query
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function innerJoin($table, $constraint, $tableAlias = null);

    /**
     * Left Join
     * Add an LEFT JOIN souce to the query
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function leftJoin($table, $constraint, $tableAlias = null);

    /**
     * Right Join
     * Add an RIGHT JOIN souce to the query
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function rightJoin($table, $constraint, $tableAlias = null);

    /**
     * Joins two models
     * @param QtModel $model
     * @return object
     */
    public function joinTo(QtModel $model);

    /**
     * Joins through connector model
     * @param QtModel $model
     * @return bool $switch
     */
    public function joinThrough(QtModel $model, $switch = true);

    /**
     * Gets the last query executed
     * @return string
     */
    public static function lastQuery();

    /**
     * Returns the PDOStatement instance last used
     * @return string
     */
    public static function lastStatement();

    /**
     * Get an array containing all the queries 
     * run on a specified connection up to now.
     * @return array
     */
    public static function queryLog();

    /**
     * Execute
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    public static function execute($query, $parameters = []);

    /**
     * Query
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public static function query($query, $parameters = []);
}
