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
 * @since 1.6.0
 */

namespace Quantum\Factory;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Mvc\Qt_Model;

/**
 * ModelFactory Class
 *
 * @package Quantum
 * @category Factory
 */
Class ModelFactory extends Factory
{

    /**
     * Get Model
     *
     * @param string $modelClass
     * @return object
     * @throws \Exception
     */
    public function get($modelClass)
    {
        $exceptions = [
            ExceptionMessages::MODEL_NOT_FOUND,
            ExceptionMessages::NOT_INSTANCE_OF_MODEL
        ];

        return parent::get($modelClass, Qt_Model::class, $exceptions);
    }

}
