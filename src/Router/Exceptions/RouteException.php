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

namespace Quantum\Router\Exceptions;

use Quantum\Router\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class RouteException
 * @package Quantum\Exceptions
 */
class RouteException extends BaseException
{
    /**
     * @return RouteException
     */
    public static function routeNotFound(): RouteException
    {
        return new static(ExceptionMessages::ROUTE_NOT_FOUND, E_ERROR);
    }

    /**
     * @return RouteException
     */
    public static function notClosure(): RouteException
    {
        return new static(ExceptionMessages::ROUTE_NOT_CLOSURE, E_WARNING);
    }

    /**
     * @param string $name
     * @return RouteException
     */
    public static function repetitiveRouteSameMethod(string $name): RouteException
    {
        return new static(_message(ExceptionMessages::REPETITIVE_ROUTE_WITH_SAME_NAME, [$name]), E_WARNING);
    }

    /**
     * @return RouteException
     */
    public static function repetitiveRouteDifferentModules(): RouteException
    {
        return new static(ExceptionMessages::REPETITIVE_ROUTE_IN_DIFFERENT_MODULES, E_WARNING);
    }

    /**
     * @param string|null $name
     * @return RouteException
     */
    public static function incorrectMethod(?string $name): RouteException
    {
        return new static(_message(ExceptionMessages::INCORRECT_METHOD, [$name]), E_WARNING);
    }

    /**
     * @return RouteException
     */
    public static function nameBeforeDefinition(): RouteException
    {
        return new static(ExceptionMessages::NAME_BEFORE_ROUTE_DEFINITION, E_WARNING);
    }

    /**
     * @return RouteException
     */
    public static function nameOnGroup(): RouteException
    {
        return new static(ExceptionMessages::NAME_ON_ROUTE_GROUP, E_WARNING);
    }

    /**
     * @return RouteException
     */
    public static function nonUniqueName(): RouteException
    {
        return new static(ExceptionMessages::NAME_NOT_UNIQUE, E_WARNING);
    }

    /**
     * @param string $name
     * @return RouteException
     */
    public static function paramNameNotAvailable(string $name): RouteException
    {
        return new static(_message(ExceptionMessages::ROUTE_PARAM_NAME_IN_USE, [$name]), E_WARNING);
    }

    /**
     * @return RouteException
     */
    public static function paramNameNotValid(): RouteException
    {
        return new static(ExceptionMessages::INVALID_ROUTE_PARAM_NAME, E_WARNING);
    }
}