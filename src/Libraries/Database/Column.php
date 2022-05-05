<?php

namespace Quantum\Libraries\Database;

class Column
{

    const ADD = 'ADD';
    const MODIFY = 'MODIFY';
    const RENAME = 'RENAME';
    const DROP = 'DROP';
    const NAME = 'name';
    const TYPE = 'type';
    const CONSTRAINT = 'constraint';
    const ATTRIBUTE = 'attribute';
    const NULLABLE = 'nullable';
    const DEFAULT = 'default';
    const AFTER = 'afterColumn';
    const AUTO_INCREMENT = 'autoincrement';
    const KEY_PRIMARY = 'primary';
    const KEY_INDEX = 'index';
    const KEY_UNIQUE = 'unique';
    const KEY_FULLTEXT = 'fulltext';
    const KEY_SPATIAL = 'spatial';

    private $name;
    private $newName;
    private $type;
    private $constraint;
    private $attribute = null;
    private $nullable = 'NOT NULL';
    private $default;
    private $defaultQuoted = true;
    private $autoincrement = null;
    private $indexKey = null;
    private $comment;
    private $afterColumn;

    public function __construct(string $name, string $type = null, $constraint = null)
    {
        $this->name = $name;

        if ($type) {
            $this->type = strtoupper($type);
            $this->constraint = $constraint;
        }
    }

    public function renameTo(string $newName)
    {
        $this->newName = $newName;
        return $this;
    }

    public function get(string $property, string $action = null)
    {
        return isset($this->$property) && ($action != self::RENAME && $action != self::DROP) ? $this->$property : null;
    }

    public function autoIncrement()
    {
        $this->autoincrement = 'AUTO_INCREMENT';
        $this->primary();
    }

    public function primary()
    {
        $this->indexKey = self::KEY_PRIMARY;
    }

    public function index()
    {
        $this->indexKey = self::KEY_INDEX;
    }

    public function unique()
    {
        $this->indexKey = self::KEY_UNIQUE;
    }

    public function fulltext()
    {
        $this->indexKey = self::KEY_FULLTEXT;
    }

    public function spatial()
    {
        $this->indexKey = self::KEY_SPATIAL;
    }

    public function type(string $type, $constraint = null)
    {
        $this->type = strtoupper($type);
        $this->constraint = $constraint;
    }

    public function nullable(bool $indeed = true)
    {
        $this->nullable = (!$indeed ? 'NOT ' : '') . 'NULL';
    }

    public function default($value, bool $quoted = true)
    {
        $this->default = $value;
        $this->defaultQuoted = $quoted;
    }

    public function attribute(string $value)
    {
        $availabelAttributes = ['BINARY', 'UNSIGNED', 'UNSIGNED ZEROFILL'];

        if (in_array(strtoupper($value), $availabelAttributes)) {
            $this->attribute = strtoupper($value);
        }
    }

    public function comment(string $comment)
    {
        $this->comment = $comment;
    }

    public function after(string $columnName)
    {
        $this->afterColumn = $columnName;
    }

    public function defaultQuoted()
    {
        return $this->defaultQuoted;
    }

}
