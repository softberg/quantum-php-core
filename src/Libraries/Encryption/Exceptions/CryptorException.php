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

namespace Quantum\Libraries\Encryption\Exceptions;

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
    public static function configNotFound(): CryptorException
    {
        return new static(t('exception.openssl_config_not_found'), E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function noPrivateKeyCreated(): CryptorException
    {
        return new static(t('exception.openssl_private_key_not_created'), E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function publicKeyNotProvided(): CryptorException
    {
        return new static(t('exception.openssl_public_key_not_provided'), E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function privateKeyNotProvided(): CryptorException
    {
        return new static(t('exception.openssl_private_key_not_provided'), E_WARNING);
    }

    /**
     * @return CryptorException
     */
    public static function invalidCipher(): CryptorException
    {
        return new static(t('exception.openssl_invalid_cipher'), E_WARNING);
    }
}