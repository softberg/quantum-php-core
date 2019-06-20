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

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Database\Database;
use Quantum\Hooks\HookManager;
use ORM;

/**
 * Base Model Class
 * 
 * Qt_Model class is a base abstract class that every model should extend,
 * This class also connects to database and prepares object relational mapping
 * 
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
abstract class Qt_Model {

    /**
     * ORM database abstract layer object
     * 
     * @var object 
     */
    private $orm;

    /**
     * Id column of table
     * 
     * @var string 
     */
    protected $idColumn = 'id';

    /**
     * The database table associated with model
     * 
     * @var string 
     */
    protected $table;

    /**
     * Models fillable properties 
     * @var array 
     */
    protected $fillable = array();

    /**
     * Model factory function
     * 
     * @var string
     */
    private $callerFunction = 'modelFactory';

    /**
     * Class constructor
     *
     * @return void
     * @throws \Exception When called directly
     */
    public final function __construct() {
        if (get_caller_function() != $this->callerFunction) {
            throw new \Exception(ExceptionMessages::DIRECT_MODEL_CALL);
        }

        Database::connect();
        $ormClass = Database::getORM();
        $this->orm = new $ormClass($this->table, $this->idColumn);
    }

    /**
     * Fill Object Properties
     * 
     * Fills the properties with values
     * 
     * @param array $arguments
     * @return void
     * @throws \Exception When the property is not appropriate
     */
    public function fillObjectProps($arguments) {
        foreach ($arguments as $key => $value) {
            if (!in_array($key, $this->fillable)) {
                throw new \Exception(_message(ExceptionMessages::INAPPROPRIATE_PROPERTY, $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * Update rules
     * 
     * Updates the validation rules of model
     * 
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function updateRules($key, $value) {
        $this->rules[$key] = $value;
    }

    /**
     * __get magic
     * 
     * Allows to access to models property
     * 
     * @param string $property
     * @return mixed
     */
    public function __get($property) {
        return isset($this->orm->ormObject->$property) ? $this->orm->ormObject->$property : NULL;
    }

    /**
     * __set magic
     * 
     * Allows to set values to models properties
     * 
     * @param string $property
     * @param mixed $vallue
     */
    public function __set($property, $value) {
        $this->orm->ormObject->$property = $value;
    }

    /**
     * __call magic
     * 
     * Allows to call models methods
     * 
     * @param string $method
     * @param mixed $args
     * @return void
     */
    public function __call($method, $args = NULL) {
        switch ($method) {
            case 'findOne':
            case 'findOneBy':
            case 'first':
            case 'create':
                $this->orm->ormObject = $this->orm->{$method}($args);
                return $this;
                break;
            case 'criterias':
            case 'orderBy':
            case 'groupBy':
            case 'limit':
            case 'offset':
            case 'save':
            case 'delete':
                $this->orm->{$method}($args);
                return $this;
                break;
            case 'get':
            case 'count':
            case 'asArray':
                return $this->orm->{$method}($args);
                break;
            default:
                throw new \Exception(_message(ExceptionMessages::UNDEFINED_MODEL_METHOD, $method));
                break;
        }
    }

}
