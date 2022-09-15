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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class JwtException
 * @package Quantum\Exceptions
 */
class JwtException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\JwtException
     */
    public static function payloadNotFound(): JwtException
    {
        return new static(t('exception.jwt_payload_not_found'), E_WARNING);
    }
}
