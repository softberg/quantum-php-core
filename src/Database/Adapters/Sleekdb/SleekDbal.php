<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Database\Adapters\Sleekdb;

use Quantum\Database\Adapters\Sleekdb\Statements\Criteria;
use Quantum\Database\Adapters\Sleekdb\Statements\Reducer;
use Quantum\Database\Adapters\Sleekdb\Statements\Result;
use Quantum\Database\Adapters\Sleekdb\Statements\Model;
use Quantum\Database\Adapters\Sleekdb\Statements\Join;
use SleekDB\Exceptions\InvalidConfigurationException;
use Quantum\Database\Exceptions\DatabaseException;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use SleekDB\Exceptions\IOException;
use SleekDB\QueryBuilder;
use SleekDB\Store;

/**
 * Class SleekDbal
 * @package Quantum\Database
 */
class SleekDbal implements DbalInterface
{
    use Model;
    use Result;
    use Criteria;
    use Reducer;
    use Join;

    protected bool $isNew = false;

    protected array $data = [];

    /**
     * @var array
     */
    protected $modifiedFields = [];

    protected array $criterias = [];

    protected array $havings = [];

    /**
     * @var array
     */
    protected $selected = [];

    /**
     * @var array
     */
    protected $grouped = [];

    /**
     * @var array
     */
    protected $ordered = [];

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * Associated model name
     */
    private ?string $modelName;

    /**
     * The database table associated with model
     */
    private string $table;

    /**
     * ID column of table
     */
    private string $idColumn;

    /**
     * Foreign keys
     */
    private array $foreignKeys;

    /**
     * Hidden fields
     * @var array
     */
    public $hidden = [];

    /**
     * ORM Model
     */
    private ?Store $ormModel = null;

    private ?QueryBuilder $queryBuilder = null;

    /**
     * Active connection
     */
    private static ?array $connection = null;

    /**
     * Operators map
     * @var string[]
     */
    private array $operators = [
        '=', '!=',
        '>', '>=',
        '<', '<=',
        'IN', 'NOT IN',
        'LIKE', 'NOT LIKE',
        'BETWEEN', 'NOT BETWEEN',
    ];

    public function __construct(
        string $table,
        ?string $modelName = null,
        string $idColumn = 'id',
        array  $foreignKeys = [],
        array  $hidden = []
    ) {
        $this->modelName = $modelName;
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->foreignKeys = $foreignKeys;
        $this->hidden = $hidden;
    }

    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public static function connect(array $config): void
    {
        self::$connection = $config;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param $modifiedFields
     */
    public function setModifiedFields($modifiedFields): void
    {
        $this->modifiedFields = $modifiedFields;
    }

    public function setIsNew(bool $isNew): void
    {
        $this->isNew = $isNew;
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
    public static function disconnect(): void
    {
        self::$connection = null;
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Gets the ORM model
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws BaseException
     */
    public function getOrmModel(): Store
    {
        if (!$this->ormModel) {
            if (!self::getConnection()) {
                throw DatabaseException::missingConfig('database');
            }

            $connection = self::getConnection();

            if (empty($connection['database_dir'])) {
                throw DatabaseException::incorrectConfig();
            }

            $connection['config']['primary_key'] = $this->idColumn;

            $this->ormModel = new Store($this->table, $connection['database_dir'], $connection['config']);
        }

        return $this->ormModel;
    }

    public function updateOrmModel(?array $modelData): void
    {
        $this->data = $modelData;
        $this->modifiedFields = $modelData;
        $this->isNew = false;
    }

    /**
     * @inheritdoc
     */
    public function truncate(): bool
    {
        try {
            $this->getOrmModel()->deleteStore();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the query builder object
     * @throws BaseException
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function getBuilder(): QueryBuilder
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = $this->getOrmModel()->createQueryBuilder();
        }

        if ($this->selected !== []) {
            $this->queryBuilder->select($this->selected);
        }

        if ($this->joins !== []) {
            $this->applyJoins();
        }

        if ($this->criterias !== []) {
            $this->queryBuilder->where($this->criterias);
        }

        if ($this->havings !== []) {
            $this->queryBuilder->having($this->havings);
        }

        if ($this->grouped !== []) {
            $this->queryBuilder->groupBy($this->grouped);
        }

        if ($this->ordered !== []) {
            $this->queryBuilder->orderBy($this->ordered);
        }

        if ($this->offset) {
            $this->queryBuilder->skip($this->offset);
        }

        if ($this->limit) {
            $this->queryBuilder->limit($this->limit);
        }

        return $this->queryBuilder;
    }

    /**
     * Gets foreign keys
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Gets the associated model name
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * Resets the builder state
     */
    protected function resetBuilderState(): void
    {
        $this->criterias = [];
        $this->havings = [];
        $this->selected = [];
        $this->grouped = [];
        $this->ordered = [];
        $this->offset = null;
        $this->limit = null;
        $this->joins = [];
        $this->queryBuilder = null;
    }
}
