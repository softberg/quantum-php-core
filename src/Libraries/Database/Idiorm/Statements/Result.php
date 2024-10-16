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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Database\Idiorm\Statements;

use Quantum\Libraries\Database\PaginatorInterface;
use Quantum\Libraries\Database\Idiorm\Paginator;
use Quantum\Libraries\Database\DbalInterface;
use Quantum\Exceptions\DatabaseException;

/**
 * Trait Result
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Result
{

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function get()
    {
        return $this->getOrmModel()->find_many();
    }

    /**
     * @inheritDoc
     * @return PaginatorInterface
     * @throws DatabaseException
     */
    public function paginate(int $perPage, int $currentPage = 1): PaginatorInterface
    {
        return new Paginator($this, $perPage, $currentPage);
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function findOne(int $id): DbalInterface
    {
        $ormObject = $this->getOrmModel()->find_one($id);

        if ($ormObject) {
            $this->updateOrmModel($ormObject);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function findOneBy(string $column, $value): DbalInterface
    {
        $ormObject = $this->getOrmModel()->where($column, $value)->find_one();
        if ($ormObject) {
            $this->updateOrmModel($ormObject);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function first(): DbalInterface
    {
        $ormObject = $this->getOrmModel()->find_one();
        if ($ormObject) {
            $this->updateOrmModel($ormObject);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function count(): int
    {
        return $this->getOrmModel()->count();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function asArray(): array
    {
        $result = $this->getOrmModel()->as_array();

        if (count($this->hidden) > 0) {
            $result = $this->setHidden($result);
        }

        return $result;
    }

    /**
     * @param $result
     * @return array
     */
    public function setHidden($result): array
    {
        return array_diff_key($result, array_flip($this->hidden));
    }
}
