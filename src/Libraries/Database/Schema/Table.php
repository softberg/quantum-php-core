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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Database\Schema;

use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\MigrationException;
use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\LangException;

/**
 * Class Table
 * @package Quantum\Libraries\Database
 *
 * @method self autoIncrement()
 * @method self primary()
 * @method self index(string $name = null)
 * @method self unique(string $name = null)
 * @method self fulltext(string $name = null)
 * @method self spatial(string $name = null)
 * @method self nullable(bool $indeed = true)
 * @method self default($value, bool $quoted = true)
 * @method self defaultQuoted()
 * @method self attribute(?string $value)
 * @method self comment(?string $value)
 */
class Table
{

    use TableBuilder;

    /**
     * Action create
     */
    const CREATE = 1;

    /**
     * Action alter
     */
    const ALTER = 2;

    /**
     * Action drop
     */
    const DROP = 3;

    /**
     * Action rename
     */
    const RENAME = 4;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $newName;

    /**
     * @var int
     */
    private $action = null;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $indexKeys = [];

    /**
     * @var array
     */
    private $droppedIndexKeys = [];

    /**
     * Table constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Table destructor
     * Saves the data before object goes out of scope
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * Renames the table
     * @param string $newName
     * @return Table
     */
    public function renameTo(string $newName): Table
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Sets an action on a table to be performed
     * @param int $action
     * @param array|null $data
     * @return Table
     */
    public function setAction(int $action, ?array $data = null): Table
    {
        $this->action = $action;

        if ($data) {
            $key = key($data);
            $this->$key = $data[$key];
        }

        return $this;
    }

    /**
     * Adds column to the table
     * @param string $name
     * @param string $type
     * @param mixed $constraint
     * @return Table
     */
    public function addColumn(string $name, string $type, $constraint = null): Table
    {
        $this->columns[] = [
            'column' => new Column($name, $type, $constraint),
            'action' => $this->action == self::ALTER ? Column::ADD : null
        ];

        return $this;
    }

    /**
     * Modifies the column
     * @param string $name
     * @param string $type
     * @param mixed $constraint
     * @return Table
     */
    public function modifyColumn(string $name, string $type, $constraint = null): Table
    {
        if ($this->action == self::ALTER) {
            $this->columns[] = [
                'column' => new Column($name, $type, $constraint),
                'action' => Column::MODIFY
            ];
        }

        return $this;
    }

    /**
     * Renames the column name
     * @param string $oldName
     * @param string $newName
     */
    public function renameColumn(string $oldName, string $newName)
    {
        if ($this->action == self::ALTER) {
            $this->columns[] = [
                'column' => (new Column($oldName))->renameTo($newName),
                'action' => Column::RENAME
            ];
        }
    }

    /**
     * Drops the column
     * @param string $name
     */
    public function dropColumn(string $name)
    {
        if ($this->action == self::ALTER) {
            $this->columns[] = [
                'column' => new Column($name),
                'action' => Column::DROP
            ];
        }
    }

    /**
     * Adds new index to column
     * @param string $columnName
     * @param string $indexType
     * @param string|null $indexName
     */
    public function addIndex(string $columnName, string $indexType, string $indexName = null)
    {
        if ($this->action == self::ALTER) {
            $this->columns[] = [
                'column' => new Column($columnName),
                'action' => Column::ADD_INDEX
            ];

            $this->$indexType($indexName);
        }
    }

    /**
     * Drops the column index
     * @param string $indexName
     */
    public function dropIndex(string $indexName)
    {
        if ($this->action == self::ALTER) {
            $this->columns[] = [
                'column' => (new Column('dummy'))->indexDrop($indexName),
                'action' => Column::DROP_INDEX
            ];
        }
    }

    /**
     * Adds columns after specified one
     * @param string $columnName
     * @return $this
     */
    public function after(string $columnName): Table
    {
        $this->columns[$this->columnKey()]['column']->after($columnName);
        return $this;
    }

    /**
     * Gets the generated query
     * @return string
     */
    public function getSql(): string
    {
        $sql = '';

        switch ($this->action) {
            case self::CREATE:
                $sql = $this->createTableSql();
                break;
            case self::ALTER:
                $sql = $this->alterTableSql();
                break;
            case self::RENAME:
                $sql = $this->renameTableSql();
                break;
            case self::DROP:
                $sql = $this->dropTableSql();
                break;
        }

        return $sql;
    }

    /**
     * Allows to call methods of Column class
     * @param string $method
     * @param array|null $arguments
     * @return $this
     * @throws MigrationException
     * @throws LangException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists(Column::class, $method)) {
            throw MigrationException::methodNotDefined($method);
        }

        $this->columns[$this->columnKey()]['column']->{$method}(...$arguments);
        return $this;
    }

    /**
     * Saves the query
     * @throws DatabaseException
     */
    private function save()
    {
        $sql = $this->getSql();

        if ($sql) {
            Database::execute($sql);
        }
    }

    /**
     * Checks if column exists on a table
     * @param string $columnName
     * @return bool
     * @throws DatabaseException
     */
    private function checkColumnExists(string $columnName): bool
    {
        $columnIndex = null;

        $columns = Database::fetchColumns($this->name);

        foreach ($columns as $index => $column) {
            if ($columnName == $column) {
                $columnIndex = $index;
                break;
            }
        }

        return !is_null($columnIndex);
    }

    /**
     * Gets the column key
     * @return int
     */
    private function columnKey(): int
    {
        return (int)array_key_last($this->columns);
    }

}
