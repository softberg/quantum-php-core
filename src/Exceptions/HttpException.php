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
 * ViewException class
 * 
 * @package Quantum
 * @category Exceptions
 */
class HttpException extends \Exception
{
    /**
     * Unexpected request initialization
     */
    const UNEXPECTED_REQUEST_INITIALIZATION = 'HTTP Request can not be initialized outside of the core';

    /**
     * Unexpected response initialization
     */
    const UNEXPECTED_RESPONSE_INITIALIZATION = 'HTTP Response can not be initialized outside of the core';

    /**
     * Unavailable request method
     */
    const METHOD_NOT_AVAILABLE = 'Provided request method is not available';
}
