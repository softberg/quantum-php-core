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

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormPatch;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Enums\Relation;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\DbModel;

/**
 * Trait Join
 * @package Quantum\Libraries\Database
 */
trait Join
{
    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function join(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        $this->getOrmModel()->join($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        $this->getOrmModel()->inner_join($table, $constraint, $tableAlias);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()
            ->use($this->getOrmModel())
            ->leftJoin($table, $constraint, $tableAlias);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): DbalInterface
    {
        IdiormPatch::getInstance()
            ->use($this->getOrmModel())
            ->rightJoin($table, $constraint, $tableAlias);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws BaseException
     * @throws ModelException
     */
    public function joinTo(DbModel $relatedModel, bool $switch = true): DbalInterface
    {
        $relation = $this->getValidatedRelation($relatedModel);

        switch ($relation['type']) {
            case Relation::HAS_ONE:
            case Relation::HAS_MANY:
                $this->applyHasRelation($relatedModel, $relation);
                break;

            case Relation::BELONGS_TO:
                $this->applyBelongsTo($relatedModel, $relation);
                break;

            default:
                throw ModelException::unsupportedRelationType($relation['type']);
        }

        if ($switch) {
            $this->modelName = get_class($relatedModel);
            $this->table = $relatedModel->table;
            $this->idColumn = $relatedModel->idColumn;
            $this->foreignKeys = $relatedModel->relations();
        }

        return $this;
    }

    /**
     * @param DbModel $relatedModel
     * @param array $relation
     * @return void
     * @throws BaseException
     */
    protected function applyHasRelation(DbModel $relatedModel, array $relation): void
    {
        $this->getOrmModel()->join(
            $relatedModel->table,
            [
                $relatedModel->table . '.' . $relation['foreign_key'],
                '=',
                $this->table . '.' . $relation['local_key'],
            ]
        );
    }

    /**
     * @param DbModel $relatedModel
     * @param array $relation
     * @return void
     * @throws BaseException
     */
    protected function applyBelongsTo(DbModel $relatedModel, array $relation): void
    {
        $this->getOrmModel()->join(
            $relatedModel->table,
            [
                $relatedModel->table . '.' . $relation['local_key'],
                '=',
                $this->table . '.' . $relation['foreign_key'],
            ]
        );
    }

    /**
     * @param DbModel $modelToJoin
     * @return array
     * @throws ModelException
     */
    private function getValidatedRelation(DbModel $modelToJoin): array
    {
        $relations = $this->getForeignKeys();
        $relatedModelName = get_class($modelToJoin);

        if (!isset($relations[$relatedModelName])) {
            throw ModelException::wrongRelation($this->getModelName(), $relatedModelName);
        }

        $relation = $relations[$relatedModelName];

        if (empty($relation['type'])) {
            throw ModelException::relationTypeMissing($this->getModelName(), $relatedModelName);
        }

        if (empty($relation['foreign_key']) || empty($relation['local_key'])) {
            throw ModelException::missingRelationKeys($this->getModelName(), $relatedModelName);
        }

        return $relation;
    }
}
