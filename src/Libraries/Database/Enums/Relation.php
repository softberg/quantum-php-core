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
 * Class Relation
 * @package Quantum\Libraries\Database
 */
class Relation
{
    public const HAS_ONE = 'hasOne';
    public const HAS_MANY = 'hasMany';
    public const BELONGS_TO = 'belongsTo';
    public const BELONGS_TO_MANY = 'belongsToMany';
}
