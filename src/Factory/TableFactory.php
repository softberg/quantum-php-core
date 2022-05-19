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

namespace Quantum\Factory;

use Quantum\Libraries\Database\Schema\Table;
use Quantum\Exceptions\MigrationException;
use Quantum\Libraries\Database\Database;

/**
 * Class TableFactory
 * @package Quantum\Factory
 */
class TableFactory
{

    /**
     * Creates new table
     * @param string $name
     * @return Table
     * @throws Quantum\Exceptions\MigrationException
     */
    public function create(string $name): Table
    {
        if ($this->checkTableExists($name)) {
            throw MigrationException::tableAlreadyExists($name);
        }

        return $this->createInstance($name)->setAction(Table::CREATE);
    }

    /**
     * Get the table
     * @param string $name
     * @return Table
     * @throws Quantum\Exceptions\MigrationException
     */
    public function get(string $name): Table
    {
        if (!$this->checkTableExists($name)) {
            throw MigrationException::tableDoesnotExists($name);
        }

        return $this->createInstance($name)->setAction(Table::ALTER);
    }

    /**
     * Renames the table
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function rename(string $oldName, string $newName): bool
    {
        if (!$this->checkTableExists($oldName)) {
            throw MigrationException::tableDoesnotExists($oldName);
        }

        $this->createInstance($oldName)->setAction(Table::RENAME, ['newName' => $newName]);
        return true;
    }

    /**
     * Drops the table
     * @param string $name
     * @return bool
     */
    public function drop(string $name): bool
    {
        if (!$this->checkTableExists($name)) {
            throw MigrationException::tableDoesnotExists($name);
        }

        $this->createInstance($name)->setAction(Table::DROP);
        return true;
    }

    /**
     * Checks if the DB table exists
     * @param string $name
     * @return bool
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function checkTableExists(string $name): bool
    {
        try {
            Database::query('SELECT 1 FROM ' . $name);
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    /**
     * Creates new Table instance
     * @param string $name
     * @return Table
     */
    private function createInstance(string $name)
    {
        return new Table($name);
    }

}
