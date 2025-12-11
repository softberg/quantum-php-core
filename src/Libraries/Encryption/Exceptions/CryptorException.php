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
    public static function publicKeyNotProvided(): CryptorException
    {
        return new static(ExceptionMessages::PUBLIC_KEY_MISSING, E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function privateKeyNotProvided(): CryptorException
    {
        return new static(ExceptionMessages::PRIVATE_KEY_MISSING, E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function invalidCipher(): CryptorException
    {
        return new static(ExceptionMessages::INVALID_CIPHER, E_WARNING);
    }
}