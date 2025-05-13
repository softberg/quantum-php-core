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
 * @since 2.9.7
 */

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\QtModel;

/**
 * @param DbalInterface $ormInstance
 * @param string $modelClass
 * @return QtModel
 */
function wrapToModel(DbalInterface $ormInstance, string $modelClass): QtModel
{
    if (!class_exists($modelClass)) {
        throw new InvalidArgumentException("Model class '$modelClass' does not exist.");
    }

    $model = new $modelClass();

    if (!$model instanceof QtModel) {
        throw new InvalidArgumentException("Model class '$modelClass' must extend QtModel.");
    }

    $model->setOrmInstance($ormInstance);

    return $model;
}