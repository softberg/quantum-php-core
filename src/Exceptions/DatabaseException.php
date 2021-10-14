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
 * Class DatabaseException
 * @package Quantum\Exceptions
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

    /**
     * ORM class not defined message
     */
    const ORM_CLASS_NOT_DEFINED = 'Missing ORM defination in config file';

    /**
     * Orm class not found message
     */
    const ORM_CLASS_NOT_FOUND = 'ORM `{%1}` class not found';

    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function incorrectConfig(): DatabaseException
    {
        return new static(self::INCORRECT_CONFIG, E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function ormClassNotDefined(): DatabaseException
    {
        return new static(self::ORM_CLASS_NOT_DEFINED, E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function ormClassNotFound(string $name): DatabaseException
    {
        return new static(_message(self::ORM_CLASS_NOT_FOUND, $name), E_ERROR);
    }
}
