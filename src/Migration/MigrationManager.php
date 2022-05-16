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

    const UPGRADE = 'up';
    const DOWNGRADE = 'down';

    /**
     * Migrations queue
     * @var array
     */
    private $actions = ['create', 'alter', 'rename', 'drop'];
    private $drivers = ['mysql', 'pgsql', 'sqlite'];
    private $migrations = [];
    private $tableFactory;
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

        $this->tableFactory = new TableFactory();

        $this->migrationFolder = base_dir() . DS . 'migrations';
    }

    public function generateMigration(string $table, string $action)
    {
        if (!in_array($action, $this->actions)) {
            throw MigrationException::unsupportedAction($action);
        }

        $migrationName = $action . '_table_' . strtolower($table) . '_' . time();

        $migrationTemplate = MigrationTemplate::{$action}($migrationName, strtolower($table));

        $this->fs->put($this->migrationFolder . DS . $migrationName . '.php', $migrationTemplate);

        return $migrationName;
    }

    public function applyMigrations(string $direction, ?int $step = null): ?int
    {
        $databaseDriver = $this->db->getConfigs()['driver'];

        if (!in_array($databaseDriver, $this->drivers)) {
            throw MigrationException::unsupportedDriver($databaseDriver);
        }

        switch ($direction) {
            case self::UPGRADE:
                $migrated = $this->upgrade($step);
                break;
            case self::DOWNGRADE:
                $migrated = $this->downgrade($step);
                break;
            default:
                throw MigrationException::wrongDirection();
        }

        return $migrated;
    }

    /**
     *
     */
    private function upgrade(?int $step = null): int
    {
        if (!$this->tableFactory->checkTableExists(MigrationTable::TABLE)) {
            $migrationTable = new MigrationTable();
            $migrationTable->up($this->tableFactory);
        }

        $this->prepareUpMigrations($step);

        if (empty($this->migrations)) {
            throw MigrationException::nothingToMigrate();
        }

        $migratedEntries = [];

        foreach ($this->migrations as $migrationFile) {
            $this->fs->require($migrationFile);

            $migrationClassName = pathinfo($migrationFile, PATHINFO_FILENAME);

            $migration = new $migrationClassName();

            $migration->up($this->tableFactory);

            array_push($migratedEntries, $migrationClassName);
        }

        $this->addMigratedEntreis($migratedEntries);

        return count($migratedEntries);
    }

    private function downgrade(?int $step): int
    {
        $this->prepareDownMigrations($step);

        if (empty($this->migrations)) {
            throw MigrationException::nothingToMigrate();
        }

        $migratedEntries = [];

        foreach ($this->migrations as $migrationFile) {
            $this->fs->require($migrationFile);

            $migrationClassName = pathinfo($migrationFile, PATHINFO_FILENAME);

            $migration = new $migrationClassName();

            $migration->down($this->tableFactory);

            array_push($migratedEntries, $migrationClassName);
        }

        $this->removeMigratedEntries($migratedEntries);

        return count($migratedEntries);
    }

    private function prepareUpMigrations(?int $step = null)
    {
        $migratedEntries = $this->getMigaratedEntries();
        $migrationFiles = $this->getMigrationFiles();

        if (empty($migratedEntries) && empty($migrationFiles)) {
            throw MigrationException::nothingToMigrate();
        }

        foreach ($migrationFiles as $timestamp => $migrationFile) {
            foreach ($migratedEntries as $migratedEntry) {
                if (pathinfo($migrationFile, PATHINFO_FILENAME) == $migratedEntry['migration']) {
                    continue 2;
                }
            }

            $this->migrations[$timestamp] = $migrationFile;
        }

        ksort($this->migrations);
    }

    private function prepareDownMigrations(?int $step = null)
    {
        $migratedEntries = $this->getMigaratedEntries();

        if (empty($migratedEntries)) {
            throw MigrationException::nothingToMigrate();
        }

        foreach ($migratedEntries as $migratedEntry) {
            $exploded = explode('_', $migratedEntry['migration']);
            $this->migrations[array_pop($exploded)] = $this->migrationFolder . DS . $migratedEntry['migration'] . '.php';
        }

        if (!is_null($step)) {
            $this->migrations = array_slice($this->migrations, count($this->migrations) - $step, $step, true);
        }

        krsort($this->migrations);
    }

    private function getMigrationFiles(): array
    {
        $migrationsFiles = $this->fs->glob($this->migrationFolder . DS . '*.php');

        $migrations = [];

        if (!empty($migrationsFiles)) {
            foreach ($migrationsFiles as $migration) {
                $exploded = explode('_', pathinfo($migration, PATHINFO_FILENAME));
                $migrations[array_pop($exploded)] = $migration;
            }
        }

        return $migrations;
    }

    private function getMigaratedEntries(): array
    {
        return Database::query("SELECT * FROM " . MigrationTable::TABLE);
    }

    private function addMigratedEntreis(array $entries)
    {
        foreach ($entries as $entry) {
            Database::execute('INSERT INTO ' . MigrationTable::TABLE . '(migration) VALUES(:migration)', ['migration' => $entry]);
        }
    }

    private function removeMigratedEntries(array $entries)
    {
        foreach ($entries as $entry) {
            Database::execute('DELETE FROM ' . MigrationTable::TABLE . ' WHERE migration=:migration', ['migration' => $entry]);
        }
    }

}
