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

namespace Quantum\Libraries\Database;

/**
 * Class Column
 * @package Quantum\Libraries\Database
 */
class Column
{

    /**
     * Action add
     */
    const ADD = 'ADD';

    /**
     * Action modify
     */
    const MODIFY = 'MODIFY';

    /**
     * Action rename
     */
    const RENAME = 'RENAME';

    /**
     * Action drop
     */
    const DROP = 'DROP';

    /**
     * Name property
     */
    const NAME = 'name';

    /**
     * Type property
     */
    const TYPE = 'type';

    /**
     * Constraint property
     */
    const CONSTRAINT = 'constraint';

    /**
     * Attribute property
     */
    const ATTRIBUTE = 'attribute';

    /**
     * Nullable property
     */
    const NULLABLE = 'nullable';

    /**
     * Default property
     */
    const DEFAULT = 'default';

    /**
     * After column property
     */
    const AFTER = 'afterColumn';

    /**
     * Comment property
     */
    const COMMENT = 'comment';

    /**
     * Auto increment property
     */
    const AUTO_INCREMENT = 'autoincrement';

    /**
     * Attribute binary
     */
    const ATTR_BINARY = 'BINARY';

    /**
     * Attribute unsigned
     */
    const ATTR_UNSIGNED = 'UNSIGNED';

    /**
     * Attribute zero fill
     */
    const ATTR_ZEROFILL = 'UNSIGNED ZEROFILL';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $newName;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $constraint;

    /**
     * @var string
     */
    private $attribute = null;

    /**
     * @var string
     */
    private $nullable = 'NOT NULL';

    /**
     * @var string
     */
    private $default;

    /**
     * @var bool
     */
    private $defaultQuoted = true;

    /**
     * @var string
     */
    private $autoincrement = null;

    /**
     * @var string
     */
    private $indexKey = null;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $afterColumn;

    /**
     * Column constructor.
     * @param string $name
     * @param string $type
     * @param mixed $constraint
     */
    public function __construct(string $name, string $type = null, $constraint = null)
    {
        $this->name = $name;

        if ($type) {
            $this->type = strtoupper($type);
            $this->constraint = $constraint;
        }
    }

    /**
     * Renames the column
     * @param string $newName
     * @return $this
     */
    public function renameTo(string $newName): Column
    {
        $this->newName = $newName;
        return $this;
    }

    /**
     * Gets the column property
     * @param string $property
     * @param string $action
     * @return mixed
     */
    public function get(string $property, string $action = null)
    {
        return isset($this->$property) && ($action != self::RENAME && $action != self::DROP) ? $this->$property : null;
    }

    /**
     * Makes the column auto incremental
     */
    public function autoIncrement()
    {
        $this->autoincrement = 'AUTO_INCREMENT';
        $this->primary();
    }

    /**
     * Adds a primary key the column
     */
    public function primary()
    {
        $this->indexKey = Key::PRIMARY;
    }

    /**
     * Adds a index key to the column
     */
    public function index()
    {
        $this->indexKey = Key::INDEX;
    }

    /**
     * Adds unique key to the column
     */
    public function unique()
    {
        $this->indexKey = Key::UNIQUE;
    }

    /**
     * Adds a fulltext key the column
     */
    public function fulltext()
    {
        $this->indexKey = Key::FULLTEXT;
    }

    /**
     * Adds a spatial key the column
     */
    public function spatial()
    {
        $this->indexKey = Key::SPATIAL;
    }

    /**
     * Adds a type to the column
     * @param string $type
     * @param type $constraint
     */
    public function type(string $type, $constraint = null)
    {
        $this->type = strtoupper($type);
        $this->constraint = $constraint;
    }

    /**
     * Adds or removes nullable property
     * @param bool $indeed
     */
    public function nullable(bool $indeed = true)
    {
        $this->nullable = (!$indeed ? 'NOT ' : '') . 'NULL';
    }

    /**
     * Adds default value to the column
     * @param type $value
     * @param bool $quoted
     */
    public function default($value, bool $quoted = true)
    {
        $this->default = $value;
        $this->defaultQuoted = $quoted;
    }

    /**
     * Adds or removes attribute to the column
     * @param string $value
     */
    public function attribute(?string $value)
    {
        $this->attribute = $value ? strtoupper($value) : null;
    }

    /**
     * Adds or removes comment to the column
     * @param string $comment
     */
    public function comment(?string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Adds column after a given column
     * @param string $columnName
     */
    public function after(string $columnName)
    {
        $this->afterColumn = $columnName;
    }

    /**
     * Adds quotes on default value
     * @return bool
     */
    public function defaultQuoted(): bool
    {
        return $this->defaultQuoted;
    }

}
