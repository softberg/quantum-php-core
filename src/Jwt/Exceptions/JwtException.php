<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Jwt\Exceptions;

use Quantum\App\Exceptions\BaseException;
use Quantum\Jwt\Enums\ExceptionMessages;

/**
 * Class JwtException
 * @package Quantum\JwtToken
 */
class JwtException extends BaseException
{
    public static function payloadNotFound(): self
    {
        return new self(
            ExceptionMessages::MISSING_PAYLOAD,
            E_WARNING
        );
    }
}
