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

namespace Quantum\Libraries\Database;

use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\ModelException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;

/**
 * Class Database
 * @package Quantum\Libraries\Database
 * @method static bool execute(string $query, array $parameters = [])
 * @method static array query(string $query, array $parameters = [])
 * @method static string lastQuery()
 * @method static object lastStatement()
 * @method static array queryLog()
 * @mixin \Quantum\Libraries\Database\IdiormDbal
 */
class Database
{

    /**
     * Loader object
     * @var \Quantum\Loader\Loader
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
     * Instance of Database
     * @var \Quantum\Libraries\Database\Database
     */
    private static $instance;

    /**
     * Database constructor.
     * @param \Quantum\Loader\Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get Instance
     * @param \Quantum\Loader\Loader $loader
     * @return \Quantum\Libraries\Database\Database
     */
    public static function getInstance(Loader $loader): Database
    {
        if (self::$instance === null) {
            self::$instance = new self($loader);
        }

        return self::$instance;
    }

    /**
     * Gets the ORM
     * @param string $table
     * @param string|null $modelName
     * @param string $idColumn
     * @return \Quantum\Libraries\Database\IdiormDbal
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\LoaderException
     * @throws \Quantum\Exceptions\ModelException
     */
    public function getORM(string $table, ?string $modelName = null, string $idColumn = 'id'): IdiormDbal
    {
        $dbalClass = $this->getDbalClass();

        if (!$this->connected()) {
            $this->connect($dbalClass);
        }

        if (empty($table)) {
            throw ModelException::noTableDefined($modelName);
        }

        return new $dbalClass($table, $idColumn);
    }

    /**
     * Checks the active connection
     * @return bool
     */
    public function connected(): bool
    {
        if ($this->activeConnection) {
            return true;
        }

        return false;
    }

    /**
     * Connects to database
     * @param string $dbalClass
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\LoaderException
     */
    public function connect(string $dbalClass)
    {
        $configs = $this->loader->setup(new Setup('config', 'database'))->load();

        if (!key_exists('current', $configs)) {
            throw DatabaseException::incorrectConfig();
        }

        $currentKey = $configs['current'];

        if (!key_exists($currentKey, $configs)) {
            throw DatabaseException::incorrectConfig();
        }

        self::$configs = $configs[$currentKey];

        $this->activeConnection = $dbalClass::dbConnect(self::$configs);
    }

    /**
     * Gets the ORM class defined in config, otherwise default ORM will be used
     * @return string
     */
    public static function getDbalClass(): string
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
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments = null)
    {
        if (in_array($method, self::$commonMethods)) {
            $dbalClass = self::getDbalClass();
            return $dbalClass::$method(...$arguments);
        }
    }

}
