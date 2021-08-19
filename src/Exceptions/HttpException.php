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
 * Class HttpException
 * @package Quantum\Exceptions
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
    const METHOD_NOT_AVAILABLE = 'Provided request method `{%1}` is not available';

    /**
     * Content type not supported
     */
    const NOT_SUPPORTED_CONTENT_TYPE = 'The content type is not supported';

    /**
     * @return \Quantum\Exceptions\HttpException
     */
    public static function unexpectedRequestInitialization(): HttpException
    {
        return new static(self::UNEXPECTED_REQUEST_INITIALIZATION, E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\HttpException
     */
    public static function unexpectedResponseInitialization(): HttpException
    {
        return new static(self::UNEXPECTED_RESPONSE_INITIALIZATION, E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\HttpException
     */
    public static function methodNotAvailable(string $name): HttpException
    {
        return new static(_message(self::METHOD_NOT_AVAILABLE, $name), E_WARNING);
    }

    /**
     * @return static
     */
    public static function contentTypeNotSupported() {
        return new static(self::NOT_SUPPORTED_CONTENT_TYPE, E_WARNING);
    }

}
