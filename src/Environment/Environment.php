<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Environment;

use Quantum\Environment\Exceptions\EnvException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Enums\Env;
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
     * Environment file
     */
    private string $envFile = '.env';

    private bool $isMutable = false;

    /**
     * Loaded env content
     */
    private array $envContent = [];

    private bool $loaded = false;

    private static string $appEnv = Env::PRODUCTION;

    /**
     * Instance of Environment
     */
    private static ?Environment $instance = null;

    /**
     * GetInstance
     */
    public static function getInstance(): Environment
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setMutable(bool $isMutable): Environment
    {
        $this->isMutable = $isMutable;
        return $this;
    }

    /**
     * Loads environment variables from file
     * @throws BaseException
     * @throws EnvException
     * @throws DiException
     * @throws ReflectionException
     */
    public function load(Setup $setup): void
    {
        if ($this->loaded) {
            return;
        }

        $envConfig = Di::get(Loader::class)->setup($setup)->load();

        $appEnv = $envConfig['app_env'] ?? Env::PRODUCTION;

        $this->envFile = '.env' . ($appEnv !== Env::PRODUCTION ? ".$appEnv" : '');

        if (!fs()->exists($this->getEnvFilePath())) {
            throw EnvException::fileNotFound($this->envFile);
        }

        $this->envContent = $this->loadDotenvFile();

        $this->loaded = true;
        self::$appEnv = $appEnv;
    }

    /**
     * Gets the app current environment
     */
    public function getAppEnv(): string
    {
        return self::$appEnv;
    }

    /**
     * Gets the environment variable value
     * @param null|mixed $default
     * @return mixed
     * @throws EnvException
     */
    public function getValue(string $key, $default = null)
    {
        if (!$this->loaded) {
            throw EnvException::environmentNotLoaded();
        }

        if (array_key_exists($key, $this->envContent)) {
            return $this->envContent[$key];
        }

        return $default;
    }

    /**
     * Checks if there is a such key
     */
    public function hasKey(string $key): bool
    {
        return $this->findKeyRow($key) !== null;
    }

    /**
     * Gets the row of .env file by given key
     */
    public function getRow(string $key): ?string
    {
        return $this->findKeyRow($key);
    }

    /**
     * Creates or updates the row in .env
     * @throws BaseException
     * @throws DiException
     * @throws EnvException
     * @throws ReflectionException
     * @throws ConfigException
     */
    public function updateRow(string $key, ?string $value): void
    {
        if (!$this->isMutable) {
            throw EnvException::environmentImmutable();
        }

        if (!$this->loaded) {
            throw EnvException::environmentNotLoaded();
        }

        $envFilePath = $this->getEnvFilePath();
        $row = $this->getRow($key);

        if ($row) {
            $envFileContent = fs()->get($envFilePath);
            $envFileContent = preg_replace('/^' . preg_quote($row, '/') . '/m', $key . '=' . $value, $envFileContent);

            fs()->put($envFilePath, (string) $envFileContent);
        } else {
            fs()->append($envFilePath, PHP_EOL . $key . '=' . $value . PHP_EOL);
        }

        $this->envContent[$key] = $value;
    }

    /**
     * Finds the row by provided key
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

    private function loadDotenvFile(bool $forceMutableReload = false): array
    {
        $baseDir = App::getBaseDir();

        $dotenv = ($forceMutableReload || $this->isMutable)
            ? Dotenv::createMutable($baseDir, $this->envFile)
            : Dotenv::createImmutable($baseDir, $this->envFile);

        $loadedVars = $dotenv->load();

        return is_array($loadedVars) ? $loadedVars : [];
    }

    private function getEnvFilePath(): string
    {
        return App::getBaseDir() . DS . $this->envFile;
    }
}
