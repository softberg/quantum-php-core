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

namespace Quantum\Libraries\Database\Sleekdb\Statements;

use Quantum\Libraries\Database\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\DbalInterface;
use SleekDB\QueryBuilder;
use Quantum\Mvc\QtModel;

/**
 * Trait Join
 * @package Quantum\Libraries\Database\Idiorm\Statements
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
     * @param \Quantum\Mvc\QtModel $model
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
     */
    private function applyJoins()
    {
        if (isset($this->joins[0])) {
            $this->applyJoin($this->queryBuilder, $this, $this->joins[0]);
        }
    }

    /**
     * Apply the join to query builder
     * @param \SleekDB\QueryBuilder $queryBuilder
     * @param \Quantum\Libraries\Database\Sleekdb\SleekDbal $currentItem
     * @param array $nextItem
     * @param int $level
     * @return \SleekDB\QueryBuilder
     */
    private function applyJoin(QueryBuilder $queryBuilder, SleekDbal $currentItem, array $nextItem, int $level = 1): QueryBuilder
    {
        $modelToJoin = unserialize($nextItem['model']);
        $switch = $nextItem['switch'];
        $joinType = $nextItem['type'];

        $queryBuilder->join(function ($item) use ($currentItem, $modelToJoin, $switch, $joinType, $level) {

            $newQueryBuilder = (new self($modelToJoin->table))->getOrmModel()->createQueryBuilder();

            if ($joinType == self::JOINTO) {
                $newQueryBuilder->where([
                    $modelToJoin->foreignKeys[$currentItem->table],
                    '=',
                    $item[$currentItem->idColumn]
                ]);
            } else if ($joinType == self::JOINTHROUGH) {
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