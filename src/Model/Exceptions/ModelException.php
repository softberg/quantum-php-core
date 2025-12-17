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
        return new static(
            _message(ExceptionMessages::INAPPROPRIATE_MODEL_PROPERTY, $name),
            E_WARNING
        );
    }

    /**
     * @param string $modelName
     * @param string $tableName
     * @return ModelException
     */
    public static function wrongRelation(string $modelName, string $tableName): ModelException
    {
        return new static(
            _message(ExceptionMessages::WRONG_RELATION, [$modelName, $tableName]),
            E_ERROR
        );
    }

    /**
     * @param string $modelName
     * @param string $relatedModelName
     * @return ModelException
     */
    public static function relationTypeMissing(string $modelName, string $relatedModelName): ModelException
    {
        return new static(
            _message(ExceptionMessages::RELATION_TYPE_MISSING, [$modelName, $relatedModelName]),
            E_ERROR
        );
    }

    /**
     * @param string $modelName
     * @param string $relatedModelName
     * @return ModelException
     */
    public static function missingRelationKeys(string $modelName, string $relatedModelName): ModelException
    {
        return new static(
            _message(ExceptionMessages::MISSING_RELATION_KEYS, [$modelName, $relatedModelName]),
            E_ERROR
        );
    }

    /**
     * @param string $modelName
     * @param string $foreignKey
     * @return ModelException
     */
    public static function missingForeignKeyValue(string $modelName, string $foreignKey): ModelException
    {
        return new static(
            _message(ExceptionMessages::MISSING_FOREIGN_KEY, [$foreignKey, $modelName]),
            E_ERROR
        );
    }

    /**
     * @param string $relationType
     * @return ModelException
     */
    public static function unsupportedRelationType(string $relationType): ModelException
    {
        return new static(
            _message(ExceptionMessages::UNSUPPORTED_RELATION, [$relationType]),
            E_ERROR
        );
    }
}