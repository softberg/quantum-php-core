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

namespace Quantum\Router\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Router
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    public const CONTROLLER_NOT_FOUND = 'Controller class `{%1}` not found.';

    public const ACTION_NOT_DEFINED = 'Action `{%1}` not defined"';

    public const ROUTE_NOT_FOUND = 'Route not found';

    public const ROUTE_NOT_CLOSURE = 'Route is not a closure';

    public const REPETITIVE_ROUTE_WITH_SAME_NAME = 'Repetitive Routes with same method `{%1}`';

    public const REPETITIVE_ROUTE_IN_DIFFERENT_MODULES = 'Repetitive Routes in different modules';

    public const INCORRECT_METHOD = 'Incorrect route method `{%1}`';

    public const NAME_BEFORE_ROUTE_DEFINITION = 'Names can not be set before route definition';

    public const NAME_ON_ROUTE_GROUP = 'Name can not be set on route groups';

    public const NAME_NOT_UNIQUE = 'Route names should be unique';

    public const ROUTE_PARAM_NAME_IN_USE = 'Route param name `{%1}` already in use';

    public const INVALID_ROUTE_PARAM_NAME = 'Route param names can only contain letters';
}
