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
 * @since 2.7.0
 */

namespace Quantum\Exceptions;

/**
 * MigrationException class
 *
 * @package Quantum
 * @category Exceptions
 */
class MigrationException extends \Exception
{

    const WRONG_MIGRATION_DIRECTION = 'Migration direction can only be [up] or [down]';
    const NON_SUPPORTED_DRIVERER = 'The driver `{%1}`, does not support migrations';
    const NON_SUPPORTED_ACTION = 'The action `{%1}`, is not supported';
    const TABLE_ALREADY_EXISTS = 'The table `{%1}` is already exists';
    const TABLE_DOES_NOT_EXISTS = 'The table `{%1}` does not exists';
    const COLUMN_NOT_AVAILABLE = 'The column `{%1}` is not available';
    const METHOD_NOT_DEFINED = 'The method `{%1}` is not defined';
    const NOTHING_TO_MIGRATE = 'Nothing to migrate';

    /**
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function wrongDirection(): MigrationException
    {
        return new static(self::WRONG_MIGRATION_DIRECTION, E_ERROR);
    }

    /**
     * @param string $databaseDriver
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function unsupportedDriver(string $databaseDriver): MigrationException
    {
        return new static(_message(self::NON_SUPPORTED_DRIVERER, $databaseDriver), E_ERROR);
    }

    /**
     * @param string $action
     * @return MigrationException
     */
    public static function unsupportedAction(string $action): MigrationException
    {
        return new static(_message(self::NON_SUPPORTED_ACTION, $action), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function tableAlreadyExists(string $name): MigrationException
    {
        return new static(_message(self::TABLE_ALREADY_EXISTS, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function tableDoesnotExists(string $name): MigrationException
    {
        return new static(_message(self::TABLE_DOES_NOT_EXISTS, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function columnNotAvailable(string $name): MigrationException
    {
        return new static(_message(self::COLUMN_NOT_AVAILABLE, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function methodNotDefined(string $name): MigrationException
    {
        return new static(_message(self::METHOD_NOT_DEFINED, $name), E_ERROR);
    }

    /**
     * @return \Quantum\Exceptions\MigrationException
     */
    public static function nothingToMigrate(): MigrationException
    {
        return new static(self::NOTHING_TO_MIGRATE, E_NOTICE);
    }

}
