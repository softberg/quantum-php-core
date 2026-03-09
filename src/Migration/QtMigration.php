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

namespace Quantum\Migration;

use Quantum\Database\Factories\TableFactory;

/**
 * Class QtMigration
 * @package Quantum\Migration
 */
abstract class QtMigration
{
    /**
     * Upgrades with the specified migration class
     */
    abstract public function up(?TableFactory $tableFactory);

    /**
     * Downgrades with the specified migration class
     */
    abstract public function down(?TableFactory $tableFactory);
}
