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

namespace Quantum\Libraries\Database\Idiorm;

use Quantum\Libraries\Database\Idiorm\Statements\Criteria;
use Quantum\Libraries\Database\Idiorm\Statements\Result;
use Quantum\Libraries\Database\Idiorm\Statements\Model;
use Quantum\Libraries\Database\Idiorm\Statements\Query;
use Quantum\Libraries\Database\Idiorm\Statements\Join;
use Quantum\Libraries\Database\DbalInterface;
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
     * ORM Model
     * @var object
     */
    private $ormModel;

    /**
     * Active connection
     * @var array|null
     */
    private static $connection = null;

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
    }

    /**
     * @inheritDoc
     */
    public static function connect(array $config)
    {
        $configuration = [
            'connection_string' => self::buildConnectionString($config),
            'logging' => config()->get('debug', false),
            'error_mode' => PDO::ERRMODE_EXCEPTION,
        ];

        if ($config['driver'] == 'mysql' || $config['driver'] == 'pgsql') {
            $configuration = array_merge($configuration, [
                'username' => $config['username'] ?? null,
                'password' => $config['password'] ?? null,
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . ($config['charset'] ?? 'utf8')
                ]
            ]);

        }

        (self::$ormClass)::configure($configuration);

        self::$connection = (self::$ormClass)::get_config();
    }

    /**
     * @inheritDoc
     */
    public static function getConnection(): ?array
    {
        return self::$connection;
    }

    /**
     * @inheritDoc
     */
    public static function disconnect()
    {
        self::$connection = null;
        (self::$ormClass)::reset_db();
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
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

        return $this->getOrmModel()->select_many($columns);
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $column, string $direction): object
    {
        switch (strtolower($direction)) {
            case 'asc':
                $this->getOrmModel()->order_by_asc($column);
                break;
            case 'desc':
                $this->getOrmModel()->order_by_desc($column);
                break;
        }

        return $this->getOrmModel();
    }

    /**
     * @inheritDoc
     */
    public function groupBy(string $column): object
    {
        return $this->getOrmModel()->group_by($column);
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): object
    {
        return $this->getOrmModel()->limit($limit);
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): object
    {
        return $this->getOrmModel()->offset($offset);
    }

    /**
     * @inheritDoc
     */
    public function deleteAll(): bool
    {
        return $this->getOrmModel()->delete_many();
    }

    /**
     * @inheritDoc
     */
    public function getOrmModel(): object
    {
        if (!$this->ormModel) {
            $this->ormModel = (self::$ormClass)::for_table($this->table)->use_id_column($this->idColumn);
        }

        return $this->ormModel;
    }

    /**
     * @inheritDoc
     */
    public function updateOrmModel(object $ormModel)
    {
        $this->ormModel = $ormModel;
    }

    /**
     * Builds connection string
     * @param array $connectionDetails
     * @return string
     */
    private static function buildConnectionString(array $connectionDetails): string
    {
        $connectionString = $connectionDetails['driver'] . ':';

        switch ($connectionDetails['driver']) {
            case 'sqlite':
                $connectionString .= $connectionDetails['database'];
                break;
            case 'mysql':
            case 'pgsql':
                $connectionString .= 'host=' . $connectionDetails['host'] . ';';

                if (isset($connectionDetails['port'])) {
                    $connectionString .= 'post=' . $connectionDetails['port'] . ';';
                }

                $connectionString .= 'dbname=' . $connectionDetails['dbname'] . ';';

                if (isset($connectionDetails['charset'])) {
                    $connectionString .= 'charset=' . $connectionDetails['charset'] . ';';
                }
                break;
        }

        return $connectionString;
    }

}
