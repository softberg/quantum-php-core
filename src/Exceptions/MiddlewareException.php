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
 * @since 2.0.0
 */

namespace Quantum\Exceptions;

/**
 * RouteException class
 *
 * @package Quantum
 * @category Exceptions
 */
class MiddlewareException extends \Exception
{
    /**
     * Middleware not handled correctly
     */
    const MIDDLEWARE_NOT_HANDLED = 'Middleware `{%1}` not handled correctly';

    /**
     * Middleware not defined message
     */
    const MIDDLEWARE_NOT_DEFINED = 'Middleware `{%1}` not defined';

    /**
     * Middleware not found message
     */
    const MIDDLEWARE_NOT_FOUND = 'Middleware `{%1}` not found';
}
