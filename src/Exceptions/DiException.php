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
 * @since 2.6.0
 */

namespace Quantum\Exceptions;

/**
 * Class DiException
 * @package Quantum\Exceptions
 */
class DiException extends \Exception
{

    /**
     * Dependency not defined message
     */
    const NOT_FOUND = 'Dependency `{%1}` not defined';

    /**
     * @param string $name
     * @return \Quantum\Exceptions\DiException
     */
    public static function dependencyNotDefined(string $name): DiException
    {
        return new self(_message(self::NOT_FOUND, $name), E_ERROR);
    }
}
