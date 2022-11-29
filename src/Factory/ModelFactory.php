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
 * @since 2.8.0
 */

namespace Quantum\Factory;

use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\ModelException;
use Quantum\Loader\Setup;
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
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ModelException
     * @throws \ReflectionException
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

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database'));
        }

        $model->setOrm(Database::getInstance()->getOrm($model->table, $model->idColumn, $model->foreignKeys ?? []));

        return $model;
    }

}
