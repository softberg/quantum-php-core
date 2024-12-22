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

namespace Quantum\Libraries\Database\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Idiorm\IdiormPatch;
use Quantum\Mvc\QtModel;

/**
 * Trait Join
 * @package Quantum\Libraries\Database
 */
trait Join
{

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function join(string $table, array $constraint, string $tableAlias = null): DbalInterface
    {
        $this->getOrmModel()->join($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        $this->getOrmModel()->inner_join($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()->use($this->getOrmModel())->leftJoin($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()->use($this->getOrmModel())->rightJoin($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws ModelException
     */
    public function joinTo(QtModel $model, bool $switch = true): DbalInterface
    {
        if (!isset($model->foreignKeys[$this->table])) {
            throw ModelException::wrongRelation(get_class($model), $this->table);
        }

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
     * @throws DatabaseException
     * @throws ModelException
     */
    public function joinThrough(QtModel $model, bool $switch = true): DbalInterface
    {
        if (!isset($this->foreignKeys[$model->table])) {
            throw ModelException::wrongRelation(get_class($model), $this->table);
        }

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
