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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

/**
 * Trait Modifier
 * @package Quantum\Libraries\Database
 */
trait Reducer
{

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function select(...$columns): DbalInterface
    {
        array_walk($columns, function (&$column) {
            if (is_array($column)) {
                $column = array_flip($column);
            }
        });

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($columns));
        $columns = iterator_to_array($iterator, true);

        $this->getOrmModel()->select_many($columns);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function groupBy(string $column): DbalInterface
    {
        $this->getOrmModel()->group_by($column);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function orderBy(string $column, string $direction): DbalInterface
    {
        switch (strtolower($direction)) {
            case 'asc':
                $this->getOrmModel()->order_by_asc($column);
                break;
            case 'desc':
                $this->getOrmModel()->order_by_desc($column);
                break;
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function offset(int $offset): DbalInterface
    {
        $this->getOrmModel()->offset($offset);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function limit(int $limit): DbalInterface
    {
        $this->getOrmModel()->limit($limit);
        return $this;
    }
}