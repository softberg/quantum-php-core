<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\RateLimit\Exceptions;

use Quantum\RateLimit\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

class RateLimitException extends BaseException
{
    public static function adapterNotSupported(string $adapter): self
    {
        return new self(
            _message(ExceptionMessages::ADAPTER_NOT_SUPPORTED, [$adapter]),
            E_ERROR
        );
    }
}
