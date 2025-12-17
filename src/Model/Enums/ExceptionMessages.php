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

namespace Quantum\Model\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Model
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const INAPPROPRIATE_MODEL_PROPERTY = 'Inappropriate property `{%1}` for fillable object';

    const WRONG_RELATION = 'The model `{%1}` does not define relation with `{%2}`';

    const RELATION_TYPE_MISSING = 'Relation type is missing for model `{%1}` with related model `{%2}`';

    const MISSING_RELATION_KEYS = 'Relation keys `foreign_key` or `local_key` are missing for model `{%1}` with related model `{%2}`';

    const MISSING_FOREIGN_KEY = 'Foreign key `{%1}` is missing in model `{%2}`';

    const UNSUPPORTED_RELATION = 'Relation type `{%1}` is not supported';
}