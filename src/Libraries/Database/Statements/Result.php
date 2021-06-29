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
 * Trait Result
 * @package Quantum\Libraries\Database\Statements
 */
trait Result
{
    /**
     * Finds the record by primary key
     * @inheritDoc
     */
    public function findOne(int $id): object
    {
        $result = $this->ormObject->find_one($id);
        return $result ?: $this->ormObject();
    }

    /**
     * Finds the record by given column and value
     * @inheritDoc
     */
    public function findOneBy(string $column, $value): object
    {
        $result = $this->ormObject->where($column, $value)->find_one();
        return $result ?: $this->ormObject();
    }

    /**
     * Gets the first item
     * @inheritDoc
     */
    public function first(): object
    {
        $result = $this->ormObject->find_one();
        return $result ?: $this->ormObject();
    }

    /**
     * Counts the result set
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->ormObject->count();
    }

    /**
     * Returns the result as array
     * @inheritDoc
     */
    public function asArray(): array
    {
        return $this->ormObject->as_array();
    }

}