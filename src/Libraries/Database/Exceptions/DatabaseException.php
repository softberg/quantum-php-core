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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Database\Exceptions;

use Quantum\Libraries\Database\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class DatabaseException
 * @package Quantum\Libraries\Database
 */
class DatabaseException extends BaseException
{

    /**
     * @return DatabaseException
     */
    public static function incorrectConfig(): DatabaseException
    {
        return new static(ExceptionMessages::INCORRECT_CONFIG, E_ERROR);
    }

    /**
     * @param string $operator
     * @return DatabaseException
     */
    public static function operatorNotSupported(string $operator): DatabaseException
    {
        return new static(_message(ExceptionMessages::NOT_SUPPORTED_OPERATOR, [$operator]), E_WARNING);
    }

    /**
     * @param string $name
     * @return DatabaseException
     */
    public static function tableAlreadyExists(string $name): DatabaseException
    {
        return new static(_message(ExceptionMessages::TABLE_ALREADY_EXISTS, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return DatabaseException
     */
    public static function tableDoesNotExists(string $name): DatabaseException
    {
        return new static(_message(ExceptionMessages::TABLE_NOT_EXISTS, $name), E_ERROR);
    }
}