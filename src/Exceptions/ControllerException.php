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
 * ControllerException class
 *
 * @package Quantum
 * @category Exceptions
 */
class ControllerException extends \Exception
{
    /**
     * @param string|null $name
     * @return \Quantum\Exceptions\ControllerException
     */
    public static function controllerNotFound(?string $name): ControllerException
    {
        return new static(t('controller_not_found'), E_ERROR);
    }

    /**
     * @param string|null $name
     * @return \Quantum\Exceptions\ControllerException
     */
    public static function controllerNotDefined(?string $name): ControllerException
    {
        return new static(t('controller_not_defined', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ControllerException
     */
    public static function actionNotDefined(string $name): ControllerException
    {
        return new static(t('action_not_defined', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ControllerException
     */
    public static function undefinedMethod(string $name): ControllerException
    {
        return new static(t('undefined_method', $name), E_ERROR);
    }
}
