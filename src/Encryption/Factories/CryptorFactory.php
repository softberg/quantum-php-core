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
 * @since 3.0.0
 */

namespace Quantum\Encryption\Factories;

use Quantum\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Encryption\Adapters\SymmetricEncryptionAdapter;
use Quantum\Encryption\Exceptions\CryptorException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Encryption\Cryptor;

/**
 * Class Cryptor
 * @package Quantum\Encryption
 */
class CryptorFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        Cryptor::SYMMETRIC => SymmetricEncryptionAdapter::class,
        Cryptor::ASYMMETRIC => AsymmetricEncryptionAdapter::class,
    ];

    /**
     * @var array
     */
    private static array $instances = [];

    /**
     * @param string $type
     * @return Cryptor
     * @throws BaseException
     */
    public static function get(string $type = Cryptor::SYMMETRIC): Cryptor
    {
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = self::createInstance($type);
        }

        return self::$instances[$type];
    }

    /**
     * @param string $type
     * @return Cryptor
     * @throws BaseException
     */
    private static function createInstance(string $type): Cryptor
    {
        if (!isset(self::ADAPTERS[$type])) {
            throw CryptorException::adapterNotSupported($type);
        }

        $adapterClass = self::ADAPTERS[$type];

        return new Cryptor(new $adapterClass());
    }
}
