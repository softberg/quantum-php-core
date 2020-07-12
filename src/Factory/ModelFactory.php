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
 * @since 2.0.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\ModelException;
use Quantum\Helpers\Helper;
use Quantum\Mvc\QtModel;

/**
 * ModelFactory Class
 *
 * @package Quantum
 * @category Factory
 */
Class ModelFactory
{

    /**
     * Get Model
     *
     * @param string $modelClass
     * @return object
     * @throws \Exception
     */
    public function get($modelClass): QtModel
    {
        if (!class_exists($modelClass)) {
            throw new ModelException(Helper::_message(ExceptionMessages::MODEL_NOT_FOUND, $modelClass));
        }

        $model = new $modelClass();

        if (!$model instanceof QtModel) {
            throw new ModelException(Helper::_message(ExceptionMessages::NOT_INSTANCE_OF_MODEL, [$modelClass, QtModel::class]));
        }

        return $model;
    }

}
