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
    public static function loadDefinitions()
    {
        if (empty(self::$dependencies)) {
            self::$dependencies = self::coreDependencies();

            foreach (self::userDependencies() as $dependency) {
                self::add($dependency);
            }
        }
    }

    /**
     * Creates and injects dependencies.
     * @param callable $entry
     * @param array $additional
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    public static function autowire(callable $entry, array $additional = []): array
    {
        $reflection = is_closure($entry)
            ? new ReflectionFunction($entry)
            : new ReflectionMethod(...$entry);

        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $params[] = self::resolveParameter($param, $additional);
        }

        return $params;
    }

    /**
     * Adds new dependency
     * @param string $dependency
     */
    public static function add(string $dependency)
    {
        if (!in_array($dependency, self::$dependencies) && class_exists($dependency)) {
            self::$dependencies[] = $dependency;
        }
    }

    /**
     * Gets the dependency from the container
     * @param string $dependency
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(string $dependency)
    {
        if (!in_array($dependency, self::$dependencies)) {
            throw DiException::dependencyNotDefined($dependency);
        }

        if (!isset(self::$container[$dependency])) {
            self::$container[$dependency] = self::instantiate($dependency);
        }

        return self::$container[$dependency];

    }

    /**
     * Instantiates the dependency
     * @param string $dependency
     * @return mixed
     * @throws DiException
     * @throws ReflectionException
     */
    protected static function instantiate(string $dependency)
    {
        $class = new ReflectionClass($dependency);

        $constructor = $class->getConstructor();

        $params = [];

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $params[] = self::resolveParameter($param);
            }
        }

        return new $dependency(...$params);
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
     * @param ReflectionParameter $param
     * @param array|null $additional
     * @return array|mixed|null
     * @throws DiException
     * @throws ReflectionException
     */
    private static function resolveParameter(ReflectionParameter $param, ?array &$additional = [])
    {
        $type = $param->getType() ? $param->getType()->getName() : null;

        if ($type && self::instantiable($type)) {
            return self::get($type);
        }

        if ($type === 'array') {
            return $additional;
        }

        if (count($additional)) {
            return array_shift($additional);
        }

        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;

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
     * Gets the core dependencies
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
