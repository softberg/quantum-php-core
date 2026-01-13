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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Database\Traits;

use Quantum\Libraries\Database\Schemas\Column;
use Quantum\Libraries\Database\Enums\Key;

/**
 * Trait TableTrait
 * @package Quantum\Libraries\Database
 */
trait TableTrait
{
    /**
     * Generates create table statement
     * @return string
     */
    protected function createTableSql(): string
    {
        $tableSchema = array_filter([$this->columnsSql(), $this->indexesSql()]);
        $sql = '';

        if ($tableSchema !== []) {
            $sql = 'CREATE TABLE `' . $this->name . '` (';
            $sql .= implode(', ', $tableSchema);
            $sql .= ');';
        }

        return $sql;
    }

    /**
     * Generates alter table statement
     * @return string
     */
    protected function alterTableSql(): string
    {
        $tableSchema = array_filter([$this->columnsSql(), $this->indexesSql(), $this->dropIndexesSql()]);
        $sql = '';

        if ($tableSchema !== []) {
            $sql = 'ALTER TABLE `' . $this->name . '` ';
            $sql .= implode(', ', $tableSchema);
            $sql .= ';';
        }

        return $sql;
    }

    /**
     * Prepares rename table statement
     * @return string
     */
    protected function renameTableSql(): string
    {
        return 'RENAME TABLE `' . $this->name . '` TO `' . $this->newName . '`;';
    }

    /**
     * Prepares drop table statement
     * @return string
     */
    protected function dropTableSql(): string
    {
        return 'DROP TABLE `' . $this->name . '`';
    }

    /**
     * Prepares columns statements for table
     * @return string
     */
    protected function columnsSql(): string
    {
        $sql = '';

        if ($this->columns) {
            $columns = [];

            foreach ($this->columns as $entry) {
                $columnString = '';

                if ($entry['action'] != Column::ADD_INDEX && $entry['action'] != Column::DROP_INDEX) {
                    $columnString .= ($entry['action'] ? $entry['action'] . ' COLUMN ' : '');
                    $columnString .= $this->composeColumn($entry['column'], $entry['action']);
                }

                if ($entry['column']->get('indexKey')) {
                    $this->indexKeys[$entry['column']->get('indexKey')][] = [
                        'columnName' => $entry['column']->get('name'),
                        'indexName' => $entry['column']->get('indexName'),
                    ];
                }

                if ($entry['column']->get('indexDrop')) {
                    $this->droppedIndexKeys[] = $entry['column']->get('indexDrop');
                }

                if ($columnString !== '' && $columnString !== '0') {
                    $columns[] = $columnString;
                }
            }

            $sql = implode(', ', $columns);
        }

        return $sql;
    }

    /**
     * Composes the column
     * @param Column $column
     * @param string|null $action
     * @return string
     */
    protected function composeColumn(Column $column, ?string $action = null): string
    {
        return
            $this->columnAttrSql($column->get(Column::NAME), '`', '`') .
            $this->columnAttrSql($column->get(Column::NEW_NAME), ' TO `', '`') .
            $this->columnAttrSql($column->get(Column::TYPE), ' ') .
            $this->columnAttrSql($column->get(Column::CONSTRAINT), '(', ')') .
            $this->columnAttrSql($column->get(Column::ATTRIBUTE), ' ') .
            $this->columnAttrSql($column->get(Column::NULLABLE, $action), ' ') .
            $this->columnAttrSql($column->get(Column::DEFAULT), ' DEFAULT ' . ($column->defaultQuoted() ? '\'' : ''), ($column->defaultQuoted() ? '\'' : '')) .
            $this->columnAttrSql($column->get(Column::COMMENT), ' COMMENT \'', '\'') .
            $this->columnAttrSql($column->get(Column::AFTER), ' AFTER `', '`') .
            $this->columnAttrSql($column->get(Column::AUTO_INCREMENT), ' ');
    }

    /**
     * Prepares column attributes
     * @param mixed $definition
     * @param string $before
     * @param string $after
     * @return string
     */
    protected function columnAttrSql($definition, string $before = '', string $after = ''): string
    {
        $sql = '';

        if (!is_null($definition)) {

            $sql .= $before . (is_array($definition) ? ("'" . implode("', '", $definition) . "'") : $definition) . $after;
        }

        return $sql;
    }

    /**
     * Prepares statement for primary key
     * @return string
     */
    protected function primaryKeysSql(): string
    {
        $sql = '';

        if (isset($this->indexKeys['primary'])) {
            $sql .= ($this->action == self::ALTER ? 'ADD ' : '');

            $sql .= 'PRIMARY KEY (';

            foreach ($this->indexKeys['primary'] as $key => $primaryKey) {
                $sql .= '`' . $primaryKey['columnName'] . '`';
                $sql .= (array_key_last($this->indexKeys['primary']) != $key ? ', ' : '');
            }

            $sql .= ')';
        }

        return $sql;
    }

    /**
     * Prepares statement for index keys
     * @param string $type
     * @return string
     */
    protected function indexKeysSql(string $type): string
    {
        $sql = '';

        if (isset($this->indexKeys[$type])) {
            $indexes = [];

            foreach ($this->indexKeys[$type] as $indexKey) {
                $indexString = ($this->action == self::ALTER ? 'ADD ' : '');
                $indexString .= strtoupper($type);
                $indexString .= ($indexKey['indexName'] ? ' `' . $indexKey['indexName'] . '`' : '');
                $indexString .= ' (`' . $indexKey['columnName'] . '`)';

                $indexes[] = $indexString;
            }

            $sql = implode(', ', $indexes);
        }

        return $sql;
    }

    /**
     * Builds a complete statement for index keys
     * @return string
     */
    protected function indexesSql(): string
    {
        $indexes = [
            $this->primaryKeysSql(),
            $this->indexKeysSql(Key::INDEX),
            $this->indexKeysSql(Key::UNIQUE),
            $this->indexKeysSql(Key::FULLTEXT),
            $this->indexKeysSql(Key::SPATIAL),
        ];

        return implode(', ', array_filter($indexes));
    }

    /**
     * Builds a statement for drop indexes
     * @return string
     */
    protected function dropIndexesSql(): string
    {
        $sql = '';

        if (!empty($this->droppedIndexKeys)) {
            $indexes = [];

            foreach ($this->droppedIndexKeys as $index) {
                $indexes[] = 'DROP INDEX `' . $index . '`';
            }

            $sql .= implode(', ', $indexes);
        }

        return $sql;
    }
}
