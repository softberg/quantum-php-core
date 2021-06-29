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
 * @since 2.4.0
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
     * @var \Quantum\Libraries\Config\Config
     */
    private static $configInstance = null;

    /**
     * GetInstance
     * @return \Quantum\Libraries\Config\Config|null
     */
    public static function getInstance(): ?Config
    {
        if (self::$configInstance === null) {
            self::$configInstance = new self();
        }

        return self::$configInstance;
    }

    /**
     * Loads configuration
     * @param \Quantum\Loader\Loader $loader
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
     * @param \Quantum\Loader\Loader $loader
     * @param string $fileName
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\LoaderException
     */
    public function import(Loader $loader, string $fileName)
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
    public function get(string $key, $default = null)
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
    public function has(string $key): bool
    {
        $data = new Data(self::$configs);

        return !empty($data->get($key));
    }

    /**
     * Sets new value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $data = new Data(self::$configs);

        $data->set($key, $value);
        self::$configs = $data->export();
    }

    /**
     * Removes the data from config
     * @param string $key
     */
    public function delete(string $key)
    {
        $data = new Data(self::$configs);

        $data->remove($key);
        self::$configs = $data->export();
    }

    /**
     * Deletes whole config data
     */
    public function flush()
    {
        self::$configs = [];
    }

}
