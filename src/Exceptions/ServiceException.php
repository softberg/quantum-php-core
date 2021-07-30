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
 * @since 2.5.0
 */

namespace Quantum\Exceptions;

/**
 * Class ServiceException
 * @package Quantum\Exceptions
 */
class ServiceException extends \Exception
{
    /**
     * Service not found message
     */
    const SERVICE_NOT_FOUND = 'Service `{%1}` not found';

    /**
     * Model not instance of QtModel
     */
    const NOT_INSTANCE_OF_SERVICE = 'Service `{%1}` is not instance of `{%2}`';

    /**
     * Undefined method
     */
    const UNDEFINED_METHOD = 'The method `{%1}` is not defined';

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function serviceNotFound(string $name): ServiceException
    {
        return new static(_message(self::SERVICE_NOT_FOUND, $name), E_ERROR);
    }

    /**
     * @param array $names
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function notServiceInstance(array $names): ServiceException
    {
        return new static(_message(self::NOT_INSTANCE_OF_SERVICE, $names), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ServiceException
     */
    public static function undefinedMethod(string $name): ServiceException
    {
        return new static(_message(self::UNDEFINED_METHOD, $name), E_ERROR);
    }
}
