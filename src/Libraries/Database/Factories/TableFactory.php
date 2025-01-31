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

namespace Quantum\Libraries\Database\Factories;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Schemas\Table;
use Quantum\Libraries\Database\Database;
use PDOException;

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
     * @throws DatabaseException
     */
    public function create(string $name): Table
    {
        if ($this->checkTableExists($name)) {
            throw DatabaseException::tableAlreadyExists($name);
        }

        return $this->createInstance($name)->setAction(Table::CREATE);
    }

    /**
     * Get the table
     * @param string $name
     * @return Table
     * @throws DatabaseException
     */
    public function get(string $name): Table
    {
        if (!$this->checkTableExists($name)) {
            throw DatabaseException::tableDoesNotExists($name);
        }

        return $this->createInstance($name)->setAction(Table::ALTER);
    }

    /**
     * Renames the table
     * @param string $oldName
     * @param string $newName
     * @return bool
     * @throws DatabaseException
     */
    public function rename(string $oldName, string $newName): bool
    {
        if (!$this->checkTableExists($oldName)) {
            throw DatabaseException::tableDoesNotExists($oldName);
        }

        $this->createInstance($oldName)->setAction(Table::RENAME, ['newName' => $newName]);
        return true;
    }

    /**
     * Drops the table
     * @param string $name
     * @return bool
     * @throws DatabaseException
     */
    public function drop(string $name): bool
    {
        if (!$this->checkTableExists($name)) {
            throw DatabaseException::tableDoesNotExists($name);
        }

        $this->createInstance($name)->setAction(Table::DROP);
        return true;
    }

    /**
     * Checks if the DB table exists
     * @param string $name
     * @return bool
     * @throws DatabaseException
     */
    public function checkTableExists(string $name): bool
    {
        try {
            Database::query('SELECT 1 FROM ' . $name);
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    /**
     * Creates new Table instance
     * @param string $name
     * @return Table
     */
    private function createInstance(string $name): Table
    {
        return new Table($name);
    }
}