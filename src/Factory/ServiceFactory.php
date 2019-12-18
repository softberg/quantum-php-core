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
     * Creates a service by given class
     *
     * @param string $serviceClass
     * @return $this
     * @throws \Exception
     */
    public function get($serviceClass, $proxy = false)
    {
        if (!class_exists($serviceClass)) {
            throw new \Exception(_message(ExceptionMessages::SERVICE_NOT_FOUND, $serviceClass));
        }
        $service = new $serviceClass();
        if (!$service instanceof Qt_Service) {
            throw new \Exception(_message(ExceptionMessages::NOT_INSTANCE_OF_SERVICE, [$serviceClass, Qt_Service::class]));
        }

        $this->service = $service;

        if (!isset(self::$initialized[$serviceClass]) || (isset(self::$initialized[$serviceClass]) && !self::$initialized[$serviceClass])) {
            if (method_exists($this->service, '__init')) {
                $this->service->__init();
                self::$initialized[$serviceClass] = true;
            }
        }

        if ($proxy) {
            return $this;
        }

        return $this->service;
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

            $args = [];

            $reflaction = new \ReflectionMethod($this->service, $methodName);
            $params = $reflaction->getParameters();

            foreach ($params as $param) {
                $paramType = $param->getType();

                if ($paramType && $paramType == 'Quantum\Factory\ModelFactory') {
                    array_push($args, new ModelFactory());
                } else {
                    array_push($args, current($arguments));
                    next($arguments);
                }
            }

            return call_user_func([$this->service, $methodName], ...$args);
        } else {
            throw new \BadMethodCallException(_message(ExceptionMessages::UNDEFINED_METHOD, $methodName));
        }
    }

}
