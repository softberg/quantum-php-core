<?php

declare(strict_types=1);

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

namespace Quantum\Database\Adapters\Idiorm\Statements;

use Quantum\Database\Exceptions\DatabaseException;
use Quantum\Database\Contracts\DbalInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Trait Result
 * @package Quantum\Database
 */
trait Result
{
    /**
     * @inheritDoc
     * @throws DatabaseException|BaseException
     */
    public function get(): array
    {
        return array_map(function ($element): object {
            $item = clone $this;
            $item->updateOrmModel($element);
            return $item;
        }, $this->getOrmModel()->find_many());
    }

    /**
     * @inheritDoc
     * @throws DatabaseException|BaseException
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
     * @throws DatabaseException|BaseException
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
     * @throws DatabaseException|BaseException
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
     * @throws DatabaseException|BaseException
     */
    public function count(): int
    {
        return $this->getOrmModel()->count();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException|BaseException
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
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    public function setHidden($result): array
    {
        return array_diff_key($result, array_flip($this->hidden));
    }
}
