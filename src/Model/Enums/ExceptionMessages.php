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
 * @since 3.0.0
 */

namespace Quantum\Model\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Model
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const INAPPROPRIATE_MODEL_PROPERTY = 'Inappropriate property `{%1}` for fillable object';

    public const WRONG_RELATION = 'The model `{%1}` does not define relation with `{%2}`';

    public const RELATION_TYPE_MISSING = 'Relation type is missing for model `{%1}` with related model `{%2}`';

    public const MISSING_RELATION_KEYS = 'Relation keys `foreign_key` or `local_key` are missing for model `{%1}` with related model `{%2}`';

    public const MISSING_FOREIGN_KEY = 'Foreign key `{%1}` is missing in model `{%2}`';

    public const UNSUPPORTED_RELATION = 'Relation type `{%1}` is not supported';

    public const ORM_IS_NOT_SET = 'ORM instance not initialized';
}
