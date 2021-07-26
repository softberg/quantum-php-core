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
 * @since 2.5.0
 */

namespace Quantum\Mvc;

use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\ModelException;
use Quantum\Loader\Loader;

/**
 * Class QtModel
 * @package Quantum\Mvc
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
     * ORM database abstract layer object
     * @var object
     */
    private $orm;

    /**
     * The model
     * @var string|null
     */
    private $model;

    /**
     * QtModel constructor.
     * @param \Quantum\Loader\Loader $loader
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\LoaderException
     * @throws \Quantum\Exceptions\ModelException
     */
    public final function __construct(Loader $loader)
    {
        $this->model = get_called_class();
        $this->orm = Database::getInstance($loader)->getORM($this->table, $this->model, $this->idColumn);
    }

    /**
     * Fills the object properties
     * @param array $arguments
     * @return \Quantum\Mvc\QtModel
     * @throws \Quantum\Exceptions\ModelException
     */
    public function fillObjectProps(array $arguments): QtModel
    {
        foreach ($arguments as $key => $value) {
            if (!in_array($key, $this->fillable)) {
                throw new ModelException(_message(ModelException::INAPPROPRIATE_PROPERTY, $key));
                throw ModelException::inappropriateProperty($key);
            }

            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Allows to access to models property
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->orm->ormObject->$property ?? null;
    }

    /**
     * Allows to set values to models properties
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $this->orm->ormObject->$property = $value;
    }

    /**
     * Allows to call models methods
     * @param string $method
     * @param mixed|null $args
     * @return $this|array|int|string
     * @throws \Quantum\Exceptions\ModelException
     */
    public function __call(string $method, $args = null)
    {
        if (method_exists($this->orm, $method)) {

            $result = $this->orm->{$method}(...$args);

            if (is_array($result) || is_int($result) || is_string($result)) {
                return $result;
            } else {
                if (is_object($result)) {
                    $this->orm->ormObject = $result;
                }

                return $this;
            }
        } else {
            throw ModelException::undefinedMethod($method);
        }
    }

}
