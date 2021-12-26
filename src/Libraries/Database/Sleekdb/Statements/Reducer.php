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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Database\Sleekdb\Statements;

use Quantum\Libraries\Database\DbalInterface;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

/**
 * Trait Modifier
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Reducer
{

    /**
     * @inheritDoc
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

        $this->selected = $columns;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function groupBy(string $column): DbalInterface
    {
        array_push($this->grouped, $column);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $column, string $direction): DbalInterface
    {
        $this->ordered[$column] = $direction;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): DbalInterface
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): DbalInterface
    {
        $this->limit = $limit;
        return $this;
    }


}