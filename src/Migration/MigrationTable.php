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
 * @since 2.1.0
 */

namespace Quantum\Migration;

/**
 * Class MigrationTable
 * @package Quantum\Migration
 */
class MigrationTable extends QtMigration
{

    private $migrationTable = 'migrations';


    /**
     * 
     */
    public function up(Schema $schema)
    {
        $schema->createTable($this->migrationTable, [
           'id' => ['type' => 'int', 'length' => 11, 'autoincrement' => true], 
           'migration' => ['type' => 'varchar', 'length' => 255],
           'applied_at' => ['type' => 'date', 'default' => time()]
        ]);
    }

    /**
     * 
     */
    public function down(Schema $schema)
    {
        $schema->dropTable($this->migrationTable);
    }

}
