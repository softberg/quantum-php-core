<?php

declare(strict_types=1);

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

namespace Quantum\Di\Exceptions;

use Quantum\Di\Enums\ExceptionMessages;
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
        return new self(_message(ExceptionMessages::DEPENDENCY_NOT_REGISTERED, [$name]), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyAlreadyRegistered(string $name): DiException
    {
        return new self(_message(ExceptionMessages::DEPENDENCY_ALREADY_REGISTERED, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyNotInstantiable(string $name): DiException
    {
        return new self(_message(ExceptionMessages::DEPENDENCY_NOT_INSTANTIABLE, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DiException
     */
    public static function invalidAbstractDependency(string $name): DiException
    {
        return new self(_message(ExceptionMessages::INVALID_ABSTRACT_DEPENDENCY, $name), E_ERROR);
    }

    /**
     * @param string $chain
     * @return DiException
     */
    public static function circularDependency(string $chain): DiException
    {
        return new self(_message(ExceptionMessages::CIRCULAR_DEPENDENCY, [$chain]), 0);
    }
}
