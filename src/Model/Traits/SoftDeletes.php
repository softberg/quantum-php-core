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
 * @since 3.0.0
 */

namespace Quantum\Model\Traits;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\ModelCollection;
use Quantum\Paginator\Paginator;
use Quantum\Model\DbModel;

/**
 * Trait SoftDeletes
 * @package Quantum\Model
 */
trait SoftDeletes
{
    /**
     * @var bool
     */
    protected bool $includeTrashed = false;

    /**
     * Soft delete the model by setting the deleted_at timestamp.
     * @return bool
     * @throws ModelException
     */
    public function delete(): bool
    {
        $this->prop($this->getDeleteAtColumn(), date('Y-m-d H:i:s'));
        return $this->save();

    }

    /**
     * Restore a soft deleted model.
     * @return bool
     * @throws ModelException
     */
    public function restore(): bool
    {
        $this->prop($this->getDeleteAtColumn(), null);
        return $this->save();
    }

    /**
     * Force delete the model from the database.
     * @return bool
     * @throws ModelException
     */
    public function forceDelete(): bool
    {
        return parent::delete();
    }

    /**
     * Include soft deleted records in the query.
     * @return static
     */
    public function withTrashed(): self
    {
        $this->includeTrashed = true;

        return $this;
    }

    /**
     * Return only soft deleted records.
     * @return static
     * @throws ModelException
     */
    public function onlyTrashed(): self
    {
        $this->includeTrashed = true;

        $this->getOrmInstance()->isNotNull($this->getDeleteAtColumn());

        return $this;
    }

    /**
     * Get all non-deleted records unless withTrashed is called.
     * @return ModelCollection
     * @throws BaseException
     */
    public function get(): ModelCollection
    {
        $this->applySoftDeleteScope();

        return parent::get();
    }

    /**
     * Paginate non-deleted records unless withTrashed is called.
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    public function paginate(int $perPage, int $currentPage = 1): Paginator
    {
        $this->applySoftDeleteScope();

        return parent::paginate($perPage, $currentPage);
    }

    /**
     * Count all non-deleted records unless withTrashed() is called.
     * @return int
     * @throws ModelException
     */
    public function count(): int
    {
        $this->applySoftDeleteScope();

        return parent::count();
    }

    /**
     * Find one record by its ID, excluding soft deleted unless withTrashed() is called.
     * @param int $id
     * @return DbModel|null
     * @throws BaseException
     */
    public function findOne(int $id): ?DbModel
    {
        $this->applySoftDeleteScope();

        return parent::findOne($id);
    }

    /**
     * Find one record by column and value, excluding soft deleted unless withTrashed() is called.
     * @param string $column
     * @param $value
     * @return DbModel|null
     * @throws BaseException
     */
    public function findOneBy(string $column, $value): ?DbModel
    {
        $this->applySoftDeleteScope();

        return parent::findOneBy($column, $value);
    }

    /**
     * Get the first record, excluding soft deleted unless withTrashed() is called.
     * @return DbModel|null
     * @throws BaseException
     */
    public function first(): ?DbModel
    {
        $this->applySoftDeleteScope();

        return parent::first();
    }

    /**
     * Apply soft delete scope to the current query if not including trashed.
     * @throws ModelException
     */
    protected function applySoftDeleteScope(): void
    {
        if (!$this->includeTrashed) {
            $this->getOrmInstance()->isNull($this->getDeleteAtColumn());
        }
    }

    /**
     * Get the column name used for soft deletes.
     * @return string
     */
    protected function getDeleteAtColumn(): string
    {
        if (defined(static::class . '::DELETED_AT')) {
            return static::DELETED_AT;
        }

        return 'deleted_at';
    }
}
