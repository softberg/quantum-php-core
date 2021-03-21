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

namespace Quantum\Libraries\Config;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\ConfigException;
use Quantum\Contracts\StorageInterface;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use stdClass;

/**
 * Class Config
 * @package Quantum\Libraries\Config
 */
class Config implements StorageInterface
{

    /**
     * Configs
     * @var array
     */
    private static $configs = [];

    /**
     * Instance of Config
     * @var Config 
     */
    private static $configInstance = null;

    /**
     * Config setup
     * @var object 
     */
    private $setup = null;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->setup = new stdClass();
        $this->setup->module = current_module();
        $this->setup->hierarchical = true;
        $this->setup->env = 'config';
        $this->setup->fileName = 'config';
        $this->setup->exceptionMessage = ExceptionMessages::CONFIG_FILE_NOT_FOUND;
    }

    /**
     * GetInstance
     * @return Config
     */
    public static function getInstance()
    {
        if (self::$configInstance === null) {
            self::$configInstance = new self();
        }

        return self::$configInstance;
    }

    /**
     * Loads configuration
     * @param Loader $loader
     * @throws ConfigException When config file is not found
     */
    public function load(Loader $loader)
    {
        if (!$this->setup) {
            throw new ConfigException(_message(ExceptionMessages::SETUP_NOT_PROVIDED, __CLASS__));
        }

        if (empty(self::$configs)) {
            self::$configs = $loader->setup($this->setup)->load();
        }
    }

    /**
     * Imports new config file
     * @param Loader $loader
     * @param string $fileName
     * @throws ConfigException When config file is not found or there are config collision between modules
     */
    public function import(Loader $loader, $fileName)
    {
        if ($this->has($fileName)) {
            throw new ConfigException(_message(ExceptionMessages::CONFIG_COLLISION, $fileName));
        }

        if (!$this->setup) {
            throw new ConfigException(_message(ExceptionMessages::SETUP_NOT_PROVIDED, __CLASS__));
        }

        self::$configs[$fileName] = $loader->load();
    }

    /**
     * Gets the config item by given key
     * @param string $key
     * @param mixed $default
     * @return mixed|null The configuration item or NULL, if the item does not exists
     */
    public function get($key, $default = null)
    {
        $data = new Data(self::$configs);

        if ($this->has($key)) {
            return $data->get($key);
        }

        return $default;
    }

    /**
     * Get all configs
     * @return array
     */
    public function all(): array
    {
        return self::$configs;
    }

    /**
     * Checks config data
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $data = new Data(self::$configs);

        return ($data->has($key));
    }

    /**
     * Sets new value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $data = new Data(self::$configs);

        $data->set($key, $value);
        self::$configs = $data->export();
    }

    /**
     * Removes the data from config
     * @param $key
     */
    public function delete($key)
    {
        $data = new Data(self::$configs);

        $data->remove($key);
        self::$configs = $data->export();
    }

    /**
     * Deletes whole config data
     * @return void
     */
    public function flush()
    {
        self::$configs = [];
    }

}
