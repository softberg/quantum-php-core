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

namespace Quantum\Libraries\Jwt\Exceptions;

use Quantum\Libraries\Jwt\Enums\ExceptionMessages;
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
        return new static(ExceptionMessages::MISSING_PAYLOAD, E_WARNING);
    }
}