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
 * @since 2.0.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\ServiceException;
use Quantum\Factory\ModelFactory;
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
     * Creates and initialize the service once
     * @param string $serviceClass
     * @return QtService
     * @throws ServiceException
     */
    public function get($serviceClass): QtService
    {
        return $this->locate($serviceClass);
    }

    /**
     * Creates and initialize the service
     * @param string $serviceClass
     * @return QtService
     */
    public function create($serviceClass): QtService
    {
        return $this->instantiate($serviceClass);
    }

    /**
     * Locates the service
     * @param string $serviceClass
     * @return QtService
     */
    private function locate($serviceClass): QtService
    {
        if (isset($this->instantiated[$serviceClass])) {
            return $this->instantiated[$serviceClass];
        }

        return $this->instantiate($serviceClass);
    }

    /**
     * Instantiates the service
     * @param string $serviceClass
     * @return QtService
     * @throws ServiceException
     */
    private function instantiate($serviceClass): QtService
    {
        if (!class_exists($serviceClass)) {
            throw new ServiceException(_message(ExceptionMessages::SERVICE_NOT_FOUND, $serviceClass));
        }
        
        $service = new $serviceClass();

        if (!$service instanceof QtService) {
            throw new ServiceException(_message(ExceptionMessages::NOT_INSTANCE_OF_SERVICE, [$serviceClass, QtService::class]));
        }

        $this->instantiated[$serviceClass] = $service;

        if (method_exists($service, '__init')) {
            $service->__init(...$this->getArgs($service, '__init'));
        }

        return $service;
    }

    /**
     * Gets arguments
     * @param string $methodName
     * @param array $arguments
     * @return array
     */
    private function getArgs(QtService $service, $methodName, $arguments = [])
    {
        $args = [];

        $reflaction = new \ReflectionMethod($service, $methodName);
        $params = $reflaction->getParameters();

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
