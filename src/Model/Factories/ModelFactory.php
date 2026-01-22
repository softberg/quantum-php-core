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

namespace Quantum\Model\Factories;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Libraries\Database\Database;
use Quantum\Model\DbModel;
use Quantum\Model\Model;

/**
 * Class ModelFactory
 * @package Quantum\Model
 */
class ModelFactory
{
    /**
     * Gets the Model
     * @param string $modelClass
     * @return Model
     * @throws ModelException
     */
    public static function get(string $modelClass): Model
    {
        if (!class_exists($modelClass)) {
            throw ModelException::notFound('Model', $modelClass);
        }

        $model = new $modelClass();

        if (!$model instanceof Model) {
            throw ModelException::notInstanceOf($modelClass, Model::class);
        }

        if ($model instanceof DbModel) {
            $ormInstance = self::createOrmInstance(
                $model->table,
                $modelClass,
                $model->idColumn,
                $model->relations(),
                $model->hidden ?? []
            );

            $model->setOrmInstance($ormInstance);
        }

        return $model;
    }

    /**
     * Creates anonymous dynamic model
     * @param string $table
     * @param string $modelName
     * @param string $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return DbModel
     */
    public static function createDynamicModel(
        string $table,
        string $modelName = '@anonymous',
        string $idColumn = 'id',
        array  $foreignKeys = [],
        array  $hidden = []
    ): DbModel {
        $model = new class () extends DbModel {};

        $ormInstance = self::createOrmInstance(
            $table,
            $modelName,
            $idColumn,
            $foreignKeys,
            $hidden
        );

        $model->setOrmInstance($ormInstance);

        return $model;
    }

    /**
     * @param string $table
     * @param string $modelName
     * @param string $idColumn
     * @param array $foreignKeys
     * @param array $hidden
     * @return DbalInterface
     */
    protected static function createOrmInstance(
        string $table,
        string $modelName,
        string $idColumn,
        array  $foreignKeys = [],
        array  $hidden = []
    ): DbalInterface {
        $ormClass = Database::getInstance()->getOrmClass();

        return new $ormClass(
            $table,
            $modelName,
            $idColumn,
            $foreignKeys,
            $hidden
        );
    }
}
