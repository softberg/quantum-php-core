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
 * @since 3.0.0
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
    public const INT = 'int';

    /**
     * Type tiny integer
     */
    public const TINYINT = 'tinyint';

    /**
     * Type float
     */
    public const FLOAT = 'float';

    /**
     * Type double
     */
    public const DOUBLE = 'double';

    /**
     * Type decimal
     */
    public const DECIMAL = 'decimal';

    /**
     * Type boolean
     */
    public const BOOL = 'bool';

    /**
     * Type char
     */
    public const CHAR = 'char';

    /**
     * Type varchar
     */
    public const VARCHAR = 'varchar';

    /**
     * Type text
     */
    public const TEXT = 'text';

    /**
     * Type binary
     */
    public const BINARY = 'binary';

    /**
     * Type blob
     */
    public const BLOB = 'blob';

    /**
     * Type enum
     */
    public const ENUM = 'enum';

    /**
     * Type date
     */
    public const DATE = 'date';

    /**
     * Type datetime
     */
    public const DATETIME = 'datetime';

    /**
     * Type timestamp
     */
    public const TIMESTAMP = 'timestamp';

    /**
     * Type json
     */
    public const JSON = 'json';

    /**
     * Type geometry
     */
    public const GEOMETRY = 'geometry';

}
