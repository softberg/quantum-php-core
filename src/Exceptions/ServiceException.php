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
 * Class ServiceException
 * @package Quantum\Exceptions
 */
class ServiceException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function serviceNotFound(string $name): ServiceException
    {
        return new static(t('service_not_found', $name), E_ERROR);
    }

    /**
     * @param array $names
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function notServiceInstance(array $names): ServiceException
    {
        return new static(t('not_instance_of_service', $names), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function undefinedMethod(string $name): ServiceException
    {
        return new static(t('undefined_method', $name), E_ERROR);
    }
}
