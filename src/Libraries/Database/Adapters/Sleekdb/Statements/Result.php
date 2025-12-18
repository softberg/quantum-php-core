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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use SleekDB\Exceptions\IOException;

/**
 * Trait Result
 * @package Quantum\Libraries\Database
 */
trait Result
{

    abstract protected function resetBuilderState(): void;

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        try {
            return array_map(function ($element) {
                $item = clone $this;
                $item->updateOrmModel($element);
                return $item;
            }, $this->getBuilder()->getQuery()->fetch());
        } finally {
            $this->resetBuilderState();
        }
    }

    /**
     * @inheritDoc
     * @throws BaseException
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function findOne(int $id): DbalInterface
    {
        try {
            $result = $this->getBuilder()->where(['id', '=', $id])->getQuery()->first();
            $this->updateOrmModel($result);
        } finally {
            $this->resetBuilderState();
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function findOneBy(string $column, $value): DbalInterface
    {
        try {
            $result = $this->getBuilder()->where([$column, '=', $value])->getQuery()->first();
            $this->updateOrmModel($result);
        } finally {
            $this->resetBuilderState();
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function first(): DbalInterface
    {
        try {
            $result = $this->getBuilder()->getQuery()->first();
            $this->updateOrmModel($result);
        } finally {
            $this->resetBuilderState();
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     * @throws BaseException
     */
    public function count(): int
    {
        return count($this->getBuilder()->getQuery()->fetch());
    }

    /**
     * @inheritDoc
     */
    public function asArray(): array
    {
        $result = $this->data ?: [];

        if (count($this->hidden) > 0 && count($result)) {
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