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

use Quantum\Exceptions\MigrationException;
use Quantum\Libraries\Database\Database;
use Quantum\Libraries\Database\Table;

/**
 * Class TableFactory
 * @package Quantum\Factory
 */
class TableFactory
{

    public function create(string $name): Table
    {
        if ($this->checkTableExists($name)) {
            throw MigrationException::tableAlreadyExists($name);
        }

        return (new Table($name))->setAction(Table::CREATE);
    }

    public function get(string $name, int $action = Table::ALTER): Table
    {
        if (!$this->checkTableExists($name)) {
            throw MigrationException::tableDoesnotExists($name);
        }

        return (new Table($name))->setAction($action);
    }

    public function rename(string $oldName, string $newName): bool
    {
        $this->get($oldName)->setAction(Table::RENAME, ['newName' => $newName]);
        return true;
    }

    public function drop(string $name): bool
    {
        $this->get($name)->setAction(Table::DROP);
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

}
