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
 * @since 1.9.5
 */

namespace Quantum\Exceptions;

/**
 * ModelException class
 *
 * @package Quantum
 * @category Exceptions
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
}
