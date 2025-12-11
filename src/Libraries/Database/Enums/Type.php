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

namespace Quantum\Libraries\Database\Enums;

/**
 * Class Type
 * @package Quantum\Libraries\Database
 */
class Type
{

    /**
     * Type integer
     */
    const INT = 'int';

    /**
     * Type tiny integer
     */
    const TINYINT = 'tinyint';

    /**
     * Type float
     */
    const FLOAT = 'float';

    /**
     * Type double
     */
    const DOUBLE = 'double';

    /**
     * Type decimal
     */
    const DECIMAL = 'decimal';

    /**
     * Type boolean
     */
    const BOOL = 'bool';

    /**
     * Type char
     */
    const CHAR = 'char';

    /**
     * Type varchar
     */
    const VARCHAR = 'varchar';

    /**
     * Type text
     */
    const TEXT = 'text';

    /**
     * Type binary
     */
    const BINARY = 'binary';

    /**
     * Type blob
     */
    const BLOB = 'blob';

    /**
     * Type enum
     */
    const ENUM = 'enum';

    /**
     * Type date
     */
    const DATE = 'date';

    /**
     * Type datetime
     */
    const DATETIME = 'datetime';

    /**
     * Type timestamp
     */
    const TIMESTAMP = 'timestamp';

    /**
     * Type json
     */
    const JSON = 'json';

    /**
     * Type geometry
     */
    const GEOMETRY = 'geometry';

}
