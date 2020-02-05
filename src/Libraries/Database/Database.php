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

namespace Quantum\Libraries\Database;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\DatabaseException;

/**
 * Database class
 *
 * Initialize the database
 *
 * @package Quantum
 * @subpackage Libraries.Database
 * @category Libraries
 */
class Database
{

    /**
     * Default ORM DBAL
     *
     * @var string
     */
    private static $defaultDbal = IdiormDbal::class;

    /**
     * Database configurations
     *
     * @var array
     */
    private static $dbConfig;

    /**
     * Active Connection
     *
     * @var mixed
     */
    private static $activeConnection = null;
    
    /**
     * 
     * @param string $modelName
     * @param string $table
     * @param string $idColumn
     * @return \Quantum\Libraries\Database\dbalClass
     * @throws DatabaseException When table is not defined in user defined model
     */
    public static function getDbalInstance($modelName, $table, $idColumn = 'id')
    {
        $dbalClass = self::getDbalClass();

        if (!self::connected()) {
            self::connect($dbalClass);
        }

        if (empty($table)) {
            throw new DatabaseException(_message(ExceptionMessages::MODEL_WITHOUT_TABLE_DEFINED, $modelName));
        }

        return new $dbalClass($table, $idColumn);
    }

    /**
     * Connected
     *
     * Checks the active connection
     *
     * @return bool
     */
    public static function connected()
    {
        if (self::$activeConnection)
            return true;

        return false;
    }

    /**
     * Connect
     *
     * Connects to database
     *
     * @uses HookManager::call
     * @return void
     * @throws DatabaseException
     */
    private static function connect($dbalClass)
    {
        self::$activeConnection = $dbalClass::dbConnect(self::getConfig());
    }

    /**
     * Set DB Config
     *
     * Finds and sets the DB configs from current config/database.php of module or
     * from top config/database.php if in module it's not defined
     *
     * @return void
     * @throws DatabaseException When config not found
     */
    private static function setConfig()
    {
        if (file_exists(modules_dir() . DS . current_module() . DS . 'Config' . DS . 'database.php')) {
            if (!self::$dbConfig) {
                self::$dbConfig = require_once modules_dir() . DS . current_module() . DS . 'Config' . DS . 'database.php';
            }
        } else {
            if (file_exists(BASE_DIR . DS . 'config' . DS . 'database.php')) {
                if (!self::$dbConfig) {
                    self::$dbConfig = require_once BASE_DIR . DS . 'config' . DS . 'database.php';
                }
            } else {
                throw new DatabaseException(ExceptionMessages::DB_CONFIG_NOT_FOUND);
            }
        }
    }

    /**
     * Get DB Config
     *
     * @return array
     * @throws DatabaseException When config is not found or incorrect
     */
    private static function getConfig()
    {
        self::setConfig();

        if (!empty(self::$dbConfig) && is_array(self::$dbConfig) && key_exists('current', self::$dbConfig)) {
            $current_key = self::$dbConfig['current'];

            if ($current_key && key_exists($current_key, self::$dbConfig)) {
                return self::$dbConfig[$current_key];
            } else {
                throw new DatabaseException(ExceptionMessages::INCORRECT_CONFIG);
            }
        } else {
            throw new DatabaseException(ExceptionMessages::INCORRECT_CONFIG);
        }
    }

    /**
     * Gets the ORM Class
     *
     * Gets the ORM class defined in config/database.php if exists, otherwise default ORM will be used
     *
     * @return string
     */
    public static function getDbalClass()
    {
        $dbalClass = (isset(self::$dbConfig['DBAL']) && !empty(self::$dbConfig['DBAL']) ? self::$dbConfig['DBAL'] : self::$defaultDbal);

        if (class_exists($dbalClass)) {
            return $dbalClass;
        }
    }

    /**
     * Raw execute
     *
     * @param $query
     * @param $parameters
     * @return bool
     */
    public static function execute($query, $parameters)
    {
        $dbalClass = self::getDbalClass();

        return $dbalClass::execute($query, $parameters);
    }

    /**
     * Raw query
     *
     * @param $query
     * @param $parameters
     * @return array
     */
    public static function query($query, $parameters)
    {
        $dbalClass = self::getDbalClass();

        return $dbalClass::query($query, $parameters);
    }

    /**
     * Gets the last query executed
     *
     * @return string
     */
    public static function getLastQuery()
    {
        $dbalClass = self::getDbalClass();

        return $dbalClass::getLastQuery();
    }

    /**
     * Returns the PDOStatement instance last used
     *
     * @return string
     */
    public static function getLastStatement()
    {
        $dbalClass = self::getDbalClass();

        return $dbalClass::getLastStatement();
    }

    /**
     * Get an array containing all the queries 
     * run on a specified connection up to now.
     *
     * @return array
     */
    public static function getQueryLog()
    {
        $dbalClass = self::getDbalClass();
        
        return $dbalClass::getQueryLog();
    }

}
