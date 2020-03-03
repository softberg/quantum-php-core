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

use PDO;
use ORM;
use Quantum\Mvc\Qt_Model;

/**
 * Class IdiormDbal
 *
 * Database Abstract Layer class for IdiOrm
 * Default DBAL for framework
 *
 * @package Quantum\Libraries\Database
 */
class IdiormDbal implements DbalInterface
{

    /**
     * The database table associated with model
     *
     * @var string
     */
    private $table;

    /**
     * Id column of table
     *
     * @var string
     */
    private $idColumn;

    /**
     * Foreign keys
     * 
     * @var array 
     */
    private $foreignKeys = [];

    /**
     * Idiorm object
     *
     * @var object
     */
    public $ormObject;

    /**
     * ORM Class
     *
     * @var string
     */
    private static $ormClass = ORM::class;

    /**
     * Orm Object
     *
     * @return mixed
     */
    private function ormObject()
    {
        return (self::$ormClass)::for_table($this->table)->use_id_column($this->idColumn);
    }

    /**
     * Class constructor
     *
     * @param string $table
     * @param string $idColumn
     */
    public function __construct($table, $idColumn = 'id')
    {
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->ormObject = $this->ormObject();
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * DB Connect
     *
     * Connects to database
     *
     * @param array $connectionDetails
     * @return array
     */
    public static function dbConnect($connectionDetails)
    {
        $attributes = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $connectionDetails['charset'] ?? 'utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        (self::$ormClass)::configure([
            'connection_string' => $connectionDetails['driver'] . ':host=' . $connectionDetails['host'] . ';dbname=' . $connectionDetails['dbname'],
            'username' => $connectionDetails['username'],
            'password' => $connectionDetails['password'],
            'driver_options' => $attributes,
            'logging' => get_config('debug', false)
        ]);

        return (self::$ormClass)::get_config();
    }

    /**
     * Select 
     * 
     * Selects the given table columns 
     * 
     * @param mixed $columns
     * @return array
     */
    public function select(...$columns)
    {
        array_walk($columns, function(&$column) {
            if (is_array($column)) {
                $column = array_flip($column);
            }
        });

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns));
        $columns = iterator_to_array($iterator, true);

        return $this->ormObject->select_many($columns);
    }

    /**
     * Find one
     *
     * Gets record by primary key
     *
     * @param int $id
     * @return object
     */
    public function findOne($id)
    {
        $result = $this->ormObject->find_one($id);
        return $result ? $result : $this->ormObject();
    }

    /**
     * FindOneBy
     *
     * Gets record by given column
     *
     * @param string $column
     * @param mixed $value
     * @return object
     */
    public function findOneBy($column, $value)
    {
        $result = $this->ormObject->where($column, $value)->find_one();
        return $result ? $result : $this->ormObject();
    }

    /**
     * First
     *
     * Gets the first item
     *
     * @return object
     */
    public function first()
    {
        $result = $this->ormObject->find_one();
        return $result ? $result : $this->ormObject();
    }

    /**
     * Criterias
     *
     * Adds where criterias
     *
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias)
    {
        foreach ($criterias as $criteria) {
            $column = $criteria[0];
            $operator = $criteria[1];
            $value = $criteria[2] ?? null;

            switch ($operator) {
                case '=':
                    $this->addCriteria($column, $operator, $value, 'where_equal');
                    break;
                case '!=':
                    $this->addCriteria($column, $operator, $value, 'where_not_equal');
                    break;
                case '>':
                    $this->addCriteria($column, $operator, $value, 'where_gt');
                    break;
                case '>=':
                    $this->addCriteria($column, $operator, $value, 'where_gte');
                    break;
                case '<':
                    $this->addCriteria($column, $operator, $value, 'where_lt');
                    break;
                case '<=':
                    $this->addCriteria($column, $operator, $value, 'where_lte');
                    break;
                case 'IN':
                    $this->addCriteria($column, $operator, $value, 'where_in');
                    break;
                case 'NOT IN':
                    $this->addCriteria($column, $operator, $value, 'where_not_in');
                    break;
                case 'LIKE':
                    $this->addCriteria($column, $operator, $value, 'where_like');
                    break;
                case 'NOT LIKE':
                    $this->addCriteria($column, $operator, $value, 'where_not_like');
                    break;
                case 'IS NULL':
                    $this->addCriteria($column, $operator, $value, 'where_null');
                    break;
                case 'IS NOT NULL':
                    $this->addCriteria($column, $operator, $value, 'where_not_null');
                    break;
                case '#=#':
                    $this->whereColumnsEqual($column, $value);
                    break;
            }
        }

        return $this->ormObject;
    }

    /**
     * Order By
     *
     * Orders the result by ascending or descending
     *
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy($column, $direction)
    {
        if (strtolower($direction) == 'asc') {
            $this->ormObject->order_by_asc($column);
        } elseif (strtolower($direction) == 'desc') {
            $this->ormObject->order_by_desc($column);
        }

        return $this->ormObject;
    }

    /**
     * Group By
     *
     * Groups the result by column
     *
     * @param string $column
     * @return object
     */
    public function groupBy($column)
    {
        return $this->ormObject->group_by($column);
    }

    /**
     * Limit
     *
     * Returns the result by given limit
     *
     * @param $limit
     * @return object
     */
    public function limit($limit)
    {
        return $this->ormObject->limit($limit);
    }

    /**
     * Offset
     *
     * Returns the result by given offset
     *
     * @param array $params
     * @return object
     */
    public function offset($offset)
    {
        return $this->ormObject->offset($offset);
    }

    /**
     * Get
     *
     * Gets the result set
     *
     * @param null $returnType
     * @return mixed
     */
    public function get($returnType = null)
    {
        return ($returnType == 'object') ? $this->ormObject->find_many() : $this->ormObject->find_array();
    }

    /**
     * Count
     *
     * Counts the result set
     *
     * @return int
     */
    public function count()
    {
        return $this->ormObject->count();
    }

    /**
     * asArray
     *
     * Casts the ormObject object to array
     *
     * @return array
     */
    public function asArray()
    {
        return $this->ormObject->as_array();
    }

    /**
     * Create
     *
     * Creates new db record
     *
     * @return object
     */
    public function create()
    {
        return $this->ormObject->create();
    }

    /**
     * Save
     *
     * Saves the data into the database
     *
     * @return bool
     */
    public function save()
    {
        return $this->ormObject->save();
    }

    /**
     * Delete
     *
     * Deletes the data from the database
     *
     * @return bool
     */
    public function delete()
    {
        return $this->ormObject->delete();
    }

    /**
     * 
     * Delete All
     * 
     * Deletes all records by previously applied criteria
     * 
     * @return bool
     */
    public function deleteAll()
    {
        return $this->ormObject->delete_many();
    }

    /**
     * Join
     *
     * Add a simple JOIN source to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function join($table, $constraint, $tableAlias = null)
    {
        return $this->ormObject->join($table, $constraint, $tableAlias);
    }

    /**
     * Join
     *
     * Add an INNER JOIN souce to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function innerJoin($table, $constraint, $tableAlias = null)
    {
        return $this->ormObject->inner_join($table, $constraint, $tableAlias);
    }

    /**
     * Left Join
     *
     * Add an LEFT JOIN souce to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $tableAlias
     * @return object
     */
    public function leftJoin($table, $constraint, $tableAlias = null)
    {
        $this->ormPatch = IdiormPatch::getInstance()->setOrmObject($this->ormObject);
        return $this->ormPatch->left_join($table, $constraint, $tableAlias);
    }

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
    public function rightJoin($table, $constraint, $tableAlias = null)
    {
        $this->ormPatch = IdiormPatch::getInstance()->setOrmObject($this->ormObject);
        return $this->ormPatch->right_join($table, $constraint, $tableAlias);
    }

    /**
     * Join To
     * 
     * Joins two models
     * 
     * @param Qt_Model $model
     * @return object
     */
    public function joinTo(Qt_Model $model, $switch = true)
    {
        $resultObject = $this->ormObject->join($model->table,
                [
                    $model->table . '.' . $model->foreignKeys[$this->table],
                    '=',
                    $this->table . '.' . $this->idColumn
                ]
        );

        if ($switch) {
            $this->table = $model->table;
            $this->idColumn = $model->idColumn;
            $this->foreignKeys = $model->foreignKeys;
        }

        return $resultObject;
    }

    /**
     * Join Through 
     * 
     * @param Qt_Model $model
     * @return object
     */
    public function joinThrough(Qt_Model $model, $switch = true)
    {
        $resultObject = $this->ormObject->join($model->table,
                [
                    $model->table . '.' . $model->idColumn,
                    '=',
                    $this->table . '.' . $this->foreignKeys[$model->table]
                ]
        );

        if ($switch) {
            $this->table = $model->table;
            $this->idColumn = $model->idColumn;
            $this->foreignKeys = $model->foreignKeys;
        }

        return $resultObject;
    }

    /**
     * Execute
     *
     * Raw execute
     *
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    public static function execute($query, $parameters = [])
    {
        return (self::$ormClass)::raw_execute($query, $parameters);
    }

    /**
     * Query
     *
     * Raw query
     *
     * @param $query
     * @param array $parameters
     * @return array
     */
    public static function query($query, $parameters = [])
    {
        return (self::$ormClass)::for_table('dummy')->raw_query($query, $parameters)->find_array();
    }

    /**
     * Gets the last query executed
     *
     * @return string
     */
    public static function getLastQuery()
    {
        return (self::$ormClass)::get_last_query();
    }

    /**
     * Returns the PDOStatement instance last used
     *
     * @return string
     */
    public static function getLastStatement()
    {
        return (self::$ormClass)::get_last_statement();
    }

    /**
     * Get an array containing all the queries 
     * run on a specified connection up to now.
     *
     * @return array
     */
    public static function getQueryLog()
    {
        return (self::$ormClass)::get_query_log();
    }

    /**
     * Compares column from one table to column to other table
     * 
     * @param string $columnOne
     * @param string $columnTwo
     * @return object
     */
    private function whereColumnsEqual($columnOne, $columnTwo)
    {
        return $this->ormObject->where_raw($columnOne . ' = ' . $columnTwo);
    }

    /**
     * Add Criteria 
     * 
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $func
     */
    private function addCriteria($column, $operator, $value, $func)
    {
        if (is_array($value) && key($value) == 'fn') {
            $this->ormObject->where_raw($column . ' ' . $operator . ' ' . $value['fn']);
        } else {
            $this->ormObject->$func($column, $value);
        }
    }

}
