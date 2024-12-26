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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Database\Sleekdb\Statements;

use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Sleekdb\SleekDbal;
use Quantum\Mvc\QtModel;
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

            $newQueryBuilder = (new self($modelToJoin->table))->getOrmModel()->createQueryBuilder();

            if ($joinType == self::JOINTO) {
                if (!isset($modelToJoin->foreignKeys[$currentItem->table])) {
                    throw ModelException::wrongRelation(get_class($modelToJoin), $currentItem->table);
                }

                $newQueryBuilder->where([
                    $modelToJoin->foreignKeys[$currentItem->table],
                    '=',
                    $item[$currentItem->idColumn]
                ]);
            } else if ($joinType == self::JOINTHROUGH) {
                if (!isset($currentItem->foreignKeys[$modelToJoin->table])) {
                    throw ModelException::wrongRelation(get_class($modelToJoin), $currentItem->table);
                }

                $newQueryBuilder->where([
                    $modelToJoin->idColumn,
                    '=',
                    $item[$currentItem->foreignKeys[$modelToJoin->table]]
                ]);
            }

            if ($switch && isset($this->joins[$level])) {
                $sleekModel = new self($modelToJoin->table, $modelToJoin->idColumn, $modelToJoin->foreignKeys);
                $this->applyJoin($newQueryBuilder, $sleekModel, $this->joins[$level], ++$level);
            }

            return $newQueryBuilder;
            
        }, $modelToJoin->table);

        if (!$switch && isset($this->joins[$level])) {
            $this->applyJoin($queryBuilder, $currentItem, $this->joins[$level], ++$level);
        }

        return $queryBuilder;
    }

}
