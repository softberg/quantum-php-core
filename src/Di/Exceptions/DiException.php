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

namespace Quantum\Di\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class DiException
 * @package Quantum\Di
 */
class DiException extends BaseException
{
    /**
     * @param string $name
     * @return DiException
     */
    public static function dependencyNotRegistered(string $name): DiException
    {
        return new self(t('exception.dependency_not_registered', $name), E_ERROR);
    }
}
