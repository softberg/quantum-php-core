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
 * MigrationException class
 *
 * @package Quantum
 * @category Exceptions
 */
class MigrationException extends AppException
{
    /**
     * @return MigrationException
     * @throws LangException
     */
    public static function wrongDirection(): MigrationException
    {
        return new static(t('exception.wrong_migration_direction'), E_ERROR);
    }

    /**
     * @param string $action
     * @return MigrationException
     * @throws LangException
     */
    public static function unsupportedAction(string $action): MigrationException
    {
        return new static(t('exception.non_supported_action', $action), E_ERROR);
    }

    /**
     * @param string $name
     * @return MigrationException
     * @throws LangException
     */
    public static function tableAlreadyExists(string $name): MigrationException
    {
        return new static(t('exception.table_already_exists', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return MigrationException
     * @throws LangException
     */
    public static function tableDoesnotExists(string $name): MigrationException
    {
        return new static(t('exception.table_does_not_exists', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return MigrationException
     * @throws LangException
     */
    public static function columnNotAvailable(string $name): MigrationException
    {
        return new static(t('exception.column_not_available', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return MigrationException
     * @throws LangException
     */
    public static function methodNotDefined(string $name): MigrationException
    {
        return new static(t('exception.method_not_defined', $name), E_ERROR);
    }

    /**
     * @return MigrationException
     * @throws LangException
     */
    public static function nothingToMigrate(): MigrationException
    {
        return new static(t('exception.nothing_to_migrate'), E_NOTICE);
    }

}
