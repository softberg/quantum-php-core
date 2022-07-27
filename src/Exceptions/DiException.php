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
 * Class DiException
 * @package Quantum\Exceptions
 */
class DiException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\DiException
     */
    public static function dependencyNotDefined(string $name): DiException
    {
        return new self(t('dependency_not_found', $name), E_ERROR);
    }
}
