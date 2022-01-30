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
    
    const NON_SUPPORTED_DRIVERE = 'The driver {%1}, does not support migrations';

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
        return new static(_message(self::NON_SUPPORTED_DRIVERE, $databaseDriver), E_ERROR);
    }

}
