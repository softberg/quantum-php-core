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
 * @since 2.8.0
 */

namespace Quantum\Environment;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\EnvException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
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
     * @var \Quantum\Libraries\Storage\FileSystem
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
     * @var \Quantum\Environment\Environment
     */
    private static $instance = null;

    /**
     * GetInstance
     * @return \Quantum\Environment\Environment
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
     * @param \Quantum\Loader\Setup $setup
     * @return $this
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     * @throws \Quantum\Exceptions\EnvException
     */
    public function load(Setup $setup): Environment
    {
        $env = Di::get(Loader::class)->setup($setup)->load();

        if (isset($env['app_env']) && $env['app_env'] != 'production') {
            $this->envFile = '.env.' . $env['app_env'];
        }

        $this->fs = Di::get(FileSystem::class);

        if (!$this->fs->exists(base_dir() . DS . $this->envFile)) {
            throw EnvException::fileNotFound();
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

        if ($val === false) {
            if ($default) {
                return $default;
            }

            return null;
        }

        return $val;
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
