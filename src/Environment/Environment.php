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

namespace Quantum\Environment;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;
use Dotenv\Dotenv;
use stdClass;

/**
 * Class Environment
 * @package Quantum\Environment
 * @uses Dotenv
 */
class Environment
{

    /**
     * Environment file
     * @var string 
     */
    private $envFile = '.env';

    /**
     * Loaded env content
     * @var array 
     */
    private $envContent = [];

    /**
     * Instance of Environment
     * @var Environment 
     */
    private static $envInstance = null;

    /**
     * File System
     * @var FileSystem
     */
    private $fs;

    /**
     * Environment setup
     * @var object 
     */
    private $setup = null;

    /**
     * Class constructor
     */
    private function __construct(FileSystem $fs)
    {
        $this->fs = $fs;

        $this->setup = new stdClass();
        $this->setup->module = null;
        $this->setup->hierarchical = true;
        $this->setup->env = 'config';
        $this->setup->fileName = 'env';
        $this->setup->exceptionMessage = ExceptionMessages::CONFIG_FILE_NOT_FOUND;
    }

    /**
     * GetInstance
     * @return Environment
     */
    public static function getInstance(FileSystem $fs)
    {
        if (self::$envInstance === null) {
            self::$envInstance = new self($fs);
        }

        return self::$envInstance;
    }

    /**
     * Loads the environment variables from .env file
     * @return $this
     */
    public function load(Loader $loader)
    {
        $env = $loader->setup($this->setup)->load();

        if ($env['app_env'] != 'production') {
            $this->envFile = '.env.' . $env['app_env'];
        }

        $this->envContent = (new Dotenv(base_dir(), $this->envFile))->load();
        return $this;
    }

    /**
     * Gets the environment variable value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        $val = getenv($key);

        if ($val === false) {
            if ($default) {
                return $default;
            }

            return null;
        } else {
            return $val;
        }
    }

    /**
     * Creates or updates the row in .env
     * @param string $key
     * @param string $value
     */
    public function updateRow($key, $value)
    {
        $oldRow = $this->getRow($key);

        $envFilePath = base_dir() . DS . $this->envFile;

        if ($oldRow) {
            $this->fs->put($envFilePath, preg_replace(
                            '/^' . $oldRow . '/m',
                            $key . "=" . $value . PHP_EOL,
                            $this->fs->get($envFilePath)
            ));
        } else {
            $this->fs->put($envFilePath, $key . "=" . $value . PHP_EOL, FILE_APPEND);
        }

        $this->envContent = (new Dotenv(base_dir(), $this->envFile))->overload();
    }

    /**
     * Gets the row of .env file by given key
     * @param $key
     * @return string|null
     */
    private function getRow($key)
    {
        foreach ($this->envContent as $row) {
            if (preg_match('/^' . $key . '=/', $row)) {
                return preg_quote($row, '/');
            }
        }

        return null;
    }

}
