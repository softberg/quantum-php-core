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
 * @since 2.9.5
 */

namespace Quantum\Http\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class HttpException
 * @package Quantum\Exceptions
 */
class HttpException extends BaseException
{
    /**
     * @return HttpException
     */
    public static function unexpectedRequestInitialization(): HttpException
    {
        return new static(t('exception.unexpected_request_initialization'), E_WARNING);
    }

    /**
     * @return HttpException
     */
    public static function unexpectedResponseInitialization(): HttpException
    {
        return new static(t('exception.unexpected_response_initialization'), E_WARNING);
    }

    /**
     * @param string $name
     * @return HttpException
     */
    public static function methodNotAvailable(string $name): HttpException
    {
        return new static(t('exception.method_not_available', $name), E_WARNING);
    }

    /**
     * @return static
     */
    public static function contentTypeNotSupported(): HttpException
    {
        return new static(t('exception.not_supported_content_type'), E_WARNING);
    }

}
