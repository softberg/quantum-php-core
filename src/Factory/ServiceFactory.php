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
 * @since 2.6.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ServiceException;
use Quantum\Mvc\QtService;
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
    private $instantiated = [];

    /**
     * Creates and initiates the service once
     * @param string $serviceClass
     * @param array $args
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    public function get(string $serviceClass, array $args = []): QtService
    {
        return $this->locate($serviceClass, $args);
    }

    /**
     * Creates and initiates the service
     * @param string $serviceClass
     * @param array $args
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    public function create(string $serviceClass, array $args = []): QtService
    {
        return $this->instantiate($serviceClass, $args);
    }

    /**
     * Locates the service
     * @param string $serviceClass
     * @param array $args
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    private function locate(string $serviceClass, array $args = []): QtService
    {
        if (isset($this->instantiated[$serviceClass])) {
            return $this->instantiated[$serviceClass];
        }

        return $this->instantiate($serviceClass, $args);
    }

    /**
     * Instantiates the service
     * @param string $serviceClass
     * @param array $args
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    private function instantiate(string $serviceClass, array $args = []): QtService
    {
        if (!class_exists($serviceClass)) {
            throw ServiceException::serviceNotFound($serviceClass);
        }

        $service = new $serviceClass();

        if (!$service instanceof QtService) {
            throw ServiceException::notServiceInstance([$serviceClass, QtService::class]);
        }

        $this->instantiated[$serviceClass] = $service;

        if (method_exists($service, '__init')) {
            call_user_func_array([$service, '__init'], $this->getArgs([$service, '__init'], $args));
        }

        return $service;

    }

    /**
     * Gets arguments
     * @param callable $callable
     * @param array $args
     * @return array
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private function getArgs(callable $callable, array $args): array
    {
        return Di::autowire($callable, $args);
    }
}
