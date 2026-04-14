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
     * Register dependencies
     * @param array<string, mixed> $dependencies
     * @throws DiException
     */
    public static function registerDependencies(array $dependencies): void
    {
        self::getCurrent()->registerDependencies($dependencies);
    }

    /**
     * Registers new dependency
     * @throws DiException
     */
    public static function register(string $concrete, ?string $abstract = null): void
    {
        self::getCurrent()->register($concrete, $abstract);
    }

    /**
     * Checks if a dependency registered
     */
    public static function isRegistered(string $abstract): bool
    {
        return self::getCurrent()->isRegistered($abstract);
    }

    /**
     * Checks if an instance exists in the container
     */
    public static function has(string $abstract): bool
    {
        return self::getCurrent()->has($abstract);
    }

    /**
     * Sets an instance into container
     * @template T of object
     * @param class-string<T> $abstract
     * @param T $instance
     * @throws DiException
     */
    public static function set(string $abstract, object $instance, bool $override = true): void
    {
        self::getCurrent()->set($abstract, $instance, $override);
    }

    /**
     * Retrieves a shared instance of the given dependency.
     * @template T of object
     * @param class-string<T> $dependency
     * @param array<mixed> $args
     * @return T
     * @throws DiException|ReflectionException
     */
    public static function get(string $dependency, array $args = [])
    {
        return self::getCurrent()->get($dependency, $args);
    }

    /**
     * Creates new instance of the given dependency.
     * @template T of object
     * @param class-string<T> $dependency
     * @param array<mixed> $args
     * @return T
     * @throws DiException|ReflectionException
     */
    public static function create(string $dependency, array $args = [])
    {
        return self::getCurrent()->create($dependency, $args);
    }

    /**
     * Autowire callable parameters
     * @param array<mixed> $args
     * @return array<int, mixed>
     * @throws DiException|ReflectionException
     */
    public static function autowire(callable $entry, array $args = []): array
    {
        return self::getCurrent()->autowire($entry, $args);
    }

    /**
     * Resets the current container by replacing it with a fresh instance
     */
    public static function reset(): void
    {
        self::$current = new DiContainer();
    }

    /**
     * Resets only the resolved instances, keeping dependency registrations
     */
    public static function resetContainer(): void
    {
        self::getCurrent()->resetContainer();
    }
}
