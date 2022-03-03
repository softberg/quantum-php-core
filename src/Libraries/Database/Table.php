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
 */
class Table
{

    const ALTER_ADD = 'ADD';

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isNew = false;

    /**
     * @var array
     */
    private $columns = [];

    private $indexKeys = [];

    public function __construct(string $name, $isNew = false)
    {
        $this->name = $name;
        $this->isNew = $isNew;
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
            'action' => !$this->isNew ? self::ALTER_ADD : null
        ]);

        return $this;
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

    public function save()
    {
        $this->runSql($this->getSql());
    }

    public function runSql(string $sql)
    {
//        Database::execute($sql);
    }

    public function getSql()
    {
        if ($this->isNew) {
            $sql = $this->createTableSql();
        } else {
            $sql = $this->alterTableSql();
        }

        dump($sql);

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

    private function columnSql(?string $definition, string $before = '', string $after = ''): string
    {
        $sql = '';

        if (!is_null($definition)) {
            $sql .= $before . $definition . $after;
        }

        return $sql;
    }

    protected function columnsSql()
    {
        $columns = [];

        foreach ($this->columns as $entry) {
            $columns[] = ($entry['action'] . ' ') .
                $this->columnSql($entry['column']->get(Column::NAME), '`', '`') .
                $this->columnSql($entry['column']->get(Column::TYPE), ' ') .
                $this->columnSql($entry['column']->get(Column::CONSTRAINT), '(', ')') .
                $this->columnSql($entry['column']->get(Column::ATTRIBUTE), ' ') .
                $this->columnSql($entry['column']->get(Column::NULLABLE), ' ') .
                $this->columnSql($entry['column']->get(Column::DEFAULT), ' DEFAULT \'', '\'') .
                $this->columnSql($entry['column']->get(Column::AFTER), ' AFTER `', '`');

            if ($entry['column']->get('indexKey')) {
                $this->indexKeys[$entry['column']->get('indexKey')][] = $entry['column']->get('name');
            }
        }

        return implode(', ', $columns);
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
                $sql .= (!$this->isNew ? 'ADD ' : '') . strtoupper($type) . ' (`' . $index . '`)' . ($lastKey != $key ? ', ' : '');
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