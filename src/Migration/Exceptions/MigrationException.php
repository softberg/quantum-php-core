<?php

declare(strict_types=1);

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

namespace Quantum\Migration\Exceptions;

use Quantum\Migration\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * MigrationException class
 *
 * @package Quantum
 * @category Exceptions
 */
class MigrationException extends BaseException
{

    /**
     * @return MigrationException
     */
    public static function wrongDirection(): MigrationException
    {
        return new static(ExceptionMessages::WRONG_MIGRATION_DIRECTION, E_ERROR);
    }

    /**
     * @param string $action
     * @return MigrationException
     */
    public static function unsupportedAction(string $action): MigrationException
    {
        return new static(_message(ExceptionMessages::NOT_SUPPORTED_ACTION, [$action]), E_ERROR);
    }

    /**
     * @return MigrationException
     */
    public static function nothingToMigrate(): MigrationException
    {
        return new static(ExceptionMessages::NOTHING_TO_MIGRATE, E_NOTICE);
    }
}