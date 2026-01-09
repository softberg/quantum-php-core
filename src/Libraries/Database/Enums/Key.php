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
 * Class Key
 * @package Quantum\Libraries\Database
 */
class Key
{
    /**
     * Primary key definition
     */
    public const PRIMARY = 'primary';

    /**
     * Index key definition
     */
    public const INDEX = 'index';

    /**
     * Unique key definition
     */
    public const UNIQUE = 'unique';

    /**
     * Full-text key definition
     */
    public const FULLTEXT = 'fulltext';

    /**
     * Spatial key definition
     */
    public const SPATIAL = 'spatial';

}
