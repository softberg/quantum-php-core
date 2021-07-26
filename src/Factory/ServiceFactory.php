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
 * @since 2.5.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ServiceException;
use Quantum\Mvc\QtService;
use Quantum\Loader\Loader;
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
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    public function get(string $serviceClass): QtService
    {
        return $this->locate($serviceClass);
    }

    /**
     * Creates and initiates the service
     * @param string $serviceClass
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    public function create(string $serviceClass): QtService
    {
        return $this->instantiate($serviceClass);
    }

    /**
     * Locates the service
     * @param string $serviceClass
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    private function locate(string $serviceClass): QtService
    {
        if (isset($this->instantiated[$serviceClass])) {
            return $this->instantiated[$serviceClass];
        }

        return $this->instantiate($serviceClass);
    }

    /**
     * Instantiates the service
     * @param string $serviceClass
     * @return \Quantum\Mvc\QtService
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ServiceException
     * @throws \ReflectionException
     */
    private function instantiate(string $serviceClass): QtService
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
            $service->__init(...$this->getArgs($service));
        }

        return $service;
    }

    /**
     * Gets arguments
     * @param \Quantum\Mvc\QtService $service
     * @return array
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private function getArgs(QtService $service): array
    {
        $arguments = [];
        $args = [];

        $reflection = new \ReflectionMethod($service, '__init');
        $params = $reflection->getParameters();

        foreach ($params as $param) {
            $paramType = $param->getType();

            if ($paramType) {
                switch ($paramType) {
                    case ModelFactory::class:
                        array_push($args, Di::get(ModelFactory::class));
                        break;
                    case Loader::class:
                        array_push($args, Di::get(Loader::class));
                        break;
                    default :
                        array_push($args, current($arguments));
                        next($arguments);
                }
            }
        }

        return $args;
    }
}
