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

namespace Quantum\Libraries\Database;

use Quantum\Exceptions\AppException;

/**
 * Class DatabaseException
 * @package Quantum\Libraries\Database
 */
class DatabaseException extends AppException
{
    /**
     * @return DatabaseException
     */
    public static function missingConfig(): DatabaseException
    {
        return new static(t('exception.config_not_provided'), E_ERROR);
    }

    /**
     * @return DatabaseException
     */
    public static function incorrectConfig(): DatabaseException
    {
        return new static(t('exception.incorrect_config'), E_ERROR);
    }

    /**
     * @return DatabaseException
     */
    public static function ormClassNotDefined(): DatabaseException
    {
        return new static(t('exception.orm_class_not_defined'), E_ERROR);
    }

    /**
     * @param string $name
     * @return DatabaseException
     */
    public static function ormClassNotFound(string $name): DatabaseException
    {
        return new static(t('exception.orm_class_not_found', $name), E_ERROR);
    }

    /**
     * @param string $operator
     * @return DatabaseException
     */
    public static function operatorNotSupported(string $operator): DatabaseException
    {
        return new static(t('not_supported_operator', [$operator]), E_WARNING);
    }
}
