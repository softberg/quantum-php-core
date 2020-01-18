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
 * @since 1.6.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\ServiceException;
use Quantum\Factory\ModelFactory;
use Quantum\Helpers\Helper;
use Quantum\Mvc\Qt_Service;

/**
 * Class ServiceFactory
 * @package Quantum\Factory
 */
class ServiceFactory
{

    private static $initialized = [];

    /**
     * @var object
     */
    private $service;

    /**
     * Get
     *
     * Creates and initialize the service once
     *
     * @param string $serviceClass
     * @return $this
     * @throws \Exception
     */
    public function get($serviceClass): Qt_Service
    {
        $this->service = $this->getInstance($serviceClass);

        if (method_exists($this->service, '__init')) {
            $this->initOnce($serviceClass);
        }

        return $this->service;
    }

    /**
     * Create
     * 
     * Creates and initialize the service
     * 
     * @param string $serviceClass
     * @return Qt_Service
     */
    public function create($serviceClass): Qt_Service
    {
        $this->service = $this->getInstance($serviceClass);

        if (method_exists($this->service, '__init')) {
            $this->service->__init(...$this->getArgs('__init'));
        }

        return $this->service;
    }

    /**
     * Proxy
     * 
     * Creates and initialize the service once,
     * directs the method calls in chain to service 
     * 
     * @param string $serviceClass
     * @return \self
     */
    public function proxy($serviceClass): self
    {
        $this->service = $this->getInstance($serviceClass);

        if (method_exists($this->service, '__init')) {
            $this->initOnce($serviceClass);
        }

        return $this;
    }

    /**
     * __call magic
     *
     * Allows to call service methods
     *
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments)
    {
        if (is_callable([$this->service, $methodName])) {
            return call_user_func([$this->service, $methodName], ...$this->getArgs($methodName, $arguments));
        } else {
            throw new \BadMethodCallException(_message(ExceptionMessages::UNDEFINED_METHOD, $methodName));
        }
    }

    /**
     * Init Once
     * 
     * Initialize the service once
     * 
     * @param string $serviceClass
     */
    private function initOnce($serviceClass)
    {
        if (!isset(self::$initialized[$serviceClass]) || (isset(self::$initialized[$serviceClass]) && !self::$initialized[$serviceClass])) {
            $this->service->__init(...$this->getArgs('__init'));
            self::$initialized[$serviceClass] = true;
        }
    }

    /**
     * Get Args
     * 
     * Gets arguments
     * 
     * @param string $methodName
     * @param array $arguments
     * @return array
     */
    private function getArgs($methodName, $arguments = [])
    {
        $args = [];

        $reflaction = new \ReflectionMethod($this->service, $methodName);
        $params = $reflaction->getParameters();

        foreach ($params as $param) {
            $paramType = $param->getType();

            if ($paramType && $paramType == ModelFactory::class) {
                array_push($args, new ModelFactory());
            } else {
                array_push($args, current($arguments));
                next($arguments);
            }
        }

        return $args;
    }

    /**
     * Get Instance
     * 
     * Gets service instance 
     * 
     * @param string $serviceClass
     * @return Qt_Service
     * @throws ServiceException
     */
    private function getInstance($serviceClass)
    {
        if (!class_exists($serviceClass)) {
            throw new ServiceException(Helper::_message(ExceptionMessages::SERVICE_NOT_FOUND, $serviceClass));
        }
        $service = new $serviceClass();

        if (!$service instanceof Qt_Service) {
            throw new ServiceException(Helper::_message(ExceptionMessages::NOT_INSTANCE_OF_SERVICE, [$serviceClass, Qt_Service::class]));
        }

        return $service;
    }

}
