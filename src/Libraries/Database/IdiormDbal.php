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

use Quantum\Mvc\Qt_Model;
use PDO;
use ORM;

/**
 * IdiORM DBAL
 * 
 * Database Abstract Layer class for IdiOrm
 * Default DBAL for framework
 * 
 * @package Quantum
 * @subpackage Libraries.Database
 * @category Libraries
 */
class IdiormDbal implements DbalInterface {

    /**
     * DB Connect
     * 
     * Connects to database
     * 
     * @param array $connectionString
     * @uses ORM::configure Idiorm
     * @return void
     */
    public static function dbConnect($connectionString) {
        ORM::configure(array(
            'connection_string' => $connectionString['driver'] . ':host=' . $connectionString['host'] . ';dbname=' . $connectionString['dbname'],
            'username' => $connectionString['username'],
            'password' => $connectionString['password'],
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $connectionString['charset']),
            'logging' => get_config('debug', false)
        ));
    }
    
    /**
     * Find one
     * 
     * Gets record by primary key
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function findOne($params) {
        return ORM::for_table($params['table'])->use_id_column($params['idColumn'])->find_one($params['args'][0]);
    }

    /**
     * FindOneBy
     * 
     * Gets record by given column
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function findOneBy($params) {
        return ORM::for_table($params['table'])->where($params['args'][0], $params['args'][1])->use_id_column($params['idColumn'])->find_one();
    }

    /**
     * Criterias
     * 
     * Adds where criterias
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function criterias($params) {
        $orm = ORM::for_table($params['table']);

        foreach ($params['args'] as $arg) {
            $column = $arg[0];
            $operation = $arg[1];
            $value = $arg[2];
            
            switch($operation) {
                case '=':
                    $orm->where_equal($column, $value);
                    break;
                case '!=':
                    $orm->where_not_equal($column, $value);
                    break;
                case '>':
                    $orm->where_gt($column, $value);
                    break;
                case '>=':
                    $orm->where_gte($column, $value);
                    break;
                case '<':
                    $orm->where_lt($column, $value);
                    break;
                case '<=':
                    $orm->where_lte($column, $value);
                    break;
                case 'LIKE':
                    $orm->where_like($column, $value);
                    break;
                case 'NOT LIKE':
                    $orm->where_not_like($column, $value);
                    break;
            }
        }

        return $orm;
    }
    
    /**
     * Order By
     * 
     * Orders the result by ascending or descending 
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function orderBy($params) {
        $orderCriterias = array_flip($params['args'][0]);
        $ormObject = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);
        
        foreach ($orderCriterias as $direction => $column) {
            if(strtolower($direction) == 'asc') {
                $ormObject->order_by_asc($column);
            } elseif(strtolower($direction) == 'desc') {
                $ormObject->order_by_desc($column);
            }
        }
        return $ormObject;
    }    
    
    /**
     * Group By
     * 
     * Groups the result by column
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function groupBy($params) {
        $column = $params['args'][0];
        $ormObject = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);
        
        return $ormObject->group_by($column);
    }
    
    /**
     * Limit
     * 
     * Returns the result by given limit
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function limit($params) {
        $limit = $params['args'][0];
        $ormObject = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);
        
        return $ormObject->limit($limit);
    }
    
    /**
     * Offset
     * 
     * Returns the result by given offset
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function offset($params) {
        $offset = $params['args'][0];
        $ormObject = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);
        
        return $ormObject->offset($offset);
    }

    /**
     * First
     * 
     * Gets the first item
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public function first($params) {
        return $params['ormObject']->use_id_column($params['idColumn'])->find_one();
    }
    
    /**
     * Get
     * 
     * Gets the result set
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return mixed
     */
    public function get($params) {
        $orm = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);

        return ($params['args'] && $params['args'][0] == 'object') ? $orm->find_many() : $orm->find_array();
    }
    
    /**
     * Count
     * 
     * Counts the result set
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return int
     */
    public function count($params) {
        $orm = !is_null($params['ormObject']) ? $params['ormObject'] : ORM::for_table($params['table']);

        return $orm->count();
    }

    /**
     * asArray
     * 
     * Casts the orm object to array
     * 
     * @param ORM $ormObject
     * @uses ORM Idiorm
     * @return array
     */
    public static function asArray($ormObject) {
        return $ormObject ? $ormObject->as_array() : array();
    }

    /**
     * Create
     * 
     * Creates new db record
     * 
     * @param array $params
     * @uses ORM Idiorm
     * @return object
     */
    public static function create($params) {
        return ORM::for_table($params['table'])->create();
    }
    
    /**
     * Save
     * 
     * Saves the data into the database
     * 
     * @param ORM $ormObject
     * @uses ORM Idiorm
     */
    public static function save($ormObject) {
        $ormObject->save();
    }
    
    /**
     * Delete
     * 
     * Deletes the data from the database
     * 
     * @param ORM $ormObject
     * @uses ORM Idiorm
     */
    public static function delete($ormObject) {
        $ormObject->delete();
    }

}
