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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * DatabaseException class
 * 
 * @package Quantum
 * @category Exceptions
 */
class DatabaseException extends \Exception
{
    /**
     * Incorrect config message
     */
    const INCORRECT_CONFIG = 'The structure of config is not correct';

    /**
     * Config file not found message
     */
    const CONFIG_FILE_NOT_FOUND = 'Config file `{%1}` does not exists';
}
