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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Jwt\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class JwtException
 * @package Quantum\Libraries\JwtToken
 */
class JwtException extends BaseException
{
    /**
     * @return JwtException
     */
    public static function payloadNotFound(): JwtException
    {
        return new static(t('exception.jwt_payload_not_found'), E_WARNING);
    }
}