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

namespace Quantum\Di;

use Quantum\Di\Exceptions\DiException;
use ReflectionParameter;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Quantum\App\App;
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
     * Loads dependency definitions
     */
    public static function registerDependencies()
    {
        foreach (self::coreDependencies() as $dependency) {
            if (!self::isRegistered($dependency)) {
                self::register($dependency);
            }
        }

        foreach (self::userDependencies() as $dependency) {
            if (!self::isRegistered($dependency)) {
                self::register($dependency);
            }
        }
    }

    /**
     * Registers new dependency
     * @param string $dependency
     * @return bool
     */
    public static function register(string $dependency): bool
    {
        if (!in_array($dependency, self::$dependencies) && class_exists($dependency)) {
            self::$dependencies[] = $dependency;
            return true;
        }

        return false;
    }

    /**
     * Checks if a dependency registered
     * @param string $dependency
     * @return bool
     */
    public static function isRegistered(string $dependency): bool
    {
        return in_array($dependency, self::$dependencies);
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
    }

    /**
     * Resolves the dependency
     * @param string $dependency
     * @param array $args
     * @param bool $singleton
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolve(string $dependency, array $args = [], bool $singleton = true)
    {
        if ($singleton) {
            if (!isset(self::$container[$dependency])) {
                self::$container[$dependency] = self::instantiate($dependency, $args);
            }

            return self::$container[$dependency];
        }

        return self::instantiate($dependency, $args);
    }

    /**
     * Instantiates the dependency
     * @param string $dependency
     * @param array $args
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    private static function instantiate(string $dependency, array $args = [])
    {
        $class = new ReflectionClass($dependency);

        $constructor = $class->getConstructor();

        $params = $constructor ? self::resolveParameters($constructor->getParameters(), $args) : [];

        return new $dependency(...$params);
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
     * @param array|null $args
     * @return array|mixed|null
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolveParameter(ReflectionParameter $param, ?array &$args = [])
    {
        $type = $param->getType() ? $param->getType()->getName() : null;

        if ($type && self::instantiable($type)) {
            return self::create($type);
        }

        if ($type === 'array') {
            return $args;
        }

        if (!empty($args)) {
            return array_shift($args);
        }

        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }

    /**
     * Checks if the class is instantiable
     * @param string $type
     * @return bool
     */
    protected static function instantiable(string $type): bool
    {
        return class_exists($type) && (new ReflectionClass($type))->isInstantiable();
    }

    /**
     * Loads user defined dependencies
     * @return array
     */
    private static function userDependencies(): array
    {
        $userDependencies = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'dependencies.php';

        if (!file_exists($userDependencies)) {
            return [];
        }

        return (array)require_once $userDependencies;
    }

    /**
     * Loads the core dependencies
     * @return array
     */
    private static function coreDependencies(): array
    {
        return [
            \Quantum\Loader\Loader::class,
            \Quantum\Http\Request::class,
            \Quantum\Http\Response::class,
            \Quantum\Factory\ViewFactory::class,
        ];
    }
}
