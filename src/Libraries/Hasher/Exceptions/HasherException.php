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

namespace Quantum\Libraries\Hasher\Exceptions;

use Quantum\Libraries\Hasher\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class HasherException
 * @package Quantum\Libraries\Hasher
 */
class HasherException extends BaseException
{
    /**
     *
     * @param string $algorithm
     * @return self
     */
    public static function algorithmNotSupported(string $algorithm): self
    {
        return new self(
            _message(ExceptionMessages::ALGORITHM_NOT_SUPPORTED, $algorithm),
            E_WARNING
        );
    }

    /**
     *
     * @return self
     */
    public static function invalidBcryptCost(): self
    {
        return new self(
            ExceptionMessages::INVALID_BCRYPT_COST,
            E_WARNING
        );
    }
}
