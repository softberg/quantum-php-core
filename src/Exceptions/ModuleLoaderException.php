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
 * @since 1.9.5
 */

namespace Quantum\Exceptions;

/**
 * ModuleLoaderException class
 * 
 * @package Quantum
 * @category Exceptions
 */
class ModuleLoaderException extends \Exception
{
    /**
     * Module not found message
     */
    const MODULE_NOT_FOUND = 'Module `{%1}` not found';
}
