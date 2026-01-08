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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Database;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\Traits\TransactionTrait;
use Quantum\Libraries\Database\Traits\RelationalTrait;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class Database
 * @package Quantum\Libraries\Database
 */
class Database
{

    use TransactionTrait;
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
    private $configs;

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

        $adapter = config()->get('database.default');

        $this->ormClass = $this->getAdapterClass($adapter);

        $this->configs = config()->get('database.' . $adapter);

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
     * Gets the ORM class
     * @return string
     */
    public function getOrmClass(): string
    {
        return $this->ormClass;
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
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw DatabaseException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}