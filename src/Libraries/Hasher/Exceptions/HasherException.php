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

namespace Quantum\Libraries\Hasher\Exceptions;

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
        return new static(t('exception.not_supported_algorithm', $algorithm), E_WARNING);
    }

    /**
     *
     * @return self
     */
    public static function invalidBcryptCost(): self
    {
        return new static(t('exception.invalid_bcrypt_cost'), E_WARNING);
    }
}