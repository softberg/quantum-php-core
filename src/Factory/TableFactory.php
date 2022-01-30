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

use Quantum\Libraries\Database\Database;
use Quantum\Migration\Table;

/**
 * Class TableFactory
 * @package Quantum\Factory
 */
class TableFactory
{

    public function create(string $name): Table
    {
        if ($this->checkTableExists($name)) {
            dd('Table already exists');
        }

        return new Table($name);
    }

//    public function get(string $name): ?Table
//    {
//
//    }
//
//    public function drop(string $name): bool
//    {
//
//    }

    protected function checkTableExists(string $name)
    {
        try {
            Database::query('SELECT 1 FROM ' . $name);
        } catch (DatabaseException $e) {
            return false;
        }

        return true;
    }

}
