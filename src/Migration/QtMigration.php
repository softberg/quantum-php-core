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

namespace Quantum\Migration;

use Quantum\Factory\TableFactory;

/**
 * Class QtMigration
 * @package Quantum\Migration
 */
abstract class QtMigration
{

    /**
     * Upgrades with the specified migration class
     * @param TableFactory|null $tableFactory
     */
    abstract public function up(?TableFactory $tableFactory);

    /**
     * Downgrades with the specified migration class
     * @param TableFactory|null $tableFactory
     */
    abstract public function down(?TableFactory $tableFactory);
}
