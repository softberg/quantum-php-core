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
 * @since 1.0.0
 */

namespace Quantum\Exceptions;

/**
 * RouteException class
 *
 * @package Quantum
 * @category Exceptions
 */
class RouteException extends \Exception
{
    /**
     * Route not found message
     */
    const ROUTE_NOT_FOUND = 'Route Not Found';

    /**
     * Route is not a closure message
     */
    const ROUTES_NOT_CLOSURE = 'Route is not a closure';

    /**
     * Repetitive route message with same method
     */
    const REPETITIVE_ROUTE_SAME_METHOD = 'Repetitive Routes with same method `{%1}`';

    /**
     * Repetitive route message with in different modules message
     */
    const REPETITIVE_ROUTE_DIFFERENT_MODULES = 'Repetitive Routes in different modules';

    /**
     * Incorrect method message
     */
    const INCORRECT_METHOD = 'Incorrect Method `{%1}`';
}
