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
use Quantum\Routes\RouteController;
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
     * Current route
     * 
     * @var mixed 
     */
    private static $currentRoute;
    
    /**
     * Path to ORM class
     * 
     * @var string 
     */
    private static $ormPath;
    
    /**
     * Active Connection
     * 
     * @var mixed 
     */
    private static $activeConnection = NULL;

    /**
     * Class constructor 
     * 
     * @param mixed $currentRoute
     * @return $this Database instance
     */
    public function __construct($currentRoute) {
        $this->currentRoute = $currentRoute;

        return $this;
    }

    /**
     * Connect
     *
     * Connects to database
     *
     * @uses HookManager::call
     * @param $currentRoute
     * @throws \Exception
     * @return void
     */
    public static function connect() {
        if(!self::$activeConnection) {
            $dbConfig = self::getConfig();
            self::setORM($dbConfig);
            self::$activeConnection = HookManager::call('dbConnect', $dbConfig, self::$ormPath);
        }
    }

    /**
     * Find Db config File
     *
     * Finds db configs from current config/database.php of module or
     * from top config/database.php if in module it's not defined
     *
     * @return array
     * @throws \Exception When config not found
     */
    private static function findDbConfigFile() {
        if (file_exists(MODULES_DIR . DS . get_current_module() . '/Config/database.php')) {
            return require_once MODULES_DIR . DS . get_current_module() . '/Config/database.php';
        }
        else {
            if (file_exists(BASE_DIR . '/config/database.php')) {
                return require_once BASE_DIR . '/config/database.php';
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
        $dbConfig = self::findDbConfigFile();

        if (!empty($dbConfig) && is_array($dbConfig) && key_exists('current', $dbConfig)) {
            $current_key =  $dbConfig['current'];

            if ($current_key && key_exists($current_key, $dbConfig) ) {
                return $dbConfig[$current_key];
            } else {
                throw new \Exception(ExceptionMessages::INCORRECT_CONFIG);
            }
        } else {
            throw new \Exception(ExceptionMessages::INCORRECT_CONFIG);
        }
    }

    /**
     * Sets ORM
     * 
     * @param array $dbConfig
     */
    private static function setORM(array $dbConfig) {
        self::$ormPath = (isset($dbConfig['orm']) && !empty($dbConfig['orm']) ? $dbConfig['orm'] : '\\Quantum\\Libraries\\Database\\IdiormDbal');
    }

    /**
     * Get ORM
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
