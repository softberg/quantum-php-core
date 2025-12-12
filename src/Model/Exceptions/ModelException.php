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

namespace Quantum\Model\Exceptions;

use Quantum\Model\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class ModelException
 * @package Quantum\Model
 */
class ModelException extends BaseException
{

    /**
     * @param string $name
     * @return ModelException
     */
    public static function inappropriateProperty(string $name): ModelException
    {
        return new static(_message(ExceptionMessages::INAPPROPRIATE_MODEL_PROPERTY, $name), E_WARNING);
    }

    /**
     * @param string $modelName
     * @param string $tableName
     * @return ModelException
     */
    public static function wrongRelation(string $modelName, string $tableName): ModelException
    {
        return new static(_message(ExceptionMessages::WRONG_RELATION, [$modelName, $tableName]), E_ERROR);
    }
}
