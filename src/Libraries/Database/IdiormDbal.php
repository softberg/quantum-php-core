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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Database;

use Quantum\Libraries\Database\Statements\Criteria;
use Quantum\Libraries\Database\Statements\Result;
use Quantum\Libraries\Database\Statements\Model;
use Quantum\Libraries\Database\Statements\Query;
use Quantum\Libraries\Database\Statements\Join;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use PDO;
use ORM;


/**
 * Class IdiormDbal
 * @package Quantum\Libraries\Database
 */
class IdiormDbal implements DbalInterface
{

    use Model;
    use Result;
    use Criteria;
    use Join;
    use Query;

    /**
     * Type array
     */
    const TYPE_ARRAY = 1;

    /**
     * Type object
     */
    const TYPE_OBJECT = 2;

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
     * Idiorm Patch object
     * @var \Quantum\Libraries\Database\IdiormPatch
     */
    private $ormPatch = null;

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
    public function __construct(string $table, string $idColumn = 'id')
    {
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->ormObject = $this->ormObject();
    }

    /**
     * Get table
     * @inheritDoc
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Connects to database
     * @inheritDoc
     */
    public static function dbConnect(array $connectionDetails): array
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
     * Selects the values from provided table columns
     * @inheritDoc
     */
    public function select(...$columns): object
    {
        array_walk($columns, function (&$column) {
            if (is_array($column)) {
                $column = array_flip($column);
            }
        });

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($columns));
        $columns = iterator_to_array($iterator, true);

        return $this->ormObject->select_many($columns);
    }

    /**
     * Orders the result by ascending or descending
     * @inheritDoc
     */
    public function orderBy(string $column, string $direction): object
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
     * Groups the result by given column
     * @inheritDoc
     */
    public function groupBy(string $column): object
    {
        return $this->ormObject->group_by($column);
    }

    /**
     * Returns the limited result set
     * @inheritDoc
     */
    public function limit(int $limit): object
    {
        return $this->ormObject->limit($limit);
    }

    /**
     * Returns the result by given offset (works when limit also applied)
     * @inheritDoc
     */
    public function offset(int $offset): object
    {
        return $this->ormObject->offset($offset);
    }

    /**
     * Deletes all records by previously applied criteria
     * @inheritDoc
     */
    public function deleteAll(): bool
    {
        return $this->ormObject->delete_many();
    }

    /**
     * Orm Object
     * @return mixed
     */
    protected function ormObject()
    {
        return (self::$ormClass)::for_table($this->table)->use_id_column($this->idColumn);
    }

    /**
     * Builds connection string
     * @param array $connectionDetails
     * @return string
     */
    private static function buildConnectionString(array $connectionDetails): string
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

}
