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

namespace Quantum\Libraries\JWToken;

use Quantum\Exceptions\AppException;

/**
 * Class JWTException
 * @package Quantum\Libraries\JWToken
 */
class JWTException extends AppException
{
    /**
     * @return JWTException
     */
    public static function payloadNotFound(): JWTException
    {
        return new static(t('exception.jwt_payload_not_found'), E_WARNING);
    }
}
