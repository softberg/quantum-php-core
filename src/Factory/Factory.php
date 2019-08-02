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

/**
 * Factory Class
 *
 * @package Quantum
 * @category Factory
 */
abstract Class Factory
{

    /**
     * Get
     *
     * @param string $class
     * @param string $type
     * @param array $exceptions
     * @return object
     * @throws \Exception
     */
    protected function getInstance($class, $type, $exceptions)
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
