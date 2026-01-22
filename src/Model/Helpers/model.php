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

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Model\DbModel;
use Quantum\Model\Model;

/**
 * Gets the model instance
 * @template T of Model
 * @param class-string<T> $modelClass
 * @return T
 * @throws ModelException
 */
function model(string $modelClass): Model
{
    return ModelFactory::get($modelClass);
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
function dynamicModel(
    string $table,
    string $modelName = '@anonymous',
    string $idColumn = 'id',
    array  $foreignKeys = [],
    array  $hidden = []
): DbModel {
    return ModelFactory::createDynamicModel(
        $table,
        $modelName,
        $idColumn,
        $foreignKeys,
        $hidden
    );
}

/**
 * Wraps the orm instance into model
 * @param DbalInterface $ormInstance
 * @param string $modelClass
 * @return DbModel
 * @throws BaseException
 */
function wrapToModel(DbalInterface $ormInstance, string $modelClass): DbModel
{
    if (!class_exists($modelClass)) {
        throw ModelException::notFound('Model class', $modelClass);
    }

    $model = new $modelClass();

    if (!$model instanceof DbModel) {
        throw ModelException::notInstanceOf($modelClass, DbModel::class);
    }

    $model->setOrmInstance($ormInstance);
    $model->hydrateFromOrm($ormInstance->asArray());

    return $model;
}
