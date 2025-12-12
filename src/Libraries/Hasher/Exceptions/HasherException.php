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
        return new static(_message(ExceptionMessages::ALGORITHM_NOT_SUPPORTED, $algorithm), E_WARNING);
    }

    /**
     *
     * @return self
     */
    public static function invalidBcryptCost(): self
    {
        return new static(ExceptionMessages::INVALID_BCRYPT_COST, E_WARNING);
    }
}