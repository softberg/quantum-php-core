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
 * @since 2.4.0
 */
namespace Quantum\Libraries\Database\Statements;

use Quantum\Libraries\Database\IdiormPatch;
use Quantum\Mvc\QtModel;

/**
 * Trait Join
 * @package Quantum\Libraries\Database\Statements
 */
Trait Join
{

    /**
     * The database table associated with model
     * @var string
     */
    private $table;

    /**
     * Id column of table
     * @var string
     */
    private $idColumn;

    /**
     * Foreign keys
     * @var array
     */
    private $foreignKeys = [];

    /**
     * Idiorm Patch object
     * @var \Quantum\Libraries\Database\IdiormPatch
     */
    private $ormPatch = null;

    /**
     * Adds a simple JOIN source to the query
     * @inheritDoc
     */
    public function join(string $table, array $constraint, string $tableAlias = null): object
    {
        return $this->ormObject->join($table, $constraint, $tableAlias);
    }

    /**
     * Adds an INNER JOIN source to the query
     * @inheritDoc
     */
    public function innerJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        return $this->ormObject->inner_join($table, $constraint, $tableAlias);
    }

    /**
     * Adds an LEFT JOIN source to the query
     * @inheritDoc
     */
    public function leftJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        $this->ormPatch = IdiormPatch::getInstance()->setOrmObject($this->ormObject);
        return $this->ormPatch->left_join($table, $constraint, $tableAlias);
    }

    /**
     * Adds an RIGHT JOIN source to the query
     * @inheritDoc
     */
    public function rightJoin(string $table, array $constraint, ?string $tableAlias = null): object
    {
        $this->ormPatch = IdiormPatch::getInstance()->setOrmObject($this->ormObject);
        return $this->ormPatch->right_join($table, $constraint, $tableAlias);
    }

    /**
     * Joins two models
     * @inheritDoc
     */
    public function joinTo(QtModel $model, bool $switch = true): object
    {
        $resultObject = $this->ormObject->join($model->table,
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
     * Joins through connector model
     * @inheritDoc
     */
    public function joinThrough(QtModel $model, bool $switch = true): object
    {
        $resultObject = $this->ormObject->join($model->table,
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