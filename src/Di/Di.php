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
 * @since 2.8.0
 */

namespace Quantum\Di;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\DiException;
use ReflectionFunction;
use ReflectionClass;
use ReflectionMethod;

/**
 * Di Class
 *
 * @package Quantum
 * @category Di
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
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function autowire(callable $entry, array $additional = []): array
    {
        if (is_closure($entry)) {
            $reflection = new ReflectionFunction($entry);
        } else {
            $reflection = new ReflectionMethod(...$entry);
        }

        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();

            if ($type) {
                if (self::instantiable($type->getName())) {
                    array_push($params, self::get($type->getName()));
                } else if ($type->getName() == 'array') {
                    array_push($params, $additional);
                } else {
                    array_push($params, current($additional));
                    next($additional);
                }
            } else {
                array_push($params, current($additional));
                next($additional);
            }
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
            array_push(self::$dependencies, $dependency);
        }
    }

    /**
     * Gets the dependency from the container
     * @param string $dependency
     * @return mixed
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public static function get(string $dependency)
    {
        if (!in_array($dependency, self::$dependencies)) {
            throw DiException::dependencyNotDefined($dependency);
        }

        if (!isset(self::$container[$dependency])) {
            self::instantiate($dependency);
        }

        return self::$container[$dependency];
    }

    /**
     * Instantiates the dependency
     * @param string $dependency
     * @throws \Quantum\Exceptions\DiException|\ReflectionException
     */
    protected static function instantiate(string $dependency)
    {
        $class = new ReflectionClass($dependency);

        $constructor = $class->getConstructor();

        $params = [];

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $type = $param->getType()->getName();

                if (!$type || !self::instantiable($type)) {
                    continue;
                }

                $params[] = self::get($type);
            }
        }

        self::$container[$dependency] = new $dependency(...$params);
    }

    /**
     * Checks if the class is instantiable
     * @param string $type
     * @return bool
     */
    protected static function instantiable(string $type): bool
    {
        if (class_exists($type)) {
            $reflectionClass = new ReflectionClass($type);

            return $reflectionClass->isInstantiable();
        }

        return false;
    }

    /**
     * Loads user defined dependencies
     * @return array
     */
    private static function userDependencies(): array
    {
        $fs = new FileSystem();

        $userDependencies = base_dir() . DS . 'shared' . DS . 'config' . DS . 'dependencies.php';

        if (!$fs->exists($userDependencies)) {
            return [];
        }

        return (array) require_once $userDependencies;
    }

    /**
     * Gets the core dependencies
     * @return array
     */
    private static function coreDependencies(): array
    {
        return [
            \Quantum\Http\Request::class,
            \Quantum\Http\Response::class,
            \Quantum\Loader\Loader::class,
            \Quantum\Factory\ViewFactory::class,
            \Quantum\Libraries\Mailer\Mailer::class,
            \Quantum\Libraries\Storage\FileSystem::class,
        ];
    }

}
