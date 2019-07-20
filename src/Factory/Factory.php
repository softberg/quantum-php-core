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
 * @since 1.5.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Mvc\Qt_Service;
use Quantum\Mvc\Qt_Model;

/**
 * Factory Class
 *
 * @package Quantum
 * @category Factory
 */
Class Factory
{

    /**
     * Get Model
     *
     * @param string $modelClass
     * @return object
     * @throws \Exception
     */
    public function getModel($modelClass)
    {
        $exceptions = [
            ExceptionMessages::MODEL_NOT_FOUND,
            ExceptionMessages::NOT_INSTANCE_OF_MODEL
        ];

        return $this->get($modelClass, Qt_Model::class, $exceptions);
    }

    /**
     * Get Service
     *
     * @param string $serviceClass
     * @return object
     * @throws \Exception
     */
    public function getService($serviceClass)
    {
        $exceptions = [
            ExceptionMessages::SERVICE_NOT_FOUND,
            ExceptionMessages::NOT_INSTANCE_OF_SERVICE
        ];

        return $this->get($serviceClass, Qt_Service::class, $exceptions);
    }

    /**
     * Get
     *
     * @param string $class
     * @param string $type
     * @param array $exceptions
     * @return object
     * @throws \Exception
     */
    private function get($class, $type, $exceptions)
    {
        if (!class_exists($class)) {
            throw new \Exception(_message($exceptions[0], $class));
        }

        $object = new $class();

        if (!$object instanceof $type) {
            throw new \Exception(_message($exceptions[1], [$class, $type]));
        }

        return $object;
    }

}
