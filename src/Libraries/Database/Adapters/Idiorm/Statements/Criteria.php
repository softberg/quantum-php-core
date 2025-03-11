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
 * @since 2.9.6
 */

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Contracts\DbalInterface;

/**
 * Trait Criteria
 * @package Quantum\Libraries\Database
 */
trait Criteria
{

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function criteria(string $column, string $operator, $value = null): DbalInterface
    {
        if (!key_exists($operator, $this->operators)) {
            throw DatabaseException::operatorNotSupported($operator);
        }

        $this->addCriteria($column, $operator, $value, $this->operators[$operator]);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function criterias(...$criterias): DbalInterface
    {
        foreach ($criterias as $criteria) {
            if (isset($criteria[0]) && is_array($criteria[0])) {
                $this->orCriteria($criteria);
            } else {
                $this->criteria(...$criteria);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function having(string $column, string $operator, string $value = null): DbalInterface
    {
        if (!key_exists($operator, $this->operators)) {
            throw DatabaseException::operatorNotSupported($operator);
        }

        $func = $this->operators[$operator];
        $this->getOrmModel()->$func($column, $value);
        return $this;
    }

    /**
     * Adds Criteria
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string|null $func
     * @throws DatabaseException
     */
    protected function addCriteria(string $column, string $operator, $value, string $func = null)
    {
        if ($operator == '#=#') {
            $this->getOrmModel()->where_raw($column . ' = ' . $value);
        } else if (is_array($value) && count($value) == 1 && key($value) == 'fn') {
            $this->getOrmModel()->where_raw($column . ' ' . $operator . ' ' . $value['fn']);
        } else {
            $this->getOrmModel()->$func($column, $value);
        }
    }

    /**
     * Adds one or more OR criteria in brackets
     * @param array $orCriterias
     * @throws DatabaseException
     */
    protected function orCriteria(array $orCriterias)
    {
        $clause = '';
        $params = [];

        foreach ($orCriterias as $index => $criteria) {
            if ($index == 0) {
                $clause .= '(';
            }

            $clause .= '`' . $criteria[0] . '` ' . $criteria[1] . ' ?';

            if ($index == array_key_last($orCriterias)) {
                $clause .= ')';
            } else {
                $clause .= ' OR ';
            }

            $params[] = $criteria[2];
        }

        $this->getOrmModel()->where_raw($clause, $params);
    }
}