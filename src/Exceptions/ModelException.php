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

namespace Quantum\Exceptions;

/**
 * Class ModelException
 * @package Quantum\Exceptions
 */
class ModelException extends \Exception
{
    /**
     * Model not found message
     */
    const MODEL_NOT_FOUND = 'Model `{%1}` not found';

    /**
     * Model not instance of QtModel
     */
    const NOT_INSTANCE_OF_MODEL = 'Model `{%1}` is not instance of `{%2}`';

    /**
     * Model does not have table property defined
     */
    const MODEL_WITHOUT_TABLE_DEFINED = 'Model `{%1}` does not have $table property defined';

    /**
     * Undefined model method
     */
    const UNDEFINED_MODEL_METHOD = 'Model method `{%1}` is not defined';

    /**
     * Inappropriate property message
     */
    const INAPPROPRIATE_PROPERTY = 'Inappropriate property `{%1}` for fillable object';

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ModelException
     */
    public static function notFound(string $name): ModelException
    {
        return new static(_message(self::MODEL_NOT_FOUND, $name), E_ERROR);
    }

    /**
     * @param array $names
     * @return \Quantum\Exceptions\ModelException
     */
    public static function notModelInstance(array $names): ModelException
    {
        return new static(_message(self::NOT_INSTANCE_OF_MODEL, $names), E_WARNING);
    }

    /**
     * @param string|null $name
     * @return \Quantum\Exceptions\ModelException
     */
    public static function noTableDefined(?string $name): ModelException
    {
        return new static(_message(self::MODEL_WITHOUT_TABLE_DEFINED, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ModelException
     */
    public static function undefinedMethod(string $name): ModelException
    {
        return new static(_message(self::UNDEFINED_MODEL_METHOD, $name), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\ModelException
     */
    public static function inappropriateProperty(string $name): ModelException
    {
        return new static(_message(self::INAPPROPRIATE_PROPERTY, $name), E_WARNING);
    }
}
