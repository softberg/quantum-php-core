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
 * @since 2.9.5
 */

namespace Quantum\Migration;

use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Database\Factories\TableFactory;
use Quantum\Migration\Exceptions\MigrationException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Migration\Templates\MigrationTemplate;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Libraries\Database\Database;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;

/**
 * Class MigrationManager
 * @package Quantum\Migration
 */
class MigrationManager
{

    /**
     * Migration direction for upgrade
     */
    const UPGRADE = 'up';

    /**
     * Migration direction for downgrade
     */
    const DOWNGRADE = 'down';

    /**
     * Available actions
     */
    const ACTIONS = ['create', 'alter', 'rename', 'drop'];

    /**
     * Supported drivers
     */
    const DRIVERS = ['mysql', 'pgsql', 'sqlite'];

    /**
     * @var array
     */
    private $migrations = [];

    /**
     * @var TableFactory
     */
    private $tableFactory;

    /**
     * @var string
     */
    private $migrationFolder;

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var Database
     */
    private $db;

    /**
     * @throws BaseException
     * @throws FileSystemException
     */
    public function __construct()
    {
        $this->fs = FileSystemFactory::get();

        $this->db = Database::getInstance();

        $this->tableFactory = new TableFactory();

        $this->migrationFolder = base_dir() . DS . 'migrations';

        if (!$this->fs->isDirectory($this->migrationFolder)) {
            throw FileSystemException::directoryNotExists($this->migrationFolder);
        }
    }

    /**
     * Generates new migration file
     * @param string $table
     * @param string $action
     * @return string
     * @throws MigrationException
     * @throws LangException
     */
    public function generateMigration(string $table, string $action): string
    {
        if (!in_array($action, self::ACTIONS)) {
            throw MigrationException::unsupportedAction($action);
        }

        $migrationName = $action . '_table_' . strtolower($table) . '_' . time();

        $migrationTemplate = MigrationTemplate::{$action}($migrationName, strtolower($table));

        $this->fs->put($this->migrationFolder . DS . $migrationName . '.php', $migrationTemplate);

        return $migrationName;
    }

    /**
     * Applies migrations
     * @param string $direction
     * @param int|null $step
     * @return int|null
     * @throws BaseException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws LangException
     * @throws MigrationException
     */
    public function applyMigrations(string $direction, ?int $step = null): ?int
    {
        $databaseDriver = $this->db->getConfigs()['driver'];

        if (!in_array($databaseDriver, self::DRIVERS)) {
            throw MigrationException::driverNotSupported($databaseDriver);
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
     * Runs up migrations
     * @param int|null $step
     * @return int
     * @throws DatabaseException
     * @throws LangException
     * @throws MigrationException
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
            $this->fs->require($migrationFile, true);

            $migrationClassName = pathinfo($migrationFile, PATHINFO_FILENAME);

            $migration = new $migrationClassName();

            $migration->up($this->tableFactory);

            $migratedEntries[] = $migrationClassName;
        }

        $this->addMigratedEntries($migratedEntries);

        return count($migratedEntries);
    }

    /**
     * Runs down migrations
     * @param int|null $step
     * @return int
     * @throws DatabaseException
     * @throws LangException
     * @throws MigrationException
     */
    private function downgrade(?int $step): int
    {
        if (!$this->tableFactory->checkTableExists(MigrationTable::TABLE)) {
            throw DatabaseException::tableDoesNotExists(MigrationTable::TABLE);
        }

        $this->prepareDownMigrations($step);

        if (empty($this->migrations)) {
            throw MigrationException::nothingToMigrate();
        }

        $migratedEntries = [];

        foreach ($this->migrations as $migrationFile) {
            $this->fs->require($migrationFile, true);

            $migrationClassName = pathinfo($migrationFile, PATHINFO_FILENAME);

            $migration = new $migrationClassName();

            $migration->down($this->tableFactory);

            $migratedEntries[] = $migrationClassName;
        }

        $this->removeMigratedEntries($migratedEntries);

        return count($migratedEntries);
    }

    /**
     * Prepares up migrations
     * @param int|null $step
     * @throws MigrationException
     * @throws DatabaseException
     *
     */
    private function prepareUpMigrations(?int $step = null)
    {
        $migratedEntries = $this->getMigratedEntries();
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

    /**
     * Prepares down migrations
     * @param int|null $step
     * @throws DatabaseException
     * @throws MigrationException
     */
    private function prepareDownMigrations(?int $step = null)
    {
        $migratedEntries = $this->getMigratedEntries();

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

    /**
     * Gets migration files
     * @return array
     */
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

    /**
     * Gets migrated entries from migrations table
     * @return array
     * @throws DatabaseException
     */
    private function getMigratedEntries(): array
    {
        return Database::query("SELECT * FROM " . MigrationTable::TABLE);
    }

    /**
     * Adds migrated entries to migrations table
     * @param array $entries
     * @throws DatabaseException
     */
    private function addMigratedEntries(array $entries)
    {
        foreach ($entries as $entry) {
            Database::execute('INSERT INTO ' . MigrationTable::TABLE . '(migration) VALUES(:migration)', ['migration' => $entry]);
        }
    }

    /**
     * Removes migrated entries from migrations table
     * @param array $entries
     * @throws DatabaseException
     */
    private function removeMigratedEntries(array $entries)
    {
        foreach ($entries as $entry) {
            Database::execute('DELETE FROM ' . MigrationTable::TABLE . ' WHERE migration=:migration', ['migration' => $entry]);
        }
    }
}