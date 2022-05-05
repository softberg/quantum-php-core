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

use Quantum\Libraries\Database\Type;
use Quantum\Factory\TableFactory;

/**
 * Class MigrationTable
 * @package Quantum\Migration
 */
class MigrationTable extends QtMigration
{

    const TABLE = 'migrations';

    /**
     * 
     */
    public function up(?TableFactory $tableFactory)
    {
        $table = $tableFactory->create(self::TABLE);
        $table->addColumn('id', Type::INT, 11)->autoIncrement();
        $table->addColumn('migration', Type::VARCHAR, 255);
        $table->addColumn('applied_at', Type::TIMESTAMP)->default('CURRENT_TIMESTAMP', false);
    }

    /**
     * 
     */
    public function down(?TableFactory $tableFactory)
    {
        $tableFactory->dropTable(self::TABLE);
    }

}
