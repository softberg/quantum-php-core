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

namespace Quantum\Model\Factories;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Database\Database;
use Quantum\Model\QtModel;

/**
 * Class ModelFactory
 * @package Quantum\Model
 */
class ModelFactory
{

    /**
     * Gets the Model
     * @param string $modelClass
     * @return QtModel
     * @throws ModelException
     * @throws BaseException
     */
    public static function get(string $modelClass): QtModel
    {
        if (!class_exists($modelClass)) {
            throw ModelException::notFound('Model', $modelClass);
        }

        $model = new $modelClass();

        if (!$model instanceof QtModel) {
            throw ModelException::notInstanceOf($modelClass, QtModel::class);
        }

        $ormInstance = self::createOrmInstance(
            $model->table,
            $modelClass,
            $model->idColumn,
            $model->relations(),
            $model->hidden ?? []
        );

        $model->setOrmInstance($ormInstance);

        return $model;
    }

    /**
     * Creates anonymous dynamic model
     * @param string $table
     * @param string $modelName
     * @param string|null $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return QtModel
     */
    public static function createDynamicModel(
        string $table,
        string $modelName = '@anonymous',
        string $idColumn = 'id',
        array  $foreignKeys = [],
        array  $hidden = []
    ): QtModel
    {
        $model = new class extends QtModel {};

        $ormInstance = self::createOrmInstance($table, $modelName, $idColumn, $foreignKeys, $hidden);

        $model->setOrmInstance($ormInstance);

        return $model;
    }

    /**
     * @param string $table
     * @param string|null $modelName
     * @param string $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return mixed
     */
    protected static function createOrmInstance(
        string $table,
        string $modelName,
        string $idColumn,
        array  $foreignKeys = [],
        array  $hidden = []
    ): DbalInterface
    {
        $ormClass = Database::getInstance()->getOrmClass();

        return new $ormClass($table, $modelName, $idColumn, $foreignKeys, $hidden);
    }
}