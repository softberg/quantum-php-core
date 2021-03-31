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
 * @since 2.2.0
 */

namespace Quantum\Di;

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
    private static $contianer = [];

    /**
     * Loads dependency definitions
     */
    public static function loadDefinitions()
    {
        self::$dependencies = self::coreDependencies();
    }

    /**
     * Create and inject dependencies.
     * @param string|callable $entry
     * @param array $additional
     * @return array
     */
    public static function autowire($entry, array $additional = [])
    {
        if (is_callable($entry)) {
            $reflaction = new ReflectionFunction($entry);
        } else {
            list($controller, $action) = explode(':', $entry);

            $reflaction = new ReflectionMethod($controller, $action);
        }

        $params = [];

        foreach ($reflaction->getParameters() as $param) {
            $type = $param->getType();

            if (!$type || !self::instatiatable($type)) {
                array_push($params, current($additional));
                next($additional);
                continue;
            }

            $dependency = $param->getType();

            array_push($params, self::get($dependency));
        }

        return $params;
    }

    /**
     * Gets the dependency from the container
     * @param string $dependency
     * @return mixed
     * @throws DiException
     */
    public static function get(string $dependency)
    {
        if (!in_array($dependency, self::$dependencies)) {
            throw new DiException(_message(DiException::NOT_FOUND, $dependency));
        }

        if (!isset(self::$contianer[$dependency])) {
            self::instantiate($dependency);
        }

        return self::$contianer[$dependency];
    }

    /**
     * Instantiates the dependency
     * @param string $dependency
     */
    protected static function instantiate(string $dependency)
    {
        $class = new ReflectionClass($dependency);

        $constructor = $class->getConstructor();

        $params = [];

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $type = (string) $param->getType();

                if (!$type || !self::instatiatable($type)) {
                    continue;
                }

                $params[] = self::get($type);
            }
        }

        self::$contianer[$dependency] = new $dependency(...$params);
    }

    /**
     * Checks if the class is instantiable
     * @param mixed $type
     * @return boolean
     */
    protected static function instatiatable($type)
    {
        return $type != 'Closure' && !is_callable($type) && class_exists($type);
    }

    /**
     * Gets the core dependencies 
     * @return array
     */
    private static function coreDependencies()
    {
        return [
            \Quantum\Http\Request::class,
            \Quantum\Http\Response::class,
            \Quantum\Loader\Loader::class,
            \Quantum\Factory\ViewFactory::class,
            \Quantum\Factory\ModelFactory::class,
            \Quantum\Factory\ServiceFactory::class,
            \Quantum\Libraries\Mailer\Mailer::class,
            \Quantum\Libraries\Storage\FileSystem::class,
        ];
    }

}
