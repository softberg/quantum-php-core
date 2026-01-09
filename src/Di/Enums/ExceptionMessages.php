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

namespace Quantum\Di\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Di
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const DEPENDENCY_NOT_REGISTERED = 'The dependency `{%1}` is not registered.';

    public const DEPENDENCY_ALREADY_REGISTERED = 'The dependency `{%1}` is already registered.';

    public const DEPENDENCY_NOT_INSTANTIABLE = 'The dependency `{%1}` is not instantiable.';

    public const INVALID_ABSTRACT_DEPENDENCY = 'The dependency `{%1}` is not valid abstract class.';

    public const CIRCULAR_DEPENDENCY = 'Circular dependency detected: `{%1}`';

    public const INVALID_CALLABLE = 'Invalid callable provided: expected Closure or array-style callable `{%1}`';
}
