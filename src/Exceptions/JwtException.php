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
 * Class JwtException
 * @package Quantum\Exceptions
 */
class JwtException extends \Exception
{
    /**
     * JWT payload not found message
     */
    const JWT_PAYLOAD_NOT_FOUND = 'JWT payload is missing';

    /**
     * @return \Quantum\Exceptions\JwtException
     */
    public static function payloadNotFound(): JwtException
    {
        return new static(self::JWT_PAYLOAD_NOT_FOUND, E_WARNING);
    }
}
