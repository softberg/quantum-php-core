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

namespace Quantum\Di;

use Quantum\Di\Exceptions\DiException;
use ReflectionException;

/**
 * Di Class
 *
 * Static facade that delegates all calls to the current DiContainer instance.
 * Preserves the existing static API for full backward compatibility.
 *
 * @package Quantum/Di
 * @method static void registerDependencies(array<string, mixed> $dependencies)
 * @method static void register(string $concrete, ?string $abstract = null)
 * @method static bool isRegistered(string $abstract)
 * @method static bool has(string $abstract)
 * @method static void set(string $abstract, object $instance, bool $override = true)
 * @method static mixed get(string $dependency, array<mixed> $args = [])
 * @method static mixed create(string $dependency, array<mixed> $args = [])
 * @method static array<int, mixed> autowire(callable $entry, array<mixed> $args = [])
 * @method static void resetContainer()
 */
class Di
{
    /**
     * @var DiContainer|null
     */
    private static ?DiContainer $current = null;

    /**
     * Sets the current container instance
     */
    public static function setCurrent(DiContainer $container): void
    {
        self::$current = $container;
    }

    /**
     * Gets the current container instance, lazily creating one if needed
     */
    public static function getCurrent(): DiContainer
    {
        if (self::$current === null) {
            self::$current = new DiContainer();
        }

        return self::$current;
    }

    /**
     * Resets the current container by replacing it with a fresh instance
     */
    public static function reset(): void
    {
        self::$current = new DiContainer();
    }

    /**
     * @param array<mixed> $arguments
     * @return mixed
     * @throws DiException|ReflectionException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        if (!method_exists(self::getCurrent(), $method)) {
            throw DiException::invalidCallable($method);
        }

        return self::getCurrent()->$method(...$arguments);
    }
}
