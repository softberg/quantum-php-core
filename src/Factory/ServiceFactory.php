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

namespace Quantum\Factory;

use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Mvc\QtService;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class ServiceFactory
 * @package Quantum\Factory
 */
class ServiceFactory
{

    /**
     * Instantiated services
     * @var array
     */
    private static $instantiated = [];

    /**
     * Creates and initiates the service once
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public static function get(string $serviceClass, array $args = []): QtService
    {
        return self::locate($serviceClass, $args);
    }

    /**
     * Creates and initiates the service
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public static function create(string $serviceClass, array $args = []): QtService
    {
        return self::instantiate($serviceClass, $args);
    }

    public static function reset()
    {
        self::$instantiated = [];
    }

    /**
     * Locates the service
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    private static function locate(string $serviceClass, array $args = []): QtService
    {
        if (isset(self::$instantiated[$serviceClass])) {
            return self::$instantiated[$serviceClass];
        }

        return self::instantiate($serviceClass, $args);
    }

    /**
     * Instantiates the service
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    private static function instantiate(string $serviceClass, array $args = []): QtService
    {
        if (!class_exists($serviceClass)) {
            throw ServiceException::serviceNotFound($serviceClass);
        }

        $service = new $serviceClass();

        if (!$service instanceof QtService) {
            throw ServiceException::notServiceInstance([$serviceClass, QtService::class]);
        }

        if (!in_array($serviceClass, self::$instantiated)) {
            self::$instantiated[$serviceClass] = $service;
        }

        if (method_exists($service, '__init')) {
            call_user_func_array([$service, '__init'], self::getArgs([$service, '__init'], $args));
        }

        return $service;
    }

    /**
     * Gets arguments
     * @param callable $callable
     * @param array $args
     * @return array
     * @throws DiException
     * @throws ReflectionException
     */
    private static function getArgs(callable $callable, array $args): array
    {
        return Di::autowire($callable, $args);
    }
}