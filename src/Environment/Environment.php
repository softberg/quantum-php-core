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

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\App\App;
use Dotenv\Dotenv;
use Quantum\Di\Di;

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
     * @var bool
     */
    private $isMutable = false;

    /**
     * Loaded env content
     * @var array
     */
    private $envContent = [];

    private static $appEnv = 'production';

    /**
     * Instance of Environment
     * @var Environment
     */
    private static $instance = null;

    /**
     * @throws BaseException
     */
    private function __construct()
    {
        $this->fs = FileSystemFactory::get();
    }

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
     * @param bool $isMutable
     * @return Environment
     */
    public function setMutable(bool $isMutable): Environment
    {
        $this->isMutable = $isMutable;
        return $this;
    }

    /**
     * Loads environment variables from file
     * @param Setup $setup
     * @return void
     * @throws BaseException
     * @throws EnvException
     * @throws DiException
     * @throws ReflectionException
     */
    public function load(Setup $setup)
    {
        if (!empty($this->envContent)) {
            return;
        }

        $envConfig = Di::get(Loader::class)->setup($setup)->load();

        $appEnv = $envConfig['app_env'] ?? 'production';
        $this->envFile = ".env" . ($appEnv !== 'production' ? ".$appEnv" : '');

        if (!file_exists(App::getBaseDir() . DS . $this->envFile)) {
            throw EnvException::fileNotFound($this->envFile);
        }

        $dotenv = $this->isMutable
            ? Dotenv::createMutable(App::getBaseDir(), $this->envFile)
            : Dotenv::createImmutable(App::getBaseDir(), $this->envFile);

        $this->envContent = $dotenv->load();

        self::$appEnv = $appEnv;
    }

    /**
     * Gets the app current environment
     * @return string
     */
    public function getAppEnv(): string
    {
        return self::$appEnv;
    }

    /**
     * Gets the environment variable value
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     * @throws EnvException
     */
    public function getValue(string $key, $default = null)
    {
        if (empty($this->envContent)) {
            throw EnvException::environmentNotLoaded();
        }

        $val = getenv($key);

        return $val !== false ? $val : $default;
    }

    /**
     * Checks if there is a such key
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return $this->findKeyRow($key) !== null;
    }

    /**
     * Gets the row of .env file by given key
     * @param string $key
     * @return string|null
     */
    public function getRow(string $key): ?string
    {
        return $this->findKeyRow($key);
    }

    /**
     * Creates or updates the row in .env
     * @param string $key
     * @param string|null $value
     * @throws EnvException
     */
    public function updateRow(string $key, ?string $value)
    {
        if (!$this->isMutable) {
            throw EnvException::environmentImmutable();
        }

        if (empty($this->envContent)) {
            throw EnvException::environmentNotLoaded();
        }

        $row = $this->getRow($key);

        $envFilePath = App::getBaseDir() . DS . $this->envFile;

        if ($row) {
            $this->fs->put($envFilePath, preg_replace(
                    '/^' . $row . '/m',
                    $key . "=" . $value,
                    $this->fs->get($envFilePath)
                )
            );
        } else {
            $this->fs->append($envFilePath, PHP_EOL . $key . "=" . $value . PHP_EOL);
        }

        $this->envContent = Dotenv::createMutable(App::getBaseDir(), $this->envFile)->load();
    }

    /**
     * @param string $key
     * @return string|null
     */
    private function findKeyRow(string $key): ?string
    {
        foreach ($this->envContent as $index => $row) {
            if (preg_match('/^' . $key . '/', $index)) {
                return $key . '=' . preg_quote($row, '/');
            }
        }

        return null;
    }
}