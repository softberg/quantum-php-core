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

use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\ModelException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;

/**
 * Database class
 * @package Quantum
 * @subpackage Libraries
 * @category Database
 * @method static array queryLog()
 * @method static string lastStatement()
 * @method static string lastQuery()
 * @method static bool($query, $parameters = [])
 * @method static array query($query, $parameters = [])
 */
class Database
{

    /**
     * Loader object
     * @var Loader 
     */
    private $loader;

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
    }

    /**
     * Gets the ORM
     * @param string $table
     * @param string|null $modelName
     * @param string $idColumn
     * @return \Quantum\Libraries\Database\IdiormDbal
     * @throws ModelException
     */
    public function getORM($table, $modelName = null, $idColumn = 'id'): IdiormDbal
    {
        $dbalClass = $this->getDbalClass();

        if (!$this->connected()) {
            $this->connect($dbalClass);
        }

        if (empty($table)) {
            throw new ModelException(_message(ModelException::MODEL_WITHOUT_TABLE_DEFINED, $modelName));
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
        $configs = $this->loader->setup(new Setup('config', 'database'))->load();

        if (!key_exists('current', $configs)) {
            throw new DatabaseException(DatabaseException::INCORRECT_CONFIG);
        }

        $currentKey = $configs['current'];

        if (!key_exists($currentKey, $configs)) {
            throw new DatabaseException(DatabaseException::INCORRECT_CONFIG);
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
