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

namespace Quantum\Mvc;

use Quantum\Libraries\Database\DbalInterface;
use Quantum\Exceptions\ModelException;

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
     * @var \Quantum\Libraries\Database\DbalInterface
     */
    private $orm;

    /**
     * Sets the ORM
     * @param \Quantum\Libraries\Database\DbalInterface $orm
     */
    public function setOrm(DbalInterface $orm)
    {
        $this->orm = $orm;
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
                throw ModelException::inappropriateProperty($key);
            }

            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Allows accessing to model property
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->orm->getOrmModel()->$property ?? null;
    }

    /**
     * Allows to set values to models properties
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $this->orm->getOrmModel()->$property = $value;
    }

    /**
     * Allows calling the model methods
     * @param string $method
     * @param mixed|null $args
     * @return $this|array|int|string
     * @throws \Quantum\Exceptions\ModelException
     */
    public function __call(string $method, $args = null)
    {
        if (method_exists($this->orm, $method)) {

            $result = $this->orm->{$method}(...$args);

            if (!is_object($result)) {
                return $result;
            }

            $this->orm->updateOrmModel($result);
            return $this;
        } else {
            throw ModelException::undefinedMethod($method);
        }
    }

}
