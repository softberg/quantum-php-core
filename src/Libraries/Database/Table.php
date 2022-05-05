<?php

namespace Quantum\Libraries\Database;

use Quantum\Exceptions\MigrationException;

/**
 *
 * @method self autoIncrement()
 * @method self primary()
 * @method self index()
 * @method self unique()
 * @method self fulltext()
 * @method self spatial()
 * @method self nullable()
 * @method self default($value)
 * @method self attribute(string $value)
 * @method self comment(string $value)
 * @method self type(string $type, $constraint)
 */
class Table
{

    const CREATE = 1;
    const ALTER = 2;
    const DROP = 3;
    const RENAME = 4;

    /**
     * @var string
     */
    private $name;
    private $newName;

    /**
     * @var int 
     */
    private $action = null;

    /**
     * @var array
     */
    private $columns = [];
    private $indexKeys = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __destruct()
    {
        $this->save();
    }

    public function renameTo(string $newName)
    {
        $this->newName = $newName;
        return $this;
    }

    public function setAction(int $action, ?array $data = null)
    {
        $this->action = $action;

        if ($data) {
            $key = key($data);
            $this->$key = $data[$key];
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $type
     * @param $constraint
     */
    public function addColumn(string $name, string $type, $constraint = null)
    {
        array_push($this->columns, [
            'column' => new Column($name, $type, $constraint),
            'action' => $this->action == self::ALTER ? Column::ADD : null
        ]);

        return $this;
    }

    public function modifyColumn(string $name, string $type = null, $constraint = null)
    {
        array_push($this->columns, [
            'column' => new Column($name, $type, $constraint),
            'action' => Column::MODIFY
        ]);

        return $this;
    }

    public function renameColumn(string $oldName, string $newName)
    {
        array_push($this->columns, [
            'column' => (new Column($oldName))->renameTo($newName),
            'action' => Column::RENAME
        ]);

        return $this;
    }

    public function dropColumn(string $name)
    {
        array_push($this->columns, [
            'column' => new Column($name),
            'action' => Column::DROP
        ]);
    }

    public function after(string $columnName)
    {
        $this->columns[$this->columnKey()]['column']->after($columnName);

        return $this;
    }

    public function __call(string $method, $arguments)
    {
        if (!method_exists(Column::class, $method)) {
            throw MigrationException::methodNotDefined($method);
        }

        $this->columns[$this->columnKey()]['column']->{$method}(...$arguments);
        return $this;
    }

    protected function save()
    {
        Database::execute($this->getSql());
    }

    protected function getSql()
    {
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

    protected function createTableSql()
    {
        $sql = 'CREATE TABLE `' . $this->name . '` (';
        $sql .= $this->columnsSql();
        $sql .= $this->indexesSql();
        $sql .= ');';

        return $sql;
    }

    protected function alterTableSql(): string
    {
        $sql = 'ALTER TABLE `' . $this->name . '` ';
        $sql .= $this->columnsSql();
        $sql .= $this->indexesSql();
        $sql .= ';';

        return $sql;
    }

    protected function renameTableSql(): string
    {
        return 'RENAME TABLE `' . $this->name . '` TO `' . $this->newName . '`;';
    }

    protected function dropTableSql(): string
    {
        return 'DROP TABLE `' . $this->name . '`';
    }

    protected function columnsSql(): string
    {
        $columns = [];

        foreach ($this->columns as $entry) {
            $columnString = ($entry['action'] ? $entry['action'] . ' COLUMN ' : '');
            $columnString .= $this->composeColumn($entry['column'], $entry['action']);

            if ($entry['column']->get('indexKey')) {
                $this->indexKeys[$entry['column']->get('indexKey')][] = $entry['column']->get('name');
            }

            array_push($columns, $columnString);
        }

        return implode(', ', $columns);
    }

    protected function composeColumn(Column $column, string $action = null): string
    {
        return
                $this->columnAttrSql($column->get(Column::NAME), '`', '`') .
                $this->columnAttrSql($column->get(Column::TYPE), ' ') .
                $this->columnAttrSql($column->get(Column::CONSTRAINT), '(', ')') .
                $this->columnAttrSql($column->get(Column::ATTRIBUTE), ' ') .
                $this->columnAttrSql($column->get(Column::NULLABLE, $action), ' ',) .
                $this->columnAttrSql($column->get(Column::DEFAULT), ' DEFAULT ' . ($column->defaultQuoted() ? '\'' : ''), ($column->defaultQuoted() ? '\'' : '')) .
                $this->columnAttrSql($column->get(Column::AFTER), ' AFTER `', '`') .
                $this->columnAttrSql($column->get(Column::AUTO_INCREMENT), ' ');
    }

    protected function columnAttrSql(?string $definition, string $before = '', string $after = ''): string
    {
        $sql = '';

        if (!is_null($definition)) {
            $sql .= $before . $definition . $after;
        }

        return $sql;
    }

    protected function primaryKeysSql(): string
    {
        $sql = '';

        if (isset($this->indexKeys['primary'])) {
            $sql .= ', PRIMARY KEY (`' . implode('`, `', $this->indexKeys['primary']) . '`)';
        }

        return $sql;
    }

    protected function indexKeysSql(string $type): string
    {
        $sql = '';

        if (isset($this->indexKeys[$type])) {
            $sql .= ', ';
            $lastKey = array_key_last($this->indexKeys[$type]);
            foreach ($this->indexKeys[$type] as $key => $index) {
                $sql .= ($this->action == self::ALTER ? 'ADD ' : '') . strtoupper($type) . ' (`' . $index . '`)' . ($lastKey != $key ? ', ' : '');
            }
        }

        return $sql;
    }

    protected function indexesSql(): string
    {
        return $this->primaryKeysSql() .
                $this->indexKeysSql(Column::KEY_INDEX) .
                $this->indexKeysSql(Column::KEY_UNIQUE) .
                $this->indexKeysSql(Column::KEY_FULLTEXT) .
                $this->indexKeysSql(Column::KEY_SPATIAL);
    }

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

    private function columnKey(): int
    {
        return array_key_last($this->columns);
    }

}
