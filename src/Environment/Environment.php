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
 * @since 2.5.0
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
    private static $envInstance = null;

    /**
     * GetInstance
     * @return \Quantum\Environment\Environment
     */
    public static function getInstance(): ?Environment
    {
        if (self::$envInstance === null) {
            self::$envInstance = new self();
        }

        return self::$envInstance;
    }

    /**
     * Loads environment variables from .env file
     * @param \Quantum\Loader\Loader $loader
     * @return $this
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\EnvException
     * @throws \Quantum\Exceptions\LoaderException
     * @throws \ReflectionException
     */
    public function load(Loader $loader): Environment
    {
        $env = $loader->setup(new Setup('config', 'env', true))->load();

        if ($env['app_env'] != 'production') {
            $this->envFile = '.env.' . $env['app_env'];
        }

        $this->fs = Di::get(FileSystem::class);

        if (!$this->fs->exists(BASE_DIR . DS . $this->envFile)) {
            throw new EnvException(EnvException::ENV_FILE_NOT_FOUND);
        }

        $this->envContent = (new Dotenv(base_dir(), $this->envFile))->load();

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
     * @param string $value
     */
    public function updateRow(string $key, string $value)
    {
        $row = $this->getRow($key);

        $envFilePath = base_dir() . DS . $this->envFile;

        if ($row) {
            $this->fs->put($envFilePath, preg_replace(
                '/^' . $row . '/m',
                $key . "=" . $value . PHP_EOL,
                $this->fs->get($envFilePath)
            ));
        } else {
            $this->fs->append($envFilePath, $key . "=" . $value . PHP_EOL);
        }

        $this->envContent = (new Dotenv(base_dir(), $this->envFile))->overload();
    }

    /**
     * Gets the row of .env file by given key
     * @param string $key
     * @return string|null
     */
    private function getRow(string $key): ?string
    {
        foreach ($this->envContent as $row) {
            if (preg_match('/^' . $key . '=/', $row)) {
                return preg_quote($row, '/');
            }
        }

        return null;
    }

}
