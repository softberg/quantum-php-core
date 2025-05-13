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
 * @since 2.9.7
 */

namespace Quantum\Router\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * ControllerException class
 * @package Quantum\Router
 */
class RouteControllerException extends BaseException
{
    /**
     * @param string|null $name
     * @return RouteControllerException
     */
    public static function controllerNotFound(?string $name): RouteControllerException
    {
        return new static(t('exception.controller_not_found', $name), E_ERROR);
    }

    /**
     * @param string|null $name
     * @return RouteControllerException
     */
    public static function controllerNotDefined(?string $name): RouteControllerException
    {
        return new static(t('exception.controller_not_defined', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return RouteControllerException
     */
    public static function actionNotDefined(string $name): RouteControllerException
    {
        return new static(t('exception.action_not_defined', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return RouteControllerException
     */
    public static function undefinedMethod(string $name): RouteControllerException
    {
        return new static(t('exception.undefined_method', $name), E_ERROR);
    }
}