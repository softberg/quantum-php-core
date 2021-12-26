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

namespace Quantum\Libraries\Database;

use Quantum\Exceptions\DatabaseException;
use Quantum\Loader\Setup;

/**
 * Class Database
 * @package Quantum\Libraries\Database
 */
class Database
{

    /**
     * Database configurations
     * @var array
     */
    private $configs = [];

    /**
     * Database instance
     * @var \Quantum\Libraries\Database\Database|null
     */
    private static $instance = null;

    /**
     * Database constructor.
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private function __construct()
    {
        $this->configs = $this->getConfigs();
    }

    /**
     * Get Instance
     * @return \Quantum\Libraries\Database\Database|null
     */
    public static function getInstance(): ?Database
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
     * @return \Quantum\Libraries\Database\DbalInterface
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function getOrm(string $table, string $idColumn = 'id', array $foreignKeys = []): DbalInterface
    {
        $ormClass = $this->getOrmClass();

        return new $ormClass($table, $idColumn, $foreignKeys);
    }

    /**
     * Raw execute
     * @param string $query
     * @param array $parameters
     * @return bool
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        return self::resolveQuery(__FUNCTION__, $query, $parameters);
    }

    /**
     * Raw query
     * @param string $query
     * @param array $parameters
     * @return array
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function query(string $query, array $parameters = []): array
    {
        return self::resolveQuery(__FUNCTION__, $query, $parameters);
    }

    /**
     * Gets the last query executed
     * @return string|null
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function lastQuery(): ?string
    {
        return self::resolveQuery(__FUNCTION__);
    }

    /**
     * Get an array containing all the queries
     * run on a specified connection up to now.
     * @return array
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public static function queryLog(): array
    {
        return self::resolveQuery(__FUNCTION__);
    }

    /**
     * Gets the DB configurations
     * @return array
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    protected function getConfigs(): ?array
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

    /**
     * Gets the ORM class
     * @return string
     * @throws \Quantum\Exceptions\DatabaseException
     */
    protected function getOrmClass(): string
    {
        $ormClass = $this->configs['orm'];

        if (!class_exists($ormClass)) {
            throw DatabaseException::ormClassNotFound($ormClass);
        }

        if (!$ormClass::getConnection()) {
            $ormClass::connect($this->configs);
        }

        return $ormClass;
    }

    /**
     * Resolves the requested query
     * @param string $method
     * @param string $query
     * @param array $parameters
     * @return mixed
     * @throws \Quantum\Exceptions\DatabaseException
     */
    protected static function resolveQuery(string $method, string $query = '', array $parameters = [])
    {
        $self = self::getInstance();

        $ormClass = $self->getOrmClass();

        return $ormClass::$method($query, $parameters);
    }

}
