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

namespace Quantum\Environment;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\AppException;
use Quantum\Exceptions\EnvException;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;
use Dotenv\Dotenv;

/**
 * Class Environment
 * @package Quantum\Environment
 * @uses Dotenv
 */
class Environment
{

    /**
     * FileSystem instance
     * @var FileSystem
     */
    private $fs;

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
    private static $instance = null;

    /**
     * GetInstance
     * @return Environment
     */
    public static function getInstance(): Environment
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Loads environment variables from file
     * @param Setup $setup
     * @return $this
     * @throws AppException
     * @throws EnvException
     * @throws DiException
     * @throws ReflectionException
     */
    public function load(Setup $setup): Environment
    {
        $env = Di::get(Loader::class)->setup($setup)->load();

        if (isset($env['app_env']) && $env['app_env'] != 'production') {
            $this->envFile = '.env.' . $env['app_env'];
        }

        $this->fs = Di::get(FileSystem::class);

        if (!$this->fs->exists(base_dir() . DS . $this->envFile)) {
            throw EnvException::fileNotFound('.env');
        }

        $this->envContent = Dotenv::createMutable(base_dir(), $this->envFile)->load();

        return $this;
    }

    /**
     * Gets the environment variable value
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getValue(string $key, $default = null)
    {
        $val = getenv($key);

        return $val !== false ? $val : $default;
    }

    /**
     * Creates or updates the row in .env
     * @param string $key
     * @param string|null $value
     */
    public function updateRow(string $key, ?string $value)
    {
        $row = $this->getRow($key);

        $envFilePath = base_dir() . DS . $this->envFile;

        if ($row) {
            $this->fs->put($envFilePath, preg_replace(
                '/^' . $row . '/m',
                $key . "=" . $value,
                $this->fs->get($envFilePath)
            ));
        } else {
            $this->fs->append($envFilePath, PHP_EOL . $key . "=" . $value . PHP_EOL);
        }

        $this->envContent = Dotenv::createMutable(base_dir(), $this->envFile)->load();
    }

    /**
     * Gets the row of .env file by given key
     * @param string $key
     * @return string|null
     */
    private function getRow(string $key): ?string
    {
        foreach ($this->envContent as $index => $row) {
            if (preg_match('/^' . $key . '/', $index)) {
                return $key . '=' . preg_quote($row, '/');
            }
        }

        return null;
    }

    /**
     * Checks if there is a such key
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        foreach ($this->envContent as $index => $row) {
            if (preg_match('/^' . $key . '/', $index)) {
                return true;
            }
        }

        return false;
    }
}
