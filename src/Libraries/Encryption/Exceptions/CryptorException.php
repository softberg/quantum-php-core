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

namespace Quantum\Libraries\Encryption\Exceptions;

use Quantum\Libraries\Encryption\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class CryptorException
 * @package Quantum\Libraries\Encryption
 */
class CryptorException extends BaseException
{
    /**
     * @return CryptorException
     */
    public static function publicKeyNotProvided(): self
    {
        return new self(
            ExceptionMessages::PUBLIC_KEY_MISSING,
            E_WARNING
        );
    }

    /**
     * @return CryptorException
     */
    public static function privateKeyNotProvided(): self
    {
        return new self(
            ExceptionMessages::PRIVATE_KEY_MISSING,
            E_WARNING
        );
    }

    /**
     * @return CryptorException
     */
    public static function invalidCipher(): self
    {
        return new self(
            ExceptionMessages::INVALID_CIPHER,
            E_WARNING
        );
    }
}
