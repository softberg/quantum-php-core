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

namespace Quantum\Model\Exceptions;

use Quantum\Exceptions\BaseException;

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
    public static function notFound(string $name): ModelException
    {
        return new static(t('exception.model_not_found', $name), E_ERROR);
    }

    /**
     * @param array $names
     * @return ModelException
     */
    public static function notModelInstance(array $names): ModelException
    {
        return new static(t('exception.not_instance_of_model', $names), E_WARNING);
    }

    /**
     * @param string|null $name
     * @return ModelException
     */
    public static function noTableDefined(?string $name): ModelException
    {
        return new static(t('exception.model_without_table_defined', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return ModelException
     */
    public static function undefinedMethod(string $name): ModelException
    {
        return new static(t('exception.undefined_model_method', $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return ModelException
     */
    public static function inappropriateProperty(string $name): ModelException
    {
        return new static(t('exception.inappropriate_property', $name), E_WARNING);
    }

    /**
     * @param string $modelName
     * @param string $tableName
     * @return ModelException
     */
    public static function wrongRelation(string $modelName, string $tableName): ModelException
    {
        return new static(t('exception.wrong_relation', [$modelName, $tableName]), E_ERROR);
    }
}
