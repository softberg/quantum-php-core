<?php

namespace Quantum\Libraries\Database;

class Column
{

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

    private $type;

    private $constraint;

    private $attribute = null;

    private $nullable = 'NOT NULL';

    private $default;

    private $autoincrement = null;

    private $indexKey = null;

    private $comment;

    private $afterColumn;

    public function __construct(string $name, string $type, $constraint)
    {
        $this->name = $name;
        $this->type = strtoupper($type);
        $this->constraint = $constraint;
    }

    public function get(string $property)
    {
        return $this->$property ?? null;
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

    public function nullable()
    {
        $this->nullable = 'NULL';
    }

    public function default($value)
    {
        $this->default = $value;
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


}