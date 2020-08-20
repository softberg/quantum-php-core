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

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\DatabaseException;
use Quantum\Loader\Loader;
use stdClass;

/**
 * Database class
 * @package Quantum
 * @subpackage Libraries
 * @category Database
 */
class Database
{

    /**
     * Loader object
     * @var Loader 
     */
    private $loader;

    /**
     * Database setup
     * @var object 
     */
    private $setup = null;

    /**
     * Database configurations
     * @var array 
     */
    private static $configs = [];

    /**
     * Default Database Abstract Layer class
     * @var string
     */
    private static $defaultDbalClass = IdiormDbal::class;

    /**
     * Database common methods
     * @var array
     */
    private static $commonMethods = ['execute', 'query', 'lastQuery', 'lastStatement', 'queryLog'];

    /**
     * Active Connection
     * @var mixed
     */
    private $activeConnection = null;

    /**
     * Database constructor.
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;

        $this->setup = new stdClass();
        $this->setup->module = current_module();
        $this->setup->env = 'config';
        $this->setup->fileName = 'database';
        $this->setup->exceptionMessage = ExceptionMessages::CONFIG_FILE_NOT_FOUND;
    }

    /**
     * Gets the ORM
     * @param string $modelName
     * @param string $table
     * @param string $idColumn
     * @return IdiormDbal
     * @throws DatabaseException When table is not defined in user defined model
     */
    public function getORM($modelName, $table, $idColumn = 'id'): IdiormDbal
    {
        $dbalClass = $this->getDbalClass();

        if (!$this->connected()) {
            $this->connect($dbalClass);
        }

        if (empty($table)) {
            throw new DatabaseException(_message(ExceptionMessages::MODEL_WITHOUT_TABLE_DEFINED, $modelName));
        }

        return new $dbalClass($table, $idColumn);
    }

    /**
     * Checks the active connection
     * @return bool
     */
    public function connected()
    {
        if ($this->activeConnection) {
            return true;
        }

        return false;
    }

    /**
     * Connects to database
     * @throws DatabaseException
     */
    public function connect($dbalClass)
    {
        $configs = $this->loader->setup($this->setup)->load();

        if (!key_exists('current', $configs)) {
            throw new DatabaseException(ExceptionMessages::INCORRECT_CONFIG);
        }

        $currentKey = $configs['current'];

        if (!key_exists($currentKey, $configs)) {
            throw new DatabaseException(ExceptionMessages::INCORRECT_CONFIG);
        }

        self::$configs = $configs[$currentKey];

        $this->activeConnection = $dbalClass::dbConnect(self::$configs);
    }

    /**
     * Gets the ORM class defined in config, otherwise default ORM will be used
     * @return string
     */
    public static function getDbalClass()
    {
        $dbalClass = (isset(self::$configs['DBAL']) && !empty(self::$configs['DBAL']) ? self::$configs['DBAL'] : self::$defaultDbalClass);

        if (class_exists($dbalClass)) {
            return $dbalClass;
        }
    }

    /**
     * Gives access to some common methods 
     * @param string $method
     * @param array|null $arguments
     */
    public static function __callStatic($method, $arguments = null)
    {
        if (in_array($method, self::$commonMethods)) {
            $dbalClass = self::getDbalClass();
            return $dbalClass::$method(...$arguments);
        }
    }

}
