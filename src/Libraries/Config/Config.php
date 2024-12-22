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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Config;

use Quantum\Contracts\StorageInterface;
use Quantum\Exceptions\DiException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Class Config
 * @package Quantum\Libraries\Config
 */
class Config implements StorageInterface
{

    /**
     * Configs
     * @var Data|null
     */
    private static $configs = null;

    /**
     * Instance of Config
     * @var Config
     */
    private static $instance = null;

    /**
     * GetInstance
     * @return Config|null
     */
    public static function getInstance(): ?Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Loads configuration
     * @param Setup $setup
     * @throws ConfigException
     * @throws DiException
     * @throws \ReflectionException
     */
    public function load(Setup $setup)
    {
        if (self::$configs) {
            throw ConfigException::configAlreadyLoaded();
        }

        self::$configs = new Data(Di::get(Loader::class)->setup($setup)->load());
    }

    /**
     * Imports new config file
     * @param Setup $setup
     * @throws ConfigException
     * @throws DiException
     * @throws \ReflectionException
     */
    public function import(Setup $setup)
    {
        $fileName = $setup->getFilename();

        if ($fileName && $this->has($fileName)) {
            throw ConfigException::configCollision($fileName);
        }

        if (!self::$configs) {
            self::$configs = new Data([$fileName => Di::get(Loader::class)->setup($setup)->load()]);
        } else {
            self::$configs->import([$fileName => Di::get(Loader::class)->setup($setup)->load()]);
        }
    }

    /**
     * Gets the config item by given key
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (self::$configs && self::$configs->has($key)) {
            return self::$configs->get($key);
        }

        return $default;
    }

    /**
     * Get all configs
     * @return Data
     */
    public function all(): ?Data
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
        return self::$configs && !empty(self::$configs->has($key));
    }

    /**
     * Sets new value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        if (!self::$configs) {
            self::$configs = new Data([$key => $value]);
        } else {
            self::$configs->set($key, $value);
        }
    }

    /**
     * Removes the data from config
     * @param string $key
     */
    public function delete(string $key)
    {
        self::$configs && self::$configs->remove($key);
    }

    /**
     * Deletes whole config data
     */
    public function flush()
    {
        self::$configs = null;
    }

}
