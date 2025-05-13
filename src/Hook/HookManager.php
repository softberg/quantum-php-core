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
 * @since 2.9.7
 */

namespace Quantum\Hook;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Hook\Exceptions\HookException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class HookManager
 * @package Quantum\Hooks
 */
class HookManager
{

    /**
     * Core hooks 
     */
    const CORE_HOOKS = [];

    /**
     * Registered hooks store
     * @var array
     */
    private static $store = [];

    /**
     * @var HookManager|null
     */
    private static $instance = null;

    /**
     * @throws HookException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private function __construct()
    {
        if (!config()->has('hooks')) {
            config()->import(new Setup('config', 'hooks'));
        }

        $registeredHooks = array_merge(self::CORE_HOOKS, config()->get('hooks') ?: []);

        foreach ($registeredHooks as $hookName) {
            $this->register($hookName);
        }
    }

    /**
     * HookManager instance
     * @return HookManager
     */
    public static function getInstance(): HookManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Adds new listener for given hook
     * @param string $name
     * @param callable $function
     * @throws HookException
     */
    public function on(string $name, callable $function)
    {
        if (!$this->exists($name)) {
            throw HookException::unregisteredHookName($name);
        }

        self::$store[$name][] = $function;
    }

    /**
     * Fires the hook
     * @param string $name
     * @param array|null $args
     * @throws HookException
     */
    public function fire(string $name, ?array $args = null)
    {
        if (!$this->exists($name)) {
            throw HookException::unregisteredHookName($name);
        }

        foreach (self::$store[$name] as $index => $fn) {
            unset(self::$store[$name][$index]);
            $fn($args);
        }
    }

    /**
     * Gets all registered hooks
     * @return array
     */
    public static function getRegistered(): array
    {
        return self::$store;
    }

    /**
     * Registers new hook 
     * @param string $name
     * @throws HookException
     */
    protected function register(string $name)
    {
        if ($this->exists($name)) {
            throw HookException::hookDuplicateName($name);
        }

        self::$store[$name] = [];
    }

    /**
     * Checks if hooks registered
     * @param string $name
     * @return bool
     */
    protected function exists(string $name): bool
    {
        return key_exists($name, self::$store);
    }
}
