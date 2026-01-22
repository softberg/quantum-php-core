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
 * @since 3.0.0
 */

namespace Quantum\Model;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Paginator\Paginator;

/**
 * Class DbModel
 * @package Quantum\Model
 *
 * @method string getTable()
 * @method string getModelName()
 * @method static select(...$columns)
 * @method static criteria(string $column, string $operator, $value = null)
 * @method static criterias(...$criterias)
 * @method static having(string $column, string $operator, string $value = null)
 * @method static orderBy(string $column, string $direction)
 * @method static offset(int $offset)
 * @method static limit(int $limit)
 * @method int count()
 * @method bool deleteMany()
 * @method static joinTo(DbModel $model, bool $switch = true)
 * @method static isNull(string $column)
 * @method static isNotNull(string $column)
 * /
 */
abstract class DbModel extends Model
{
    /**
     * Database table
     * @var string
     */
    public string $table = '';

    /**
     * Primary key column
     * @var string
     */
    public string $idColumn = 'id';

    /**
     * ORM instance (Idiorm or SleekDB)
     * @var DbalInterface|null
     */
    protected ?DbalInterface $ormInstance = null;

    /**
     * Set ORM instance
     * @param DbalInterface $ormInstance
     * @return void
     */
    public function setOrmInstance(DbalInterface $ormInstance): void
    {
        $this->ormInstance = $ormInstance;
    }

    /**
     * Get ORM instance
     * @return DbalInterface
     * @throws ModelException
     */
    public function getOrmInstance(): DbalInterface
    {
        if (!isset($this->ormInstance)) {
            throw ModelException::ormIsNotSet();
        }

        return $this->ormInstance;
    }

    /**
     * @return array
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * Finds the record by primary key and returns a new model instance
     * @param int $id
     * @return DbModel|null
     * @throws BaseException
     */
    public function findOne(int $id): ?DbModel
    {
        $orm = $this->ormInstance->findOne($id);

        return wrapToModel($orm, static::class);
    }

    /**
     * Finds the record by given column and value and returns a new model instance
     * @param string $column
     * @param mixed $value
     * @return DbModel|null
     * @throws BaseException
     */
    public function findOneBy(string $column, $value): ?DbModel
    {
        $orm = $this->ormInstance->findOneBy($column, $value);

        return wrapToModel($orm, static::class);
    }

    /**
     * Gets the first record and returns a new model instance
     * @return DbModel|null
     * @throws BaseException
     */
    public function first(): ?DbModel
    {
        $orm = $this->ormInstance->first();

        return wrapToModel($orm, static::class);
    }

    /**
     * Fetch multiple results
     * @return ModelCollection
     * @throws BaseException
     */
    public function get(): ModelCollection
    {
        $models = array_map(
            fn ($item) => wrapToModel($item, static::class),
            $this->ormInstance->get()
        );

        return new ModelCollection($models);
    }

    /**
     * Paginates the result
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    public function paginate(int $perPage, int $currentPage = 1): Paginator
    {
        return PaginatorFactory::create(Paginator::MODEL, [
            'model' => $this,
            'perPage' => $perPage,
            'page' => $currentPage,
        ]);
    }

    /**
     * Creates a new record
     * @return $this
     */
    public function create(): self
    {
        $this->attributes = [];
        $this->ormInstance->create();
        return $this;
    }

    /**
     * Save model
     * @return bool
     */
    public function save(): bool
    {
        $this->syncAttributesToOrm();

        $result = $this->ormInstance->save();

        $this->syncPrimaryKeyFromOrm();

        return $result;
    }

    /**
     * Delete model
     * @return bool
     */
    public function delete(): bool
    {
        return $this->ormInstance->delete();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function hydrateFromOrm(array $data): DbModel
    {
        $this->attributes = $data;
        return $this;
    }

    /**
     * @param string $method
     * @param $args
     * @return $this|DbModel
     * @throws ModelException
     */
    public function __call(string $method, $args = null)
    {
        if (!method_exists($this->ormInstance, $method)) {
            throw ModelException::methodNotSupported(
                $method,
                get_class($this->ormInstance)
            );
        }

        $result = $this->ormInstance->{$method}(...$args);

        return $result instanceof DbalInterface ? $this : $result;
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return [
            'table',
            'idColumn',
            'hidden',
        ];
    }

    /**
     * Sync model attributes into ORM
     * @return void
     */
    protected function syncAttributesToOrm(): void
    {
        foreach ($this->attributes as $key => $value) {
            $this->ormInstance->prop($key, $value);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function shouldFill(string $key): bool
    {
        if ($key === $this->idColumn) {
            return false;
        }

        return parent::shouldFill($key);
    }

    /**
     * Syncs primary key from ORM to model attributes
     * @return void
     */
    private function syncPrimaryKeyFromOrm(): void
    {
        $id = $this->ormInstance->prop($this->idColumn);

        if ($id !== null) {
            $this->attributes[$this->idColumn] = $id;
        }
    }
}
