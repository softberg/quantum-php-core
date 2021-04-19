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

use Quantum\Exceptions\ConfigException;
use Quantum\Contracts\StorageInterface;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;

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
     * @throws \Quantum\Exceptions\LoaderException
     */
    public function load(Loader $loader)
    {
        if (empty(self::$configs)) {
            self::$configs = $loader->setup(new Setup('config', 'config', true))->load();
        }
    }

    /**
     * Imports new config file
     * @param Loader $loader
     * @param string $fileName
     * @throws ConfigException
     * @throws \Quantum\Exceptions\LoaderException
     */
    public function import(Loader $loader, $fileName)
    {
        if ($this->has($fileName)) {
            throw new ConfigException(_message(ConfigException::CONFIG_COLLISION, $fileName));
        }

        self::$configs[$fileName] = $loader->load();
    }

    /**
     * Gets the config item by given key
     * @param string $key
     * @param mixed $default
     * @return mixed|null
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
