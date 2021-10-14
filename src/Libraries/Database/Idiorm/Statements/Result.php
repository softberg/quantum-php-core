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

namespace Quantum\Libraries\Database\Idiorm\Statements;

/**
 * Trait Result
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Result
{

    /**
     * @inheritDoc
     */
    public function findOne(int $id): object
    {
        $result = $this->getOrmModel()->find_one($id);
        return $result ?: $this->getOrmModel();
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(string $column, $value): object
    {
        $result = $this->getOrmModel()->where($column, $value)->find_one();
        return $result ?: $this->getOrmModel();
    }

    /**
     * @inheritDoc
     */
    public function first(): object
    {
        $result = $this->getOrmModel()->find_one();
        return $result ?: $this->getOrmModel();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->getOrmModel()->count();
    }

    /**
     * @inheritDoc
     */
    public function asArray(): array
    {
        return $this->getOrmModel()->as_array();
    }

}