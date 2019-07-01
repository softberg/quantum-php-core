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
interface DbalInterface {

    /**
     * DB Connect
     * 
     * Should be implemented in classes for db connect
     * 
     * @param mixed $connectionString
     */
    public static function dbConnect($connectionString);
    
    /**
     * Find One
     * 
     * Should be implemented in classes to get record by primary key
     * 
     * @param mixed $params
     */
    public function findOne($params);

    /**
     * FindOneBy
     * 
     * Should be implemented in classes to get record by given column
     * 
     * @param mixed $params
     */
    public function findOneBy($params);

    /**
     * Criterias
     * 
     * Should be implemented in classes to add where criterias 
     * 
     * @param mixed $params
     */
    public function criterias($params);
    
    /**
     * Order By
     * 
     * Should be implemented in classes to order the result
     * 
     * @param array $params
     */
    public function orderBy($params);
    
    /**
     * Group By
     * 
     * Should be implemented in classes to group the result
     * 
     * @param array $params
     */
    public function groupBy($params);
    
    /**
     * Limit
     * 
     * Should be implemented in classes to get result by given limit
     * 
     * @param array $params
     */
    public function limit($params);
    
    /**
     * Offset
     * 
     * Should be implemented in classes to get result by given offset
     * 
     * @param array $params
     */
    public function offset($params);
    
    /**
     * Get
     * 
     * Should be implemented in classes to get result set
     * 
     * @param array $params
     */
    public function get($params);
    
    /**
     * Count
     * 
     * Should be implemented in classes to get count of result set
     */
    public function count();

    /**
     * First
     * 
     * Should be implemented in classes to get the first item
     */    
    public function first();
    
    /**
     * asArray
     * 
     * Should be implemented in classes to cast the orm object to array
     */
    public function asArray();

    /**
     * Create
     * 
     * Should be implemented in classes for creating new db record
     */
    public function create();
    
    /**
     * Save
     * 
     * Should be implemented in classes for saving the data into the database
     */
    public function save();
    
    /**
     * Delete
     * 
     * Should be implemented in classes for deleting the data from the database
     */
    public function delete();

    /**
     * Execute
     *
     * Should be implemented in classes for executing custom query
     */
    public function execute($query, $parameters = []);

    /**
     * Query
     *
     * Should be implemented in classes for retriving data by custom query
     */
    public function query($query, $parameters = [], $many = true);
}
