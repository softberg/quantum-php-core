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
 * Class MiddlewareException
 * @package Quantum\Exceptions
 */
class MiddlewareException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\MiddlewareException
     */
    public static function notDefined(string $name): MiddlewareException
    {
        return new static(t('middleware_not_defined', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\MiddlewareException
     */
    public static function middlewareNotFound(string $name): MiddlewareException
    {
        return new static(t('middleware_not_found', $name), E_WARNING);
    }

}
