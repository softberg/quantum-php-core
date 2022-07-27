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
 * Class DatabaseException
 * @package Quantum\Exceptions
 */
class DatabaseException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function missingConfig(): DatabaseException
    {
        return new static(t('config_not_provided'), E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function incorrectConfig(): DatabaseException
    {
        return new static(t('incorrect_config'), E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function ormClassNotDefined(): DatabaseException
    {
        return new static(t('orm_class_not_defined'), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function ormClassNotFound(string $name): DatabaseException
    {
        return new static(t('orm_class_not_found', $name), E_ERROR);
    }

    /**
     * @param string $methodName
     * @param string $ormName
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function methodNotSupported(string $methodName, string $ormName): DatabaseException
    {
        return new static(t('not_supported_method', [$methodName, $ormName]), E_WARNING);
    }

    /**
     * @param string $operator
     * @return \Quantum\Exceptions\DatabaseException
     */
    public static function operatorNotSupported(string $operator): DatabaseException
    {
        return new static(t('not_supported_operator', [$operator]), E_WARNING);
    }
}
