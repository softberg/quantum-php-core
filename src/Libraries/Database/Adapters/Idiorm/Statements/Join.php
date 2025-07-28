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

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormPatch;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\QtModel;

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
    public function joinTo(QtModel $modelToJoin, bool $switch = true): DbalInterface
    {
        $foreignKeys = $modelToJoin->relations();
        $relatedModelName = $this->getModelName();

        if (!isset($foreignKeys[$relatedModelName])) {
            throw ModelException::wrongRelation(get_class($modelToJoin), $relatedModelName);
        }

        $this->getOrmModel()->join($modelToJoin->table,
            [
                $modelToJoin->table . '.' . $foreignKeys[$relatedModelName]['foreign_key'],
                '=',
                $this->table . '.' . $foreignKeys[$relatedModelName]['local_key']
            ]
        );

        if ($switch) {
            $this->modelName = get_class($modelToJoin);
            $this->table = $modelToJoin->table;
            $this->idColumn = $modelToJoin->idColumn;
            $this->foreignKeys = $foreignKeys;
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws ModelException
     */
    public function joinThrough(QtModel $modelToJoin, bool $switch = true): DbalInterface
    {
        $foreignKeys = $this->getForeignKeys();
        $relatedModelName = get_class($modelToJoin);

        if (!isset($foreignKeys[$relatedModelName])) {
            throw ModelException::wrongRelation($relatedModelName, $this->getModelName());
        }

        $this->getOrmModel()->join($modelToJoin->table,
            [
                $modelToJoin->table . '.' . $foreignKeys[$relatedModelName]['local_key'],
                '=',
                $this->table . '.' .  $foreignKeys[$relatedModelName]['foreign_key'],
            ]
        );

        if ($switch) {
            $this->modelName = get_class($modelToJoin);
            $this->table = $modelToJoin->table;
            $this->idColumn = $modelToJoin->idColumn;
            $this->foreignKeys = $modelToJoin->relations();
        }

        return $this;
    }
}