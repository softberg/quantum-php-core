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
 * @since 2.9.6
 */

namespace Quantum\Factory;

use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Database;
use Quantum\Mvc\QtModel;

/**
 * Class ModelFactory
 * @package Quantum\Factory
 */
class ModelFactory
{

    /**
     * Gets the Model
     * @param string $modelClass
     * @return QtModel
     * @throws ModelException
     */
    public static function get(string $modelClass): QtModel
    {
        if (!class_exists($modelClass)) {
            throw ModelException::notFound($modelClass);
        }

        $model = new $modelClass();

        if (!$model instanceof QtModel) {
            throw ModelException::notModelInstance([$modelClass, QtModel::class]);
        }

        $model->setOrm(Database::getInstance()->getOrm($model->table, $model->idColumn, $model->foreignKeys ?? [], $model->hidden));

        return $model;
    }
}