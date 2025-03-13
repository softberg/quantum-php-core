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
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Traits\RelationalTrait;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class Database
 * @package Quantum\Libraries\Database
 */
class Database
{

    use RelationalTrait;

    const ADAPTERS = [
        'sleekdb' => SleekDbal::class,
        'mysql' => IdiormDbal::class,
        'sqlite' => IdiormDbal::class,
        'pgsql' => IdiormDbal::class,
    ];

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
     * @var string
     */
    private $ormClass;

    /**
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private function __construct()
    {
        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database'));
        }

        $adapterName = config()->get('database.current');

        if (!array_key_exists($adapterName, self::ADAPTERS)) {
            throw DatabaseException::adapterNotSupported($adapterName);
        }

        $this->ormClass = self::ADAPTERS[$adapterName];

        if (!class_exists($this->ormClass)) {
            throw DatabaseException::ormClassNotFound($this->ormClass);
        }

        $this->configs = config()->get('database.' . $adapterName);

        if (!$this->ormClass::getConnection()) {
            $this->ormClass::connect($this->configs);
        }
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
     * Gets the DB configurations
     * @return array|null
     */
    public function getConfigs(): ?array
    {
        return $this->configs;
    }

    /**
     * Gets the ORM class
     * @return string
     */
    public function getOrmClass(): string
    {
        return $this->ormClass;
    }

    /**
     * Gets the ORM
     * @param string $table
     * @param string $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return DbalInterface
     */
    public function getOrm(string $table, string $idColumn = 'id', array $foreignKeys = [], array $hidden = []): DbalInterface
    {
        return new $this->ormClass($table, $idColumn, $foreignKeys, $hidden);
    }
}