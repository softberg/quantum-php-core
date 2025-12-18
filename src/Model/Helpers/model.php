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

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Model\QtModel;

/**
 * Gets the model instance
 * @param string $modelClass
 * @return QtModel
 * @throws ModelException
 * @throws BaseException
 */
function model(string $modelClass): QtModel
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
 * @return QtModel
 */
function dynamicModel(
    string $table,
    string $modelName = '@anonymous',
    string $idColumn = 'id',
    array  $foreignKeys = [],
    array  $hidden = []
): QtModel
{
    return ModelFactory::createDynamicModel($table, $modelName, $idColumn, $foreignKeys, $hidden);
}

/**
 * Wraps the orm instance into model
 * @param DbalInterface $ormInstance
 * @param string $modelClass
 * @return QtModel
 * @throws BaseException
 */
function wrapToModel(DbalInterface $ormInstance, string $modelClass): QtModel
{
    if (!class_exists($modelClass)) {
        throw ModelException::notFound('Model class', $modelClass);
    }

    $model = new $modelClass();

    if (!$model instanceof QtModel) {
        throw ModelException::notInstanceOf($modelClass, QtModel::class);
    }

    $model->setOrmInstance($ormInstance);

    return $model;
}