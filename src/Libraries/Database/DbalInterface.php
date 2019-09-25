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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Database;

/**
 * Database Abstract Layer interface
 *
 * The common interface for DBAL, which should implemented by all DBAL classes
 *
 * @package Quantum
 * @subpackage Libraries.Database
 * @category Libraries
 */
interface DbalInterface
{

    /**
     * DB Connect
     *
     * Should be implemented in classes for db connect
     *
     * @param array $connectionDetails
     * @return array
     */
    public static function dbConnect($connectionDetails);

    /**
     * Find One
     *
     * Should be implemented in classes to get record by primary key
     *
     * @param int $id
     * @return object
     */
    public function findOne($id);

    /**
     * FindOneBy
     *
     * Should be implemented in classes to get record by given column
     *
     * @param string $column
     * @param mixed $value
     * @return object
     */
    public function findOneBy($column, $value);

    /**
     * First
     *
     * Should be implemented in classes to get the first item
     *
     * @return object
     */
    public function first();

    /**
     * Criterias
     *
     * Should be implemented in classes to add where criterias
     *
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias);

    /**
     * Order By
     *
     * Should be implemented in classes to order the result
     *
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy($column, $direction);

    /**
     * Group By
     *
     * Should be implemented in classes to group the result
     *
     * @param string $column
     * @return object
     */
    public function groupBy($column);

    /**
     * Limit
     *
     * Should be implemented in classes to get result by given limit
     *
     * @param integer $params
     * @return object
     */
    public function limit($limit);

    /**
     * Offset
     *
     * Should be implemented in classes to get result by given offset
     *
     * @param integer $offset
     * @return object
     */
    public function offset($offset);

    /**
     * Get
     *
     * Should be implemented in classes to get result set
     *
     * @param null $returnType
     * @return mixed
     */
    public function get($returnType = null);

    /**
     * Count
     *
     * Should be implemented in classes to get count of result set
     *
     * @return integer
     */
    public function count();

    /**
     * asArray
     *
     * Should be implemented in classes to cast the orm object to array
     *
     * @return array
     */
    public function asArray();

    /**
     * Create
     *
     * Should be implemented in classes for creating new db record
     *
     * @return object
     */
    public function create();

    /**
     * Save
     *
     * Should be implemented in classes for saving the data into the database
     *
     * @return bool
     */
    public function save();

    /**
     * Delete
     *
     * Should be implemented in classes for deleting the data from the database
     *
     * @return bool
     */
    public function delete();

    /**
     * Join
     *
     * Should be implemented in classes for to make joining
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function join($table, $constraint, $tableAlias = null);

    /**
     * Inner Join
     *
     * Add an INNER JOIN souce to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function innerJoin($table, $constraint, $tableAlias = null);

    /** Left Join
     *
     * Add an LEFT JOIN souce to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function leftJoin($table, $constraint, $tableAlias = null);

    /**
     * Right Join
     *
     * Add an RIGHT JOIN souce to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function rightJoin($table, $constraint, $tableAlias = null);

    /**
     * Execute
     *
     * Should be implemented in classes for executing custom query
     *
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    public static function execute($query, $parameters = []);

    /**
     * Query
     *
     * Should be implemented in classes for retriving data by custom query
     *
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public static function query($query, $parameters = []);
}
