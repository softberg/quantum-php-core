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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Database;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Traits\RelationalTrait;
use Quantum\Di\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class Database
 * @package Quantum\Libraries\Database
 */
class Database
{

    use RelationalTrait;

    /**
     * Database configurations
     * @var array
     */
    private $configs = [];

    /**
     * Database instance
     * @var Database|null
     */
    private static $instance = null;

    /**
     * Database constructor.
     * @throws ConfigException
     * @throws DiException
     * @throws DatabaseException
     * @throws ReflectionException
     */
    private function __construct()
    {
        $this->configs = $this->getConfigs();
    }

    /**
     * Get Instance
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the ORM
     * @param string $table
     * @param string $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return DbalInterface
     * @throws DatabaseException
     */
    public function getOrm(string $table, string $idColumn = 'id', array $foreignKeys = [], array $hidden = []): DbalInterface
    {
        $ormClass = $this->getOrmClass();

        return new $ormClass($table, $idColumn, $foreignKeys, $hidden);
    }

    /**
     * Gets the DB configurations
     * @return array
     * @throws ConfigException
     * @throws DiException
     * @throws DatabaseException
     * @throws ReflectionException
     */
    public function getConfigs(): ?array
    {
        if (!config()->has('database') || !config()->has('database.current')) {
            config()->import(new Setup('config', 'database'));
        }

        $currentKey = config()->get('database.current');

        if (!config()->has('database.' . $currentKey)) {
            throw DatabaseException::incorrectConfig();
        }

        if (!config()->has('database.' . $currentKey . '.orm')) {
            throw DatabaseException::incorrectConfig();
        }

        return config()->get('database.' . $currentKey);
    }
}