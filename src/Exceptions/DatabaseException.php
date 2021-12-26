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
    const CONFIG_NOT_PROVIDED = 'Configuration does not provided';

    /**
     * ORM class not defined message
     */
    const ORM_CLASS_NOT_DEFINED = 'Missing ORM definition in config file';

    /**
     * Orm class not found message
     */
    const ORM_CLASS_NOT_FOUND = 'ORM `{%1}` class not found';

    /**
     * Method not supported message
     */
    const NOT_SUPPORTED_METHOD = 'The method `{%1}` is not supported for ORM `{%2}`';

    /**
     * Method not supported message
     */
    const NOT_SUPPORTED_OPERATOR = 'The operator `{%1}` is not supported';


    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function missingConfig(): DatabaseException
    {
        return new static(self::CONFIG_NOT_PROVIDED, E_ERROR);
    }

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

    /**
     * @param string $methodName
     * @param string $ormName
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function methodNotSupported(string $methodName, string $ormName): DatabaseException
    {
        return new static(_message(self::NOT_SUPPORTED_METHOD, [$methodName, $ormName]), E_WARNING);
    }

    /**
     * @param string $operator
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function operatorNotSupported(string $operator): DatabaseException
    {
        return new static(_message(self::NOT_SUPPORTED_OPERATOR, [$operator]), E_WARNING);
    }
}
