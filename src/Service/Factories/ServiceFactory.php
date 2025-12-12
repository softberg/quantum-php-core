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
 * @since 2.9.9
 */

namespace Quantum\Service\Factories;

use Quantum\Service\Exceptions\ServiceException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Service\QtService;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class ServiceFactory
 * @package Quantum\Service
 */
class ServiceFactory
{

    /**
     * Creates and initiates the service once
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    public static function get(string $serviceClass, array $args = []): QtService
    {
        self::validate($serviceClass);

        if (!Di::isRegistered($serviceClass)) {
            Di::register($serviceClass);
        }

        return Di::get($serviceClass, $args);
    }

    /**
     * Creates new service instance
     * @param string $serviceClass
     * @param array $args
     * @return QtService
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws ServiceException
     */
    public static function create(string $serviceClass, array $args = []): QtService
    {
        self::validate($serviceClass);

        return Di::create($serviceClass, $args);
    }

    /**
     * Validates the service class
     * @param string $serviceClass
     * @return void
     * @throws ServiceException
     * @throws BaseException
     */
    private static function validate(string $serviceClass): void
    {
        if (!class_exists($serviceClass)) {
            throw ServiceException::notFound('Service', $serviceClass);
        }

        if (!is_subclass_of($serviceClass, QtService::class)) {
            throw ServiceException::notInstanceOf($serviceClass, QtService::class);
        }
    }
}