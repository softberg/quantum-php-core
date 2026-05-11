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

namespace Quantum\Database\Adapters\Sleekdb\Statements;

use SleekDB\Exceptions\InvalidConfigurationException;
use Quantum\Database\Exceptions\DatabaseException;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use SleekDB\Exceptions\IOException;

/**
 * Trait Result
 * @package Quantum\Database
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
            $results = $this->fetchFilteredResults();

            return array_map(function ($element): object {
                $item = clone $this;
                $item->updateOrmModel($element);
                return $item;
            }, $results);
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
            $this->getBuilder()->where(['id', '=', $id]);
            $results = $this->fetchFilteredResults();
            $result = $results[0] ?? [];
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
            $this->getBuilder()->where([$column, '=', $value]);
            $results = $this->fetchFilteredResults();
            $result = $results[0] ?? [];
            $this->updateOrmModel($result);
        } finally {
            $this->resetBuilderState();
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws ModelException|DatabaseException|BaseException|IOException|InvalidArgumentException|InvalidConfigurationException
     */
    public function first(): DbalInterface
    {
        try {
            $results = $this->fetchFilteredResults();
            $result = $results[0] ?? [];
            $this->updateOrmModel($result);
        } finally {
            $this->resetBuilderState();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        try {
            $results = $this->fetchFilteredResults();
            return count($results);
        } finally {
            $this->resetBuilderState();
        }
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
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    public function setHidden(array $result): array
    {
        return array_diff_key($result, array_flip($this->hidden));
    }

    /**
     * Applies adapter-specific post-fetch filters when available (SleekDB only).
     * @param array<int, array<string, mixed>> $results
     * @return array<int, array<string, mixed>>
     */
    protected function applyPostFetchFilters(array $results): array
    {
        return $this->applyRelatedCriteriaPostFilter($results);
    }

    /**
     * Fetches current query results and applies post-fetch filters.
     * @return array<int, array<string, mixed>>
     * @throws ModelException|DatabaseException|BaseException|IOException|InvalidArgumentException|InvalidConfigurationException
     */
    protected function fetchFilteredResults(): array
    {
        return $this->applyPostFetchFilters($this->getBuilder()->getQuery()->fetch());
    }
}
