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
 * @since 2.4.0
 */
namespace Quantum\Libraries\Database\Statements;

/**
 * Trait Criteria
 * @package Quantum\Libraries\Database\Statements
 */
trait Criteria
{

    /**
     * Operators map
     * @var string[]
     */
    private $map = [
        '=' => 'where_equal',
        '!=' => 'where_not_equal',
        '>' => 'where_gt',
        '>=' => 'where_gte',
        '<' => 'where_lt',
        '<=' => 'where_lte',
        'IN' => 'where_in',
        'NOT IN' => 'where_not_in',
        'LIKE' => 'where_like',
        'NOT LIKE' => 'where_not_like',
        'NULL' => 'where_null',
        'NOT NULL' => 'where_not_null',
    ];

    /**
     * Adds a criteria to query
     * @inheritDoc
     */
    public function criteria(string $column, string $operator, $value = null): object
    {

        foreach ($this->map as $key => $method) {
            if ($operator == $key) {
                $this->addCriteria($column, $operator, $value, $method);
                break;
            }
        }

        if ($operator == '#=#') {
            $this->whereColumnsEqual($column, $value);
        }

        return $this->ormObject;
    }

    /**
     * Adds many where criteria
     * @param array ...$criterias
     * @return object
     */
    public function criterias(...$criterias): object
    {
        foreach ($criterias as $criteria) {

            if (is_array($criteria[0])) {
                $this->scopedORCriteria($criteria);
                continue;
            }

            $value = $criteria[2] ?? null;

            $this->criteria($criteria[0], $criteria[1], $value);
        }

        return $this->ormObject;
    }

    /**
     * Compares values from two columns
     * @param string $columnOne
     * @param string $columnTwo
     */
    protected function whereColumnsEqual(string $columnOne, string $columnTwo)
    {
        $this->ormObject->where_raw($columnOne . ' = ' . $columnTwo);
    }

    /**
     * Adds one or more OR criteria in brackets
     * @param array $criteria
     */
    protected function scopedORCriteria(array $criteria)
    {
        $clause = '';
        $params = [];

        foreach ($criteria as $index => $orCriteria) {
            if ($index == 0) {
                $clause .= '(';
            }

            $clause .= '`' . $orCriteria[0] . '` ' . $orCriteria[1] . ' ?';

            if ($index == count($criteria) - 1) {
                $clause .= ')';
            } else {
                $clause .= ' OR ';
            }

            array_push($params, $orCriteria[2]);
        }

        $this->ormObject->where_raw($clause, $params);
    }

    /**
     * Adds Criteria
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $func
     */
    protected function addCriteria(string $column, string $operator, $value, string $func)
    {
        if (is_array($value) && count($value) == 1 && key($value) == 'fn') {
            $this->ormObject->where_raw($column . ' ' . $operator . ' ' . $value['fn']);
        } else {
            $this->ormObject->$func($column, $value);
        }
    }

}