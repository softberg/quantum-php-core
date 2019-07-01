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
 * @since 1.5.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Mvc\Qt_Model;

/**
 * Factory Trait
 *
 * @package Quantum
 * @category Factory
 */
trait Factory
{
    /**
     * Model Factory
     *
     * Deliver an object of request model
     *
     * @param string $modelName
     * @param string $module
     * @return Qt_Model
     * @throws \Exception When model is not istance of Qt_Model
     */
    public function modelFactory($modelName, $module = null)
    {
        $modelClass = $this->findModelFile($modelName, $module);

        $model = new $modelClass();

        if ($model instanceof Qt_Model) {
            return $model;
        } else {
            throw new \Exception(_message(ExceptionMessages::NOT_INSTANCEE_OF_MODEL, [$modelName, Qt_Model::class]));
        }
    }
}