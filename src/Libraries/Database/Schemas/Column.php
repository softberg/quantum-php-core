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

namespace Quantum\Libraries\Database\Schemas;

use Quantum\Libraries\Database\Enums\Key;

/**
 * Class Column
 * @package Quantum\Libraries\Database
 */
class Column
{
    /**
     * Action add
     */
    public const ADD = 'ADD';

    /**
     * Action modify
     */
    public const MODIFY = 'MODIFY';

    /**
     * Action rename
     */
    public const RENAME = 'RENAME';

    /**
     * Action drop
     */
    public const DROP = 'DROP';

    /**
     * Action add index
     */
    public const ADD_INDEX = 'ADD_INDEX';

    /**
     * Action drop index
     */
    public const DROP_INDEX = 'DROP_INDEX';

    /**
     * Name property
     */
    public const NAME = 'name';

    /**
     * New Name property
     */
    public const NEW_NAME = 'newName';

    /**
     * Type property
     */
    public const TYPE = 'type';

    /**
     * Constraint property
     */
    public const CONSTRAINT = 'constraint';

    /**
     * Attribute property
     */
    public const ATTRIBUTE = 'attribute';

    /**
     * Nullable property
     */
    public const NULLABLE = 'nullable';

    /**
     * Default property
     */
    public const DEFAULT = 'default';

    /**
     * After column property
     */
    public const AFTER = 'afterColumn';

    /**
     * Comment property
     */
    public const COMMENT = 'comment';

    /**
     * Auto increment property
     */
    public const AUTO_INCREMENT = 'autoincrement';

    /**
     * Attribute binary
     */
    public const ATTR_BINARY = 'BINARY';

    /**
     * Attribute unsigned
     */
    public const ATTR_UNSIGNED = 'UNSIGNED';

    /**
     * Attribute zero fill
     */
    public const ATTR_ZEROFILL = 'UNSIGNED ZEROFILL';

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
     * @var mixed
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
    private $indexName = null;

    /**
     * @var string
     */
    private $indexDrop = null;

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
     * @param string|null $type
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

    public function indexDrop(string $indexName): Column
    {
        $this->indexDrop = $indexName;
        return $this;
    }

    /**
     * Gets the column property
     * @param string $property
     * @param string|null $action
     * @return mixed
     */
    public function get(string $property, string $action = null)
    {
        return (isset($this->$property) && $action != self::RENAME && $action != self::DROP) ? $this->$property : null;
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
     * Adds an index key to the column
     * @param string|null $name
     */
    public function index(string $name = null)
    {
        $this->indexKey = Key::INDEX;

        if ($name) {
            $this->indexName = $name;
        }
    }

    /**
     * Adds unique key to the column
     * @param string|null $name
     */
    public function unique(string $name = null)
    {
        $this->indexKey = Key::UNIQUE;

        if ($name) {
            $this->indexName = $name;
        }
    }

    /**
     * Adds a fulltext key the column
     * @param string|null $name
     */
    public function fulltext(string $name = null)
    {
        $this->indexKey = Key::FULLTEXT;

        if ($name) {
            $this->indexName = $name;
        }
    }

    /**
     * Adds a spatial key the column
     * @param string|null $name
     */
    public function spatial(string $name = null)
    {
        $this->indexKey = Key::SPATIAL;

        if ($name) {
            $this->indexName = $name;
        }
    }

    /**
     * Adds or removes nullable property
     * @param bool $indeed
     */
    public function nullable(bool $indeed = true)
    {
        $this->nullable = ($indeed ? '' : 'NOT ') . 'NULL';
    }

    /**
     * Adds default value to the column
     * @param mixed $value
     * @param bool $quoted
     */
    public function default($value, bool $quoted = true)
    {
        $this->default = $value;
        $this->defaultQuoted = $quoted;
    }

    /**
     * Adds or removes attribute to the column
     * @param string|null $value
     */
    public function attribute(?string $value)
    {
        $this->attribute = $value ? strtoupper($value) : null;
    }

    /**
     * Adds or removes comment to the column
     * @param string|null $comment
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
