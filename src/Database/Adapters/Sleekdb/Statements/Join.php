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

use Quantum\Database\Adapters\Sleekdb\SleekDbal;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Database\Enums\Relation;
use Quantum\Model\DbModel;
use SleekDB\QueryBuilder;
use RuntimeException;

/**
 * Trait Join
 * @package Quantum\Database
 */
trait Join
{
    /**
     * @inheritDoc
     */
    public function joinTo(DbModel $model, bool $switch = true): DbalInterface
    {
        $this->joins[] = [
            'model' => serialize($model),
            'switch' => $switch,
        ];

        return $this;
    }

    /**
     * Starts to apply joins
     * @throws ModelException
     */
    private function applyJoins(): void
    {
        if (!empty($this->joins)) {
            if ($this->queryBuilder === null) {
                throw new RuntimeException('Cannot apply joins without an initialized query builder.');
            }

            $this->applyJoin($this->queryBuilder, $this, $this->joins[0]);
        }
    }

    /**
     * Apply the join to query builder
     * @param array<string, mixed> $nextItem
     * @throws ModelException
     */
    private function applyJoin(QueryBuilder $queryBuilder, SleekDbal $currentItem, array $nextItem, int $level = 1): QueryBuilder
    {
        $modelToJoin = unserialize($nextItem['model']);
        $switch = $nextItem['switch'];

        if (!$modelToJoin instanceof DbModel) {
            throw new RuntimeException('Failed to unserialize join model.');
        }

        $queryBuilder->join(function ($item) use ($currentItem, $modelToJoin, $switch, $level) {

            $sleekModel = new self(
                $modelToJoin->table,
                get_class($modelToJoin),
                $modelToJoin->idColumn,
                $modelToJoin->relations()
            );

            $newQueryBuilder = $sleekModel->getOrmModel()->createQueryBuilder();

            $this->applyJoinTo($newQueryBuilder, $modelToJoin, $currentItem, $item);

            if ($switch && isset($this->joins[$level])) {
                $this->applyJoin($newQueryBuilder, $sleekModel, $this->joins[$level], $level + 1);
            }

            return $newQueryBuilder;

        }, $modelToJoin->table);

        if (!$switch && isset($this->joins[$level])) {
            $this->applyJoin($queryBuilder, $currentItem, $this->joins[$level], $level + 1);
        }

        return $queryBuilder;
    }

    /**
     * Apply join condition for JOINTO type
     * @param array<string, mixed> $currentItem
     * @throws InvalidArgumentException
     * @throws ModelException
     */
    private function applyJoinTo(QueryBuilder $queryBuilder, DbModel $relatedModel, SleekDbal $currentModel, array $currentItem): void
    {
        $relation = $this->getValidatedRelation($currentModel, $relatedModel);

        switch ($relation['type']) {
            case Relation::HAS_ONE:
            case Relation::HAS_MANY:
                $this->applyHasRelation($queryBuilder, $currentItem, $relation);
                break;

            case Relation::BELONGS_TO:
                $this->applyBelongsTo($queryBuilder, $currentItem, $relation, $currentModel);
                break;

            default:
                throw ModelException::unsupportedRelationType($relation['type']);
        }
    }

    /**
     * @param array<string, mixed> $currentItem
     * @param array<string, mixed> $relation
     * @throws InvalidArgumentException
     */
    private function applyHasRelation(QueryBuilder $queryBuilder, array $currentItem, array $relation): void
    {
        $queryBuilder->where([
            $relation['foreign_key'],
            '=',
            $currentItem[$relation['local_key']],
        ]);
    }

    /**
     * @param array<string, mixed> $currentItem
     * @param array<string, mixed> $relation
     * @throws InvalidArgumentException
     * @throws ModelException
     */
    private function applyBelongsTo(QueryBuilder $queryBuilder, array $currentItem, array $relation, SleekDbal $currentModel): void
    {
        if (!isset($currentItem[$relation['foreign_key']])) {
            throw ModelException::missingForeignKeyValue($currentModel->getModelName(), $relation['foreign_key']);
        }

        $queryBuilder->where([
            $relation['local_key'],
            '=',
            $currentItem[$relation['foreign_key']],
        ]);
    }

    /**
     * @return array<string, mixed>
     * @throws ModelException
     */
    private function getValidatedRelation(SleekDbal $currentModel, DbModel $relatedModel): array
    {
        $relations = $currentModel->getForeignKeys();
        $relatedModelName = get_class($relatedModel);

        if (!isset($relations[$relatedModelName])) {
            throw ModelException::wrongRelation($currentModel->getModelName(), $relatedModelName);
        }

        $relation = $relations[$relatedModelName];

        if (empty($relation['type'])) {
            throw ModelException::relationTypeMissing($currentModel->getModelName(), $relatedModelName);
        }

        if (empty($relation['foreign_key']) || empty($relation['local_key'])) {
            throw ModelException::missingRelationKeys($currentModel->getModelName(), $relatedModelName);
        }

        return $relation;
    }
}
