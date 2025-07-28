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
 * @since 2.9.8
 */

namespace Quantum\Libraries\Database\Adapters\Sleekdb\Statements;

use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use SleekDB\Exceptions\InvalidArgumentException;
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
        $this->addJoin(__FUNCTION__, $model, $switch);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function joinThrough(QtModel $model, bool $switch = true): DbalInterface
    {
        $this->addJoin(__FUNCTION__, $model, $switch);
        return $this;
    }

    /**
     * Adds join
     * @param string $type
     * @param QtModel $model
     * @param bool $switch
     */
    private function addJoin(string $type, QtModel $model, bool $switch = true)
    {
        $this->joins[] = [
            'type' => $type,
            'model' => serialize($model),
            'switch' => $switch,
        ];
    }

    /**
     * Starts to apply joins
     * @throws ModelException
     */
    private function applyJoins()
    {
        if (isset($this->joins[0])) {
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
        $joinType = $nextItem['type'];

        $queryBuilder->join(function ($item) use ($currentItem, $modelToJoin, $switch, $joinType, $level) {

            $sleekModel = new self($modelToJoin->table, get_class($modelToJoin), $modelToJoin->idColumn, $modelToJoin->relations());

            $newQueryBuilder = $sleekModel->getOrmModel()->createQueryBuilder();

            if ($joinType == self::JOINTO) {
                $this->applyJoinTo($newQueryBuilder, $modelToJoin, $currentItem, $item);
            } else if ($joinType == self::JOINTHROUGH) {
                $this->applyJoinThrough($newQueryBuilder, $modelToJoin, $currentItem, $item);
            }

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
     * @param QtModel $modelToJoin
     * @param SleekDbal $currentItem
     * @param array $item
     * @return void
     * @throws InvalidArgumentException
     * @throws ModelException
     */
    private function applyJoinTo(QueryBuilder $queryBuilder, QtModel $modelToJoin, SleekDbal $currentItem, array $item): void
    {
        $foreignKeys = $modelToJoin->relations();
        $relatedModelName = $currentItem->getModelName();

        if (!isset($foreignKeys[$relatedModelName])) {
            throw ModelException::wrongRelation(get_class($modelToJoin), $relatedModelName);
        }

        $queryBuilder->where([
            $foreignKeys[$relatedModelName]['foreign_key'],
            '=',
            $item[$foreignKeys[$relatedModelName]['local_key']]
        ]);
    }

    /**
     * Apply join condition for JOINTHROUGH type
     * @param QueryBuilder $queryBuilder
     * @param QtModel $modelToJoin
     * @param SleekDbal $currentItem
     * @param array $item
     * @return void
     * @throws ModelException
     * @throws InvalidArgumentException
     */
    private function applyJoinThrough(QueryBuilder $queryBuilder, QtModel $modelToJoin, SleekDbal $currentItem, array $item): void
    {
        $foreignKeys = $currentItem->getForeignKeys();
        $relatedModelName = get_class($modelToJoin);

        if (!isset($foreignKeys[$relatedModelName])) {
            throw ModelException::wrongRelation($relatedModelName, $currentItem->getModelName());
        }

        $queryBuilder->where([
            $foreignKeys[$relatedModelName]['local_key'],
            '=',
            $item[$foreignKeys[$relatedModelName]['foreign_key']]
        ]);
    }
}