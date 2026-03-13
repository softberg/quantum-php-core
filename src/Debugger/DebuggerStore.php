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

namespace Quantum\Debugger;

use Quantum\Contracts\StorageInterface;

/**
 * Class DebuggerStore
 * @package Quantum\Debugger
 */
class DebuggerStore implements StorageInterface
{
    private static array $store = [];

    public function init(array $keys): void
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

    public function has(string $key): bool
    {
        return isset(self::$store[$key]);
    }

    public function get(string $key): array
    {
        return self::$store[$key] ?? [];
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        if (is_array($value)) {
            foreach ($value as $level => $data) {
                self::$store[$key][] = [$level => $data];
            }
        }
    }

    public function delete(string $key): void
    {
        if ($this->has($key)) {
            self::$store[$key] = [];
        }
    }

    public function flush(): void
    {
        self::$store = [];
    }
}
