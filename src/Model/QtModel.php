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
 * @since 2.9.9
 */

namespace Quantum\Model;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Paginator\Paginator;

/**
 * Class QtModel
 * @package Quantum\Model
 * @method string getTable()
 * @method string getModelName()
 * @method static select(...$columns)
 * @method static findOne(int $id)
 * @method static findOneBy(string $column, $value)
 * @method static first()
 * @method static criteria(string $column, string $operator, $value = null)
 * @method static criterias(...$criterias)
 * @method static having(string $column, string $operator, string $value = null)
 * @method static orderBy(string $column, string $direction)
 * @method static offset(int $offset)
 * @method static limit(int $limit)
 * @method int count()
 * @method array asArray()
 * @method static create()
 * @method bool save()
 * @method bool delete()
 * @method bool deleteMany()
 * @method static joinTo(QtModel $model, bool $switch = true)
 * @method static isNull(string $column)
 * @method static isNotNull(string $column)
 */
abstract class QtModel
{

    /**
     * The database table associated with model
     * @var string
     */
    public $table;

    /**
     * Id column of table
     * @var string
     */
    public $idColumn = 'id';

    /**
     * Foreign keys
     * @var array
     */
    public $foreignKeys = [];

    /**
     * Models fillable properties
     * @var array
     */
    protected $fillable = [];

    /**
     * Models hidden properties
     * @var array
     */
    public $hidden = [];

    /**
     * ORM database abstract layer object
     * @var DbalInterface
     */
    protected $ormInstance;

    /**
     * Sets the ORM instance
     * @param DbalInterface $ormInstance
     */
    public function setOrmInstance(DbalInterface $ormInstance)
    {
        $this->ormInstance = $ormInstance;
    }

    /**
     * Gets ORM instance of current model
     * @return DbalInterface
     */
    public function getOrmInstance(): DbalInterface
    {
        return $this->ormInstance;
    }

    /**
     * Returns the model's foreign key relations.
     * @return array
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * @return ModelCollection
     * @throws BaseException
     */
    public function get(): ModelCollection
    {
        $models = array_map(fn($item) => wrapToModel($item, static::class), $this->ormInstance->get());

        return new ModelCollection($models);
    }

    /**
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
            'page' => $currentPage
        ]);
    }

    /**
     * Fills the object properties
     * @param array $props
     * @return QtModel
     * @throws ModelException
     */
    public function fillObjectProps(array $props): QtModel
    {
        foreach ($props as $key => $value) {
            if ($key == $this->idColumn) {
                continue;
            }

            if (!in_array($key, $this->fillable)) {
                throw ModelException::inappropriateProperty($key);
            }

            $this->prop($key, $value);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->asArray());
    }


    /**
     * Sets or gets the model property
     * @param string $property
     * @param mixed|null $value
     * @return mixed
     */
    public function prop(string $property, $value = null)
    {
        return $this->ormInstance->prop(...func_get_args());
    }

    /**
     * Gets the model property with magic
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->prop($property);
    }

    /**
     * Sets a value to the model property with magic
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $this->prop($property, $value);
    }

    /**
     * @param string $method
     * @param $args
     * @return $this
     * @throws BaseException
     */
    public function __call(string $method, $args = null)
    {
        if (!method_exists($this->ormInstance, $method)) {
            throw ModelException::methodNotSupported($method, get_class($this->ormInstance));
        }

        $result = $this->ormInstance->{$method}(...$args);

        if (!$result instanceof DbalInterface) {
            return $result;
        }

        return $this;
    }

    /**
     * Keeps only relevant props at serialization
     * @return string[]
     */
    public function __sleep()
    {
        return [
            'table',
            'idColumn',
            'hidden'
        ];
    }
}