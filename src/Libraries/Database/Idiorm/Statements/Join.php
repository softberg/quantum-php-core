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

namespace Quantum\Libraries\Database\Idiorm\Statements;

use Quantum\Libraries\Database\Idiorm\IdiormPatch;
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
    public function join(string $table, array $constraint, string $tableAlias = null): object
    {
        return $this->getOrmModel()->join($table, $constraint, $tableAlias);
    }

    /**
     * @inheritDoc
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        return $this->getOrmModel()->inner_join($table, $constraint, $tableAlias);
    }

    /**
     * @inheritDoc
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        return IdiormPatch::getInstance()->use($this->getOrmModel())->leftJoin($table, $constraint, $tableAlias);
    }

    /**
     * @inheritDoc
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        return IdiormPatch::getInstance()->use($this->getOrmModel())->rightJoin($table, $constraint, $tableAlias);
    }

    /**
     * @inheritDoc
     */
    public function joinTo(QtModel $model, bool $switch = true): object
    {
        $resultObject = $this->getOrmModel()->join($model->table,
            [
                $model->table . '.' . $model->foreignKeys[$this->table],
                '=',
                $this->table . '.' . $this->idColumn
            ]
        );

        if ($switch) {
            $this->table = $model->table;
            $this->idColumn = $model->idColumn;
            $this->foreignKeys = $model->foreignKeys;
        }

        return $resultObject;
    }

    /**
     * @inheritDoc
     */
    public function joinThrough(QtModel $model, bool $switch = true): object
    {
        $resultObject = $this->getOrmModel()->join($model->table,
            [
                $model->table . '.' . $model->idColumn,
                '=',
                $this->table . '.' . $this->foreignKeys[$model->table]
            ]
        );

        if ($switch) {
            $this->table = $model->table;
            $this->idColumn = $model->idColumn;
            $this->foreignKeys = $model->foreignKeys;
        }

        return $resultObject;
    }

}