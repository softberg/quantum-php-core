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
 * Class ModuleLoaderException
 * @package Quantum\Exceptions
 */
class ModuleLoaderException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\ModuleLoaderException
     */
    public static function notFound(string $name): ModuleLoaderException
    {
        return new static(t('module_not_found', $name), E_ERROR);
    }
}
