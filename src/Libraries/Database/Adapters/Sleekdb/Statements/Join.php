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

use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\QtModel;
use SleekDB\QueryBuilder;

/**
 * Trait Join
 * @package Quantum\Libraries\Database
 */
trait Join
{

    /**
     * @inheritDoc
     */
    public function joinTo(QtModel $model, bool $switch = true): DbalInterface
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
    private function applyJoins()
    {
        if (!empty($this->joins)) {
            $this->applyJoin($this->queryBuilder, $this, $this->joins[0]);
        }
    }

    /**
     * Apply the join to query builder
     * @param QueryBuilder $queryBuilder
     * @param SleekDbal $currentItem
     * @param array $nextItem
     * @param int $level
     * @return QueryBuilder
     * @throws ModelException
     */
    private function applyJoin(QueryBuilder $queryBuilder, SleekDbal $currentItem, array $nextItem, int $level = 1): QueryBuilder
    {
        $modelToJoin = unserialize($nextItem['model']);
        $switch = $nextItem['switch'];

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
     * @param QueryBuilder $queryBuilder
     * @param QtModel $relatedModel
     * @param SleekDbal $currentModel
     * @param array $currentItem
     * @return void
     * @throws InvalidArgumentException
     * @throws ModelException
     */
    private function applyJoinTo(QueryBuilder $queryBuilder, QtModel $relatedModel, SleekDbal $currentModel, array $currentItem): void
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
     * @param QueryBuilder $queryBuilder
     * @param array $currentItem
     * @param array $relation
     * @return void
     * @throws InvalidArgumentException
     */
    private function applyHasRelation(QueryBuilder $queryBuilder, array $currentItem, array $relation): void
    {
        $queryBuilder->where([
            $relation['foreign_key'],
            '=',
            $currentItem[$relation['local_key']]
        ]);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $currentItem
     * @param array $relation
     * @param SleekDbal $currentModel
     * @return void
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
            $currentItem[$relation['foreign_key']]
        ]);
    }

    /**
     * @param SleekDbal $currentModel
     * @param QtModel $relatedModel
     * @return array
     * @throws ModelException
     */
    private function getValidatedRelation(SleekDbal $currentModel, QtModel $relatedModel): array
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