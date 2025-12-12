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

namespace Quantum\Migration;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Factories\TableFactory;
use Quantum\Libraries\Database\Enums\Type;

/**
 * Class MigrationTable
 * @package Quantum\Migration
 */
class MigrationTable extends QtMigration
{

    /**
     * Migrations table name
     */
    const TABLE = 'migrations';

    /**
     * Creates the migrations table
     * @param TableFactory|null $tableFactory
     * @return void
     * @throws DatabaseException
     */
    public function up(?TableFactory $tableFactory)
    {
        $table = $tableFactory->create(self::TABLE);
        $table->addColumn('id', Type::INT, 11)->autoIncrement();
        $table->addColumn('migration', Type::VARCHAR, 255);
        $table->addColumn('applied_at', Type::TIMESTAMP)->default('CURRENT_TIMESTAMP', false);
    }

    /**
     * Drops the migrations table
     * @param TableFactory|null $tableFactory
     * @return void
     * @throws DatabaseException
     */
    public function down(?TableFactory $tableFactory)
    {
        $tableFactory->drop(self::TABLE);
    }
}