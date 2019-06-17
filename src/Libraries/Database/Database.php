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
use Quantum\Hooks\HookManager;
use ORM;

/**
 * Database class
 * 
 * Initialize the database
 * 
 * @package Quantum
 * @subpackage Libraries.Database
 * @category Libraries
 */
class Database {

    /**
     * Path to ORM class
     * 
     * @var string 
     */
    private static $ormPath;

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
    private static $activeConnection = NULL;

    /**
     * Connect
     *
     * Connects to database
     *
     * @uses HookManager::call
     * @throws \Exception
     * @return void
     */
    public static function connect() {
        if(!self::$activeConnection) {
            self::setORM();
            self::$activeConnection = HookManager::call('dbConnect', self::getConfig(), self::getORM());
        }
    }

    /**
     * Set DB Config
     *
     * Finds and sets db configs from current config/database.php of module or
     * from top config/database.php if in module it's not defined
     *
     * @return void
     * @throws \Exception When config not found
     */
    private static function setConfig() {
        if (file_exists(MODULES_DIR . DS . get_current_module() . '/Config/database.php')) {
            if ( !self::$dbConfig ) {
                self::$dbConfig = require_once MODULES_DIR . DS . get_current_module() . '/Config/database.php';
            }
        }
        else {
            if (file_exists(BASE_DIR . '/config/database.php')) {
                if ( !self::$dbConfig )  {
                    self::$dbConfig = require_once BASE_DIR . '/config/database.php';
                }
            } else {
                throw new \Exception(ExceptionMessages::DB_CONFIG_NOT_FOUND);
            }
        }
    }

    /**
     * Get DB Config
     * 
     * @return array
     * @throws \Exception When config is not found or incorrect
     */
    private static function getConfig() {
        self::setConfig();

        if (!empty(self::$dbConfig) && is_array(self::$dbConfig) && key_exists('current', self::$dbConfig)) {
            $current_key =  self::$dbConfig['current'];

            if ($current_key && key_exists($current_key, self::$dbConfig) ) {
                return self::$dbConfig[$current_key];
            } else {
                throw new \Exception(ExceptionMessages::INCORRECT_CONFIG);
            }
        } else {
            throw new \Exception(ExceptionMessages::INCORRECT_CONFIG);
        }
    }

    /**
     * Sets the ORM
     * 
     * @return void
     */
    private static function setORM() {
        self::$ormPath = (isset(self::$dbConfig['orm']) && !empty(self::$dbConfig['orm']) ? $dbConfig['orm'] : '\\Quantum\\Libraries\\Database\\IdiormDbal');
    }

    /**
     * Gets the ORM
     *
     * Gets the ORM defined in config/database.php if exists, otherwise
     * default ORM
     *
     * @return string
     */
    public static function getORM() {
        return self::$ormPath;
    }

}
