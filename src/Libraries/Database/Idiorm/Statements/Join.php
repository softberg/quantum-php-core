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
use Quantum\Libraries\Database\DbalInterface;
use Quantum\Mvc\QtModel;

/**
 * Trait Join
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Join
{

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function join(string $table, array $constraint, string $tableAlias = null): DbalInterface
    {
         $this->getOrmModel()->join($table, $constraint, $tableAlias);
         return $this;
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        $this->getOrmModel()->inner_join($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()->use($this->getOrmModel())->leftJoin($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()->use($this->getOrmModel())->rightJoin($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function joinTo(QtModel $model, bool $switch = true): DbalInterface
    {
        $this->getOrmModel()->join($model->table,
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

        return $this;
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     */
    public function joinThrough(QtModel $model, bool $switch = true): DbalInterface
    {
        $this->getOrmModel()->join($model->table,
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

        return $this;
    }

}