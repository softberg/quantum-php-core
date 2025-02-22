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
 * @since 2.9.5
 */

namespace Quantum\Mvc;

use Quantum\Libraries\Database\Contracts\PaginatorInterface;
use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Contracts\DbalInterface;

/**
 * Class QtModel
 * @package Quantum\Mvc
 * @method string getTable()
 * @method DbalInterface select(...$columns)
 * @method DbalInterface findOne(int $id)
 * @method DbalInterface findOneBy(string $column, $value)
 * @method DbalInterface first()
 * @method DbalInterface criteria(string $column, string $operator, $value = null)
 * @method DbalInterface criterias(...$criterias)
 * @method DbalInterface having(string $column, string $operator, string $value = null)
 * @method DbalInterface orderBy(string $column, string $direction)
 * @method DbalInterface limit(int $limit)
 * @method mixed get()
 * @method array asArray()
 * @method DbalInterface create()
 * @method bool save()
 * @method bool delete()
 * @method bool deleteMany()
 * @method DbalInterface joinTo(QtModel $model, bool $switch = true)
 * @method DbalInterface joinThrough(QtModel $model, bool $switch = true)
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
    private $orm;

    /**
     * Sets the ORM
     * @param DbalInterface $orm
     */
    public function setOrm(DbalInterface $orm)
    {
        $this->orm = $orm;
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
     * Sets or gets the model property
     * @param string $property
     * @param mixed|null $value
     * @return mixed
     */
    public function prop(string $property, $value = null)
    {
        return $this->orm->prop(...func_get_args());
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
     * Allows calling the model methods
     * @param string $method
     * @param mixed|null $args
     * @return $this|array|int|string
     * @throws ModelException
     */
    public function __call(string $method, $args = null)
    {
        if (!method_exists($this->orm, $method)) {
            throw ModelException::undefinedMethod($method);
        }

        $result = $this->orm->{$method}(...$args);

        if (!is_object($result) || $result instanceof PaginatorInterface) {
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
            'foreignKeys',
            'hidden'
        ];
    }
}
