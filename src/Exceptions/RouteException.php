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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class RouteException
 * @package Quantum\Exceptions
 */
class RouteException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function notFound(): RouteException
    {
        return new static(t('exception.route_not_found'), E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function notClosure(): RouteException
    {
        return new static(t('exception.routes_not_closure'), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\RouteException
     */
    public static function repetitiveRouteSameMethod(string $name): RouteException
    {
        return new static(t('exception.repetitive_route_same_method', $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function repetitiveRouteDifferentModules(): RouteException
    {
        return new static(t('exception.repetitive_route_different_modules'), E_WARNING);
    }

    /**
     * @param string|null $name
     * @return \Quantum\Exceptions\RouteException
     */
    public static function incorrectMethod(?string $name): RouteException
    {
        return new static(t('exception.incorrect_method', $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function nameBeforeDefinition(): RouteException
    {
        return new static(t('exception.name_before_route_definition'));
    }

    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function nameOnGroup(): RouteException
    {
        return new static(t('exception.name_on_group'));
    }

    /**
     * @return \Quantum\Exceptions\RouteException
     */
    public static function nonUniqueName(): RouteException
    {
        return new static(t('exception.name_is_not_unique'));
    }

    /**
     * @param string $param
     * @return RouteException
     */
    public static function paramNameNotAvailable(string $name): RouteException
    {
        return new static(t('exception.param_name_not_available', $name), E_WARNING);
    }
    
    /**
     * @param string $param
     * @return RouteException
     */
    public static function paramNameNotValid(): RouteException
    {
        return new static(t('exception.param_name_not_valid'), E_WARNING);
    }

}
