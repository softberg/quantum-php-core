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

namespace Quantum\Libraries\Config;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Loader\Loader;
use Dflydev\DotAccessData\Data;

/**
 * Config Class
 *
 * Config class allows to load and import configuration files and get/set/remove config items
 *
 * @package Quantum
 * @category Config
 */
class Config
{

    /**
     * @var string
     */
    public static $module;

    /**
     * @var string
     */
    public static $exceptionMessage = ExceptionMessages::CONFIG_FILE_NOT_FOUND;

    /**
     * @var string
     */
    public static $fileName = 'config';

    /**
     * @var string
     */
    public static $env = 'config';

    /**
     *
     * @var array
     */
    public static $configs = array();

    /**
     * Get Setup
     *
     * Gets the config setup
     *
     * @return object
     */
    public static function getSetup()
    {
        return (object)[
            'module' => get_current_module(),
            'env' => self::$env,
            'fileName' => self::$fileName,
            'exceptionMessage' => self::$exceptionMessage,
        ];
    }

    /**
     * Load
     *
     * Loads configuration
     *
     * @param Loader $loader
     * @return void
     * @throws \Exception When config file is not found
     */
    public static function load(Loader $loader)
    {
        if (empty(self::$configs)) {
            self::$configs = $loader->load();
        }
    }

    /**
     * Import
     *
     * Imports new config
     *
     * @param Loader $loader
     * @param string $fileName
     * @return void
     * @throws \Exception When config file is not found or there are config collision between modules
     */
    public static function import(Loader $loader, $fileName)
    {
        $loader->set('fileName', $fileName);

        $allConfigs = self::getAll();
        foreach ($allConfigs as $key => $config) {
            if ($fileName == $key) {
                throw new \Exception(ExceptionMessages::CONFIG_COLLISION);
            }
        }

        self::$configs[$fileName] = $loader->load();
    }

    /**
     * Gets all the config data
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$configs;
    }

    /**
     * Gets a config item
     *
     * @param string $key
     * @param mixed $default
     * @return mixed|null The configuration item or NULL, if the item does not exists
     */
    public static function get($key, $default = null)
    {
        $data = new Data(self::$configs);
        if (self::has($key)) {
            return $data->get($key);
        }

        return $default;

    }

    /**
     * Checks config data
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        $data = new Data(self::$configs);
        return ($data->has($key));
    }

    /**
     * Sets new value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        $data = new Data(self::$configs);
        $data->set($key, $value);
        self::$configs = $data->export();
    }

    /**
     * Removes data from config
     *
     * @param $key
     * @return void
     */
    public static function remove($key)
    {
        $data = new Data(self::$configs);
        $data->remove($key);
        self::$configs = $data->export();
    }

}
