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
 * @since 2.9.8
 */

namespace Quantum\Di\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class DiException
 * @package Quantum\Di
 */
class DiException extends BaseException
{
    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyNotRegistered(string $name): DiException
    {
        return new self(t('exception.dependency_not_registered', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyAlreadyRegistered(string $name): DiException
    {
        return new self(t('exception.dependency_already_registered', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyNotInstantiable(string $name): DiException
    {
        return new self(t('exception.dependency_not_instantiable', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function invalidAbstractDependency(string $name): DiException
    {
        return new self(t('exception.invalid_abstract_dependency', $name), E_ERROR);
    }

    /**
     * @param string $chain
     * @return DiException
     */
    public static function circularDependency(string $chain): DiException
    {
        return new self("Circular dependency detected: $chain", 0);
    }
}
