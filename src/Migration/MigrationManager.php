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

use Quantum\Exceptions\MigrationException;
use Quantum\Libraries\Database\Database;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Factory\TableFactory;

class MigrationManager
{

    const ALTER_MIGRATION = 'alter';

    const CREATE_MIGRATION = 'create';

    const UPGRADE_MIGRATION = 'up';

    const DOWNGRADE_MIGRATION = 'down';

    /**
     * Migrations queue
     * @var array
     */
    private $migrations = [];

    private $migrationFolder;

    private $fs;

    private $db;

    /**
     * MigrationManager constructor.
     */
    public function __construct()
    {
        $this->fs = new FileSystem();

        $this->db = Database::getInstance();

        $this->migrationFolder = base_dir() . DS . 'migrations';
    }

    public function applyMigrations(string $direction)
    {
        $databaseDriver = $this->db->getConfigs()['driver'];

        if (!in_array($databaseDriver, ['mysql', 'pgsql', 'sqlite'])) {
            throw MigrationException::unsupportedDriver($databaseDriver);
        }

        switch ($direction) {
            case self::UPGRADE_MIGRATION:
                $this->upgrade();
                break;
            case self::DOWNGRADE_MIGRATION:
                $this->downgrade();
                break;
            default:
                throw MigrationException::wrongDirection();
                break;
        }
    }

    /**
     *
     */
    public function upgrade()
    {
        $migrationFiles = $this->getMigrationFiles();

        if (!empty($migrationFiles)) {
            foreach ($migrationFiles as $migrationFile) {
                $this->fs->require($migrationFile);

                $migrationClassName = pathinfo($migrationFile, PATHINFO_FILENAME);

                $migration = new $migrationClassName();

                $migration->up(new TableFactory);
            }
        }

//        dd($migrations);
    }

    public function downgrade()
    {

    }

    public function getMigrationFiles()
    {
        return $this->fs->glob($this->migrationFolder . DS . '*.php');
    }

    public function createMigration(string $table, string $type)
    {
        $migrationName = ucfirst($type) . 'Table' . ucfirst(strtolower($table)) . time();

        $migrationTemplate = MigrationTemplate::{$type}($migrationName, strtolower($table));

        $this->fs->put($this->migrationFolder . DS . $migrationName . '.php', $migrationTemplate);

        return $migrationName;
    }


    public function checkTable(string $table)
    {

    }

    private function getMigarationsTable()
    {

    }

}
