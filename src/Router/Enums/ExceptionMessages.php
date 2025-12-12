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

namespace Quantum\Router\Enums;

use Quantum\App\Enums\ExceptionMessages as BaseExceptionMessages;

/**
 * Class ExceptionMessages
 * @package Quantum\Router
 */
final class ExceptionMessages extends BaseExceptionMessages
{
    const CONTROLLER_NOT_FOUND = 'Controller class `{%1}` not found.';

    const ACTION_NOT_DEFINED = 'Action `{%1}` not defined"';

    const ROUTE_NOT_FOUND = 'Route not found';

    const ROUTE_NOT_CLOSURE = 'Route is not a closure';

    const REPETITIVE_ROUTE_WITH_SAME_NAME = 'Repetitive Routes with same method `{%1}`';

    const REPETITIVE_ROUTE_IN_DIFFERENT_MODULES = 'Repetitive Routes in different modules';

    const INCORRECT_METHOD = 'Incorrect route method `{%1}`';

    const NAME_BEFORE_ROUTE_DEFINITION = 'Names can not be set before route definition';

    const NAME_ON_ROUTE_GROUP = 'Name can not be set on route groups';

    const NAME_NOT_UNIQUE = 'Route names should be unique';

    const ROUTE_PARAM_NAME_IN_USE = 'Route param name `{%1}` already in use';

    const INVALID_ROUTE_PARAM_NAME = 'Route param names can only contain letters';
}