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

use PDO;
use ORM;
use Quantum\Mvc\QtModel;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Quantum\Helpers\Helper;

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
     * @var string
     */
    private $table;

    /**
     * Id column of table
     * @var string
     */
    private $idColumn;

    /**
     * Foreign keys
     * @var array 
     */
    private $foreignKeys = [];

    /**
     * Idiorm object
     * @var object
     */
    public $ormObject;

    /**
     * ORM Class
     * @var string
     */
    private static $ormClass = ORM::class;

    /**
     * Class constructor
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
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Connects to database
     * @param array $connectionDetails
     * @return array
     */
    public static function dbConnect($connectionDetails): array
    {
        $connectionString = self::buildConnectionString($connectionDetails);

        $attributes = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . ($connectionDetails['charset'] ?? 'utf8'),
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        (self::$ormClass)::configure([
            'connection_string' => $connectionString,
            'username' => $connectionDetails['username'] ?? null,
            'password' => $connectionDetails['password'] ?? null,
            'driver_options' => $attributes,
            'logging' => config()->get('debug', false)
        ]);

        return (self::$ormClass)::get_config();
    }

    /**
     * Gets the record by primary key
     * @param int $id
     * @return object
     */
    public function findOne($id)
    {
        $result = $this->ormObject->find_one($id);
        return $result ? $result : $this->ormObject();
    }

    /**
     * Gets record by given column
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
     * Gets the first item
     * @return object
     */
    public function first()
    {
        $result = $this->ormObject->find_one();
        return $result ? $result : $this->ormObject();
    }

    /**
     * Casts the ormObject object to array
     * @return array
     */
    public function asArray()
    {
        return $this->ormObject->as_array();
    }

    /**
     * Counts the result set
     * @return int
     */
    public function count()
    {
        return $this->ormObject->count();
    }

    /**
     * Gets the result set
     * @param null|string $returnType
     * @return mixed
     */
    public function get($returnType = null)
    {
        return ($returnType == 'object') ? $this->ormObject->find_many() : $this->ormObject->find_array();
    }

    /**
     * Selects the given table columns 
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

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($columns));
        $columns = iterator_to_array($iterator, true);

        return $this->ormObject->select_many($columns);
    }

    /**
     * Criteria
     * 
     * @param string $column
     * @param string $operator
     * @param mixed|null $value
     * @return object
     */
    public function criteria($column, $operator, $value = null)
    {
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
            case 'NULL':
                $this->addCriteria($column, $operator, $value, 'where_null');
                break;
            case 'NOT NULL':
                $this->addCriteria($column, $operator, $value, 'where_not_null');
                break;
            case '#=#':
                $this->whereColumnsEqual($column, $value);
                break;
        }

        return $this->ormObject;
    }

    /**
     * Adds where criterias
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias)
    {
        foreach ($criterias as $criteria) {

            if (is_array($criteria[0])) {
                $this->scoppedORCriteria($criteria);
                continue;
            }

            $value = $criteria[2] ?? null;

            $this->criteria($criteria[0], $criteria[1], $value);
        }

        return $this->ormObject;
    }

    /**
     * Orders the result by ascending or descending
     * @param string $column
     * @param string $direction
     * @return object
     */
    public function orderBy($column, $direction)
    {
        switch (strtolower($direction)) {
            case 'asc':
                $this->ormObject->order_by_asc($column);
                break;
            case 'desc':
                $this->ormObject->order_by_desc($column);
                break;
        }

        return $this->ormObject;
    }

    /**
     * Groups the result by column
     * @param string $column
     * @return object
     */
    public function groupBy($column)
    {
        return $this->ormObject->group_by($column);
    }

    /**
     * Returns the result by given limit
     * @param int $limit
     * @return object
     */
    public function limit($limit)
    {
        return $this->ormObject->limit($limit);
    }

    /**
     * Returns the result by given offset (works when limit also applied)
     * @param int $params
     * @return object
     */
    public function offset($offset)
    {
        return $this->ormObject->offset($offset);
    }

    /**
     * Creates new db record
     * @return object
     */
    public function create()
    {
        return $this->ormObject->create();
    }

    /**
     * Saves the data into the database
     * @return bool
     */
    public function save()
    {
        return $this->ormObject->save();
    }

    /**
     * Deletes the data from the database
     * @return bool
     */
    public function delete()
    {
        return $this->ormObject->delete();
    }

    /**
     * Deletes all records by previously applied criteria
     * @return bool
     */
    public function deleteAll()
    {
        return $this->ormObject->delete_many();
    }

    /**
     * Add a simple JOIN source to the query
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
     * Add an INNER JOIN source to the query
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
     * Add an LEFT JOIN source to the query
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
     * Add an RIGHT JOIN source to the query
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
     * Joins two models
     * @param QtModel $model
     * @return object
     */
    public function joinTo(QtModel $model, $switch = true)
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
     * Joins through connector model
     * @param QtModel $model
     * @return object
     */
    public function joinThrough(QtModel $model, $switch = true)
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
     * Raw execute
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    public static function execute($query, $parameters = [])
    {
        return (self::$ormClass)::raw_execute($query, $parameters);
    }

    /**
     * Raw query
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
    public static function lastQuery()
    {
        return (self::$ormClass)::get_last_query();
    }

    /**
     * Returns the PDOStatement instance last used
     *
     * @return string
     */
    public static function lastStatement()
    {
        return (self::$ormClass)::get_last_statement();
    }

    /**
     * Get an array containing all the queries 
     * run on a specified connection up to now.
     *
     * @return array
     */
    public static function queryLog()
    {

        return (self::$ormClass)::get_query_log();
    }

    /**
     * Orm Object
     * @return mixed
     */
    private function ormObject()
    {
        return (self::$ormClass)::for_table($this->table)->use_id_column($this->idColumn);
    }

    /**
     * Builds connection string
     * @param array $connectionDetails
     * @return string
     */
    private static function buildConnectionString($connectionDetails)
    {
        $connectionString = $connectionDetails['driver'] . ':';

        if ($connectionDetails['driver'] == 'sqlite') {
            $connectionString .= $connectionDetails['database'];
        } else {
            $connectionString .= 'host=' . $connectionDetails['host'] . ';';

            if (isset($connectionDetails['port'])) {
                $connectionString .= 'post=' . $connectionDetails['port'] . ';';
            }

            $connectionString .= 'dbname=' . $connectionDetails['dbname'] . ';';

            if (isset($connectionDetails['charset'])) {
                $connectionString .= 'charset=' . $connectionDetails['charset'] . ';';
            }
        }

        return $connectionString;
    }

    /**
     * Compares column from one table to column to other table
     * @param string $columnOne
     * @param string $columnTwo
     * @return object
     */
    private function whereColumnsEqual($columnOne, $columnTwo)
    {
        return $this->ormObject->where_raw($columnOne . ' = ' . $columnTwo);
    }

    /**
     * Adds one or more OR criteria in brackets 
     * @param array $criteria
     */
    private function scoppedORCriteria($criteria)
    {
        $clause = '';
        $params = [];

        foreach ($criteria as $index => $orCriteria) {
            if ($index == 0) {
                $clause .= '(';
            }

            $clause .= '`' . $orCriteria[0] . '` ' . $orCriteria[1] . ' ?';

            if ($index == count($criteria) - 1) {
                $clause .= ')';
            } else {
                $clause .= ' OR ';
            }

            array_push($params, $orCriteria[2]);
        }

        $this->ormObject->where_raw($clause, $params);
    }

    /**
     * Adds Criteria 
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $func
     */
    private function addCriteria($column, $operator, $value, $func)
    {
        if (is_array($value) && count($value) == 1 && key($value) == 'fn') {
            $this->ormObject->where_raw($column . ' ' . $operator . ' ' . $value['fn']);
        } else {
            $this->ormObject->$func($column, $value);
        }
    }

}
