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
 * @since 3.0.0
 */

namespace Quantum\Di;

use Quantum\Di\Exceptions\DiException;
use ReflectionException;
use ReflectionParameter;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionClass;

/**
 * Di Class
 * @package Quantum/Di
 */
class Di
{

    /**
     * @var array
     */
    private static $dependencies = [];

    /**
     * @var array
     */
    private static $container = [];

    /**
     * @var array
     */
    private static $resolving = [];

    /**
     * Register dependencies
     */
    public static function registerDependencies(array $dependencies)
    {
        foreach ($dependencies as $abstract => $concrete) {
            if (!self::isRegistered($abstract)) {
                self::register($concrete, $abstract);
            }
        }
    }

    /**
     * Registers new dependency
     * @param string $concrete
     * @param string|null $abstract
     * @throws DiException
     */
    public static function register(string $concrete, ?string $abstract = null)
    {
        $key = $abstract ?? $concrete;

        if (isset(self::$dependencies[$key])) {
            throw DiException::dependencyAlreadyRegistered($key);
        }

        if (!class_exists($concrete)) {
            throw DiException::dependencyNotInstantiable($concrete);
        }

        if ($abstract !== null && !class_exists($abstract) && !interface_exists($abstract)) {
            throw DiException::invalidAbstractDependency($abstract);
        }

        self::$dependencies[$key] = $concrete;
    }

    /**
     * Checks if a dependency registered
     * @param string $abstract
     * @return bool
     */
    public static function isRegistered(string $abstract): bool
    {
        return isset(self::$dependencies[$abstract]);
    }

    /**
     * Retrieves a shared instance of the given dependency.
     * @param string $dependency
     * @param array $args
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(string $dependency, array $args = [])
    {
        if (!self::isRegistered($dependency)) {
            throw DiException::dependencyNotRegistered($dependency);
        }

        return self::resolve($dependency, $args, true);
    }

    /**
     * Creates new instance of the given dependency.
     * @param string $dependency
     * @param array $args
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    public static function create(string $dependency, array $args = [])
    {
        if (!self::isRegistered($dependency)) {
            self::register($dependency);
        }

        return self::resolve($dependency, $args, false);
    }

    /**
     * Automatically resolves and injects parameters for a callable.
     * @param callable $entry
     * @param array $args
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    public static function autowire(callable $entry, array $args = []): array
    {
        $reflection = is_closure($entry)
            ? new ReflectionFunction($entry)
            : new ReflectionMethod(...$entry);

        return self::resolveParameters($reflection->getParameters(), $args);
    }

    /**
     * @return void
     */
    public static function reset(): void
    {
        self::$dependencies = [];
        self::$container = [];
        self::$resolving = [];
    }

    /**
     * Resolves the dependency
     * @param string $abstract
     * @param array $args
     * @param bool $singleton
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolve(string $abstract, array $args = [], bool $singleton = true)
    {
        self::checkCircularDependency($abstract);

        self::$resolving[$abstract] = true;

        try {
            $concrete = self::$dependencies[$abstract];

            if ($singleton) {
                if (!isset(self::$container[$abstract])) {
                    self::$container[$abstract] = self::instantiate($concrete, $args);
                }
                return self::$container[$abstract];
            }

            return self::instantiate($concrete, $args);
        } finally {
            unset(self::$resolving[$abstract]);
        }
    }

    /**
     * Instantiates the dependency
     * @param string $concrete
     * @param array $args
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    private static function instantiate(string $concrete, array $args = [])
    {
        $class = new ReflectionClass($concrete);

        $constructor = $class->getConstructor();

        $params = $constructor ? self::resolveParameters($constructor->getParameters(), $args) : [];

        return new $concrete(...$params);
    }

    /**
     * Resolves all parameters
     * @param array $parameters
     * @param array $args
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolveParameters(array $parameters, array &$args = []): array
    {
        $resolved = [];

        foreach ($parameters as $param) {
            $resolved[] = self::resolveParameter($param, $args);
        }

        return $resolved;
    }

    /**
     * Resolves the parameter
     * @param ReflectionParameter $param
     * @param array $args
     * @return array|mixed|null
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolveParameter(ReflectionParameter $param, array &$args = [])
    {
        $type = null;

        if ($param->getType() instanceof \ReflectionNamedType) {
            $type = $param->getType()->getName();
        }

        $concrete = self::$dependencies[$type] ?? $type;

        if ($concrete && self::instantiable($concrete)) {
            return self::create($concrete);
        }

        if ($type === 'array') {
            return $args;
        }

        if ($args !== null && $args !== []) {
            return array_shift($args);
        }

        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }

    /**
     * Checks if the class is instantiable
     * @param string $class
     * @return bool
     */
    protected static function instantiable(string $class): bool
    {
        return class_exists($class) && (new ReflectionClass($class))->isInstantiable();
    }

    /**
     * @param string $abstract
     * @return void
     * @throws DiException
     */
    private static function checkCircularDependency(string $abstract): void
    {
        if (isset(self::$resolving[$abstract])) {
            $chain = implode(' -> ', array_keys(self::$resolving)) . ' -> ' . $abstract;
            throw DiException::circularDependency($chain);
        }
    }
}
