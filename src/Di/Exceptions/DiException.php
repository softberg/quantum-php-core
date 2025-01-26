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
 * @since 2.9.5
 */

namespace Quantum\Di\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class DiException
 * @package Quantum\Exceptions
 */
class DiException extends BaseException
{
    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyNotDefined(string $name): DiException
    {
        return new self(t('exception.dependency_not_found', $name), E_ERROR);
    }
}
