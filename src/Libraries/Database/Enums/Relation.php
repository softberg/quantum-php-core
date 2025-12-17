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
 * Class Relation
 * @package Quantum\Libraries\Database
 */
class Relation {
    const HAS_ONE = 'hasOne';
    const HAS_MANY = 'hasMany';
    const BELONGS_TO = 'belongsTo';
    const BELONGS_TO_MANY = 'belongsToMany';
}