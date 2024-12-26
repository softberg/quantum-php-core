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

namespace Quantum\Debugger;

use Quantum\Contracts\StorageInterface;

/**
 * Class DebuggerStore
 * @package Quantum\Debugger
 */
class DebuggerStore implements StorageInterface
{
    private static $store = [];

    /**
     * @param array $keys
     * @return void
     */
    public function init(array $keys)
    {
        foreach ($keys as $key) {
            if (!isset(self::$store[$key])) {
                self::$store[$key] = [];
            }
        }
    }

    /**
     * @return array[]
     */
    public function all(): array
    {
        return self::$store;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset(self::$store[$key]);
    }

    /**
     * @param string $key
     * @return array
     */
    public function get(string $key): array
    {
        return self::$store[$key] ?? [];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $level => $data) {
                self::$store[$key][] = [$level => $data];
            }
        }
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key)
    {
        if ($this->has($key)) {
            self::$store[$key] = [];
        }
    }

    /**
     * @return void
     */
    public function flush()
    {
        self::$store = [];
    }
}
