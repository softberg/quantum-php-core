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
 * @since 2.5.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ModelException;
use Quantum\Loader\Loader;
use Quantum\Mvc\QtModel;
use Quantum\Di\Di;

/**
 * Class ModelFactory
 * @package Quantum\Factory
 */
class ModelFactory
{

    /**
     * Get Model
     * @param string $modelClass
     * @return \Quantum\Mvc\QtModel
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ModelException
     * @throws \ReflectionException
     */
    public function get(string $modelClass): QtModel
    {
        if (!class_exists($modelClass)) {
            throw ModelException::notFound($modelClass);
        }

        $model = new $modelClass(Di::get(Loader::class));

        if (!$model instanceof QtModel) {
            throw ModelException::notModelInstance([$modelClass, QtModel::class]);
        }

        return $model;
    }

}
