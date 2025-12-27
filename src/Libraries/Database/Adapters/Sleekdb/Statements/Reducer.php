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

namespace Quantum\Libraries\Database\Adapters\Sleekdb\Statements;

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

        $this->selected = $this->selectPatch($columns);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function groupBy(string $column): DbalInterface
    {
        $this->grouped[] = $column;
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

    /**
     * @param array $columns
     * @return array
     */
    private function selectPatch(array $columns): array
    {
        foreach ($columns as &$column) {
            $exploded = explode('.', $column);

            if (count($exploded) === 1) {
                $column = $exploded[0];
            } elseif ($exploded[0] == $this->getTable()) {
                $column = $exploded[1];
            } else {
                $column = $exploded[0] . '.0.' . $exploded[1];
            }
        }

        return $columns;
    }
}