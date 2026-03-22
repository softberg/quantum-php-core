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

namespace Quantum\Config;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Config\Contracts\ConfigInterface;
use Quantum\Di\Exceptions\DiException;
use Dflydev\DotAccessData\Data;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class Config
 * @package Quantum\Config
 */
class Config implements ConfigInterface
{
    private static ?Data $configs = null;

    private static ?Config $instance = null;

    /**
     * GetInstance
     */
    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     * @throws DiException|LoaderException|ReflectionException
     */
    public function load(Setup $setup): void
    {
        if (self::$configs !== null) {
            return;
        }

        self::$configs = new Data(Di::get(Loader::class)->setup($setup)->load());
    }

    /**
     * @inheritDoc
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException|LoaderException
     */
    public function import(Setup $setup): void
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
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        if (self::$configs && self::$configs->has($key)) {
            return self::$configs->get($key);
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function all(): ?Data
    {
        return self::$configs;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return self::$configs && !empty(self::$configs->has($key));
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): void
    {
        if (!self::$configs) {
            self::$configs = new Data([$key => $value]);
        } else {
            self::$configs->set($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): void
    {
        self::$configs && self::$configs->remove($key);
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        self::$configs = null;
    }
}
